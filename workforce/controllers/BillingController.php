<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Client Invoicing, GST & Payment Tracking Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Client;
use Services\BillingService;

class BillingController {
    public function index(): void {
        Auth::requirePermission('billing.invoices');
        $db = Database::getInstance();
        $cid = Auth::companyId();

        $invoices = $db->fetchAll(
            "SELECT inv.*, c.company_name as client_name, c.client_code, c.gst_number
             FROM client_invoices inv
             JOIN clients c ON inv.client_id = c.id
             WHERE inv.company_id = :cid
             ORDER BY inv.issue_date DESC",
            ['cid' => $cid]
        );

        $clients = Client::all($cid);

        View::render('billing.index', [
            'pageTitle' => 'Client Invoicing & Billing Center',
            'invoices'  => $invoices,
            'clients'   => $clients
        ]);
    }

    public function show(): void {
        Auth::requirePermission('billing.invoices');
        $db = Database::getInstance();
        $id = (int)($_GET['id'] ?? 0);

        $invoice = $db->fetch(
            "SELECT inv.*, c.company_name as client_name, c.client_code, c.gst_number, c.pan_number,
                    c.billing_address, c.city as client_city, c.state as client_state, c.pincode as client_pincode,
                    comp.name as company_name, comp.legal_name, comp.gst_number as comp_gst, comp.pan_number as comp_pan,
                    comp.address as comp_address, comp.psara_license_no
             FROM client_invoices inv
             JOIN clients c ON inv.client_id = c.id
             JOIN companies comp ON inv.company_id = comp.id
             WHERE inv.id = :id AND inv.company_id = :cid",
            ['id' => $id, 'cid' => Auth::companyId()]
        );

        if (!$invoice) {
            Session::setFlash('error', 'Invoice record not found.');
            wf_redirect('billing');
        }

        $items = $db->fetchAll("SELECT * FROM invoice_items WHERE invoice_id = :id", ['id' => $id]);

        View::render('billing.invoice', [
            'pageTitle' => 'Tax Invoice ' . $invoice['invoice_number'],
            'invoice'   => $invoice,
            'items'     => $items
        ]);
    }

    public function generate(): void {
        Auth::requirePermission('billing.invoices');
        $clientId = (int)($_POST['client_id'] ?? 0);
        $month = (int)($_POST['month'] ?? date('n'));
        $year = (int)($_POST['year'] ?? date('Y'));

        $res = BillingService::generateClientInvoice($clientId, $month, $year, Auth::companyId());
        if ($res['success']) {
            Session::setFlash('success', "Invoice #{$res['invoice_number']} generated successfully (Total: " . wf_format_currency($res['grand_total']) . ")");
            wf_redirect('billing/invoice?id=' . $res['invoice_id']);
        } else {
            Session::setFlash('error', $res['message'] ?? 'Invoice generation failed.');
            wf_redirect('billing');
        }
    }
}
