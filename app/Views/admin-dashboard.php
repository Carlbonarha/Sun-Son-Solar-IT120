<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        body { background: #f4f6f8; }
        .admin-layout { display: flex; min-height: calc(100vh - 68px); }
        .admin-side { width: 220px; background: #123b6d; padding: 24px 14px; }
        .admin-side h2 { color: white; font-size: 18px; padding: 0 12px 20px; }
        .admin-side button { width: 100%; border: 0; background: transparent; color: #dbe9f7; text-align: left; padding: 12px; border-radius: 6px; cursor: pointer; font: inherit; }
        .admin-side button:hover, .admin-side button.active { background: #1a73e8; color: white; }
        .admin-main { flex: 1; padding: 26px; min-width: 0; }
        .admin-view { display: none; }
        .admin-view.active { display: block; }
        .admin-view h1 { color: #123b6d; margin-bottom: 8px; }
        .admin-view > p { color: #666; margin-bottom: 20px; }
        .admin-grid { display: grid; grid-template-columns: minmax(260px, 350px) 1fr; gap: 20px; align-items: start; }
        .admin-card { background: white; padding: 22px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .admin-card h2 { color: #1a73e8; font-size: 19px; margin-bottom: 16px; }
        .record-list { display: grid; gap: 10px; }
        .record { background: white; border: 1px solid #e0e0e0; border-left: 4px solid #ffd400; border-radius: 6px; padding: 14px; }
        .record h3 { color: #1a73e8; font-size: 16px; margin-bottom: 6px; }
        .record p { color: #666; font-size: 14px; margin: 4px 0; }
        .record-actions { display: flex; gap: 8px; margin-top: 10px; }
        .record-actions button { flex: 1; }
        .small-btn { padding: 8px; border: 0; border-radius: 5px; cursor: pointer; font-weight: bold; }
        .edit-btn { background: #e3f0ff; color: #1557b0; }
        .delete-btn { background: #f8d7da; color: #721c24; }
        .attendance-table { width: 100%; overflow-x: auto; }
        .attendance-table table { min-width: 650px; }
        .role-filters { display: flex; flex-wrap: wrap; gap: 8px; margin: 12px 0; }
        .role-filters button, .calendar-controls button { border: 1px solid #cbd5e1; border-radius: 5px; background: #fff; padding: 8px 12px; cursor: pointer; }
        .role-filters button.active { background: #1a73e8; color: #fff; }
        .employee-search { width: 100%; max-width: 420px; padding: 10px; border: 1px solid #ccd3da; border-radius: 6px; margin: 8px 0 16px; }
        .attendance-calendar { display: grid; grid-template-columns: repeat(7, minmax(32px, 1fr)); gap: 6px; margin: 16px 0; }
        .calendar-day { min-height: 62px; padding: 8px 4px; border: 1px solid #dce2e8; border-radius: 6px; background: #fff; cursor: pointer; text-align: center; }
        .calendar-day.selected { outline: 2px solid #1a73e8; }
        .calendar-day.has-attendance::after { content: ''; display: block; width: 8px; height: 8px; margin: 5px auto 0; border-radius: 50%; background: #159447; }
        .calendar-heading { color: #123b6d; font-weight: bold; text-align: center; }
        .calendar-controls { display: flex; justify-content: space-between; align-items: center; gap: 8px; max-width: 520px; }
        .calendar-controls input { padding: 8px; border: 1px solid #ccd3da; border-radius: 5px; }
        .role-group { margin: 14px 0 20px; }
        .role-group h2 { color: #123b6d; font-size: 18px; margin-bottom: 8px; }
        .account-search { width: 100%; max-width: 520px; padding: 10px; border: 1px solid #ccd3da; border-radius: 6px; margin: 0 0 16px; }
        #attendanceDayTitle { margin: 14px 0; }
        .empty-state { color: #666; padding: 20px 0; }
        @media (max-width: 800px) { .admin-layout { display: block; } .admin-side { width: 100%; display: flex; overflow-x: auto; gap: 6px; padding: 10px; } .admin-side h2 { display: none; } .admin-side button { width: auto; white-space: nowrap; } .admin-main { padding: 18px 12px; } .admin-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">🌞 Sun Son Solar</div>
        <nav class="header-nav" aria-label="Admin navigation">
            <span id="adminUsername"></span>
            <a href="<?= site_url('admin-dashboard.php') ?>">Dashboard</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <a href="<?= site_url('profile.php') ?>">My Profile</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>

    <main class="admin-layout">
        <aside class="admin-side" aria-label="Admin sections">
            <h2>Administration</h2>
            <button class="active" data-view="usersView">Accounts</button>
            <button data-view="employeesView">Employees</button>
            <button data-view="attendanceView">Attendance</button>
        </aside>

        <section class="admin-main">
            <div id="adminAlert" class="alert" role="alert" aria-live="polite"></div>
            <section class="admin-view active" id="usersView">
                <h1>Accounts by Role</h1>
                <p>Create and manage customer and department accounts.</p>
                <label for="accountSearch">Search all accounts</label>
                <input class="account-search" id="accountSearch" type="search" placeholder="Search username, name, email, or role" autocomplete="off">
                <div class="admin-grid">
                    <form class="admin-card" id="userForm">
                        <h2 id="userFormTitle">Create User</h2>
                        <input id="userId" type="hidden">
                        <div class="form-group"><label for="userUsername">Username *</label><input id="userUsername" required></div>
                        <div class="form-group"><label for="userPassword">Password <span id="passwordHint">*</span></label><input id="userPassword" type="password"></div>
                        <div class="form-group"><label for="userRole">Role *</label><select id="userRole" required><option>Customer</option><option>Admin</option><option>IT</option><option>Dispatch</option><option>Dispatcher</option><option>Accounting</option><option>HR</option><option>Marketing</option><option>Sales</option><option>Customer Service</option><option>Technician</option></select></div>
                        <div class="form-group"><label for="userFirstName">First Name *</label><input id="userFirstName" required></div>
                        <div class="form-group"><label for="userLastName">Last Name *</label><input id="userLastName" required></div>
                        <div class="form-group"><label for="userEmail">Email *</label><input id="userEmail" type="email" required></div>
                        <div class="form-group"><label for="userPhone">Phone</label><input id="userPhone" type="tel"></div>
                        <div class="form-group"><label for="userAddress">Address</label><textarea id="userAddress"></textarea></div>
                        <button class="btn btn-primary" type="submit">Save User</button>
                        <button class="btn btn-secondary" type="button" onclick="resetUserForm()">Clear</button>
                    </form>
                    <div id="usersList"></div>
                </div>
            </section>

            <section class="admin-view" id="employeesView">
                <h1>Employees</h1>
                <p>Search employee accounts or filter the list by role.</p>
                <input class="employee-search" id="employeeSearch" type="search" placeholder="Search name, username, email, or role" aria-label="Search employee accounts">
                <div class="role-filters" id="employeeRoleFilters">
                    <button class="active" type="button" data-role="All">All employees</button>
                    <button type="button" data-role="Admin">Admin</button>
                    <button type="button" data-role="Technician">Technician</button>
                    <button type="button" data-role="Dispatch">Dispatch</button>
                    <button type="button" data-role="Dispatcher">Dispatcher (legacy)</button>
                    <button type="button" data-role="IT">IT</button>
                    <button type="button" data-role="Accounting">Accounting</button>
                    <button type="button" data-role="HR">HR</button>
                    <button type="button" data-role="Marketing">Marketing</button>
                    <button type="button" data-role="Sales">Sales</button>
                    <button type="button" data-role="Customer Service">Customer Service</button>
                </div>
                <div class="record-list" id="employeesList"></div>
            </section>

            <section class="admin-view" id="attendanceView">
                <h1>Attendance</h1>
                <p>Select a date to see which employees recorded attendance.</p>
                <div class="calendar-controls">
                    <button id="previousMonth" type="button" aria-label="Previous month">&lt;</button>
                    <label for="attendanceMonth">Month</label>
                    <input id="attendanceMonth" type="month">
                    <button id="nextMonth" type="button" aria-label="Next month">&gt;</button>
                </div>
                <div class="attendance-calendar" id="attendanceCalendar" aria-label="Attendance calendar"></div>
                <h2 id="attendanceDayTitle"></h2>
                <div class="attendance-table" id="attendanceList"></div>
            </section>
        </section>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        const adminUser = { id: <?= (int) session()->get('userId') ?>, username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>, role: 'Admin' };
        localStorage.setItem('currentUser', JSON.stringify(adminUser));
        document.getElementById('adminUsername').textContent = `@${adminUser.username}`;
        const escapeHtml = value => String(value || '').replace(/[&<>'"]/g, character => ({'&':'&amp;','<':'&lt;','>':'&gt;',"'":'&#39;','"':'&quot;'}[character]));
        async function requestUserApi(path, data = {}) {
            const response = await fetch('<?= rtrim(site_url(''), '/') . '/' ?>' + path, {
                method: data.method || 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    ...(data.method === 'POST' ? { 'Content-Type': 'application/x-www-form-urlencoded' } : {})
                },
                body: data.method === 'POST' ? new URLSearchParams(data.fields) : undefined
            });
            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.error || 'User account request failed.');
            }
            return result;
        }

        async function loadManagedUsers() {
            const result = await requestUserApi('users/list');
            const users = result.users.map(user => ({ ...user, id: Number(user.id) }));
            localStorage.setItem('users', JSON.stringify(users));
            const userIds = new Set(users.map(user => user.id));
            for (const key of ['customers', 'employees']) {
                const records = JSON.parse(localStorage.getItem(key) || '[]');
                const profiles = result.users
                    .filter(user => (key === 'customers' ? user.role === 'Customer' : user.role !== 'Customer'))
                    .map(user => ({
                        userId: Number(user.id),
                        firstName: user.firstName,
                        lastName: user.lastName,
                        email: user.email,
                        phone: user.phone,
                        address: user.address,
                        department: user.role
                    }));
                localStorage.setItem(key, JSON.stringify([
                    ...records.filter(record => userIds.has(Number(record.userId)) && !profiles.some(profile => profile.userId === Number(record.userId))),
                    ...profiles
                ]));
            }
        }

        document.querySelectorAll('.admin-side button').forEach(button => button.addEventListener('click', () => {
            document.querySelectorAll('.admin-side button').forEach(item => item.classList.remove('active'));
            document.querySelectorAll('.admin-view').forEach(view => view.classList.remove('active'));
            button.classList.add('active');
            document.getElementById(button.dataset.view).classList.add('active');
            if (button.dataset.view === 'attendanceView') {
                loadAttendanceMonth().catch(error => showAlert('adminAlert', error.message, 'error'));
            }
            if (button.dataset.view === 'employeesView') {
                loadManagedUsers().then(renderAll).catch(error => showAlert('adminAlert', error.message, 'error'));
            }
        }));

        function getUserRecord(user) {
            const account = getAccountRecord(user.id);
            return account ? account.record : {};
        }

        function renderUsers() {
            const container = document.getElementById('usersList');
            const roles = ['Customer', 'Admin', 'IT', 'Dispatch', 'Dispatcher', 'Accounting', 'HR', 'Marketing', 'Sales', 'Customer Service', 'Technician'];
            const roleTitles = { Customer: 'Customers', Admin: 'Administrators', IT: 'IT', Dispatch: 'Dispatch', Dispatcher: 'Legacy Dispatchers', Accounting: 'Accounting', HR: 'HR', Marketing: 'Marketing', Sales: 'Sales', 'Customer Service': 'Customer Service', Technician: 'Technicians' };
            container.innerHTML = roles.map(role => `<section class="role-group"><h2>${roleTitles[role]}</h2><div class="record-list" data-role-list="${role}"></div></section>`).join('');
            const query = document.getElementById('accountSearch').value.trim().toLowerCase();
            const filteredUsers = getAllUsers().filter(user => {
                const record = getUserRecord(user);
                return [user.username, user.role, record.firstName, record.lastName, record.email]
                    .some(value => String(value || '').toLowerCase().includes(query));
            });
            container.querySelectorAll('.role-group').forEach(group => { group.hidden = true; });
            filteredUsers.forEach(user => {
                const group = container.querySelector(`[data-role-list="${user.role}"]`).closest('.role-group');
                group.hidden = false;
                const record = getUserRecord(user);
                const item = document.createElement('article');
                item.className = 'record';
                item.innerHTML = `<h3>${escapeHtml(user.username)}</h3><p><strong>Role:</strong> ${escapeHtml(user.role)}</p><p><strong>Name:</strong> ${escapeHtml(`${record.firstName || ''} ${record.lastName || ''}`)}</p><p><strong>Email:</strong> ${escapeHtml(record.email || 'Not provided')}</p><div class="record-actions"><button class="small-btn edit-btn" type="button">Edit</button><button class="small-btn delete-btn" type="button">Delete</button></div>`;
                item.querySelector('.edit-btn').onclick = () => editUser(user, record);
                item.querySelector('.delete-btn').onclick = async () => {
                    if (!confirm('Delete this user?')) return;
                    try {
                        await requestUserApi('users/delete', { method: 'POST', fields: { id: user.id } });
                        const result = deleteManagedUser(user.id);
                        if (!result.success) throw new Error(result.error);
                        renderAll();
                    } catch (error) {
                        showAlert('adminAlert', error.message, 'error');
                    }
                };
                container.querySelector(`[data-role-list="${user.role}"]`)?.appendChild(item);
            });
            if (!filteredUsers.length) {
                const empty = document.createElement('p');
                empty.className = 'empty-state';
                empty.textContent = 'No matching accounts.';
                container.appendChild(empty);
            }
        }

        function renderEmployees() {
            const container = document.getElementById('employeesList');
            const role = document.querySelector('#employeeRoleFilters button.active').dataset.role;
            const query = document.getElementById('employeeSearch').value.trim().toLowerCase();
            const employees = getAllUsers().filter(user => user.role !== 'Customer' && (role === 'All' || user.role === role));
            const filteredEmployees = employees.filter(user => {
                const record = getUserRecord(user);
                return [user.username, user.role, record.firstName, record.lastName, record.email]
                    .some(value => String(value || '').toLowerCase().includes(query));
            });
            container.innerHTML = filteredEmployees.length ? '' : '<p class="empty-state">No matching employee accounts.</p>';
            filteredEmployees.forEach(user => {
                const record = getUserRecord(user);
                const item = document.createElement('article');
                item.className = 'record';
                item.innerHTML = `<h3>${escapeHtml(`${record.firstName || user.username} ${record.lastName || ''}`)}</h3><p><strong>Username:</strong> ${escapeHtml(user.username)}</p><p><strong>Role:</strong> ${escapeHtml(user.role)}</p><p><strong>Email:</strong> ${escapeHtml(record.email || 'Not provided')}</p>`;
                container.appendChild(item);
            });
        }

        let attendanceData = { employees: [], attendance: [] };
        let selectedAttendanceDate = new Date().toISOString().slice(0, 10);
        async function loadAttendanceMonth() {
            const month = document.getElementById('attendanceMonth').value;
            const result = await requestUserApi(`attendance/month?month=${encodeURIComponent(month)}`);
            attendanceData = result;
            renderAttendanceCalendar();
            renderAttendanceDay();
        }

        function renderAttendanceCalendar() {
            const container = document.getElementById('attendanceList');
            const calendar = document.getElementById('attendanceCalendar');
            const [year, month] = document.getElementById('attendanceMonth').value.split('-').map(Number);
            const firstWeekday = new Date(year, month - 1, 1).getDay();
            const daysInMonth = new Date(year, month, 0).getDate();
            const attendedDates = new Set(attendanceData.attendance.map(record => String(record.timeIn).slice(0, 10)));
            calendar.innerHTML = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(day => `<div class="calendar-heading">${day}</div>`).join('');
            for (let blank = 0; blank < firstWeekday; blank++) calendar.appendChild(document.createElement('div'));
            for (let day = 1; day <= daysInMonth; day++) {
                const date = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const button = document.createElement('button');
                button.type = 'button';
                button.className = `calendar-day${attendedDates.has(date) ? ' has-attendance' : ''}${date === selectedAttendanceDate ? ' selected' : ''}`;
                button.textContent = String(day);
                button.setAttribute('aria-label', `${date}${attendedDates.has(date) ? ', attendance recorded' : ', no attendance recorded'}`);
                button.addEventListener('click', () => {
                    selectedAttendanceDate = date;
                    renderAttendanceCalendar();
                    renderAttendanceDay();
                });
                calendar.appendChild(button);
            }
        }

        function renderAttendanceDay() {
            const dayRecords = attendanceData.attendance.filter(record => String(record.timeIn).slice(0, 10) === selectedAttendanceDate);
            const attendedEmployeeIds = new Set(dayRecords.map(record => Number(record.employeeId)));
            document.getElementById('attendanceDayTitle').textContent = `Attendance for ${selectedAttendanceDate}`;
            const container = document.getElementById('attendanceList');
            if (!attendanceData.employees.length) {
                container.innerHTML = '<p class="empty-state">No employee accounts found.</p>';
                return;
            }
            container.innerHTML = `<table><thead><tr><th>Employee</th><th>Role</th><th>Status</th><th>Check-in time</th><th>Address</th></tr></thead><tbody>${attendanceData.employees.map(employee => {
                const record = dayRecords.find(item => Number(item.employeeId) === Number(employee.employeeId));
                const name = `${employee.firstName || ''} ${employee.lastName || ''}`.trim() || employee.username;
                return `<tr><td>${escapeHtml(name)}</td><td>${escapeHtml(employee.role)}</td><td>${attendedEmployeeIds.has(Number(employee.employeeId)) ? 'Attended' : 'No attendance'}</td><td>${record ? escapeHtml(new Date(record.timeIn).toLocaleTimeString()) : '—'}</td><td>${record ? escapeHtml(record.address) : '—'}</td></tr>`;
            }).join('')}</tbody></table>`;
        }

        function renderAll() { renderUsers(); renderEmployees(); }
        function editUser(user, record) {
            document.getElementById('userId').value = user.id;
            document.getElementById('userUsername').value = user.username;
            document.getElementById('userPassword').value = '';
            document.getElementById('userRole').value = user.role;
            document.getElementById('userFirstName').value = record.firstName || '';
            document.getElementById('userLastName').value = record.lastName || '';
            document.getElementById('userEmail').value = record.email || '';
            document.getElementById('userPhone').value = record.phone || '';
            document.getElementById('userAddress').value = record.address || '';
            document.getElementById('userFormTitle').textContent = 'Edit User';
            document.getElementById('passwordHint').textContent = '(leave blank to keep current)';
        }
        function resetUserForm() { document.getElementById('userForm').reset(); document.getElementById('userId').value = ''; document.getElementById('userFormTitle').textContent = 'Create User'; document.getElementById('passwordHint').textContent = '*'; }

        document.getElementById('userForm').addEventListener('submit', async event => {
            event.preventDefault();
            const id = document.getElementById('userId').value;
            const data = { username: document.getElementById('userUsername').value.trim(), password: document.getElementById('userPassword').value, role: document.getElementById('userRole').value, firstName: document.getElementById('userFirstName').value.trim(), lastName: document.getElementById('userLastName').value.trim(), email: document.getElementById('userEmail').value.trim(), phone: document.getElementById('userPhone').value.trim(), address: document.getElementById('userAddress').value.trim() };
            if (!id && !data.password) {
                showAlert('adminAlert', 'Password is required for a new user', 'error');
                return;
            }
            const duplicateEmail = [...getCustomers(), ...getEmployees()].find(account => account.email === data.email && account.userId !== Number(id));
            if (duplicateEmail) {
                showAlert('adminAlert', 'Email already registered', 'error');
                return;
            }

            try {
                const result = await requestUserApi('users/save', {
                    method: 'POST',
                    fields: { id, username: data.username, password: data.password, role: data.role, firstName: data.firstName, lastName: data.lastName, email: data.email, phone: data.phone, address: data.address }
                });
                await loadManagedUsers();
                resetUserForm();
                renderAll();
                showAlert('adminAlert', 'User saved', 'success');
            } catch (error) {
                showAlert('adminAlert', error.message, 'error');
            }
        });

        document.getElementById('employeeSearch').addEventListener('input', renderEmployees);
        document.getElementById('accountSearch').addEventListener('input', renderUsers);
        document.querySelectorAll('#employeeRoleFilters button').forEach(button => button.addEventListener('click', () => {
            document.querySelectorAll('#employeeRoleFilters button').forEach(item => item.classList.remove('active'));
            button.classList.add('active');
            renderEmployees();
        }));
        const today = new Date();
        const currentMonth = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('attendanceMonth').value = currentMonth;
        document.getElementById('attendanceMonth').addEventListener('change', () => {
            selectedAttendanceDate = `${document.getElementById('attendanceMonth').value}-01`;
            loadAttendanceMonth().catch(error => showAlert('adminAlert', error.message, 'error'));
        });
        document.getElementById('previousMonth').addEventListener('click', () => changeAttendanceMonth(-1));
        document.getElementById('nextMonth').addEventListener('click', () => changeAttendanceMonth(1));
        function changeAttendanceMonth(amount) {
            const input = document.getElementById('attendanceMonth');
            const [year, month] = input.value.split('-').map(Number);
            const date = new Date(year, month - 1 + amount, 1);
            input.value = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`;
            selectedAttendanceDate = `${input.value}-01`;
            loadAttendanceMonth().catch(error => showAlert('adminAlert', error.message, 'error'));
        }

        loadManagedUsers()
            .then(renderAll)
            .catch(error => showAlert('adminAlert', error.message, 'error'));
        loadAttendanceMonth().catch(error => showAlert('adminAlert', error.message, 'error'));
    </script>
</body>
</html>
