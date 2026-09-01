<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Site Infrastructure, Geofencing & Live Radar Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Site;
use Models\Client;
use Models\Branch;

class SiteController {
    public function index(): void {
        Auth::requirePermission('sites.view');
        $sites = Site::all();
        View::render('sites.index', ['pageTitle' => 'Client Sites & Infrastructure', 'sites' => $sites]);
    }

    public function show(): void {
        Auth::requirePermission('sites.view');
        $id = (int)($_GET['id'] ?? 0);
        $site = Site::find($id);

        if (!$site) {
            Session::setFlash('error', 'Site record not found.');
            wf_redirect('sites');
        }

        $zones = Site::zones($id);
        $checkpoints = Site::checkpoints($id);
        $shifts = Site::shifts($id);

        View::render('sites.view', [
            'pageTitle'   => $site['site_name'] . ' - Site Details',
            'site'        => $site,
            'zones'       => $zones,
            'checkpoints' => $checkpoints,
            'shifts'      => $shifts
        ]);
    }

    public function create(): void {
        Auth::requirePermission('sites.manage');
        $clients = Client::all();
        $branches = Branch::allByCompany(Auth::companyId());

        View::render('sites.editor', [
            'pageTitle' => 'Configure New Client Site & Geofence',
            'site'      => null,
            'clients'   => $clients,
            'branches'  => $branches
        ]);
    }

    public function store(): void {
        Auth::requirePermission('sites.manage');
        $db = Database::getInstance();

        $clientId = (int)($_POST['client_id'] ?? 0);
        $branchId = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : null;
        $siteName = trim($_POST['site_name'] ?? '');
        $siteType = $_POST['site_type'] ?? 'corporate_office';
        $address = trim($_POST['address'] ?? '');
        $city = trim($_POST['city'] ?? 'New Delhi');
        $state = trim($_POST['state'] ?? 'Delhi');
        $pincode = trim($_POST['pincode'] ?? '110001');
        $lat = (float)($_POST['latitude'] ?? 28.6139);
        $lng = (float)($_POST['longitude'] ?? 77.2090);
        $radius = (int)($_POST['geofence_radius'] ?? 75);
        $contactPerson = trim($_POST['contact_person'] ?? '');
        $contactPhone = trim($_POST['contact_phone'] ?? '');
        $instructions = trim($_POST['instructions'] ?? '');
        $status = $_POST['status'] ?? 'active';

        if (empty($clientId) || empty($siteName) || empty($lat) || empty($lng)) {
            Session::setFlash('error', 'Client, Site Name, and GPS coordinates are mandatory.');
            wf_redirect('sites/create');
        }

        $siteCode = 'SITE-' . strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $siteName), 0, 3)) . '-' . rand(100, 999);

        $siteId = (int)$db->insert('sites', [
            'company_id'        => Auth::companyId(),
            'client_id'         => $clientId,
            'branch_id'         => $branchId,
            'site_code'         => $siteCode,
            'site_name'         => $siteName,
            'site_type'         => $siteType,
            'address'           => $address,
            'city'              => $city,
            'state'             => $state,
            'pincode'           => $pincode,
            'latitude'          => $lat,
            'longitude'         => $lng,
            'geofence_type'     => 'circle',
            'geofence_radius'   => $radius,
            'contact_person'    => $contactPerson,
            'contact_phone'     => $contactPhone,
            'instructions'      => $instructions,
            'status'            => $status
        ]);

        \Services\AuditService::log("Created new client site #{$siteId} ({$siteName}) with {$radius}m geofence", 'site', $siteId, 'CREATE');
        Session::setFlash('success', "Site '{$siteName}' configured with active {$radius}m Geofence.");
        wf_redirect('sites/view?id=' . $siteId);
    }

    public function radar(): void {
        Auth::requirePermission('sites.view');
        $sites = Site::all();
        View::render('sites.radar', [
            'pageTitle' => 'Live Operations Radar & Geofence Map',
            'sites'     => $sites
        ]);
    }
}
