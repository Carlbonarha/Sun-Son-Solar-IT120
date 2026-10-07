<?php
$role = (string) $department['role'];
$dashboard = (string) $department['dashboard'];
$modules = $department['modules'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($role) ?> Workspace - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .dept-shell { display: block; width: min(1240px, calc(100% - 40px)); margin: 32px auto; padding: 0; }
        .dept-intro { margin: 8px 0 24px; color: #64748b; }
        .dept-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); grid-auto-rows: minmax(160px, auto); gap: 16px; align-items: stretch; }
        .dept-card { padding: 22px; min-width: 0; }
        .dept-card h2 { color: #123b6d; margin-bottom: 10px; font-size: 19px; }
        .dept-card p { color: #64748b; line-height: 1.55; }
        .dept-card.span-2 { grid-column: span 2; }
        .dept-card.span-4 { grid-column: span 4; }
        .dept-metric { color: #1a73e8; font-size: 30px; font-weight: 750; margin-top: 8px; }
        .dept-toolbar { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin: 12px 0; }
        .dept-toolbar input { padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .dept-search { width: 100%; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 12px; }
        .dept-list { display: grid; gap: 10px; margin-top: 14px; }
        .dept-row { padding: 14px; background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 7px; }
        .dept-row h3 { color: #1a73e8; font-size: 16px; margin-bottom: 6px; }
        .dept-row p { margin: 3px 0; }
        .dept-links { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
        .dept-links a { color: #1557b0; font-weight: 700; }
        .dept-status { color: #167242 !important; font-weight: 700; }
        @media (max-width: 760px) { .dept-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .dept-card.span-4 { grid-column: span 2; } }
        @media (max-width: 480px) { .dept-shell { width: calc(100% - 24px); margin: 22px auto; } .dept-grid { grid-template-columns: 1fr; } .dept-card.span-2, .dept-card.span-4 { grid-column: auto; } }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">Sun Son Solar · <?= esc($role) ?></div>
        <nav class="header-nav" aria-label="<?= esc($role) ?> navigation">
            <span>@<?= esc((string) session()->get('username')) ?></span>
            <a href="<?= site_url($dashboard) ?>">Dashboard</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <a href="<?= site_url('employee-timein.php') ?>">Time In</a>
            <a href="<?= site_url('profile.php') ?>">My Profile</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>
    <main class="container dept-shell">
        <h1><?= esc($role) ?> Workspace</h1>
        <p class="dept-intro"><?= esc($department['description']) ?></p>
        <div id="departmentAlert" class="alert" role="alert" aria-live="polite"></div>
        <section class="dept-grid">
            <?php foreach ($modules as $module): ?>
                <article class="card dept-card <?= esc($module['size']) ?>" id="<?= esc($module['id']) ?>">
                    <h2><?= esc($module['title']) ?></h2>
                    <p><?= esc($module['description']) ?></p>
                    <?php if (isset($module['metric'])): ?><div class="dept-metric" id="<?= esc($module['metric']) ?>">—</div><?php endif; ?>
                    <?php if ($module['id'] === 'catalogLinks'): ?>
                        <div class="dept-links"><a href="<?= site_url('services.php') ?>">Open services</a><a href="<?= site_url('products.php') ?>">Open products</a></div>
                    <?php elseif ($module['id'] === 'hrTools'): ?>
                        <div class="dept-toolbar"><label for="hrMonth">Attendance month</label><input id="hrMonth" type="month"></div>
                        <div class="dept-list" id="hrDirectory">Loading employee directory…</div>
                    <?php elseif ($module['id'] === 'bookingQueue'): ?>
                        <input class="dept-search" id="bookingSearch" type="search" placeholder="Search customer, service, date, status" aria-label="Search bookings">
                        <div class="dept-list" id="departmentBookings">Loading bookings…</div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
    <script>
        localStorage.setItem('currentUser', JSON.stringify({
            id: <?= (int) session()->get('userId') ?>,
            username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            role: <?= json_encode($role, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
        }));
    </script>
    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        async function getDepartmentJson(url) {
            const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Could not load department data.');
            return result;
        }
        function setMetric(id, value) {
            const target = document.getElementById(id);
            if (target) target.textContent = String(value);
        }
        function showDepartmentError(message) {
            showAlert('departmentAlert', message, 'error');
        }
        <?php if (in_array($role, ['IT', 'Accounting', 'Marketing', 'Sales', 'Customer Service'], true)): ?>
        getDepartmentJson('<?= site_url('department/summary') ?>').then(({ summary }) => {
            const statusCounts = summary.bookingStatuses || {};
            <?php if ($role === 'IT'): ?>
            setMetric('departmentPrimaryMetric', 'Connected');
            setMetric('departmentSecondaryMetric', summary.users);
            <?php elseif ($role === 'Accounting'): ?>
            setMetric('departmentPrimaryMetric', summary.bookings);
            setMetric('departmentSecondaryMetric', statusCounts.Completed || 0);
            <?php elseif ($role === 'Marketing'): ?>
            setMetric('departmentPrimaryMetric', summary.products + summary.services);
            setMetric('departmentSecondaryMetric', summary.products + ' products · ' + summary.services + ' services');
            <?php elseif ($role === 'Sales'): ?>
            setMetric('departmentPrimaryMetric', statusCounts.Pending || 0);
            setMetric('departmentSecondaryMetric', summary.bookings);
            <?php else: ?>
            setMetric('departmentPrimaryMetric', summary.bookings);
            setMetric('departmentSecondaryMetric', statusCounts.Pending || 0);
            <?php endif; ?>
        }).catch(error => showDepartmentError(error.message));
        <?php endif; ?>
        <?php if (in_array($role, ['Sales', 'Customer Service'], true)): ?>
        let bookings = [];
        function renderDepartmentBookings() {
            const list = document.getElementById('departmentBookings');
            const query = document.getElementById('bookingSearch').value.trim().toLowerCase();
            const filtered = bookings.filter(booking => [booking.customerName, booking.serviceType, booking.date, booking.status].some(value => String(value || '').toLowerCase().includes(query)));
            list.replaceChildren();
            if (!filtered.length) { list.textContent = 'No matching bookings.'; return; }
            filtered.forEach(booking => {
                const row = document.createElement('article');
                row.className = 'dept-row';
                const heading = document.createElement('h3');
                heading.textContent = `${booking.customerName} · ${booking.serviceType}`;
                const detail = document.createElement('p');
                detail.textContent = `${booking.date} ${booking.time} · ${booking.status}`;
                const contact = document.createElement('p');
                contact.textContent = `${booking.email} · ${booking.phone}`;
                row.append(heading, detail, contact);
                list.appendChild(row);
            });
        }
        getDepartmentJson('<?= site_url('bookings/list') ?>').then(result => {
            bookings = result.bookings;
            renderDepartmentBookings();
        }).catch(error => showDepartmentError(error.message));
        document.getElementById('bookingSearch').addEventListener('input', renderDepartmentBookings);
        <?php endif; ?>
        <?php if ($role === 'HR'): ?>
        async function loadHrData() {
            try {
                const month = document.getElementById('hrMonth').value;
                const [directory, attendance] = await Promise.all([
                    getDepartmentJson('<?= site_url('hr/employees') ?>'),
                    getDepartmentJson('<?= site_url('attendance/month') ?>?month=' + encodeURIComponent(month))
                ]);
                const list = document.getElementById('hrDirectory');
                list.replaceChildren();
                const attendedIds = new Set(attendance.attendance.map(record => Number(record.employeeId)));
                directory.employees.forEach(employee => {
                    const row = document.createElement('article');
                    row.className = 'dept-row';
                    const name = document.createElement('h3');
                    name.textContent = `${employee.firstName} ${employee.lastName}`.trim();
                    const detail = document.createElement('p');
                    detail.textContent = `${employee.role} · ${employee.username} · ${employee.email || 'No email'}`;
                    const record = attendance.employees.find(entry => Number(entry.userId) === Number(employee.id));
                    const status = document.createElement('p');
                    status.className = attendedIds.has(Number(record?.employeeId)) ? 'dept-status' : '';
                    status.textContent = record ? (attendedIds.has(Number(record.employeeId)) ? 'Attendance recorded this month' : 'No attendance recorded this month') : 'Employee profile not linked';
                    row.append(name, detail, status);
                    list.appendChild(row);
                });
            } catch (error) { showDepartmentError(error.message); }
        }
        const now = new Date();
        document.getElementById('hrMonth').value = `${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}`;
        document.getElementById('hrMonth').addEventListener('change', loadHrData);
        loadHrData();
        <?php endif; ?>
    </script>
</body>
</html>
