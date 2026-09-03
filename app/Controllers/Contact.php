<?php

namespace App\Controllers;

use App\Models\ContactSubmissionModel;
use App\Models\UserModel;

class Contact extends BaseController
{
    protected $submissionModel;

    public function __construct()
    {
        $this->submissionModel = new ContactSubmissionModel();
    }

    protected function resolveLeadType(): string
    {
        $leadType = strtolower(trim($this->request->getPost('lead_type') ?? ''));
        if (in_array($leadType, ['buyer', 'supplier'])) {
            return $leadType;
        }
        $industry = strtolower(trim($this->request->getPost('industry') ?? ''));
        if (in_array($industry, ['buyer', 'supplier'])) {
            return $industry;
        }
        return 'supplier';
    }

    protected function createLeadIfNew(array $data, string $leadType): void
    {
        $email = $data['email'] ?? '';
        if (empty($email)) {
            return;
        }

        $userModel = new UserModel();
        $existing = $userModel->where('email', $email)->first();
        if ($existing) {
            return;
        }

        $countryId = $this->request->getPost('country') ?: null;
        $uid = strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));

        $leadData = [
            'uid'          => $uid,
            'name'         => $data['name'] ?? '',
            'email'        => $email,
            'phone'        => $data['phone'] ?? '',
            'company_name' => $data['company'] ?? '',
            'country_id'   => $countryId,
            'user_type'    => $leadType,
            'lead_stage'   => 'new',
            'lead_source'  => $data['source_page'] ?? '',
            'status'       => 'pending',
        ];

        $userModel->skipValidation(true)->insert($leadData);
    }

    public function submit()
    {
        if ($this->request->getMethod() !== 'POST') {
            return redirect()->back();
        }

        $formType = $this->request->getPost('form_type') ?? 'contact';

        $data = [
            'form_type'   => $formType,
            'name'        => trim($this->request->getPost('name') ?? ''),
            'email'       => trim($this->request->getPost('email') ?? ''),
            'phone'       => trim($this->request->getPost('phone') ?? ''),
            'company'     => trim($this->request->getPost('company') ?? ''),
            'industry'    => trim($this->request->getPost('industry') ?? ''),
            'quantity'    => trim($this->request->getPost('quantity') ?? ''),
            'message'     => trim($this->request->getPost('message') ?? ''),
            'source_page' => trim($this->request->getPost('source_page') ?? ''),
            'source_id'   => $this->request->getPost('source_id') ?: null,
            'status'      => 'new',
        ];

        if (empty($data['name']) && empty($data['email'])) {
            return redirect()->back()->with('error', 'Please fill in at least your name and email.');
        }

        $contentToCheck = $data['name'] . ' ' . $data['company'] . ' ' . $data['industry'] . ' ' . $data['message'];
        $restrictedWord = check_restricted_keywords($contentToCheck);
        if ($restrictedWord !== false) {
            return redirect()->back()->withInput()->with('error', 'Your submission contains a restricted keyword: "' . $restrictedWord . '". Please revise and try again.');
        }

        if ($this->submissionModel->insert($data)) {
            $leadType = $this->resolveLeadType();
            $this->createLeadIfNew($data, $leadType);

            if ($formType === 'rfq') {
                $inquiryModel = new \App\Models\BuyerInquiryModel();
                $inquiryModel->insert([
                    'title'          => trim($this->request->getPost('title') ?? ''),
                    'description'    => $data['message'],
                    'category_id'    => $this->request->getPost('category') ?: null,
                    'country_id'     => $this->request->getPost('country') ?: null,
                    'quantity'       => $data['quantity'],
                    'buyer_name'     => $data['name'],
                    'buyer_email'    => $data['email'],
                    'buyer_phone'    => $data['phone'],
                    'buyer_whatsapp' => $this->request->getPost('whatsapp') ? 1 : 0,
                    'inquiry_date'   => date('Y-m-d'),
                    'status'         => 'active',
                ]);
            }

            return redirect()->back()->with('success', 'Thank you! Your inquiry has been submitted successfully. We will get back to you soon.');
        }

        return redirect()->back()->with('error', 'Something went wrong. Please try again.');
    }
    
    public function submitAjax()
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request method.']);
        }

        $formType = $this->request->getPost('form_type') ?? 'contact';

        $skipLeadForms = ['tradeshow_application', 'agent_partner_application'];

        $partnershipRaw = $this->request->getPost('partnership');
        $partnershipJson = null;
        if (!empty($partnershipRaw)) {
            $partnershipJson = is_array($partnershipRaw) ? json_encode($partnershipRaw) : json_encode([$partnershipRaw]);
        }

        $countryId = $this->request->getPost('country_id') ?: null;
        $countryName = null;
        if ($countryId) {
            $countryModel = new \App\Models\CountryModel();
            $country = $countryModel->find($countryId);
            $countryName = $country['name'] ?? null;
        }

        $systemFields = ['csrf_test_name', 'csrf_cookie_name', 'form_type', 'source_page', 'source_id', 'OPT_IN'];
        $allPost = $this->request->getPost();
        $dynamicFields = [];
        foreach ($allPost as $key => $value) {
            if (in_array($key, $systemFields)) continue;
            if ($key === 'country_id' && $countryName) {
                $dynamicFields['country'] = $countryName;
                continue;
            }
            if ($key === 'partnership[]' || $key === 'partnership') {
                $dynamicFields['partnership'] = is_array($value) ? implode(', ', $value) : $value;
                continue;
            }
            if (is_array($value)) {
                $dynamicFields[$key] = implode(', ', $value);
            } else {
                $dynamicFields[$key] = trim($value);
            }
        }

        $data = [
            'form_type'   => $formType,
            'name'        => trim($this->request->getPost('name') ?? ''),
            'email'       => trim($this->request->getPost('email') ?? ''),
            'phone'       => trim($this->request->getPost('phone') ?? ''),
            'country_id'  => $countryId,
            'partnership' => $partnershipJson,
            'whatsapp'    => $this->request->getPost('whatsapp') ? 1 : 0,
            'company'     => trim($this->request->getPost('company') ?? ''),
            'industry'    => trim($this->request->getPost('industry') ?? ''),
            'quantity'    => trim($this->request->getPost('quantity') ?? ''),
            'message'     => trim($this->request->getPost('message') ?? ''),
            'source_page' => trim($this->request->getPost('source_page') ?? ''),
            'source_id'   => $this->request->getPost('source_id') ?: null,
            'form_data'   => json_encode($dynamicFields),
            'status'      => 'new',
        ];

        if (empty($data['name']) && empty($data['email'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Please fill in at least your name and email.']);
        }

        $contentToCheck = $data['name'] . ' ' . $data['company'] . ' ' . $data['industry'] . ' ' . $data['message'];
        $restrictedWord = check_restricted_keywords($contentToCheck);
        if ($restrictedWord !== false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Your submission contains a restricted keyword: "' . $restrictedWord . '". Please revise and try again.']);
        }

        if ($this->submissionModel->insert($data)) {
            if (!in_array($formType, $skipLeadForms)) {
                $leadType = $this->resolveLeadType();
                $this->createLeadIfNew($data, $leadType);
            }
            return $this->response->setJSON(['status' => 'success', 'message' => 'Thank you! Your inquiry has been submitted successfully. We will get back to you soon.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Something went wrong. Please try again.']);
    }
}
