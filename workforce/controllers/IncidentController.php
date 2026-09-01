<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Incident Management & Emergency SOS Command Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Site;

    public function index(): void {
        Auth::requireLogin();
        if (!Auth::can('incidents.manage') && !Auth::can('incidents.report')) {
            Session::setFlash('error', "Access Denied. Missing incident viewing permissions.");
            wf_redirect('dashboard');
        }
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $incidents = $db->fetchAll(
            "SELECT inc.*, s.site_name, s.site_code,
                    u.name as reporter_name
             FROM incidents inc
             JOIN sites s ON inc.site_id = s.id
             JOIN users u ON inc.reported_by = u.id
             WHERE inc.company_id = :cid
             ORDER BY inc.created_at DESC",
            ['cid' => $cid]
        );

        $sosAlerts = $db->fetchAll(
            "SELECT sos.*, s.site_name, s.site_code,
                    e.first_name, e.last_name, e.employee_code, e.phone
             FROM sos_alerts sos
             JOIN sites s ON sos.site_id = s.id
             JOIN employees e ON sos.employee_id = e.id
             WHERE sos.company_id = :cid
             ORDER BY sos.trigger_time DESC LIMIT 20",
            ['cid' => $cid]
        );

        View::render('incidents.index', [
            'pageTitle' => 'Incident Command & SOS Emergency Queue',
            'incidents' => $incidents,
            'sosAlerts' => $sosAlerts
        ]);
    }
}
