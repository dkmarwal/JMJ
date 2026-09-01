<?php
/**
 * JMJ Enterprises Solutions - Public API & AJAX Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Csrf;
use Core\Validator;
use Core\RateLimiter;
use Services\LeadService;
use Core\Database;

class ApiController {
    public function submitLead(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
            return;
        }

        // Validate CSRF
        if (!Csrf::validate()) {
            http_response_code(419);
            echo json_encode(['success' => false, 'error' => 'Security token expired. Please reload the page.']);
            return;
        }

        // Rate limit: max 10 submissions per 5 minutes per IP
        if (!RateLimiter::check('submit_lead', 10, 300)) {
            http_response_code(429);
            echo json_encode(['success' => false, 'error' => 'Too many requests. Please wait a few minutes before submitting again.']);
            return;
        }

        $validator = Validator::make($_POST, [
            'name'  => 'required|min:2|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'required|phone|max:30'
        ]);

        if ($validator->fails()) {
            http_response_code(422);
            echo json_encode(['success' => false, 'error' => $validator->firstError()]);
            return;
        }

        try {
            $leadId = LeadService::processSubmission($_POST);
            echo json_encode([
                'success' => true,
                'message' => 'Thank you! Your request has been recorded. Our operations desk will contact you shortly.',
                'lead_id' => $leadId
            ]);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'error' => 'Could not record submission. Please call our 24/7 helpline.']);
        }
    }

    public function submitQuote(): void {
        $this->submitLead();
    }

    public function subscribe(): void {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed.']);
            return;
        }

        if (!Csrf::validate()) {
            echo json_encode(['success' => false, 'error' => 'Security token expired.']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['success' => false, 'error' => 'Please provide a valid corporate email.']);
            return;
        }

        try {
            $db = Database::getInstance();
            $db->query(
                "INSERT INTO `newsletter_subscribers` (`email`, `status`, `ip_address`) 
                 VALUES (:email, 'active', :ip) 
                 ON DUPLICATE KEY UPDATE `status` = 'active'",
                ['email' => $email, 'ip' => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1']
            );
            echo json_encode(['success' => true, 'message' => 'Thank you for subscribing to our industry bulletins!']);
        } catch (\Throwable) {
            echo json_encode(['success' => false, 'error' => 'Subscription failed. Please try again.']);
        }
    }
}
