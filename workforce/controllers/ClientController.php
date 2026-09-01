<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Client Management & Contracts Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Client;

class ClientController {
    public function index(): void {
        Auth::requirePermission('clients.view');
        $clients = Client::all();
        View::render('clients.index', ['pageTitle' => 'Client Portfolio & Accounts', 'clients' => $clients]);
    }

    public function show(): void {
        Auth::requirePermission('clients.view');
        $id = (int)($_GET['id'] ?? 0);
        $client = Client::find($id);

        if (!$client) {
            Session::setFlash('error', 'Client account not found.');
            wf_redirect('clients');
        }

        $sites = Client::sites($id);
        $contacts = Client::contacts($id);

        View::render('clients.view', [
            'pageTitle' => $client['company_name'] . ' - Client Profile',
            'client'    => $client,
            'sites'     => $sites,
            'contacts'  => $contacts
        ]);
    }

    public function create(): void {
        Auth::requirePermission('clients.manage');
        View::render('clients.editor', [
            'pageTitle' => 'Onboard New Enterprise Client',
            'client'    => null
        ]);
    }

    public function store(): void {
        Auth::requirePermission('clients.manage');
        $db = Database::getInstance();

        $companyName = trim($_POST['company_name'] ?? '');
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['billing_address'] ?? '');
        $city = trim($_POST['city'] ?? 'New Delhi');
        $state = trim($_POST['state'] ?? 'Delhi');
        $pincode = trim($_POST['pincode'] ?? '110001');
        $gst = trim($_POST['gst_number'] ?? '');
        $pan = trim($_POST['pan_number'] ?? '');
        $cycle = $_POST['billing_cycle'] ?? 'monthly';
        $status = $_POST['status'] ?? 'active';

        if (empty($companyName) || empty($email) || empty($phone)) {
            Session::setFlash('error', 'Company name, primary email, and phone are mandatory.');
            wf_redirect('clients/create');
        }

        $clientCode = 'CLI-' . strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $companyName), 0, 3)) . '-' . rand(10, 99);

        $clientId = (int)$db->insert('clients', [
            'company_id'      => Auth::companyId(),
            'client_code'     => $clientCode,
            'company_name'    => $companyName,
            'contact_person'  => $contactPerson,
            'email'           => $email,
            'phone'           => $phone,
            'billing_address' => $address,
            'city'            => $city,
            'state'           => $state,
            'pincode'         => $pincode,
            'gst_number'      => $gst,
            'pan_number'      => $pan,
            'billing_cycle'   => $cycle,
            'status'          => $status
        ]);

        \Services\AuditService::log("Created new client #{$clientId} ({$companyName})", 'client', $clientId, 'CREATE');
        Session::setFlash('success', "Enterprise Client '{$companyName}' onboarded successfully.");
        wf_redirect('clients/view?id=' . $clientId);
    }
}
