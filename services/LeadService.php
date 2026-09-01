<?php
/**
 * JMJ Enterprises Solutions - Lead & Enquiry Service
 */

declare(strict_types=1);

namespace Services;

use Models\Enquiry;
use Core\Database;

class LeadService {
    public static function processSubmission(array $data): int {
        $id = Enquiry::create($data);

        // Attempt admin email notification
        try {
            $adminEmail = SettingService::get('email_support', 'jmjsanu@gmail.com');
            $subject = "New " . ucfirst($data['type'] ?? 'Lead') . " from " . e($data['name']) . " - JMJ Enterprises";
            $message = "You have received a new website enquiry:\n\n" .
                       "Name: " . $data['name'] . "\n" .
                       "Company: " . ($data['company'] ?? 'N/A') . "\n" .
                       "Email: " . $data['email'] . "\n" .
                       "Phone: " . $data['phone'] . "\n" .
                       "Service: " . ($data['service_required'] ?? 'N/A') . "\n" .
                       "Location: " . ($data['location'] ?? 'N/A') . "\n\n" .
                       "Message:\n" . $data['message'] . "\n\n" .
                       "View details in Admin Portal: " . url('admin/enquiries.php');

            MailService::send($adminEmail, $subject, $message);
        } catch (\Throwable) {}

        AuditService::log("Received inbound {$data['type']} enquiry from {$data['name']} ({$data['email']})", 'enquiry', $id, 'LEAD_RECEIVED');

        return $id;
    }
}
