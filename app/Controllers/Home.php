<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    private ?bool $sessionAccountActive = null;

    public function index(): string
    {
        return view('home');
    }

    public function login(): string
    {
        return view('login');
    }

    public function dashboard(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Technician', 'Customer'])) {
            return $redirect;
        }

        return redirect()->to(site_url(session()->get('role') === 'Technician' ? 'technician-dashboard.php' : 'customer-dashboard.php'));
    }

    public function customerDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Admin', 'Customer'])) {
            return $redirect;
        }

        return view('customer-dashboard');
    }

    public function customerReview(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Customer'])) {
            return $redirect;
        }

        return view('customer-review');
    }

    public function technicianDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Admin', 'Technician', 'Maintenance'])) {
            return $redirect;
        }

        return view('technician-dashboard', [
            'employeeRole' => (string) session()->get('role'),
            'dashboard' => match (session()->get('role')) {
                'Admin' => 'admin-dashboard.php',
                'Maintenance' => 'maintenance-dashboard.php',
                default => 'technician-dashboard.php',
            },
            'canCheckIn' => session()->get('role') !== 'Admin',
        ]);
    }

    public function maintenanceDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Maintenance'])) {
            return $redirect;
        }

        return view('technician-dashboard', [
            'employeeRole' => 'Maintenance',
            'dashboard' => 'maintenance-dashboard.php',
        ]);
    }

    public function adminDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Admin'])) {
            return $redirect;
        }

        return view('admin-dashboard');
    }

    public function dispatcherDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Admin', 'Dispatcher', 'Dispatch', 'Marketing'])) {
            return $redirect;
        }

        return view('dispatcher-dashboard', [
            'showBookings' => in_array(session()->get('role'), ['Admin', 'Dispatcher', 'Dispatch'], true),
            'canManageCatalog' => in_array(session()->get('role'), ['Admin', 'Marketing'], true),
            'dashboard' => match (session()->get('role')) {
                'Marketing' => 'marketing-dashboard.php',
                'Admin' => 'admin-dashboard.php',
                default => 'dispatcher-dashboard.php',
            },
        ]);
    }

    public function itDashboard(): string|RedirectResponse
    {
        return $this->renderDepartmentDashboard('IT', 'departments/it');
    }

    public function accountingDashboard(): string|RedirectResponse
    {
        return $this->renderDepartmentDashboard('Accounting', 'departments/accounting');
    }

    public function hrDashboard(): string|RedirectResponse
    {
        return $this->renderDepartmentDashboard('HR', 'departments/hr');
    }

    public function marketingDashboard(): string|RedirectResponse
    {
        return $this->renderDepartmentDashboard('Marketing', 'departments/marketing');
    }

    public function salesDashboard(): string|RedirectResponse
    {
        return $this->renderDepartmentDashboard('Sales', 'departments/sales');
    }

    public function customerServiceDashboard(): string|RedirectResponse
    {
        return $this->renderDepartmentDashboard('Customer Service', 'departments/customer-service');
    }

    public function register(): string
    {
        return view('register');
    }

    public function services(): string
    {
        return view('services');
    }

    public function quote(): string
    {
        return view('quote');
    }

    public function products(): string
    {
        return view('products');
    }

    public function catalogProducts()
    {
        $products = \Config\Database::connect()->table('catalog_products')
            ->select('id, name, category, price, description, photo')
            ->orderBy('id')->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'products' => $products]);
    }

    public function catalogServices()
    {
        $services = \Config\Database::connect()->table('catalog_services')
            ->select('id, name, service_type AS type, description, photo')
            ->orderBy('id')->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'services' => $services]);
    }

    public function saveCatalogProduct()
    {
        if (! $this->canManageCatalog()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Marketing or Admin access is required to edit the product catalog.']);
        }

        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $category = $this->request->getPost('category');
        $price = $this->request->getPost('price');
        $description = $this->request->getPost('description');
        $photo = $this->catalogPhotoInput();
        if (! is_string($name) || trim($name) === '' || ! is_string($category) || trim($category) === '' || ! is_string($price) || trim($price) === '' || ! is_string($description) || trim($description) === '' || $photo === false) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Complete product name, category, price, description, and use a valid image.']);
        }

        return $this->persistCatalogEntry('catalog_products', $id, [
            'name' => trim($name),
            'category' => trim($category),
            'price' => trim($price),
            'description' => trim($description),
            'photo' => $photo,
        ]);
    }

    public function deleteCatalogProduct()
    {
        return $this->deleteCatalogEntry('catalog_products');
    }

    public function saveCatalogService()
    {
        if (! $this->canManageCatalog()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Marketing or Admin access is required to edit the services catalog.']);
        }

        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $type = $this->request->getPost('type');
        $description = $this->request->getPost('description');
        $photo = $this->catalogPhotoInput();
        if (! is_string($name) || trim($name) === '' || ! is_string($type) || trim($type) === '' || ! is_string($description) || trim($description) === '' || $photo === false) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Complete service name, type, description, and use a valid image.']);
        }

        return $this->persistCatalogEntry('catalog_services', $id, [
            'name' => trim($name),
            'service_type' => trim($type),
            'description' => trim($description),
            'photo' => $photo,
        ]);
    }

    public function deleteCatalogService()
    {
        return $this->deleteCatalogEntry('catalog_services');
    }

    public function departmentSummary()
    {
        $role = session()->get('role');
        if (! $this->isAuthenticated() || ! in_array($role, ['Admin', 'IT', 'Marketing', 'Sales', 'Customer Service'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'This department summary is restricted.']);
        }

        $db = \Config\Database::connect();
        $summary = [
            'users' => (int) $db->table('users')->countAllResults(),
            'products' => (int) $db->table('catalog_products')->countAllResults(),
            'services' => (int) $db->table('catalog_services')->countAllResults(),
            'database' => 'connected',
        ];

        if (in_array($role, ['Admin', 'Sales', 'Customer Service'], true)) {
            $bookingCounts = [];
            foreach ($db->table('bookings')->select('status, COUNT(*) AS total')->groupBy('status')->get()->getResultArray() as $row) {
                $bookingCounts[$row['status']] = (int) $row['total'];
            }
            $summary['bookings'] = array_sum($bookingCounts);
            $summary['bookingStatuses'] = $bookingCounts;
        }
        if ($role === 'Sales' || $role === 'Admin') {
            $quoteCounts = [];
            foreach ($db->table('quote_requests')->select('status, COUNT(*) AS total')->groupBy('status')->get()->getResultArray() as $row) {
                $quoteCounts[$row['status']] = (int) $row['total'];
            }
            $summary['quoteRequests'] = array_sum($quoteCounts);
            $summary['quoteStatuses'] = $quoteCounts;
        }

        return $this->response->setJSON([
            'success' => true,
            'summary' => $summary,
        ]);
    }

    public function hrEmployeeDirectory()
    {
        if (! $this->hasPermission('employees.manage')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'HR or Admin access is required.']);
        }

        $employees = \Config\Database::connect()->table('users u')
            ->select('u.id, u.username, u.role, u.is_active AS isActive, u.approval_status AS approvalStatus, COALESCE(e.first_name, u.username) AS firstName, COALESCE(e.last_name, "") AS lastName, COALESCE(e.email, "") AS email, COALESCE(e.phone_number, "") AS phone, COALESCE(e.address, "") AS address')
            ->join('employees e', 'e.user_id = u.id', 'left')
            ->whereIn('u.role', $this->employeeRoles())
            ->orderBy('u.role')->orderBy('e.last_name')->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'employees' => $employees]);
    }

    public function forgotPassword(): string
    {
        return view('forgot-password');
    }

    public function forgotPasswordCheck()
    {
        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        if (! is_string($username) || trim($username) === ''
            || ! is_string($email) || ! filter_var(trim($email), FILTER_VALIDATE_EMAIL)
            || ! is_string($password) || strlen($password) < 6
        ) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Enter your username, valid account email, and a password of at least 6 characters.',
            ]);
        }

        $db = \Config\Database::connect();
        $user = $db->table('users u')
            ->select('u.id, c.email AS customerEmail, e.email AS employeeEmail')
            ->join('customers c', 'c.user_id = u.id', 'left')
            ->join('employees e', 'e.user_id = u.id', 'left')
            ->where('u.username', trim($username))
            ->get()->getRowArray();

        $emailMatches = $user !== null && (
            (is_string($user['customerEmail']) && strcasecmp($user['customerEmail'], trim($email)) === 0)
            || (is_string($user['employeeEmail']) && strcasecmp($user['employeeEmail'], trim($email)) === 0)
        );
        if (! $emailMatches) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'The username and account email do not match.',
            ]);
        }

        $db->transStart();
        $db->table('users')
            ->where('id', (int) $user['id'])
            ->update(['password' => password_hash($password, PASSWORD_DEFAULT)]);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Password could not be changed. Please try again.',
            ]);
        }

        return $this->response->setJSON(['success' => true, 'message' => 'Password changed. You can now log in.']);
    }

    public function profile(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole($this->supportedRoles())) {
            return $redirect;
        }

        $userId = (int) session()->get('userId');
        $role = (string) session()->get('role');
        $table = $role === 'Customer' ? 'customers' : 'employees';
        $profile = \Config\Database::connect()->table($table)
            ->select('first_name AS firstName, last_name AS lastName, middle_name AS middleName, birthdate, gender, email, phone_number AS phone, address')
            ->where('user_id', $userId)
            ->get()->getRowArray() ?? [];

        return view('profile', [
            'profile' => $profile,
            'accountUsername' => (string) session()->get('username'),
        ]);
    }

    public function updateProfile()
    {
        if (! $this->isAuthenticated()) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'error' => 'Please log in again.']);
        }

        $username = $this->request->getPost('username');
        $email = $this->request->getPost('email');
        $newPassword = $this->request->getPost('newPassword');
        $currentPassword = $this->request->getPost('currentPassword');
        $profile = $this->readProfileInput();

        if (! is_string($username) || strlen(trim($username)) < 3 || strlen(trim($username)) > 255
            || ! is_string($email) || ! filter_var(trim($email), FILTER_VALIDATE_EMAIL)
            || $profile['firstName'] === '' || strlen($profile['firstName']) > 255
            || $profile['lastName'] === '' || strlen($profile['lastName']) > 255
            || strlen(trim($email)) > 255
        ) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'Enter a valid username, first name, last name, and email address.',
            ]);
        }

        if ($newPassword !== null && $newPassword !== '' && (! is_string($newPassword) || strlen($newPassword) < 6)) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error' => 'New password must be at least 6 characters.',
            ]);
        }

        $db = \Config\Database::connect();
        $userId = (int) session()->get('userId');
        $user = $db->table('users')->select('id, password')->where('id', $userId)->get()->getRowArray();
        if ($user === null) {
            session()->destroy();

            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'error' => 'Please log in again.']);
        }

        if ($newPassword !== null && $newPassword !== '') {
            if (! is_string($currentPassword) || $currentPassword === ''
                || (! password_verify($currentPassword, $user['password']) && ! hash_equals((string) $user['password'], $currentPassword))
            ) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => 'Current password is incorrect.',
                ]);
            }
        }

        $existingUsername = $db->table('users')
            ->select('id')
            ->where('username', trim($username))
            ->where('id !=', $userId)
            ->get()->getRowArray();
        if ($existingUsername !== null) {
            return $this->response->setStatusCode(409)->setJSON(['success' => false, 'error' => 'That username is already used by another account.']);
        }

        if ($this->emailExists($db, trim($email), $userId)) {
            return $this->response->setStatusCode(409)->setJSON(['success' => false, 'error' => 'That email is already used by another account.']);
        }

        $db->transStart();
        $userUpdate = ['username' => trim($username)];
        if ($newPassword !== null && $newPassword !== '') {
            $userUpdate['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }
        $db->table('users')->where('id', $userId)->update($userUpdate);

        $role = (string) session()->get('role');
        $table = $role === 'Customer' ? 'customers' : 'employees';
        $profileUpdate = [
            'first_name' => $profile['firstName'],
            'last_name' => $profile['lastName'],
            'middle_name' => $profile['middleName'] ?: null,
            'birthdate' => $profile['birthdate'] ?: null,
            'gender' => $profile['gender'] ?: null,
            'email' => trim($email),
            'phone_number' => $profile['phone'] ?: null,
            'address' => $profile['address'] ?: null,
        ];
        $db->table($table)->where('user_id', $userId)->update($profileUpdate);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Profile could not be saved. Please try again.',
            ]);
        }

        session()->set('username', trim($username));

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Profile updated successfully.',
            'user' => ['id' => $userId, 'username' => trim($username), 'role' => $role],
        ]);
    }

    public function schedule(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Admin', 'Customer'])) {
            return $redirect;
        }

        return view('schedule');
    }

    public function employeeTimeIn(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole($this->employeeRoles())) {
            return $redirect;
        }

        return view('employee-timein');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to(site_url('login.php'));
    }

    public function loginCheck()
    {
        $db = \Config\Database::connect();
        $usernameInput = $this->request->getPost('username');
        $passwordInput = $this->request->getPost('password');

        if (! is_string($usernameInput) || trim($usernameInput) === '' || ! is_string($passwordInput) || $passwordInput === '') {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'Username at password ay kinakailangan.']);
        }

        $user = $db->table('users')
            ->select('id, username, password, role, is_active, approval_status')
            ->where('username', trim($usernameInput))
            ->get()
            ->getRowArray();

        $roles = $this->supportedRoles();
        $hashedPasswordMatches = $user !== null && password_verify($passwordInput, $user['password']);
        $legacyPasswordMatches = $user !== null
            && ! $hashedPasswordMatches
            && hash_equals((string) $user['password'], $passwordInput);
        $passwordMatches = $hashedPasswordMatches || $legacyPasswordMatches;

        if (! $passwordMatches || ! in_array($user['role'], $roles, true)) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'error' => 'Maling username o password.']);
        }

        if ($legacyPasswordMatches) {
            $db->table('users')
                ->where('id', $user['id'])
                ->update(['password' => password_hash($passwordInput, PASSWORD_DEFAULT)]);
        }

        if ((int) $user['is_active'] !== 1 || $user['approval_status'] !== 'Approved') {
            $message = match ($user['approval_status']) {
                'Pending' => 'This account is awaiting HR or administrator approval. Contact HR or an administrator for assistance.',
                'Declined' => 'This account was declined. Contact HR or an administrator for assistance.',
                default => 'This account is inactive. Contact Admin or HR for assistance.',
            };

            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => $message]);
        }

        session()->regenerate(true);
        session()->set([
            'userId'     => $user['id'],
            'username'   => $user['username'],
            'role'       => $user['role'],
            'isLoggedIn' => true,
        ]);

        $dashboard = match ($user['role']) {
            'Admin'      => 'admin-dashboard.php',
            'Dispatcher', 'Dispatch' => 'dispatcher-dashboard.php',
            'Technician' => 'technician-dashboard.php',
            'Maintenance' => 'maintenance-dashboard.php',
            'IT' => 'it-dashboard.php',
            'Accounting' => 'accounting-dashboard.php',
            'HR' => 'hr-dashboard.php',
            'Marketing' => 'marketing-dashboard.php',
            'Sales' => 'sales-dashboard.php',
            'Customer Service' => 'customer-service-dashboard.php',
            default => 'customer-dashboard.php',
        };

        return $this->response->setJSON([
            'success'  => true,
            'user'     => [
                'id'       => $user['id'],
                'username' => $user['username'],
                'role'     => $user['role'],
            ],
            'redirect' => site_url($dashboard),
        ]);
    }

    public function registerCheck()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $role     = $this->request->getPost('role');
        $profile  = $this->readProfileInput();

        if (! is_string($username) || strlen(trim($username)) < 3 || strlen(trim($username)) > 255 || ! is_string($password) || strlen($password) < 6) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'Username at password na hindi bababa sa 6 na character ay kinakailangan.']);
        }

        if ($role !== 'Customer') {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Only customers can register their own accounts. Employees must be added by HR or an administrator.']);
        }

        if ($profile['firstName'] === '' || $profile['lastName'] === '' || $profile['email'] === ''
            || ! filter_var($profile['email'], FILTER_VALIDATE_EMAIL)
            || strlen($profile['firstName']) > 255
            || strlen($profile['lastName']) > 255
            || strlen($profile['email']) > 255
        ) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'Enter a valid first name, last name, and email address.']);
        }

        $db = \Config\Database::connect();
        $existingUser = $db->table('users')
            ->select('id')
            ->where('username', trim($username))
            ->get()
            ->getRowArray();

        if ($existingUser !== null) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON(['success' => false, 'error' => 'May account na gamit ang username na ito.']);
        }

        if ($profile['email'] !== '' && $this->emailExists($db, $profile['email'])) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON(['success' => false, 'error' => 'Email already registered.']);
        }

        $db->transStart();
        $db->table('users')->insert([
            'username'  => trim($username),
            'password'  => password_hash($password, PASSWORD_DEFAULT),
            'role'      => 'Customer',
            'is_active' => 1,
            'approval_status' => 'Approved',
        ]);
        $userId = (int) $db->insertID();
        $this->saveAccountProfile($db, $userId, $role, $profile);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['success' => false, 'error' => 'Account registration could not be completed. Please try again.']);
        }

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'success' => true,
                'user'    => [
                    'id'       => $userId,
                    'username' => trim($username),
                    'role'     => 'Customer',
                    'isActive' => true,
                    'approvalStatus' => 'Approved',
                ],
                'message' => 'Customer account created successfully.',
            ]);
    }

    public function managedUsers()
    {
        if (! $this->hasPermission('users.manage')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Admin access is required.']);
        }

        $users = \Config\Database::connect()
            ->table('users u')
            ->select('u.id, u.username, u.role, u.is_active AS isActive, COALESCE(c.first_name, e.first_name, "") AS firstName, COALESCE(c.last_name, e.last_name, "") AS lastName, COALESCE(c.email, e.email, "") AS email, COALESCE(c.phone_number, e.phone_number, "") AS phone, COALESCE(c.address, e.address, "") AS address')
            ->join('customers c', 'c.user_id = u.id', 'left')
            ->join('employees e', 'e.user_id = u.id', 'left')
            ->orderBy('u.id')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['success' => true, 'users' => $users]);
    }

    public function systemUsers()
    {
        if (! $this->hasPermission('system.users.view')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'IT or Admin access is required.']);
        }

        $users = \Config\Database::connect()->table('users')
            ->select('id, username, role, is_active AS isActive, approval_status AS approvalStatus')
            ->orderBy('role')->orderBy('username')
            ->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'users' => $users]);
    }

    public function updateSystemUserStatus()
    {
        if (! $this->hasPermission('system.users.status')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'IT or Admin access is required.']);
        }

        $userId = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        $activeInput = $this->request->getPost('isActive');
        if ($userId === false || $userId < 1 || ! in_array($activeInput, ['0', '1', 0, 1], true)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'A valid user and access status are required.']);
        }
        if ($userId === (int) session()->get('userId')) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'You cannot change your own account access.']);
        }

        $db = \Config\Database::connect();
        $user = $db->table('users')->select('id, role, approval_status')
            ->where('id', $userId)->get()->getRowArray();
        if ($user === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'User not found.']);
        }
        if (session()->get('role') === 'IT' && $user['role'] === 'Admin') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'IT cannot change administrator access.']);
        }
        if ($user['approval_status'] !== 'Approved') {
            return $this->response->setStatusCode(409)->setJSON(['success' => false, 'error' => 'Pending or declined employee registrations must be handled by Admin or HR.']);
        }

        $isActive = (int) $activeInput;
        $db->table('users')->where('id', $userId)->update(['is_active' => $isActive]);

        return $this->response->setJSON(['success' => true, 'userId' => $userId, 'isActive' => (bool) $isActive]);
    }

    public function approveEmployee()
    {
        if (! $this->hasPermission('employees.approve')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Admin or HR access is required.']);
        }

        $userId = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        if ($userId === false || $userId < 1) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'A valid employee account is required.']);
        }

        $db = \Config\Database::connect();
        $employee = $db->table('users u')
            ->select('u.id, u.is_active, u.approval_status')
            ->join('employees e', 'e.user_id = u.id')
            ->where('u.id', $userId)
            ->whereIn('u.role', $this->employeeRoles())
            ->get()
            ->getRowArray();

        if ($employee === null) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['success' => false, 'error' => 'Employee registration not found.']);
        }

        if ($employee['approval_status'] !== 'Pending') {
            return $this->response
                ->setStatusCode(409)
                ->setJSON(['success' => false, 'error' => 'Only pending employee registrations can be approved.']);
        }

        $db->table('users')->where('id', $userId)->where('approval_status', 'Pending')->update([
            'approval_status' => 'Approved',
            'is_active' => 1,
        ]);
        if ($db->affectedRows() !== 1) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['success' => false, 'error' => 'Employee approval could not be saved. Please try again.']);
        }

        return $this->response->setJSON(['success' => true, 'userId' => $userId, 'isActive' => true, 'approvalStatus' => 'Approved']);
    }

    public function declineEmployee()
    {
        if (! $this->hasPermission('employees.approve')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Admin or HR access is required.']);
        }

        $userId = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        if ($userId === false || $userId < 1) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'A valid employee account is required.']);
        }

        $db = \Config\Database::connect();
        $employee = $db->table('users u')
            ->select('u.id, u.is_active, u.approval_status')
            ->join('employees e', 'e.user_id = u.id')
            ->where('u.id', $userId)
            ->whereIn('u.role', $this->employeeRoles())
            ->get()
            ->getRowArray();

        if ($employee === null) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['success' => false, 'error' => 'Employee registration not found.']);
        }

        if ($employee['approval_status'] !== 'Pending') {
            return $this->response
                ->setStatusCode(409)
                ->setJSON(['success' => false, 'error' => 'Only pending employee registrations can be declined.']);
        }

        $db->table('users')->where('id', $userId)->where('approval_status', 'Pending')->update([
            'approval_status' => 'Declined',
            'is_active' => 0,
        ]);
        if ($db->affectedRows() !== 1) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['success' => false, 'error' => 'Employee registration could not be declined. Please try again.']);
        }

        return $this->response->setJSON(['success' => true, 'userId' => $userId, 'approvalStatus' => 'Declined', 'isActive' => false]);
    }

    public function updateEmployeeStatus()
    {
        if (! $this->hasPermission('employees.manage')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Admin or HR access is required.']);
        }

        $userId = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        $activeInput = $this->request->getPost('isActive');
        if ($userId === false || $userId < 1 || ! in_array($activeInput, ['0', '1', 0, 1], true)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'A valid employee and account status are required.']);
        }

        $db = \Config\Database::connect();
        $employee = $db->table('users')
            ->select('id, is_active, approval_status')
            ->where('id', $userId)
            ->whereIn('role', $this->employeeRoles())
            ->get()->getRowArray();
        if ($employee === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Employee account not found.']);
        }
        if ($employee['approval_status'] !== 'Approved') {
            return $this->response->setStatusCode(409)->setJSON(['success' => false, 'error' => 'Pending or declined registrations must be reviewed through the approval queue.']);
        }

        $isActive = (int) $activeInput;
        $db->table('users')->where('id', $userId)->update(['is_active' => $isActive]);

        return $this->response->setJSON(['success' => true, 'userId' => $userId, 'isActive' => (bool) $isActive]);
    }

    public function saveManagedUser()
    {
        $actorRole = session()->get('role');
        $isAdmin = $this->hasPermission('users.manage');
        $isHr = $this->hasPermission('employees.manage') && $actorRole === 'HR';
        if (! $isAdmin && ! $isHr) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Admin or HR access is required.']);
        }

        $id       = $this->request->getPost('id');
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $role     = $this->request->getPost('role');
        $profile  = $this->readProfileInput();

        if (! is_string($username) || trim($username) === '' || ! is_string($role) || ! in_array($role, $this->supportedRoles(), true)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'A valid username and role are required.']);
        }

        if (is_string($password) && $password !== '' && strlen($password) < 6) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'Passwords must be at least 6 characters.']);
        }

        if ($isHr && ! in_array($role, $this->employeeRoles(), true)) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'HR may create or update employee accounts but cannot create customer or administrator accounts.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        if ($id !== null && $id !== '') {
            $userId = filter_var($id, FILTER_VALIDATE_INT);
            if ($userId === false) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['success' => false, 'error' => 'Invalid user ID.']);
            }

            if ($isAdmin && (int) session()->get('userId') === $userId && $role !== 'Admin') {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['success' => false, 'error' => 'You cannot remove your own admin role.']);
            }

            $existingUser = $builder->where('id', $userId)->get()->getRowArray();
            if ($existingUser === null) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON(['success' => false, 'error' => 'User not found.']);
            }

            if ($isHr && ! in_array($existingUser['role'], $this->employeeRoles(), true)) {
                return $this->response
                    ->setStatusCode(403)
                    ->setJSON(['success' => false, 'error' => 'HR may update employee accounts only.']);
            }

            $builder = $db->table('users');
            $builder->where('username', trim($username))->where('id !=', $userId);
            $existingUsername = $builder->get()->getRowArray();
            $update = ['username' => trim($username), 'role' => $role];
            if (is_string($password) && $password !== '') {
                $update['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $id = $userId;
        } else {
            if (! is_string($password) || $password === '') {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['success' => false, 'error' => 'A password is required for a new user.']);
            }

            $builder->where('username', trim($username));
            $existingUsername = $builder->get()->getRowArray();
            $update = [
                'username' => trim($username),
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'role'     => $role,
                'is_active' => 1,
                'approval_status' => 'Approved',
            ];
        }

        if ($existingUsername !== null) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON(['success' => false, 'error' => 'Username already exists.']);
        }

        if ($profile['email'] !== '' && $this->emailExists($db, $profile['email'], (int) ($userId ?? 0))) {
            return $this->response
                ->setStatusCode(409)
                ->setJSON(['success' => false, 'error' => 'Email already registered.']);
        }

        $db->transStart();
        if (isset($userId)) {
            $db->table('users')->where('id', $userId)->update($update);
        } else {
            $db->table('users')->insert($update);
            $id = (int) $db->insertID();
        }
        $this->saveAccountProfile($db, (int) $id, $role, $profile);
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['success' => false, 'error' => 'User account could not be saved. Please try again.']);
        }

        return $this->response->setJSON([
            'success' => true,
            'user'    => ['id' => $id, 'username' => trim($username), 'role' => $role],
        ]);
    }

    public function deleteManagedUser()
    {
        if (! $this->hasPermission('users.manage')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Admin access is required.']);
        }

        $id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        if ($id === false || $id === (int) session()->get('userId')) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'Invalid user ID or you cannot delete your own account.']);
        }

        $db = \Config\Database::connect();
        $user = $db->table('users')->where('id', $id)->get()->getRowArray();
        if ($user === null) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['success' => false, 'error' => 'User not found.']);
        }

        $db->transStart();
        $db->table('customers')->where('user_id', $id)->delete();
        $db->table('employees')->where('user_id', $id)->delete();
        $db->table('users')->where('id', $id)->delete();
        $db->transComplete();

        return $this->response->setJSON(['success' => true]);
    }

    public function createBooking()
    {
        if (! $this->isAuthenticated() || session()->get('role') !== 'Customer') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Customer login is required.']);
        }

        $fields = [];
        foreach (['firstName', 'lastName', 'email', 'phone', 'address', 'serviceType', 'date', 'time', 'duration', 'notes'] as $field) {
            $value = $this->request->getPost($field);
            $fields[$field] = is_string($value) ? trim($value) : '';
        }
        if ($fields['firstName'] === '' || $fields['lastName'] === '' || $fields['email'] === '' || $fields['phone'] === '' || $fields['address'] === '' || $fields['serviceType'] === '' || $fields['date'] === '' || $fields['time'] === '') {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Complete all required booking fields.']);
        }

        $date = \DateTimeImmutable::createFromFormat('!Y-m-d', $fields['date']);
        $time = \DateTimeImmutable::createFromFormat('!H:i', $fields['time']);
        if ($date === false || $date->format('Y-m-d') !== $fields['date'] || $time === false || $time->format('H:i') !== $fields['time']) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid booking date and time.']);
        }

        $db = \Config\Database::connect();
        $db->table('bookings')->insert([
            'customer_id'   => (int) session()->get('userId'),
            'customer_name' => $fields['firstName'] . ' ' . $fields['lastName'],
            'email'         => $fields['email'],
            'phone'         => $fields['phone'],
            'address'       => $fields['address'],
            'service_type'  => $fields['serviceType'],
            'booking_date'  => $fields['date'],
            'booking_time'  => $fields['time'],
            'duration'      => $fields['duration'] ?: '1 hour',
            'notes'         => $fields['notes'] ?: null,
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return $this->response->setStatusCode(201)->setJSON(['success' => true, 'id' => $db->insertID()]);
    }

    public function createQuoteRequest()
    {
        $fields = [];
        foreach ([
            'fullName',
            'emailAddress',
            'phoneNumber',
            'serviceNeeded',
            'propertyAddress',
            'propertyType',
            'averageMonthlyElectricityBill',
            'preferredContactMethod',
            'additionalDetails',
            'preferredDateOrSchedule',
        ] as $field) {
            $value = $this->request->getPost($field);
            $fields[$field] = is_string($value) ? trim($value) : '';
        }

        foreach ([
            'fullName' => 'Full Name',
            'emailAddress' => 'Email Address',
            'phoneNumber' => 'Phone Number',
            'serviceNeeded' => 'Service Needed',
            'propertyAddress' => 'Property Address/Project Location',
            'propertyType' => 'Property Type',
            'averageMonthlyElectricityBill' => 'Average Monthly Electricity Bill',
            'preferredContactMethod' => 'Preferred Contact Method',
            'preferredDateOrSchedule' => 'Preferred Date or Schedule',
        ] as $field => $label) {
            if ($fields[$field] === '') {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => $label . ' is required.']);
            }
        }

        if (strlen($fields['fullName']) > 255
            || strlen($fields['emailAddress']) > 255
            || ! filter_var($fields['emailAddress'], FILTER_VALIDATE_EMAIL)
            || strlen($fields['phoneNumber']) > 50
            || strlen($fields['serviceNeeded']) > 255
            || strlen($fields['propertyType']) > 255
            || strlen($fields['averageMonthlyElectricityBill']) > 100
            || strlen($fields['preferredContactMethod']) > 100
            || strlen($fields['preferredDateOrSchedule']) > 255
            || ! in_array($fields['serviceNeeded'], ['Consultation', 'Designing', 'Installation', 'Repair', 'Monitoring', 'Maintenance'], true)
        ) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Check the email address and field lengths, then try again.']);
        }

        $preferredDate = \DateTimeImmutable::createFromFormat('!Y-m-d', $fields['preferredDateOrSchedule']);
        if ($preferredDate === false || $preferredDate->format('Y-m-d') !== $fields['preferredDateOrSchedule']) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid preferred date.']);
        }

        if ($this->request->getPost('privacyConsent') !== '1') {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Privacy/Consent is required to submit this request.']);
        }

        $attachment = $this->request->getFile('attachment');
        $attachmentPath = null;
        if ($attachment !== null && $attachment->getError() !== UPLOAD_ERR_NO_FILE) {
            $allowedMimeTypes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf',
            ];
            if (! $attachment->isValid()
                || $attachment->getSize() > 5 * 1024 * 1024
                || ! isset($allowedMimeTypes[$attachment->getMimeType()])
            ) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => 'Use a JPG, PNG, WebP, or PDF file no larger than 5 MB.',
                ]);
            }
        }

        $db = \Config\Database::connect();
        $userId = session()->get('isLoggedIn') === true && session()->get('role') === 'Customer'
            ? (int) session()->get('userId')
            : null;
        $movedAttachment = null;
        if ($attachment !== null && $attachment->getError() !== UPLOAD_ERR_NO_FILE) {
            $uploadDirectory = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'quote-requests';
            if (! is_dir($uploadDirectory) && ! mkdir($uploadDirectory, 0750, true) && ! is_dir($uploadDirectory)) {
                log_message('error', 'Unable to prepare private quote attachment storage.');

                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'The request could not be saved. Please try again later.']);
            }

            $extension = $allowedMimeTypes[$attachment->getMimeType()];
            $fileName = bin2hex(random_bytes(16)) . '.' . $extension;
            try {
                $attachment->move($uploadDirectory, $fileName);
                $movedAttachment = $uploadDirectory . DIRECTORY_SEPARATOR . $fileName;
                $attachmentPath = 'quote-requests/' . $fileName;
            } catch (\Throwable $exception) {
                log_message('error', 'Unable to store the quote request attachment.');

                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'The attachment could not be saved. Please try again later.']);
            }
        }

        $db->transBegin();
        try {
            $db->table('quote_requests')->insert([
                'customer_id' => $userId,
                'full_name' => $fields['fullName'],
                'email_address' => $fields['emailAddress'],
                'phone_number' => $fields['phoneNumber'],
                'service_needed' => $fields['serviceNeeded'],
                'property_address' => $fields['propertyAddress'],
                'property_type' => $fields['propertyType'],
                'average_monthly_electricity_bill' => $fields['averageMonthlyElectricityBill'],
                'preferred_contact_method' => $fields['preferredContactMethod'],
                'additional_details' => $fields['additionalDetails'] !== '' ? $fields['additionalDetails'] : null,
                'preferred_date_or_schedule' => $fields['preferredDateOrSchedule'],
                'attachment_path' => $attachmentPath,
                'privacy_consent' => 1,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Quote request insert failed.');
            }

            $requestId = (int) $db->insertID();
            $db->transCommit();
        } catch (\Throwable $exception) {
            $db->transRollback();
            if ($movedAttachment !== null && is_file($movedAttachment) && ! unlink($movedAttachment)) {
                log_message('error', 'Unable to remove an unassociated quote request attachment.');
            }
            log_message('error', 'Unable to save a quote request.');

            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'The request could not be saved. Please try again later.']);
        }

        return $this->response->setStatusCode(201)->setJSON(['success' => true, 'id' => $requestId]);
    }

    public function listBookings()
    {
        $role = session()->get('role');
        $canViewAll = $this->hasPermission('bookings.view_all');
        if (! $this->isAuthenticated()
            || (! $canViewAll && ! in_array($role, ['Technician', 'Maintenance', 'Customer'], true))
        ) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Login is required.']);
        }

        $builder = \Config\Database::connect()
            ->table('bookings b')
            ->select('b.id, b.customer_id AS userId, b.customer_name AS customerName, b.email, b.phone, b.address, b.service_type AS serviceType, b.booking_date AS date, b.booking_time AS time, b.duration, b.notes, b.status, b.assigned_employee_id AS assignedEmployeeId, CONCAT(COALESCE(e.first_name, ""), " ", COALESCE(e.last_name, "")) AS assignedEmployeeName')
            ->join('employees e', 'e.id = b.assigned_employee_id', 'left')
            ->orderBy('b.booking_date')
            ->orderBy('b.booking_time');
        if ($role === 'Customer') {
            $builder->where('b.customer_id', (int) session()->get('userId'));
        } elseif (! $canViewAll) {
            $employee = \Config\Database::connect()->table('employees')
                ->select('id')
                ->where('user_id', (int) session()->get('userId'))
                ->get()->getRowArray();
            if ($employee === null) {
                return $this->response->setJSON(['success' => true, 'bookings' => []]);
            }
            $builder->where('b.assigned_employee_id', (int) $employee['id']);
        }

        $bookings = $builder->get()->getResultArray();
        $bookings = array_values(array_filter($bookings, fn (array $booking): bool => $this->employeeCanHandleService($role, (string) $booking['serviceType']) || $canViewAll || $role === 'Customer'));

        return $this->response->setJSON(['success' => true, 'bookings' => $bookings]);
    }

    public function updateBookingStatus()
    {
        $role = session()->get('role');
        $mayManageStatus = $this->hasPermission('bookings.manage_status');
        $mayUpdateAssignedJob = $this->hasPermission('bookings.update_assigned');
        if (! $this->isAuthenticated() || (! $mayManageStatus && ! $mayUpdateAssignedJob)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Employee login is required.']);
        }

        $id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        $status = $this->request->getPost('status');
        if ($id === false || ! is_string($status) || ! in_array($status, ['Pending', 'Confirmed', 'Completed', 'Cancelled'], true)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid booking and status.']);
        }

        $db = \Config\Database::connect();
        $booking = $db->table('bookings')->where('id', $id)->get()->getRowArray();
        if ($booking === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Booking not found.']);
        }
        if ($mayUpdateAssignedJob) {
            $employee = \Config\Database::connect()->table('employees')
                ->select('id')
                ->where('user_id', (int) session()->get('userId'))
                ->get()->getRowArray();
            if ($employee === null
                || (int) $booking['assigned_employee_id'] !== (int) $employee['id']
                || ! $this->employeeCanHandleService((string) $role, (string) $booking['service_type'])
            ) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'You may update only jobs assigned to you in your department.']);
            }
        }
        $db->table('bookings')->where('id', $id)->update(['status' => $status]);

        return $this->response->setJSON(['success' => true]);
    }

    public function assignableEmployees()
    {
        if (! $this->hasPermission('bookings.assign')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Operations or Admin access is required.']);
        }

        $employees = \Config\Database::connect()->table('users u')
            ->select('e.id AS employeeId, u.id AS userId, u.username, u.role, e.first_name AS firstName, e.last_name AS lastName')
            ->join('employees e', 'e.user_id = u.id')
            ->whereIn('u.role', ['Technician', 'Maintenance'])
            ->where('u.is_active', 1)
            ->orderBy('e.last_name')
            ->orderBy('e.first_name')
            ->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'employees' => $employees]);
    }

    public function assignBooking()
    {
        if (! $this->hasPermission('bookings.assign')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Operations or Admin access is required.']);
        }

        $bookingId = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        $employeeInput = $this->request->getPost('employeeId');
        if ($bookingId === false || $bookingId < 1 || ! is_string($employeeInput)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid booking and employee.']);
        }

        $employeeId = $employeeInput === '' ? null : filter_var($employeeInput, FILTER_VALIDATE_INT);
        if ($employeeId !== null && ($employeeId === false || $employeeId < 1)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid booking and employee.']);
        }

        $db = \Config\Database::connect();
        $booking = $db->table('bookings')->select('id, service_type')->where('id', $bookingId)->get()->getRowArray();
        if ($booking === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Booking not found.']);
        }
        if ($employeeId === null) {
            $db->table('bookings')->where('id', $bookingId)->update(['assigned_employee_id' => null]);

            return $this->response->setJSON(['success' => true, 'bookingId' => $bookingId, 'employeeId' => null]);
        }
        $employee = $db->table('employees e')
            ->select('e.id, u.role')
            ->join('users u', 'u.id = e.user_id')
            ->where('e.id', $employeeId)
            ->where('u.is_active', 1)
            ->whereIn('u.role', ['Technician', 'Maintenance'])
            ->get()->getRowArray();
        if ($employee === null || ! $this->employeeCanHandleService((string) $employee['role'], (string) $booking['service_type'])) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose an active employee in the matching service department.']);
        }

        $db->table('bookings')->where('id', $bookingId)->update(['assigned_employee_id' => $employeeId]);

        return $this->response->setJSON(['success' => true, 'bookingId' => $bookingId, 'employeeId' => (int) $employeeId]);
    }

    public function listQuoteRequests()
    {
        $role = (string) session()->get('role');
        if (! $this->isAuthenticated()
            || (! $this->hasPermission('quotes.view') && ! in_array($role, ['Technician', 'Maintenance', 'Customer'], true))
        ) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Sales, Customer Service, Operations, or Admin access is required.']);
        }

        $db = \Config\Database::connect();
        $builder = $db->table('quote_requests q')
            ->select('q.id, q.customer_id AS userId, q.full_name AS fullName, q.email_address AS email, q.phone_number AS phone, q.service_needed AS serviceNeeded, q.property_address AS propertyAddress, q.property_type AS propertyType, q.average_monthly_electricity_bill AS averageMonthlyElectricityBill, q.preferred_contact_method AS preferredContactMethod, q.additional_details AS additionalDetails, q.preferred_date_or_schedule AS preferredDateOrSchedule, q.attachment_path AS hasAttachment, q.status, q.assigned_department AS assignedDepartment, q.assigned_employee_id AS assignedEmployeeId, CONCAT(COALESCE(e.first_name, ""), " ", COALESCE(e.last_name, "")) AS assignedEmployeeName, q.created_at AS createdAt')
            ->join('employees e', 'e.id = q.assigned_employee_id', 'left')
            ->orderBy('q.created_at', 'DESC');
        if (in_array($role, ['Technician', 'Maintenance'], true)) {
            $employee = $db->table('employees')->select('id')->where('user_id', (int) session()->get('userId'))->get()->getRowArray();
            if ($employee === null) {
                return $this->response->setJSON(['success' => true, 'requests' => []]);
            }
            $builder->where('q.assigned_employee_id', (int) $employee['id']);
        } elseif ($role === 'Customer') {
            $builder->where('q.customer_id', (int) session()->get('userId'));
        } elseif (! $this->hasPermission('quotes.view')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Quote access is restricted.']);
        }
        $requests = $builder->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'requests' => $requests]);
    }

    public function quoteAssignableEmployees()
    {
        if (! $this->hasPermission('quotes.assign')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Sales, Operations, or Admin access is required.']);
        }
        $employees = \Config\Database::connect()->table('users u')
            ->select('e.id AS employeeId, u.role, e.first_name AS firstName, e.last_name AS lastName')
            ->join('employees e', 'e.user_id = u.id')
            ->whereIn('u.role', ['Technician', 'Maintenance'])
            ->where('u.is_active', 1)
            ->where('u.approval_status', 'Approved')
            ->orderBy('e.last_name')->orderBy('e.first_name')
            ->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'employees' => $employees]);
    }

    public function assignQuoteRequest()
    {
        if (! $this->hasPermission('quotes.assign')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Sales, Operations, or Admin access is required.']);
        }
        $id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        $employeeInput = $this->request->getPost('employeeId');
        if ($id === false || $id < 1 || ! is_string($employeeInput)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid quote request and employee.']);
        }
        $employeeId = $employeeInput === '' ? null : filter_var($employeeInput, FILTER_VALIDATE_INT);
        if ($employeeId !== null && ($employeeId === false || $employeeId < 1)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid employee.']);
        }
        $db = \Config\Database::connect();
        $request = $db->table('quote_requests')->select('id, service_needed')->where('id', $id)->get()->getRowArray();
        if ($request === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Quote request not found.']);
        }
        $employee = null;
        if ($employeeId !== null) {
            $employee = $db->table('employees e')->select('e.id, u.role')
                ->join('users u', 'u.id = e.user_id')
                ->where('e.id', $employeeId)->where('u.is_active', 1)->where('u.approval_status', 'Approved')
                ->get()->getRowArray();
            if ($employee === null || ! $this->employeeCanHandleService((string) $employee['role'], (string) $request['service_needed'])) {
                return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose an active employee whose department matches this service.']);
            }
        }
        $department = $employee['role'] ?? null;
        $db->transBegin();
        $updated = $db->table('quote_requests')->where('id', $id)->update([
            'assigned_employee_id' => $employeeId,
            'assigned_department' => $department,
        ]);
        $messageSaved = $employeeId === null
            || $this->recordRequestMessage('quote', (int) $id, 'Request assigned to ' . $department . ' for follow-up.', true);
        if (! $updated || ! $messageSaved || $db->transStatus() === false) {
            $db->transRollback();
            log_message('error', 'Unable to update quote request assignment.');

            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'The quote request could not be assigned. Please try again.']);
        }
        $db->transCommit();

        return $this->response->setJSON(['success' => true, 'id' => (int) $id, 'assignedEmployeeId' => $employeeId, 'assignedDepartment' => $department]);
    }

    public function updateQuoteRequestStatus()
    {
        if (! $this->hasPermission('quotes.update_status') && ! $this->hasPermission('quotes.update_assigned')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Sales, Customer Service, or Admin access is required.']);
        }

        $id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        $status = $this->request->getPost('status');
        $validStatuses = ['Pending', 'Contacted', 'In Progress', 'Quoted', 'Closed', 'Declined'];
        if ($id === false || $id < 1 || ! is_string($status) || ! in_array($status, $validStatuses, true)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid quote request and status.']);
        }

        $db = \Config\Database::connect();
        $quote = $db->table('quote_requests')->where('id', $id)->get()->getRowArray();
        if ($quote === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Quote request not found.']);
        }
        if ($this->hasPermission('quotes.update_assigned') && ! $this->hasPermission('quotes.update_status')) {
            $employee = $db->table('employees')->select('id')->where('user_id', (int) session()->get('userId'))->get()->getRowArray();
            if ($employee === null || (int) $quote['assigned_employee_id'] !== (int) $employee['id']) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'You may update only quote requests assigned to you.']);
            }
        }
        if (! $db->table('quote_requests')->where('id', $id)->update(['status' => $status])) {
            log_message('error', 'Unable to update quote request status.');

            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'The quote request status could not be saved.']);
        }

        return $this->response->setJSON(['success' => true, 'id' => $id, 'status' => $status]);
    }

    public function requestMessages()
    {
        $type = $this->request->getGet('type');
        $id = filter_var($this->request->getGet('id'), FILTER_VALIDATE_INT);
        if (! is_string($type) || ! in_array($type, ['quote', 'booking'], true) || $id === false || $id < 1
            || ! $this->canAccessRequest($type, (int) $id)
        ) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'You cannot access this request conversation.']);
        }
        $messageBuilder = \Config\Database::connect()->table('request_messages')
            ->select('id, sender_name AS senderName, message, created_at AS createdAt')
            ->where('request_type', $type)->where('request_id', $id);
        if (session()->get('role') === 'Customer') {
            $messageBuilder->where('is_internal', 0);
        }
        $messages = $messageBuilder->orderBy('created_at')->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'messages' => $messages]);
    }

    public function addRequestMessage()
    {
        $type = $this->request->getPost('type');
        $id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        $message = $this->request->getPost('message');
        if (! is_string($type) || ! in_array($type, ['quote', 'booking'], true) || $id === false || $id < 1
            || ! is_string($message) || trim($message) === '' || strlen(trim($message)) > 5000
        ) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Enter a message of up to 5,000 characters.']);
        }
        if (! $this->canAccessRequest($type, (int) $id)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'You cannot reply to this request.']);
        }
        if (! $this->recordRequestMessage($type, (int) $id, trim($message), false)) {
            log_message('error', 'Unable to save a request conversation message.');

            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'The message could not be saved. Please try again.']);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function quoteAttachment(int $id)
    {
        if (! $this->canAccessRequest('quote', $id)) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }
        $quote = \Config\Database::connect()->table('quote_requests')->select('attachment_path')->where('id', $id)->get()->getRowArray();
        if ($quote === null || ! is_string($quote['attachment_path']) || $quote['attachment_path'] === '') {
            return $this->response->setStatusCode(404)->setBody('Attachment not found');
        }
        $path = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'quote-requests' . DIRECTORY_SEPARATOR . basename($quote['attachment_path']);
        if (! is_file($path)) {
            return $this->response->setStatusCode(404)->setBody('Attachment not found');
        }
        $mime = mime_content_type($path);
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'], true)) {
            return $this->response->setStatusCode(415)->setBody('Unsupported attachment type');
        }

        return $this->response->setHeader('Content-Type', $mime)->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Content-Disposition', 'inline; filename="quote-attachment"')->setBody((string) file_get_contents($path));
    }

    public function submitCustomerReview()
    {
        if (! $this->isAuthenticated() || session()->get('role') !== 'Customer') {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Customer login is required to submit a story.']);
        }
        $service = $this->request->getPost('serviceType');
        $story = $this->request->getPost('story');
        if (! is_string($service) || ! in_array($service, ['Consultation', 'Designing', 'Installation', 'Repair', 'Monitoring', 'Maintenance'], true)
            || ! is_string($story) || trim($story) === '' || strlen(trim($story)) > 5000
            || $this->request->getPost('privacyConsent') !== '1'
        ) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a service, enter a story up to 5,000 characters, and consent to publication review.']);
        }
        $db = \Config\Database::connect();
        $customer = $db->table('customers')->select('first_name, last_name')->where('user_id', (int) session()->get('userId'))->get()->getRowArray();
        if ($customer === null) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Complete your customer profile before submitting a story.']);
        }
        if (! $db->table('customer_reviews')->insert([
            'customer_id' => (int) session()->get('userId'),
            'customer_name' => trim($customer['first_name'] . ' ' . $customer['last_name']),
            'service_type' => $service,
            'story' => trim($story),
            'privacy_consent' => 1,
            'status' => 'Pending',
            'created_at' => date('Y-m-d H:i:s'),
        ])) {
            log_message('error', 'Unable to save a customer review submission.');

            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'Your story could not be saved. Please try again.']);
        }

        return $this->response->setStatusCode(201)->setJSON(['success' => true, 'id' => (int) $db->insertID()]);
    }

    public function publicCustomerReviews()
    {
        $reviews = \Config\Database::connect()->table('customer_reviews')
            ->select('customer_name AS customerName, service_type AS serviceType, story, published_at AS publishedAt')
            ->where('status', 'Published')->orderBy('published_at', 'DESC')->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'reviews' => $reviews]);
    }

    public function marketingReviews()
    {
        if (! $this->hasPermission('reviews.manage')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Marketing or Admin access is required.']);
        }
        $reviews = \Config\Database::connect()->table('customer_reviews')
            ->select('id, customer_name AS customerName, service_type AS serviceType, story, status, created_at AS createdAt')
            ->orderBy('created_at', 'DESC')->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'reviews' => $reviews]);
    }

    public function updateCustomerReview()
    {
        if (! $this->hasPermission('reviews.manage')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Marketing or Admin access is required.']);
        }
        $id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        $status = $this->request->getPost('status');
        if ($id === false || $id < 1 || ! is_string($status) || ! in_array($status, ['Published', 'Declined', 'Pending'], true)) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid review and publication status.']);
        }
        $db = \Config\Database::connect();
        if ($db->table('customer_reviews')->where('id', $id)->countAllResults() === 0) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Review not found.']);
        }
        if (! $db->table('customer_reviews')->where('id', $id)->update([
            'status' => $status,
            'published_at' => $status === 'Published' ? date('Y-m-d H:i:s') : null,
        ])) {
            log_message('error', 'Unable to change customer review publication status.');

            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'The review publication status could not be saved.']);
        }

        return $this->response->setJSON(['success' => true, 'id' => $id, 'status' => $status]);
    }

    private function canAccessRequest(string $type, int $id): bool
    {
        if (! $this->isAuthenticated()) {
            return false;
        }
        $userId = (int) session()->get('userId');
        $role = (string) session()->get('role');
        $db = \Config\Database::connect();
        if ($role === 'Customer') {
            $table = $type === 'quote' ? 'quote_requests' : 'bookings';
            return $db->table($table)->where('id', $id)->where('customer_id', $userId)->countAllResults() > 0;
        }
        if ($type === 'quote') {
            if ($this->hasPermission('quotes.view')) {
                return true;
            }
            if (! in_array($role, ['Technician', 'Maintenance'], true)) {
                return false;
            }
            $employee = $db->table('employees')->select('id')->where('user_id', $userId)->get()->getRowArray();
            return $employee !== null && $db->table('quote_requests')->where('id', $id)->where('assigned_employee_id', (int) $employee['id'])->countAllResults() > 0;
        }
        if ($this->hasPermission('bookings.view_all')) {
            return true;
        }
        if (in_array($role, ['Technician', 'Maintenance'], true)) {
            $employee = $db->table('employees')->select('id')->where('user_id', $userId)->get()->getRowArray();
            return $employee !== null && $db->table('bookings')->where('id', $id)->where('assigned_employee_id', (int) $employee['id'])->countAllResults() > 0;
        }

        return false;
    }

    private function recordRequestMessage(string $type, int $id, string $message, bool $system): bool
    {
        $userId = (int) session()->get('userId');
        $name = $system ? (string) session()->get('username') : (string) session()->get('username');
        if (! $system) {
            $profile = \Config\Database::connect()->table('employees')->select('first_name, last_name')->where('user_id', $userId)->get()->getRowArray();
            if ($profile !== null) {
                $name = trim($profile['first_name'] . ' ' . $profile['last_name']);
            } elseif (session()->get('role') === 'Customer') {
                $profile = \Config\Database::connect()->table('customers')->select('first_name, last_name')->where('user_id', $userId)->get()->getRowArray();
                if ($profile !== null) {
                    $name = trim($profile['first_name'] . ' ' . $profile['last_name']);
                }
            }
        }
        return \Config\Database::connect()->table('request_messages')->insert([
            'request_type' => $type,
            'request_id' => $id,
            'sender_user_id' => $userId,
            'sender_name' => $name,
            'message' => $message,
            'is_internal' => $system ? 1 : 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function createAttendance()
    {
        $role = session()->get('role');
        if (! $this->isAuthenticated() || ! in_array($role, $this->employeeRoles(), true)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Employee login is required.']);
        }

        $siteName = $this->request->getPost('siteName');
        $address = $this->request->getPost('address');
        if (! is_string($siteName) || trim($siteName) === '' || ! is_string($address) || trim($address) === '') {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Site name and address are required.']);
        }

        $db = \Config\Database::connect();
        $employee = $db->table('employees')->where('user_id', (int) session()->get('userId'))->get()->getRowArray();
        if ($employee === null) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Employee profile was not found. Ask an admin to complete your employee record.']);
        }

        $latitude = $this->request->getPost('latitude');
        $longitude = $this->request->getPost('longitude');
        $db->table('technician_checkin')->insert([
            'employee_id'   => $employee['id'],
            'employee_name' => trim($employee['first_name'] . ' ' . $employee['last_name']),
            'latitude'      => is_numeric($latitude) ? $latitude : null,
            'longitude'     => is_numeric($longitude) ? $longitude : null,
            'address'       => trim($address),
        ]);

        return $this->response->setStatusCode(201)->setJSON(['success' => true]);
    }

    public function attendanceForMonth()
    {
        if (! $this->hasPermission('attendance.view')) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Admin or HR access is required.']);
        }

        $month = $this->request->getGet('month');
        if (! is_string($month) || preg_match('/^\d{4}-\d{2}$/', $month) !== 1) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid month.']);
        }
        $monthDate = \DateTimeImmutable::createFromFormat('!Y-m', $month);
        if ($monthDate === false || $monthDate->format('Y-m') !== $month) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Choose a valid month.']);
        }
        $start = $monthDate->format('Y-m-01 00:00:00');
        $end = $monthDate->modify('first day of next month')->format('Y-m-01 00:00:00');

        $db = \Config\Database::connect();
        $employees = $db->table('users u')
            ->select('u.id AS userId, e.id AS employeeId, u.username, COALESCE(e.first_name, u.username) AS firstName, COALESCE(e.last_name, "") AS lastName, u.role')
            ->join('employees e', 'u.id = e.user_id', 'left')
            ->whereIn('u.role', $this->employeeRoles())
            ->where('u.is_active', 1)
            ->orderBy('u.role')
            ->orderBy('e.last_name')
            ->get()
            ->getResultArray();
        $attendance = $db->table('technician_checkin')
            ->select('id, employee_id AS employeeId, employee_name AS employeeName, check_in_time AS timeIn, address')
            ->where('check_in_time >=', $start)
            ->where('check_in_time <', $end)
            ->orderBy('check_in_time')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['success' => true, 'employees' => $employees, 'attendance' => $attendance]);
    }

    private function readProfileInput(): array
    {
        $profile = [];
        foreach (['firstName', 'lastName', 'middleName', 'birthdate', 'gender', 'email', 'phone', 'address'] as $field) {
            $value = $this->request->getPost($field);
            $profile[$field] = is_string($value) ? trim($value) : '';
        }

        return $profile;
    }

    private function renderDepartmentDashboard(string $role, string $view): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Admin', $role])) {
            return $redirect;
        }

        return view($view);
    }

    private function hasPermission(string $permission): bool
    {
        if (! $this->isAuthenticated()) {
            return false;
        }

        $rolesByPermission = [
            'users.manage' => ['Admin'],
            'system.users.view' => ['Admin', 'IT'],
            'system.users.status' => ['Admin', 'IT'],
            'employees.manage' => ['Admin', 'HR'],
            'employees.approve' => ['Admin', 'HR'],
            'attendance.view' => ['Admin', 'HR'],
            'catalog.manage' => ['Admin', 'Marketing'],
            'bookings.view_all' => ['Admin', 'Dispatch', 'Dispatcher', 'Sales', 'Customer Service'],
            'bookings.assign' => ['Admin', 'Dispatch', 'Dispatcher'],
            'bookings.manage_status' => ['Admin', 'Dispatch', 'Dispatcher', 'Customer Service'],
            'bookings.update_assigned' => ['Technician', 'Maintenance'],
            'quotes.view' => ['Admin', 'Dispatch', 'Dispatcher', 'Sales', 'Customer Service'],
            'quotes.update_status' => ['Admin', 'Sales', 'Customer Service'],
            'quotes.assign' => ['Admin', 'Dispatch', 'Dispatcher', 'Sales'],
            'quotes.update_assigned' => ['Technician', 'Maintenance'],
            'reviews.manage' => ['Admin', 'Marketing'],
        ];

        return in_array(session()->get('role'), $rolesByPermission[$permission] ?? [], true);
    }

    private function isAuthenticated(): bool
    {
        if (session()->get('isLoggedIn') !== true) {
            return false;
        }
        if ($this->sessionAccountActive !== null) {
            return $this->sessionAccountActive;
        }

        $userId = filter_var(session()->get('userId'), FILTER_VALIDATE_INT);
        if ($userId === false || $userId < 1) {
            $this->sessionAccountActive = false;
            session()->destroy();

            return false;
        }

        $user = \Config\Database::connect()->table('users')
            ->select('role, is_active, approval_status')
            ->where('id', $userId)
            ->get()->getRowArray();
        $this->sessionAccountActive = $user !== null
            && (int) $user['is_active'] === 1
            && $user['approval_status'] === 'Approved'
            && $user['role'] === session()->get('role');

        if (! $this->sessionAccountActive) {
            session()->destroy();
        }

        return $this->sessionAccountActive;
    }

    private function employeeCanHandleService(string $role, string $serviceType): bool
    {
        if ($role === 'Maintenance') {
            return $serviceType === 'Maintenance';
        }

        return $role === 'Technician'
            && in_array($serviceType, ['Designing', 'Installation', 'Repair', 'Monitoring'], true);
    }

    /** @return list<string> */
    private function supportedRoles(): array
    {
        return ['Admin', 'IT', 'Dispatch', 'Dispatcher', 'Accounting', 'HR', 'Marketing', 'Sales', 'Customer Service', 'Technician', 'Maintenance', 'Customer'];
    }

    /** @return list<string> */
    private function employeeRoles(): array
    {
        return array_values(array_filter(
            $this->supportedRoles(),
            static fn (string $role): bool => ! in_array($role, ['Admin', 'Customer'], true)
        ));
    }

    private function canManageCatalog(): bool
    {
        return $this->hasPermission('catalog.manage');
    }

    private function catalogPhotoInput(): string|null|false
    {
        $photo = $this->request->getPost('photo');
        if (! is_string($photo) || $photo === '') {
            return null;
        }
        if (strlen($photo) > 3_000_000 || preg_match('/^data:image\/(?:png|jpeg|webp|gif);base64,[A-Za-z0-9+\/=]+$/', $photo) !== 1) {
            return false;
        }

        return $photo;
    }

    private function persistCatalogEntry(string $table, mixed $id, array $fields)
    {
        $db = \Config\Database::connect();
        if ($id !== null && $id !== '') {
            $entryId = filter_var($id, FILTER_VALIDATE_INT);
            if ($entryId === false || $db->table($table)->where('id', $entryId)->countAllResults() === 0) {
                return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Catalog item not found.']);
            }
            if ($fields['photo'] === null) {
                unset($fields['photo']);
            }
            $fields['updated_at'] = date('Y-m-d H:i:s');
            $db->table($table)->where('id', $entryId)->update($fields);
        } else {
            $fields['created_at'] = date('Y-m-d H:i:s');
            $db->table($table)->insert($fields);
            $entryId = (int) $db->insertID();
        }

        return $this->response->setJSON(['success' => true, 'id' => $entryId]);
    }

    private function deleteCatalogEntry(string $table)
    {
        if (! $this->canManageCatalog()) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Marketing or Admin access is required to edit the catalog.']);
        }
        $id = filter_var($this->request->getPost('id'), FILTER_VALIDATE_INT);
        if ($id === false) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'A valid catalog item is required.']);
        }

        $db = \Config\Database::connect();
        if ($db->table($table)->where('id', $id)->countAllResults() === 0) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Catalog item not found.']);
        }
        $db->table($table)->where('id', $id)->delete();

        return $this->response->setJSON(['success' => true]);
    }

    private function saveAccountProfile(\CodeIgniter\Database\BaseConnection $db, int $userId, string $role, array $profile): void
    {
        $table = $role === 'Customer' ? 'customers' : 'employees';
        $otherTable = $role === 'Customer' ? 'employees' : 'customers';
        $existing = $db->table($table)->where('user_id', $userId)->get()->getRowArray();
        $otherExisting = $db->table($otherTable)->where('user_id', $userId)->get()->getRowArray();
        if ($otherExisting !== null) {
            $db->table($otherTable)->where('user_id', $userId)->delete();
        }

        $data = [
            'user_id'     => $userId,
            'first_name'  => $profile['firstName'] ?: 'Account',
            'last_name'   => $profile['lastName'] ?: 'User',
            'middle_name' => $profile['middleName'] ?: null,
            'birthdate'   => $profile['birthdate'] ?: null,
            'gender'      => $profile['gender'] ?: null,
            'email'       => $profile['email'] ?: null,
            'phone_number'=> $profile['phone'] ?: null,
            'address'     => $profile['address'] ?: null,
        ];
        if ($role !== 'Customer') {
            $data['department'] = $role === 'Dispatcher' ? 'Dispatch' : $role;
        }
        if ($existing === null) {
            $db->table($table)->insert($data);
        } else {
            unset($data['user_id']);
            $db->table($table)->where('user_id', $userId)->update($data);
        }
    }

    private function emailExists(\CodeIgniter\Database\BaseConnection $db, string $email, int $exceptUserId = 0): bool
    {
        foreach (['customers', 'employees'] as $table) {
            $builder = $db->table($table)->select('id')->where('email', $email);
            if ($exceptUserId > 0) {
                $builder->where('user_id !=', $exceptUserId);
            }
            if ($builder->get()->getRowArray() !== null) {
                return true;
            }
        }

        return false;
    }

    /**
     * Redirect unauthenticated users to login and users with the wrong role to their dashboard.
     *
     * @param list<string> $roles
     */
    private function redirectUnlessRole(array $roles): ?RedirectResponse
    {
        if (! $this->isAuthenticated()) {
            return redirect()->to(site_url('login.php'));
        }

        $role = session()->get('role');
        if (! in_array($role, $roles, true)) {
            $destination = match ($role) {
                'Admin'      => 'admin-dashboard.php',
                'Dispatcher', 'Dispatch' => 'dispatcher-dashboard.php',
                'Technician' => 'technician-dashboard.php',
                'Maintenance' => 'maintenance-dashboard.php',
                'IT' => 'it-dashboard.php',
                'Accounting' => 'accounting-dashboard.php',
                'HR' => 'hr-dashboard.php',
                'Marketing' => 'marketing-dashboard.php',
                'Sales' => 'sales-dashboard.php',
                'Customer Service' => 'customer-service-dashboard.php',
                'Customer'   => 'customer-dashboard.php',
                default      => 'login.php',
            };

            return redirect()->to(site_url($destination));
        }

        return null;
    }
}
