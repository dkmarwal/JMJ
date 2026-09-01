<?php
/**
 * JMJ Enterprise Solutions - Workforce Management Platform
 * Staff Onboarding, Employee Profiles & Digital ID Cards Controller
 */

declare(strict_types=1);

namespace Controllers;

use Core\Auth;
use Core\Database;
use Core\View;
use Core\Session;
use Models\Employee;
use Models\Branch;

class StaffController {
    public function index(): void {
        Auth::requirePermission('staff.view');
        $filters = [
            'category_id' => $_GET['category'] ?? null,
            'status'      => $_GET['status'] ?? null,
            'search'      => $_GET['search'] ?? null
        ];

        $employees = Employee::all(Auth::companyId(), $filters);
        $categories = Employee::categories(Auth::companyId());

        View::render('staff.index', [
            'pageTitle'  => 'Workforce Staff Directory',
            'employees'  => $employees,
            'categories' => $categories,
            'filters'    => $filters
        ]);
    }

    public function show(): void {
        Auth::requirePermission('staff.view');
        $id = (int)($_GET['id'] ?? 0);
        $employee = Employee::find($id);

        if (!$employee) {
            Session::setFlash('error', 'Employee record not found.');
            wf_redirect('staff');
        }

        $documents = Employee::documents($id);
        $activeDeployment = Employee::activeDeployment($id);

        View::render('staff.view', [
            'pageTitle'        => $employee['first_name'] . ' ' . $employee['last_name'] . ' - Profile',
            'employee'         => $employee,
            'documents'        => $documents,
            'activeDeployment' => $activeDeployment
        ]);
    }

    public function create(): void {
        Auth::requirePermission('staff.onboard');
        $categories = Employee::categories(Auth::companyId());
        $branches = Branch::allByCompany(Auth::companyId());

        View::render('staff.onboarding', [
            'pageTitle'  => 'Employee Onboarding & Verification Wizard',
            'employee'   => null,
            'categories' => $categories,
            'branches'   => $branches
        ]);
    }

    public function store(): void {
        Auth::requirePermission('staff.onboard');
        $db = Database::getInstance();

        $firstName = trim($_POST['first_name'] ?? '');
        $lastName = trim($_POST['last_name'] ?? '');
        $gender = $_POST['gender'] ?? 'male';
        $dob = $_POST['dob'] ?? '1995-01-01';
        $phone = trim($_POST['phone'] ?? '');
        $emergencyPhone = trim($_POST['emergency_phone'] ?? $phone);
        $email = trim($_POST['email'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 1);
        $branchId = !empty($_POST['branch_id']) ? (int)$_POST['branch_id'] : 1;
        $joiningDate = $_POST['joining_date'] ?? date('Y-m-d');
        $designation = trim($_POST['designation'] ?? 'Security Officer');
        $currentAddress = trim($_POST['current_address'] ?? '');
        $permanentAddress = trim($_POST['permanent_address'] ?? $currentAddress);
        $city = trim($_POST['city'] ?? 'New Delhi');
        $state = trim($_POST['state'] ?? 'Delhi');
        $pincode = trim($_POST['pincode'] ?? '110065');
        $basicSalary = (float)($_POST['basic_salary'] ?? 18000.00);
        $bankName = trim($_POST['bank_name'] ?? 'State Bank of India');
        $accountNo = trim($_POST['bank_account_no'] ?? '');
        $ifsc = trim($_POST['ifsc_code'] ?? '');
        $pfUan = trim($_POST['pf_uan'] ?? '');
        $esicNo = trim($_POST['esic_no'] ?? '');

        if (empty($firstName) || empty($phone)) {
            Session::setFlash('error', 'First name and mobile number are required.');
            wf_redirect('staff/create');
        }

        $empCode = 'EMP-' . str_pad((string)rand(1000, 9999), 5, '0', STR_PAD_LEFT);

        $empId = (int)$db->insert('employees', [
            'company_id'        => Auth::companyId(),
            'branch_id'         => $branchId,
            'category_id'       => $categoryId,
            'employee_code'     => $empCode,
            'first_name'        => $firstName,
            'last_name'         => $lastName,
            'gender'            => $gender,
            'dob'               => $dob,
            'phone'             => $phone,
            'emergency_phone'   => $emergencyPhone,
            'email'             => $email ?: null,
            'current_address'   => $currentAddress,
            'permanent_address' => $permanentAddress,
            'city'              => $city,
            'state'             => $state,
            'pincode'           => $pincode,
            'joining_date'      => $joiningDate,
            'employment_type'   => 'full_time',
            'designation'       => $designation,
            'basic_salary'      => $basicSalary,
            'bank_name'         => $bankName,
            'bank_account_no'   => $accountNo,
            'ifsc_code'         => $ifsc,
            'pf_uan'            => $pfUan,
            'esic_no'           => $esicNo,
            'status'            => 'active'
        ]);

        \Services\AuditService::log("Onboarded new staff #{$empId} ({$empCode}: {$firstName} {$lastName})", 'employee', $empId, 'ONBOARD');
        Session::setFlash('success', "Employee {$empCode} ({$firstName} {$lastName}) onboarded successfully.");
        wf_redirect('staff/view?id=' . $empId);
    }

    public function idCard(): void {
        Auth::requirePermission('staff.view');
        $id = (int)($_GET['id'] ?? 0);
        $employee = Employee::find($id);

        if (!$employee) {
            Session::setFlash('error', 'Employee record not found.');
            wf_redirect('staff');
        }

        $company = \Models\Company::current();

        View::render('staff.id_card', [
            'pageTitle' => 'Digital Identity Card - ' . $employee['employee_code'],
            'employee'  => $employee,
            'company'   => $company
        ]);
    }
}
