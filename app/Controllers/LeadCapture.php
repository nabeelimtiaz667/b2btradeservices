<?php

namespace App\Controllers;

use App\Models\LeadModel;
use App\Models\UserModel;
use App\Models\CountryModel;
use App\Models\LeadActivityModel;
use App\Models\SiteSettingModel;

/**
 * Popup lead-capture flow (T-29): step 1 (`capture`) records a prospect into
 * `leads`; step 2 (`verify` -> `completeSignup`) is the actual account creation,
 * inserting straight into `users`. Named `LeadCapture` rather than `Lead` to stay
 * clearly distinct from the existing `LeadManagement` controller and the
 * `lead_stage`/`LeadActivityModel` CRM fields already on `users` -- unrelated
 * concepts that happen to share the word "lead". Full design:
 * .claude/plans/T-29-lead-capture.md
 */
class LeadCapture extends BaseController
{
    protected LeadModel $leadModel;
    protected UserModel $userModel;
    protected CountryModel $countryModel;

    public function __construct()
    {
        $this->leadModel = new LeadModel();
        $this->userModel = new UserModel();
        $this->countryModel = new CountryModel();
    }

    /**
     * Step 1: AJAX submit from the popup. Mirrors Contact::submitAjax's JSON
     * response shape ({status, message}) so the same front-end pattern applies.
     */
    public function capture()
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request method.']);
        }

        $email = trim((string) $this->request->getPost('email'));
        $userType = $this->request->getPost('user_type');
        $name = trim((string) $this->request->getPost('name'));

        if (!in_array($userType, ['supplier', 'buyer'], true)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please select Supplier or Buyer.']);
        }

        if (mb_strlen($name) < 2) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please enter your name.']);
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please enter a valid email address.']);
        }

        // An email that already has an account is rejected outright, whatever
        // any stale `leads` row for it might say.
        if ($this->userModel->findByEmail($email)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'code'    => 'already_user',
                'message' => 'This email already has an account. Please log in instead.',
            ]);
        }

        $data = [
            'user_type'  => $userType,
            'name'       => $name,
            'email'      => $email,
            'phone'      => $this->request->getPost('phone'),
            'phone_code' => $this->request->getPost('phone_code'),
            'whatsapp'   => $this->request->getPost('whatsapp') ? 1 : 0,
        ];

        $existingLead = $this->leadModel->findByEmail($email);

        if (!$existingLead) {
            $id = $this->leadModel->createLead($data);
            if (!$id) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Something went wrong. Please try again.',
                    'errors'  => $this->leadModel->errors(),
                ]);
            }
            $this->sendVerification($this->leadModel->find($id));
            return $this->response->setJSON(['status' => 'success', 'step' => 'check_email']);
        }

        if ($this->leadModel->isAccountRegistered($existingLead)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'code'    => 'already_user',
                'message' => 'This email already has an account. Please log in instead.',
            ]);
        }

        if ($this->leadModel->isPopupFormFilled($existingLead)) {
            $this->leadModel->reissueForNewLead($existingLead['id'], $data);
            $this->sendVerification($this->leadModel->find($existingLead['id']));
            return $this->response->setJSON(['status' => 'success', 'step' => 'check_email']);
        }

        // status = 'email_verified': this is the reactivation path -- a lead
        // who verified their email but never finished step 2 (or simply lost
        // the link) resubmits the popup to get it resent. The link itself
        // never changes here: it's still valid (expiry only ever applies to
        // 'popup_form_filled', see plan), so this re-sends the exact same URL
        // rather than issuing a new token. Cooldown is based on `updated_at`
        // (captured before the contact-details update below touches it), so
        // the same form can't be used to spam repeat emails at someone else's
        // inbox.
        $cooledDown = empty($existingLead['updated_at'])
            || $existingLead['updated_at'] < gmdate('Y-m-d H:i:s', strtotime('-' . self::RESEND_COOLDOWN_MINUTES . ' minutes'));

        $this->leadModel->updateContactOnly($existingLead['id'], $data);

        if ($cooledDown) {
            $this->sendResume($this->leadModel->find($existingLead['id']));
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'step'    => 'already_verified',
            'message' => $cooledDown
                ? "Welcome back! We've re-sent your link to finish setting up your account."
                : 'You already verified this email. Check your inbox for the link to finish setting up your account.',
        ]);
    }

    /**
     * Cooldown for the reactivation resend below -- not a token/security TTL
     * (that's LeadModel::TOKEN_TTL_DAYS), just an anti-spam throttle on how
     * often resubmitting the popup for the same already-verified email will
     * actually trigger a new email send.
     */
    protected const RESEND_COOLDOWN_MINUTES = 5;

    protected function sendVerification(array $lead): void
    {
        helper('email');
        $link = base_url('lead/verify/' . $lead['verification_token']);
        sendLeadVerificationEmail($lead['email'], $lead['name'], $link);
    }

    /**
     * Reactivation: re-sends a verified lead's existing (still-valid) link.
     * Reachable by resubmitting the popup with the same, already-verified
     * email -- this is the resolution to the previously-deferred question of
     * how a lead who verified but never finished step 2 gets back in.
     */
    protected function sendResume(array $lead): void
    {
        helper('email');
        $link = base_url('lead/verify/' . $lead['verification_token']);
        sendLeadResumeEmail($lead['email'], $lead['name'], $link);
    }

    /**
     * Step 1 -> step 2 handoff. See plan's "Status lifecycle" table: expiry
     * only ever gates the 'popup_form_filled' -> 'email_verified' transition;
     * once verified, this link has no expiry and always re-opens step 2.
     */
    public function verify(string $token = '')
    {
        $lead = $this->leadModel->findByToken($token);

        if (!$lead) {
            return $this->verifyResultView('Invalid Link', 'This verification link is invalid.');
        }

        // Reconcile against `users`: an email that has since become a real
        // account always wins over whatever this lead row's status says.
        if ($this->userModel->findByEmail($lead['email'])) {
            if (!$this->leadModel->isAccountRegistered($lead)) {
                $this->leadModel->markAccountRegistered($lead['id']);
            }
            return $this->verifyResultView('Already Registered', 'This email already has an account.', true);
        }

        if ($this->leadModel->isAccountRegistered($lead)) {
            return $this->verifyResultView('Already Registered', 'This email already has an account.', true);
        }

        if ($this->leadModel->isPopupFormFilled($lead)) {
            if ($this->leadModel->isTokenExpired($lead)) {
                return $this->verifyResultView(
                    'Link Expired',
                    'This verification link has expired. Please submit the form again to receive a new one.'
                );
            }
            $this->leadModel->markEmailVerified($lead['id']);
        }

        return redirect()->to(base_url('lead/complete/' . $lead['verification_token']));
    }

    protected function verifyResultView(string $title, string $message, bool $showLogin = false)
    {
        return view('pages/lead-verify-result', [
            'title'           => $title,
            'metaDescription' => $message,
            'message'         => $message,
            'showLogin'       => $showLogin,
        ]);
    }

    /**
     * Step 2: the actual account creation. Only reachable once a lead is
     * 'email_verified'. Combines the lead's captured name/type/email/phone
     * with this step's password/company/country/products into a single
     * `users` insert, then runs the same post-registration sequence
     * Auth::register() uses.
     */
    public function completeSignup(string $token = '')
    {
        $lead = $this->leadModel->findByToken($token);

        if (!$lead || !$this->leadModel->isEmailVerified($lead)) {
            return redirect()->to(base_url())->with('error', 'This link is not valid. Please start again.');
        }

        if ($this->userModel->findByEmail($lead['email'])) {
            $this->leadModel->markAccountRegistered($lead['id']);
            return redirect()->to('/login')->with('success', 'This email already has an account. Please log in.');
        }

        $settingModel = new SiteSettingModel();

        if ($this->request->getMethod() === 'GET') {
            return view('pages/lead-complete-signup', [
                'title'           => 'Complete Your Registration',
                'metaDescription' => 'Finish creating your B2B Trade Services account.',
                'canonical'       => current_url(),
                'countries'       => $this->countryModel->getActiveCountries(),
                'lead'            => $lead,
                'token'           => $token,
            ]);
        }

        $userType = $this->request->getPost('user_type') ?: $lead['user_type'];
        if (!in_array($userType, ['supplier', 'buyer'], true)) {
            return redirect()->back()->withInput()->with('error', 'Please select Supplier or Buyer.');
        }

        $rules = [
            'name'     => 'required|min_length[2]|max_length[255]',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Race guard: the email could have been registered directly (e.g. via
        // /register in another tab) between the GET above and this POST.
        if ($this->userModel->findByEmail($lead['email'])) {
            $this->leadModel->markAccountRegistered($lead['id']);
            return redirect()->to('/login')->with('success', 'This email already has an account. Please log in.');
        }

        $userData = [
            'name'             => $this->request->getPost('name'),
            'email'            => $lead['email'],
            'password'         => $this->request->getPost('password'),
            'phone'            => $this->request->getPost('phone'),
            'phone_code'       => $this->request->getPost('phone_code'),
            'whatsapp'         => $this->request->getPost('whatsapp') ? 1 : 0,
            'country_id'       => $this->request->getPost('country_id'),
            'user_type'        => $userType,
            'selling_products' => $this->request->getPost('selling_products'),
            'buying_products'  => $this->request->getPost('buying_products'),
            'requirement'      => $this->request->getPost('requirement'),
            'company_name'     => $this->request->getPost('company_name'),
            'website'          => $this->request->getPost('website'),
            'city'             => $this->request->getPost('city'),
            'status'           => ($settingModel->getSetting('require_admin_review', '0') === '1') ? 'pending' : $settingModel->getSetting('default_user_status', 'approved'),
            'lead_stage'       => 'new',
            'membership_level' => 'free',
            'lead_source'      => 'popup',
            'landing_page_url' => $this->request->getServer('HTTP_REFERER') ?: '',
        ];

        $insertId = $this->userModel->insert($userData);

        if (!$insertId) {
            return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
        }

        $this->leadModel->markAccountRegistered($lead['id']);

        $activityModel = new LeadActivityModel();
        $activityModel->logActivity($insertId, 'registration', 'User registered as ' . $userData['user_type'] . ' (via lead capture popup)');

        helper('email');
        notifyAdminNewRegistration($userData);
        sendWelcomeEmail($userData);

        // Matches Auth::register()'s own flow exactly: no auto-login, redirect
        // to /login either way -- pending accounts must wait for approval, and
        // approved accounts still log in through the normal form.
        if ($userData['status'] === 'pending') {
            return redirect()->to('/login')->with('success', 'Thank you for joining B2B Trade Services! Your account is pending admin approval. You will be notified once your account is activated.');
        }

        return redirect()->to('/login')->with('success', 'Thank you for joining B2B Trade Services! Your account has been created successfully. Please login to continue.');
    }
}
