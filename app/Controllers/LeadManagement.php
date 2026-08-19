<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\CountryModel;
use App\Models\LeadNoteModel;
use App\Models\LeadActivityModel;
use App\Models\LeadModel;
use App\Models\PopupLeadNoteModel;

class LeadManagement extends BaseController
{
    protected $userModel;
    protected $countryModel;
    protected $noteModel;
    protected $activityModel;
    protected $leadModel;
    protected $popupNoteModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->countryModel = new CountryModel();
        $this->noteModel = new LeadNoteModel();
        $this->activityModel = new LeadActivityModel();
        $this->leadModel = new LeadModel();
        $this->popupNoteModel = new PopupLeadNoteModel();
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

    /**
     * "Popup Leads" — a separate data source from all the other methods on
     * this controller, which list `users` rows (people who already have
     * accounts) being tracked through the CRM lead_stage pipeline. This lists
     * raw `leads` table captures instead: prospects from the popup CTA who
     * may not have an account at all yet. See
     * .claude/plans/T-29-lead-capture.md.
     */
    public function popupLeads()
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $filters = [
            'user_type'         => $this->request->getGet('user_type'),
            'status'            => $this->request->getGet('status'),
            'assigned_agent_id' => $this->request->getGet('assigned_agent_id'),
            'lead_stage'        => $this->request->getGet('lead_stage'),
            'name'              => $this->request->getGet('name'),
            'email'             => $this->request->getGet('email'),
            'phone'             => $this->request->getGet('phone'),
            'whatsapp'          => $this->request->getGet('whatsapp'),
            'date_from'         => $this->request->getGet('date_from'),
            'date_to'           => $this->request->getGet('date_to'),
            'sort'              => $this->request->getGet('sort'),
            'sort_dir'          => $this->request->getGet('sort_dir'),
        ];
        $page = max(1, (int) $this->request->getGet('page') ?: 1);

        $result = $this->leadModel->getPopupLeads($filters, 25, $page);

        $data = [
            'title'        => 'Popup Leads',
            'user'         => $this->userModel->find($this->session->get('user_id')),
            'leads'        => $result['leads'],
            'pagination'   => $result,
            'filters'      => $filters,
            'agents'       => $this->userModel->getAgents(),
            'lead_stages'  => $this->userModel->getLeadStages(),
            'latest_notes' => $this->popupNoteModel->getLatestNotesForLeads(array_column($result['leads'], 'id')),
        ];

        return view('dashboard/admin/popup-leads', $data);
    }

    /**
     * AJAX note-add for a popup lead, mirroring LeadManagement::ajaxAddNote()
     * but against PopupLeadNoteModel/`leads` instead of LeadNoteModel/`users`.
     */
    public function addPopupLeadNote()
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

        $lead = $this->leadModel->find($leadId);
        if (!$lead) {
            return $this->response->setJSON(['success' => false, 'message' => 'Lead not found']);
        }

        $this->popupNoteModel->insert([
            'lead_id'       => $leadId,
            'agent_user_id' => $this->session->get('user_id'),
            'note'          => $noteText,
        ]);

        return $this->response->setJSON(['success' => true, 'message' => 'Note added']);
    }

    /**
     * Admin correction of a popup lead's captured details -- typos, a wrong
     * phone number, etc. Deliberately not routed through LeadModel's
     * capture-flow methods (createLead/reissueForNewLead/updateContactOnly):
     * those exist to enforce the popup's own state machine (token reissue,
     * resend cooldowns...), which doesn't apply here -- this is a direct,
     * plain field edit by an admin, including `status` itself, since an admin
     * may need to correct a stuck lead by hand.
     */
    public function editPopupLead($id)
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads/popup')->with('error', 'Lead not found.');
        }

        // Server-side backstop for the disabled Edit button on the popup-leads
        // list: an account_registered lead already has a real row in `users`,
        // so this stops a direct URL hit from bypassing what's otherwise just
        // a UI-level disable. Delete is deliberately left untouched -- not
        // part of this restriction.
        if ($lead['status'] === 'account_registered') {
            return redirect()->to('/leads/popup')->with('error', "Registered leads can't be edited here — this lead already has an account in Manage Users.");
        }

        if ($this->request->getMethod() === 'GET') {
            return view('dashboard/admin/popup-lead-edit', [
                'title'       => 'Edit Popup Lead',
                'user'        => $this->userModel->find($this->session->get('user_id')),
                'lead'        => $lead,
                'agents'      => $this->userModel->getAgents(),
                'lead_stages' => $this->userModel->getLeadStages(),
            ]);
        }

        $statusOptions = ['popup_form_filled', 'email_verified', 'account_registered'];
        $status = $this->request->getPost('status');
        if (!in_array($status, $statusOptions, true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid status.');
        }

        $stageOptions = array_keys($this->userModel->getLeadStages());
        $leadStage = $this->request->getPost('lead_stage');
        if (!in_array($leadStage, $stageOptions, true)) {
            return redirect()->back()->withInput()->with('error', 'Invalid stage.');
        }

        // Agent assignment: 'assigned_agent_id' references users.id where
        // user_type='agent', same convention as users.assigned_agent_id
        // (UserModel::getAgents() -- confirmed both filter on that same
        // criterion). Empty selection means unassigned.
        $agentId = $this->request->getPost('assigned_agent_id') ?: null;
        if ($agentId !== null) {
            $agent = $this->userModel->find($agentId);
            if (!$agent || !in_array($agent['user_type'], ['agent', 'admin'], true)) {
                return redirect()->back()->withInput()->with('error', 'Invalid agent.');
            }
        }

        // Email is not editable from this form -- it's the identity the
        // lead's verification link/token is tied to, same reasoning as the
        // read-only email on the public step-2 signup page. The view's email
        // input has no `name` attribute, so it's never posted; nothing here
        // ever writes to `email`. That also means the is_unique[...,{id}]
        // rule on `email` never fires (cleanValidationRules() drops a rule
        // for any field absent from the update payload) -- so this method no
        // longer needs the 'id' => $id inclusion BLOCKERS #22 documents;
        // that's still required wherever email itself IS being changed.
        $updated = $this->leadModel->update($id, [
            'user_type'         => $this->request->getPost('user_type'),
            'name'              => $this->request->getPost('name'),
            'phone'             => $this->request->getPost('phone'),
            'phone_code'        => $this->request->getPost('phone_code'),
            'whatsapp'          => $this->request->getPost('whatsapp') ? 1 : 0,
            'status'            => $status,
            'assigned_agent_id' => $agentId,
            'lead_stage'        => $leadStage,
        ]);

        if (!$updated) {
            return redirect()->back()->withInput()->with('errors', $this->leadModel->errors());
        }

        return redirect()->to('/leads/popup')->with('success', 'Popup lead updated successfully.');
    }

    public function deletePopupLead($id)
    {
        $redirect = $this->checkAccess();
        if ($redirect) return $redirect;

        if ($this->request->getMethod() !== 'POST') {
            return redirect()->to('/leads/popup');
        }

        $lead = $this->leadModel->find($id);
        if (!$lead) {
            return redirect()->to('/leads/popup')->with('error', 'Lead not found.');
        }

        $this->leadModel->delete($id);

        return redirect()->to('/leads/popup')->with('success', 'Popup lead deleted.');
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
