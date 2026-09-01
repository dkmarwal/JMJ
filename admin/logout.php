<?php
/**
 * JMJ Enterprises Solutions - Admin Logout Handler
 */

declare(strict_types=1);

require_once dirname(__DIR__) . '/config/config.php';

\Core\Auth::logout();
\Core\Session::setFlash('success', 'You have been securely logged out.');
redirect('admin/login.php');
