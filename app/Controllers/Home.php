<?php

namespace App\Controllers;

use CodeIgniter\HTTP\RedirectResponse;

class Home extends BaseController
{
    public function index(): string
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
        if ($redirect = $this->redirectUnlessRole(['Customer'])) {
            return $redirect;
        }

        return view('customer-dashboard');
    }

    public function technicianDashboard(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Technician'])) {
            return $redirect;
        }

        return view('technician-dashboard');
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
        if ($redirect = $this->redirectUnlessRole(['Dispatcher', 'Dispatch'])) {
            return $redirect;
        }

        return view('dispatcher-dashboard');
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
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Only Dispatch can edit the product catalog.']);
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
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Only Dispatch can edit the services catalog.']);
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
        if (session()->get('isLoggedIn') !== true || ! in_array($role, ['Admin', 'IT', 'Accounting', 'Marketing', 'Sales', 'Customer Service'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'This department summary is restricted.']);
        }

        $db = \Config\Database::connect();
        $bookingCounts = [];
        foreach ($db->table('bookings')->select('status, COUNT(*) AS total')->groupBy('status')->get()->getResultArray() as $row) {
            $bookingCounts[$row['status']] = (int) $row['total'];
        }

        return $this->response->setJSON([
            'success' => true,
            'summary' => [
                'users' => (int) $db->table('users')->countAllResults(),
                'products' => (int) $db->table('catalog_products')->countAllResults(),
                'services' => (int) $db->table('catalog_services')->countAllResults(),
                'bookings' => array_sum($bookingCounts),
                'bookingStatuses' => $bookingCounts,
                'database' => 'connected',
            ],
        ]);
    }

    public function hrEmployeeDirectory()
    {
        if (session()->get('isLoggedIn') !== true || ! in_array(session()->get('role'), ['Admin', 'HR'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'HR or Admin access is required.']);
        }

        $employees = \Config\Database::connect()->table('users u')
            ->select('u.id, u.username, u.role, COALESCE(e.first_name, u.username) AS firstName, COALESCE(e.last_name, "") AS lastName, COALESCE(e.email, "") AS email')
            ->join('employees e', 'e.user_id = u.id', 'left')
            ->whereIn('u.role', $this->employeeRoles())
            ->orderBy('u.role')->orderBy('e.last_name')->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'employees' => $employees]);
    }

    public function forgotPassword(): string
    {
        return view('forgot-password');
    }

    public function profile(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole($this->supportedRoles())) {
            return $redirect;
        }

        return view('profile');
    }

    public function schedule(): string|RedirectResponse
    {
        if ($redirect = $this->redirectUnlessRole(['Customer'])) {
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
            ->select('id, username, password, role')
            ->where('username', trim($usernameInput))
            ->get()
            ->getRowArray();

        $roles = $this->supportedRoles();
        if ($user === null || $passwordInput !== $user['password'] || ! in_array($user['role'], $roles, true)) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['success' => false, 'error' => 'Maling username o password.']);
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

        if (! is_string($username) || trim($username) === '' || ! is_string($password) || strlen($password) < 6) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'Username at password na hindi bababa sa 6 na character ay kinakailangan.']);
        }

        if (! is_string($role) || ! in_array($role, ['Customer', 'Technician', 'Dispatcher'], true)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['success' => false, 'error' => 'Hindi pinapayagan ang role na ito para sa self-registration.']);
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
            'username' => trim($username),
            'password' => $password,
            'role'     => $role,
        ]);
        $userId = (int) $db->insertID();
        $this->saveAccountProfile($db, $userId, $role, $profile);
        $db->transComplete();

        return $this->response
            ->setStatusCode(201)
            ->setJSON([
                'success' => true,
                'user'    => [
                    'id'       => $userId,
                    'username' => trim($username),
                    'role'     => $role,
                ],
            ]);
    }

    public function managedUsers()
    {
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'Admin') {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Admin access is required.']);
        }

        $users = \Config\Database::connect()
            ->table('users u')
            ->select('u.id, u.username, u.role, COALESCE(c.first_name, e.first_name, "") AS firstName, COALESCE(c.last_name, e.last_name, "") AS lastName, COALESCE(c.email, e.email, "") AS email, COALESCE(c.phone_number, e.phone_number, "") AS phone, COALESCE(c.address, e.address, "") AS address')
            ->join('customers c', 'c.user_id = u.id', 'left')
            ->join('employees e', 'e.user_id = u.id', 'left')
            ->orderBy('u.id')
            ->get()
            ->getResultArray();

        return $this->response->setJSON(['success' => true, 'users' => $users]);
    }

    public function saveManagedUser()
    {
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'Admin') {
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['success' => false, 'error' => 'Admin access is required.']);
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

        $db = \Config\Database::connect();
        $builder = $db->table('users');

        if ($id !== null && $id !== '') {
            $userId = filter_var($id, FILTER_VALIDATE_INT);
            if ($userId === false) {
                return $this->response
                    ->setStatusCode(400)
                    ->setJSON(['success' => false, 'error' => 'Invalid user ID.']);
            }

            if ((int) session()->get('userId') === $userId && $role !== 'Admin') {
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

            $builder = $db->table('users');
            $builder->where('username', trim($username))->where('id !=', $userId);
            $existingUsername = $builder->get()->getRowArray();
            $update = ['username' => trim($username), 'role' => $role];
            if (is_string($password) && $password !== '') {
                $update['password'] = $password;
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
            $update = ['username' => trim($username), 'password' => $password, 'role' => $role];
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

        return $this->response->setJSON([
            'success' => true,
            'user'    => ['id' => $id, 'username' => trim($username), 'role' => $role],
        ]);
    }

    public function deleteManagedUser()
    {
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'Admin') {
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
        if (session()->get('isLoggedIn') !== true || session()->get('role') !== 'Customer') {
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

    public function listBookings()
    {
        $role = session()->get('role');
        if (session()->get('isLoggedIn') !== true || ! in_array($role, ['Admin', 'Dispatch', 'Dispatcher', 'Technician', 'Sales', 'Customer Service', 'Customer'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Login is required.']);
        }

        $builder = \Config\Database::connect()
            ->table('bookings')
            ->select('id, customer_id AS userId, customer_name AS customerName, email, phone, address, service_type AS serviceType, booking_date AS date, booking_time AS time, duration, notes, status')
            ->orderBy('booking_date')
            ->orderBy('booking_time');
        if ($role === 'Customer') {
            $builder->where('customer_id', (int) session()->get('userId'));
        }

        $bookings = $builder->get()->getResultArray();
        if ($role === 'Technician') {
            $technicianServices = ['Designing', 'Installation', 'Maintenance', 'Repair', 'Monitoring'];
            $bookings = array_values(array_filter($bookings, static fn (array $booking): bool => in_array($booking['serviceType'], $technicianServices, true)));
        }

        return $this->response->setJSON(['success' => true, 'bookings' => $bookings]);
    }

    public function updateBookingStatus()
    {
        $role = session()->get('role');
        if (session()->get('isLoggedIn') !== true || ! in_array($role, ['Admin', 'Dispatcher', 'Dispatch', 'Technician'], true)) {
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
        if ($role === 'Technician' && ! in_array($booking['service_type'], ['Designing', 'Installation', 'Maintenance', 'Repair', 'Monitoring'], true)) {
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Technicians may only update technician-related bookings.']);
        }
        $db->table('bookings')->where('id', $id)->update(['status' => $status]);

        return $this->response->setJSON(['success' => true]);
    }

    public function createAttendance()
    {
        $role = session()->get('role');
        if (session()->get('isLoggedIn') !== true || ! in_array($role, $this->employeeRoles(), true)) {
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
        if (session()->get('isLoggedIn') !== true || ! in_array(session()->get('role'), ['Admin', 'HR'], true)) {
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
        if ($redirect = $this->redirectUnlessRole([$role])) {
            return $redirect;
        }

        return view($view);
    }

    /** @return list<string> */
    private function supportedRoles(): array
    {
        return ['Admin', 'IT', 'Dispatch', 'Dispatcher', 'Accounting', 'HR', 'Marketing', 'Sales', 'Customer Service', 'Technician', 'Customer'];
    }

    /** @return list<string> */
    private function employeeRoles(): array
    {
        return array_values(array_filter($this->supportedRoles(), static fn (string $role): bool => $role !== 'Customer'));
    }

    private function canManageCatalog(): bool
    {
        return session()->get('isLoggedIn') === true && in_array(session()->get('role'), ['Dispatch', 'Dispatcher'], true);
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
            return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Only Dispatch can edit the catalog.']);
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
        if (session()->get('isLoggedIn') !== true) {
            return redirect()->to(site_url('login.php'));
        }

        $role = session()->get('role');
        if (! in_array($role, $roles, true)) {
            $destination = match ($role) {
                'Admin'      => 'admin-dashboard.php',
                'Dispatcher', 'Dispatch' => 'dispatcher-dashboard.php',
                'Technician' => 'technician-dashboard.php',
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
