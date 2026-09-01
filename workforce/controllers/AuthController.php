<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Authentication Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\View;
use Core\Session;

class AuthController {
    public function showLogin(): void {
        if (Auth::check()) {
            if (Auth::isWorker()) {
                wf_redirect('mobile');
            } else {
                wf_redirect('dashboard');
            }
        }
        View::render('auth.login', ['pageTitle' => 'Workforce Login - JMJ Enterprises'], 'auth');
    }

    public function login(): void {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            Session::setFlash('error', 'Please enter both your email address and password.');
            wf_redirect('login');
        }

        if (Auth::attempt($email, $password)) {
            $user = Auth::user();
            Session::setFlash('success', "Welcome back, {$user['name']}!");

            if (Auth::isWorker()) {
                wf_redirect('mobile');
            } elseif (Auth::isFieldOfficer()) {
                wf_redirect('audits');
            } elseif (Auth::isClient()) {
                wf_redirect('dashboard');
            } else {
                wf_redirect('dashboard');
            }
        } else {
            Session::setFlash('error', 'Invalid email or password. Please verify your credentials.');
            wf_redirect('login');
        }
    }

    public function logout(): void {
        Auth::logout();
        Session::setFlash('info', 'You have been signed out safely.');
        wf_redirect('login');
    }
}
