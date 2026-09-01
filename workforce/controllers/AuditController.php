<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Field Officer Audits & Surprise Inspection Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Site;

class AuditController {
    public function index(): void {
        Auth::requirePermission('audits.view');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $audits = $db->fetchAll(
            "SELECT sa.*, s.site_name, s.site_code,
                    u.name as auditor_name
             FROM site_audits sa
             JOIN sites s ON sa.site_id = s.id
             JOIN users u ON sa.auditor_id = u.id
             WHERE sa.company_id = :cid
             ORDER BY sa.audit_date DESC",
            ['cid' => $cid]
        );

        $sites = Site::all($cid);

        View::render('audits.index', [
            'pageTitle' => 'Field Officer Site Audits & Inspections',
            'audits'    => $audits,
            'sites'     => $sites
        ]);
    }

    public function create(): void {
        Auth::requirePermission('audits.conduct');
        $sites = Site::all(Auth::companyId());

        View::render('audits.conduct', [
            'pageTitle' => 'Conduct Mobile Field Site Audit',
            'sites'     => $sites
        ]);
    }

    public function store(): void {
        Auth::requirePermission('audits.conduct');
        $db = Database::getInstance();

        $siteId = (int)($_POST['site_id'] ?? 0);
        $type = $_POST['audit_type'] ?? 'regular';
        $guardsPresent = (int)($_POST['guards_present'] ?? 0);
        $cleanersPresent = (int)($_POST['cleaners_present'] ?? 0);
        $uniformCompliance = (int)($_POST['uniform_compliance_score'] ?? 100);
        $equipmentOk = isset($_POST['equipment_status_ok']) ? 1 : 0;
        $registersUpdated = isset($_POST['registers_updated']) ? 1 : 0;
        $clientFeedback = trim($_POST['client_feedback'] ?? '');
        $lat = (float)($_POST['latitude'] ?? 0);
        $lng = (float)($_POST['longitude'] ?? 0);

        // Calculate aggregate compliance score
        $totalScore = (int)round(($uniformCompliance * 0.4) + ($equipmentOk ? 30 : 0) + ($registersUpdated ? 30 : 0));

        $auditNumber = 'AUD-' . date('Ymd') . '-' . rand(100, 999);

        $auditId = (int)$db->insert('site_audits', [
            'audit_number'             => $auditNumber,
            'company_id'               => Auth::companyId(),
            'site_id'                  => $siteId,
            'auditor_id'               => Auth::id(),
            'audit_type'               => $type,
            'audit_date'               => date('Y-m-d H:i:s'),
            'guards_present'           => $guardsPresent,
            'cleaners_present'         => $cleanersPresent,
            'uniform_compliance_score' => $uniformCompliance,
            'equipment_status_ok'      => $equipmentOk,
            'registers_updated'        => $registersUpdated,
            'total_compliance_score'   => $totalScore,
            'client_feedback'          => $clientFeedback,
            'latitude'                 => $lat ?: null,
            'longitude'                => $lng ?: null,
            'status'                   => 'submitted'
        ]);

        \Services\AuditService::log("Field Audit #{$auditNumber} submitted for Site #{$siteId} (Score: {$totalScore}%)", 'audit', $auditId, 'AUDIT_SUBMIT');
        Session::setFlash('success', "Field Audit #{$auditNumber} submitted successfully with {$totalScore}% compliance rating.");
        wf_redirect('audits');
    }
}
