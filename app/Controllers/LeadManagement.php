<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CountryModel;
use App\Models\LeadNoteModel;
use App\Models\LeadActivityModel;

class LeadManagement extends BaseController
{
    protected $userModel;
    protected $countryModel;
    protected $noteModel;
    protected $activityModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->countryModel = new CountryModel();
        $this->noteModel = new LeadNoteModel();
        $this->activityModel = new LeadActivityModel();
        $this->session = session();
    }

    protected function checkAccess()
    {
        if (!$this->session->get('logged_in')) {
            return redirect()->to('/login');
        }
        $userType = $this->session->get('user_type');
        if (!in_array($userType, ['admin', 'agent'])) {
            return redirect()->to('/dashboard');
        }
        return null;
    }

    protected function getFilters(): array
    {
        return [
            'lead_type'        => $this->request->getGet('lead_type'),
            'uid'              => $this->request->getGet('uid'),
            'name'             => $this->request->getGet('name'),
            'country_id'       => $this->request->getGet('country_id'),
            'phone'            => $this->request->getGet('phone'),
            'whatsapp'         => $this->request->getGet('whatsapp'),
            'email'            => $this->request->getGet('email'),
            'membership_level' => $this->request->getGet('membership_level'),
            'lead_stage'       => $this->request->getGet('lead_stage'),
            'assigned_agent_id'=> $this->request->getGet('assigned_agent_id'),
            'lead_source'      => $this->request->getGet('lead_source'),
            'region'           => $this->request->getGet('region'),
            'products'         => $this->request->getGet('products'),
            'date_from'        => $this->request->getGet('date_from'),
            'date_to'          => $this->request->getGet('date_to'),
            'sort'             => $this->request->getGet('sort'),
            'sort_dir'         => $this->request->getGet('sort_dir'),
        ];
    }

    protected function getCommonData(): array
    {
        $user = $this->userModel->find($this->session->get('user_id'));
        return [
            'user' => $user,
            'countries' => $this->countryModel->getActiveCountries(),
            'region_mapping' => $this->userModel->getRegionMapping(),
            'agents' => $this->userModel->getAgents(),
            'lead_stages' => $this->userModel->getLeadStages(),
            'membership_levels' => $this->userModel->getMembershipLevels(),
        ];
    }

    public function allLeads()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $filters = $this->getFilters();
        $page = max(1, (int) $this->request->getGet('page') ?: 1);

        if ($this->session->get('user_type') === 'agent') {
            $filters['assigned_agent_id'] = $this->session->get('user_id');
        }

        $result = $this->userModel->getLeads($filters, 100, $page);

        $data = $this->getCommonData();
        $data['title'] = 'All Leads';
        $data['leads'] = $result['leads'];
        $data['pagination'] = $result;
        $data['filters'] = $filters;
        $data['page_type'] = 'all';
        $data['latest_notes'] = $this->noteModel->getLatestNotesForLeads(array_column($result['leads'], 'id'));

        return view('dashboard/admin/leads', $data);
    }

    public function buyerLeads()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $filters = $this->getFilters();
        $filters['lead_type'] = 'buyer';
        $page = max(1, (int) $this->request->getGet('page') ?: 1);

        if ($this->session->get('user_type') === 'agent') {
            $filters['assigned_agent_id'] = $this->session->get('user_id');
        }

        $result = $this->userModel->getLeads($filters, 25, $page);

        $data = $this->getCommonData();
        $data['title'] = 'Buyer Leads';
        $data['leads'] = $result['leads'];
        $data['pagination'] = $result;
        $data['filters'] = $filters;
        $data['page_type'] = 'buyer';
        $data['latest_notes'] = $this->noteModel->getLatestNotesForLeads(array_column($result['leads'], 'id'));

        return view('dashboard/admin/leads', $data);
    }

    public function supplierLeads()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $filters = $this->getFilters();
        $filters['lead_type'] = 'supplier';
        $page = max(1, (int) $this->request->getGet('page') ?: 1);

        if ($this->session->get('user_type') === 'agent') {
            $filters['assigned_agent_id'] = $this->session->get('user_id');
        }

        $result = $this->userModel->getLeads($filters, 25, $page);

        $data = $this->getCommonData();
        $data['title'] = 'Supplier Leads';
        $data['leads'] = $result['leads'];
        $data['pagination'] = $result;
        $data['filters'] = $filters;
        $data['page_type'] = 'supplier';
        $data['latest_notes'] = $this->noteModel->getLatestNotesForLeads(array_column($result['leads'], 'id'));

        return view('dashboard/admin/leads', $data);
    }

    public function mySupplierLeads()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $filters = $this->getFilters();
        $filters['lead_type'] = 'supplier';
        $filters['assigned_agent_id'] = $this->session->get('user_id');
        $page = max(1, (int) $this->request->getGet('page') ?: 1);

        $result = $this->userModel->getLeads($filters, 25, $page);

        $data = $this->getCommonData();
        $data['title'] = 'My Supplier Leads';
        $data['leads'] = $result['leads'];
        $data['pagination'] = $result;
        $data['filters'] = $filters;
        $data['page_type'] = 'my_supplier';
        $data['latest_notes'] = $this->noteModel->getLatestNotesForLeads(array_column($result['leads'], 'id'));

        return view('dashboard/admin/leads', $data);
    }

    public function myBuyerLeads()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $filters = $this->getFilters();
        $filters['lead_type'] = 'buyer';
        $filters['assigned_agent_id'] = $this->session->get('user_id');
        $page = max(1, (int) $this->request->getGet('page') ?: 1);

        $result = $this->userModel->getLeads($filters, 25, $page);

        $data = $this->getCommonData();
        $data['title'] = 'My Buyer Leads';
        $data['leads'] = $result['leads'];
        $data['pagination'] = $result;
        $data['filters'] = $filters;
        $data['page_type'] = 'my_buyer';
        $data['latest_notes'] = $this->noteModel->getLatestNotesForLeads(array_column($result['leads'], 'id'));

        return view('dashboard/admin/leads', $data);
    }

    public function leadDetail($uid)
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $lead = $this->userModel->findByUid($uid);

        if (!$lead) {
            return redirect()->to('/leads/all')->with('error', 'Lead not found.');
        }

        if ($this->session->get('user_type') === 'agent' && $lead['assigned_agent_id'] != $this->session->get('user_id')) {
            return redirect()->to('/leads/all')->with('error', 'You do not have access to this lead.');
        }

        $country = null;
        if ($lead['country_id']) {
            $country = $this->countryModel->find($lead['country_id']);
        }

        $notes = $this->noteModel->getNotesWithAgent($lead['id']);
        $activities = $this->activityModel->getActivitiesByUser($lead['id']);

        $assignedAgent = null;
        if ($lead['assigned_agent_id']) {
            $assignedAgent = $this->userModel->find($lead['assigned_agent_id']);
        }

        $data = $this->getCommonData();
        $data['title'] = 'Lead Detail - ' . $lead['uid'];
        $data['lead'] = $lead;
        $data['country'] = $country;
        $data['notes'] = $notes;
        $data['activities'] = $activities;
        $data['assigned_agent'] = $assignedAgent;

        return view('dashboard/admin/lead-detail', $data);
    }

    public function updateLeadStage()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $leadId = $this->request->getPost('lead_id');
        $stage = $this->request->getPost('lead_stage');

        $lead = $this->userModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        if ($this->session->get('user_type') === 'agent' && $lead['assigned_agent_id'] != $this->session->get('user_id')) {
            return redirect()->back()->with('error', 'You do not have access to this lead.');
        }

        $stages = $this->userModel->getLeadStages();
        if (!array_key_exists($stage, $stages)) {
            return redirect()->back()->with('error', 'Invalid lead stage.');
        }

        $this->userModel->update($leadId, ['lead_stage' => $stage]);
        $this->activityModel->logActivity($leadId, 'stage_change', 'Lead stage changed to: ' . $stages[$stage]);

        return redirect()->back()->with('success', 'Lead stage updated successfully.');
    }

    public function assignAgent()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        if ($this->session->get('user_type') !== 'admin') {
            return redirect()->back()->with('error', 'Only admins can assign agents.');
        }

        $leadId = $this->request->getPost('lead_id');
        $agentId = $this->request->getPost('agent_id');

        $lead = $this->userModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        if ($agentId) {
            $agent = $this->userModel->find($agentId);
            if (!$agent || !in_array($agent['user_type'], ['agent', 'admin'])) {
                return redirect()->back()->with('error', 'Invalid agent.');
            }
        }

        $this->userModel->update($leadId, ['assigned_agent_id' => $agentId ?: null]);

        $agentName = $agentId ? ($agent['name'] ?? 'Unknown') : 'Unassigned';
        $this->activityModel->logActivity($leadId, 'agent_assigned', 'Lead assigned to: ' . $agentName);

        return redirect()->back()->with('success', 'Agent assigned successfully.');
    }

    public function addNote()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $leadId = $this->request->getPost('lead_id');
        $noteText = $this->request->getPost('note');

        if (empty($noteText)) {
            return redirect()->back()->with('error', 'Note cannot be empty.');
        }

        $lead = $this->userModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        if ($this->session->get('user_type') === 'agent' && $lead['assigned_agent_id'] != $this->session->get('user_id')) {
            return redirect()->back()->with('error', 'You do not have access to this lead.');
        }

        $this->noteModel->insert([
            'lead_user_id' => $leadId,
            'agent_user_id' => $this->session->get('user_id'),
            'note' => $noteText,
        ]);

        return redirect()->back()->with('success', 'Note added successfully.');
    }

    public function updateMembership()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        if ($this->session->get('user_type') !== 'admin') {
            return redirect()->back()->with('error', 'Only admins can change membership levels.');
        }

        $leadId = $this->request->getPost('lead_id');
        $level = $this->request->getPost('membership_level');

        $lead = $this->userModel->find($leadId);
        if (!$lead) {
            return redirect()->back()->with('error', 'Lead not found.');
        }

        $levels = $this->userModel->getMembershipLevels();
        if (!array_key_exists($level, $levels)) {
            return redirect()->back()->with('error', 'Invalid membership level.');
        }

        $this->userModel->update($leadId, ['membership_level' => $level]);
        $this->activityModel->logActivity($leadId, 'membership_change', 'Membership changed to: ' . $levels[$level]);

        return redirect()->back()->with('success', 'Membership level updated successfully.');
    }

    public function ajaxUpdateStage()
    {
        $redirect = $this->checkAccess();
        if ($redirect) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $leadId = $this->request->getPost('lead_id');
        $stage = $this->request->getPost('lead_stage');

        $lead = $this->userModel->find($leadId);
        if (!$lead) {
            return $this->response->setJSON(['success' => false, 'message' => 'Lead not found']);
        }

        if ($this->session->get('user_type') === 'agent' && $lead['assigned_agent_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $stages = $this->userModel->getLeadStages();
        if (!array_key_exists($stage, $stages)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid stage']);
        }

        $this->userModel->update($leadId, ['lead_stage' => $stage]);
        $this->activityModel->logActivity($leadId, 'stage_change', 'Lead stage changed to: ' . $stages[$stage]);

        return $this->response->setJSON(['success' => true, 'message' => 'Stage updated']);
    }

    public function ajaxAddNote()
    {
        $redirect = $this->checkAccess();
        if ($redirect) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $leadId = $this->request->getPost('lead_id');
        $noteText = $this->request->getPost('note');

        if (empty($noteText)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Note cannot be empty']);
        }

        $lead = $this->userModel->find($leadId);
        if (!$lead) {
            return $this->response->setJSON(['success' => false, 'message' => 'Lead not found']);
        }

        if ($this->session->get('user_type') === 'agent' && $lead['assigned_agent_id'] != $this->session->get('user_id')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Access denied']);
        }

        $this->noteModel->insert([
            'lead_user_id' => $leadId,
            'agent_user_id' => $this->session->get('user_id'),
            'note' => $noteText,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Note added']);
    }
}
