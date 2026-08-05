<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\LeadActivityModel;
use App\Models\SiteSettingModel;
use App\Models\CountryModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $activityModel;
    protected $countryModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->activityModel = new LeadActivityModel();
        $this->countryModel = new CountryModel();
        $this->session = session();
    }

    protected function checkRestrictedKeywords($text)
    {
        $settingModel = new SiteSettingModel();
        $textLower = strtolower($text);

        $keywords = $settingModel->getSetting('restricted_keywords', '');
        if (!empty($keywords)) {
            $keywordList = array_map('trim', array_filter(preg_split('/[,\n]+/', strtolower($keywords))));
            foreach ($keywordList as $kw) {
                if (!empty($kw) && strpos($textLower, $kw) !== false) {
                    return $kw;
                }
            }
        }

        $profanityEnabled = $settingModel->getSetting('profanity_filter', '0');
        if ($profanityEnabled === '1') {
            $profanityList = ['damn', 'hell', 'crap', 'stupid', 'idiot', 'fool', 'scam', 'fraud', 'fake', 'spam', 'porn', 'xxx', 'casino', 'gambling', 'drugs', 'narcotic', 'cocaine', 'heroin', 'marijuana', 'counterfeit', 'pirated', 'illegal', 'terrorist', 'weapon', 'explosive', 'smuggle', 'trafficking', 'money laundering'];
            foreach ($profanityList as $word) {
                if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text)) {
                    return $word;
                }
            }
        }

        return false;
    }

    public function register()
    {
        $settingModel = new SiteSettingModel();
        $allowRegistration = $settingModel->getSetting('allow_registration', '1');
        if ($allowRegistration === '0') {
            return redirect()->to('/login')->with('error', 'Registration is currently closed. Please try again later.');
        }

        if ($this->request->getMethod() === 'GET') {
            $data['title'] = 'Register';
            $data['metaDescription'] = 'Create your free B2B Trade Services account as a buyer or supplier and start connecting with trade partners today.';
            $data['canonical'] = current_url();
            $data['countries'] = $this->countryModel->getActiveCountries();
            return view('pages/register', $data);
        }

        $userType = $this->request->getPost('user_type') ?: $this->request->getPost('role');

        $rules = [
            'name'      => 'required|min_length[2]|max_length[255]',
            'email'     => 'required|valid_email|is_unique[users.email]',
            'password'  => 'required|min_length[6]',
        ];

        if (!$userType || !in_array($userType, ['supplier', 'buyer'])) {
            return redirect()->back()->withInput()->with('error', 'Please select Supplier or Buyer.');
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $contentToCheck = $this->request->getPost('name') . ' ' . $this->request->getPost('company_name') . ' ' . $this->request->getPost('selling_products') . ' ' . $this->request->getPost('buying_products') . ' ' . $this->request->getPost('requirement');
        $restrictedWord = $this->checkRestrictedKeywords($contentToCheck);
        if ($restrictedWord !== false) {
            return redirect()->back()->withInput()->with('error', 'Your registration contains a restricted keyword: "' . $restrictedWord . '". Please revise and try again.');
        }

        $userData = [
            'name'             => $this->request->getPost('name'),
            'email'            => $this->request->getPost('email'),
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
            'lead_source'      => $this->request->getPost('lead_source') ?: 'website',
            'landing_page_url' => $this->request->getServer('HTTP_REFERER') ?: '',
        ];

        $insertId = $this->userModel->insert($userData);

        if ($insertId) {
            $this->activityModel->logActivity($insertId, 'registration', 'User registered as ' . $userData['user_type']);

            helper('email');
            notifyAdminNewRegistration($userData);
            sendWelcomeEmail($userData);

            $source = $this->request->getPost('source');
            if ($source === 'bcm-appointment') {
                return redirect()->to('/')->with('success', 'Thank you for showing interest, one of our senior Buyer Consultant Manager will contact you shortly.');
            }
            if ($userData['status'] === 'pending') {
                return redirect()->to('/login')->with('success', 'Thank you for joining B2B Trade Services! Your account is pending admin approval. You will be notified once your account is activated.');
            }
            return redirect()->to('/login')->with('success', 'Thank you for joining B2B Trade Services! Your account has been created successfully. Please login to continue.');
        }

        return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
    }

    public function login()
    {
        if ($this->request->getMethod() === 'GET') {
            $data['title'] = 'Login';
            $data['metaDescription'] = 'Log in to your B2B Trade Services account to manage your inquiries, products, and supplier profile.';
            $data['canonical'] = current_url();
            return view('pages/login', $data);
        }

        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
        }

        if (!password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
        }

        $request = $this->request;
        $this->userModel->update($user['id'], [
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $request->getIPAddress(),
            'last_device_type' => $this->detectDeviceType($request->getUserAgent()->getAgentString()),
        ]);

        $this->activityModel->logActivity($user['id'], 'login', 'User logged in');

        $sessionData = [
            'user_id'   => $user['id'],
            'name'      => $user['name'],
            'email'     => $user['email'],
            'user_type' => $user['user_type'],
            'status'    => $user['status'],
            'logged_in' => true,
        ];

        $this->session->set($sessionData);

        switch ($user['user_type']) {
            case 'admin':
            case 'agent':
                return redirect()->to('/dashboard/admin')->with('success', 'Welcome back, ' . $user['name'] . '!');
            case 'supplier':
                return redirect()->to('/dashboard/supplier')->with('success', 'Welcome back, ' . $user['name'] . '!');
            case 'buyer':
                return redirect()->to('/dashboard/buyer')->with('success', 'Welcome back, ' . $user['name'] . '!');
            default:
                return redirect()->to('/')->with('success', 'Welcome back, ' . $user['name'] . '!');
        }
    }

    public function forgotPassword()
    {
        $meta = [
            'metaDescription' => 'Reset your B2B Trade Services account password.',
            'canonical' => base_url('forgot-password'),
        ];

        if ($this->request->getMethod() === 'GET') {
            return view('pages/forget-password', ['title' => 'Forgot Password'] + $meta);
        }

        $email = trim($this->request->getPost('email') ?? '');

        $viewData = ['title' => 'Forgot Password', 'email' => $email] + $meta;

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $viewData['error'] = 'Please enter a valid email address.';
            return view('pages/forget-password', $viewData);
        }

        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            $viewData['success'] = 'If that email is registered, you will receive a reset link shortly.';
            return view('pages/forget-password', $viewData);
        }

        $token   = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $db = \Config\Database::connect();
        $db->table('users')->where('id', $user['id'])->update([
            'reset_token'         => $token,
            'reset_token_expires' => $expires,
        ]);

        $resetLink = base_url('reset-password/' . $token);

        helper('email');
        $sent = sendPasswordResetEmail($user['email'], $user['name'], $resetLink);

        if (!$sent) {
            log_message('warning', 'Password reset email not sent for user: ' . $user['email'] . ' — reset link: ' . $resetLink);
        }

        $viewData['success'] = 'If that email is registered, you will receive a reset link shortly.';
        return view('pages/forget-password', $viewData);
    }

    public function resetPassword(string $token = '')
    {
        if (empty($token)) {
            return redirect()->to('/forgot-password')->with('error', 'Invalid or missing reset token.');
        }

        $user = $this->userModel
            ->where('reset_token', $token)
            ->where('reset_token_expires >', date('Y-m-d H:i:s'))
            ->first();

        if (!$user) {
            return redirect()->to('/forgot-password')->with('error', 'This password reset link is invalid or has expired. Please request a new one.');
        }

        // Self-referencing canonical, not site-wide: the token makes this URL
        // single-use and per-request by design. It should stay out of search
        // results entirely (robots noindex, not just canonical) -- flagged
        // separately since that's a robots-directive decision, not something
        // this pass adds unasked.
        $meta = [
            'metaDescription' => 'Set a new password for your B2B Trade Services account.',
            'canonical' => current_url(),
        ];

        if ($this->request->getMethod() === 'GET') {
            return view('pages/reset-password', [
                'title' => 'Reset Password',
                'token' => $token,
            ] + $meta);
        }

        $password        = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        if (empty($password) || strlen($password) < 6) {
            return view('pages/reset-password', [
                'title' => 'Reset Password',
                'token' => $token,
                'error' => 'Password must be at least 6 characters.',
            ] + $meta);
        }

        if ($password !== $passwordConfirm) {
            return view('pages/reset-password', [
                'title' => 'Reset Password',
                'token' => $token,
                'error' => 'Passwords do not match.',
            ] + $meta);
        }

        $db = \Config\Database::connect();
        $db->table('users')->where('id', $user['id'])->update([
            'password'            => password_hash($password, PASSWORD_DEFAULT),
            'reset_token'         => null,
            'reset_token_expires' => null,
        ]);

        return redirect()->to('/login')->with('success', 'Your password has been reset successfully. Please log in with your new password.');
    }

    public function logout()
    {
        if ($this->session->get('logged_in')) {
            $this->activityModel->logActivity($this->session->get('user_id'), 'logout', 'User logged out');
        }
        $this->session->destroy();
        return redirect()->to('/login')->with('success', 'You have been logged out successfully.');
    }

    protected function detectDeviceType(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);
        if (preg_match('/mobile|android|iphone|ipod|blackberry|windows phone/', $userAgent)) {
            return 'Mobile';
        }
        if (preg_match('/tablet|ipad/', $userAgent)) {
            return 'Tablet';
        }
        return 'Desktop';
    }
}
