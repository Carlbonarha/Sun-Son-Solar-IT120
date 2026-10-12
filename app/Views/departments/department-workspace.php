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
        .employee-create-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 12px; margin-top: 14px; }
        .employee-create-form label { display: grid; gap: 5px; color: #334155; font-weight: 600; }
        .employee-create-form input, .employee-create-form select, .employee-create-form textarea { width: 100%; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; }
        .employee-create-form textarea { min-height: 72px; resize: vertical; }
        .employee-create-form button { justify-self: start; }
        .dept-search { width: 100%; padding: 9px 10px; border: 1px solid #cbd5e1; border-radius: 6px; margin-top: 12px; }
        .dept-list { display: grid; gap: 10px; margin-top: 14px; }
        .dept-row { padding: 14px; background: #f7fafc; border: 1px solid #e2e8f0; border-radius: 7px; }
        .dept-row h3 { color: #1a73e8; font-size: 16px; margin-bottom: 6px; }
        .dept-row p { margin: 3px 0; }
        .dept-links { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 12px; }
        .dept-links a { color: #1557b0; font-weight: 700; }
        .dept-status { color: #167242 !important; font-weight: 700; }
        .request-dialog { width: min(760px, calc(100% - 28px)); max-height: 85vh; padding: 24px; border: 0; border-radius: 12px; color: #213547; box-shadow: 0 16px 50px rgba(10, 36, 66, .3); }
        .request-dialog::backdrop { background: rgba(10, 36, 66, .58); }
        .request-dialog h2 { color: #123b6d; }
        .request-thread { display: grid; gap: 9px; max-height: 260px; overflow: auto; margin: 18px 0; }
        .request-message { padding: 12px; border-radius: 8px; background: #f3f7fb; white-space: pre-wrap; }
        .request-message p { margin: 5px 0 0; color: #213547; }
        .request-dialog textarea { width: 100%; min-height: 80px; margin: 8px 0; }
        @media (max-width: 760px) { .dept-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } .dept-card.span-4 { grid-column: span 2; } }
        @media (max-width: 480px) { .dept-shell { width: calc(100% - 24px); margin: 22px auto; } .dept-grid { grid-template-columns: 1fr; } .dept-card.span-2, .dept-card.span-4 { grid-column: auto; } .employee-create-form { grid-template-columns: 1fr; } }
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
        <dialog class="request-dialog" id="requestDialog">
            <button class="btn btn-secondary" id="closeRequestDialog" type="button" style="float:right">Close</button>
            <h2 id="requestDialogTitle">Request details</h2>
            <div id="requestDialogDetails"></div>
            <h3>Conversation</h3>
            <div class="request-thread" id="requestThread" aria-live="polite"></div>
            <form id="requestReplyForm">
                <label for="requestReply">Reply or handoff note</label>
                <textarea id="requestReply" maxlength="5000" required></textarea>
                <button class="btn btn-primary" type="submit">Send message</button>
            </form>
        </dialog>
        <section class="dept-grid">
            <?php foreach ($modules as $module): ?>
                <article class="card dept-card <?= esc($module['size']) ?>" id="<?= esc($module['id']) ?>">
                    <h2><?= esc($module['title']) ?></h2>
                    <p><?= esc($module['description']) ?></p>
                    <?php if (isset($module['metric'])): ?><div class="dept-metric" id="<?= esc($module['metric']) ?>">—</div><?php endif; ?>
                    <?php if ($module['id'] === 'catalogLinks'): ?>
                        <div class="dept-links"><a href="<?= site_url('dispatcher-dashboard.php') ?>">Manage services and products</a><a href="<?= site_url('services.php') ?>">Preview services</a><a href="<?= site_url('products.php') ?>">Preview products</a></div>
                    <?php elseif ($module['id'] === 'customerReviews'): ?>
                        <div class="dept-list" id="marketingReviews">Loading customer stories…</div>
                    <?php elseif ($module['id'] === 'hrTools'): ?>
                        <div class="dept-toolbar"><label for="hrMonth">Attendance month</label><input id="hrMonth" type="month"></div>
                        <div class="dept-list" id="hrDirectory">Loading employee directory…</div>
                    <?php elseif ($module['id'] === 'hrCreateEmployee'): ?>
                    <form id="hrEmployeeForm" class="employee-create-form">
                        <input id="hrUserId" name="id" type="hidden">
                        <label>Username *<input name="username" type="text" required minlength="3" autocomplete="off"></label>
                        <label>Password *<input id="hrEmployeePassword" name="password" type="password" required minlength="6" autocomplete="new-password"></label>
                        <label>Department *
                            <select name="role" required>
                                <option value="">Select department</option>
                                <option value="IT">IT</option>
                                <option value="Dispatch">Operations / Service Coordinator</option>
                                <option value="Dispatcher">Dispatcher (legacy)</option>
                                <option value="Accounting">Accounting</option>
                                <option value="HR">HR</option>
                                <option value="Marketing">Marketing</option>
                                <option value="Sales">Sales</option>
                                <option value="Customer Service">Customer Service</option>
                                <option value="Technician">Technician</option>
                                <option value="Maintenance">Maintenance</option>
                            </select>
                        </label>
                        <label>First name *<input name="firstName" type="text" required></label>
                        <label>Last name *<input name="lastName" type="text" required></label>
                        <label>Email *<input name="email" type="email" required></label>
                        <label>Phone<input name="phone" type="tel"></label>
                        <label>Address<textarea name="address"></textarea></label>
                        <button class="btn btn-primary" type="submit">Create employee account</button>
                        <button class="btn btn-secondary" id="hrEmployeeCancel" type="button" hidden>Cancel edit</button>
                    </form>
                    <?php elseif ($module['id'] === 'bookingQueue'): ?>
                        <input class="dept-search" id="bookingSearch" type="search" placeholder="Search customer, service, date, status" aria-label="Search bookings">
                        <div class="dept-list" id="departmentBookings">Loading bookings…</div>
                    <?php elseif ($module['id'] === 'quoteQueue'): ?>
                        <input class="dept-search" id="quoteSearch" type="search" placeholder="Search name, service, date, status" aria-label="Search quote requests">
                        <div class="dept-list" id="departmentQuotes">Loading quote requests…</div>
                    <?php elseif ($module['id'] === 'itUsers'): ?>
                        <div class="dept-list" id="systemUsers">Loading system users…</div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    </main>
    <script>
        let activeRequestConversation = null;
        document.getElementById('closeRequestDialog').addEventListener('click', () => document.getElementById('requestDialog').close());
        async function openRequestConversation(type, request) {
            activeRequestConversation = { type, id: request.id };
            const dialog = document.getElementById('requestDialog');
            const title = document.getElementById('requestDialogTitle');
            const details = document.getElementById('requestDialogDetails');
            const thread = document.getElementById('requestThread');
            title.textContent = `${type === 'quote' ? 'Quote request' : 'Booking'} · ${request.fullName || request.customerName}`;
            details.replaceChildren();
            const values = type === 'quote'
                ? [
                    ['Contact', `${request.email} · ${request.phone}`],
                    ['Service / department', request.serviceNeeded],
                    ['Property type', request.propertyType],
                    ['Address', request.propertyAddress],
                    ['Electricity bill', request.averageMonthlyElectricityBill],
                    ['Preferred contact', request.preferredContactMethod],
                    ['Preferred schedule', request.preferredDateOrSchedule],
                    ['Status', request.status],
                    ['Assigned to', request.assignedEmployeeName || request.assignedDepartment || 'Unassigned'],
                    ['Message', request.additionalDetails || 'No message provided']
                ]
                : [
                    ['Contact', `${request.email} · ${request.phone}`],
                    ['Service', request.serviceType],
                    ['Schedule', `${request.date} ${request.time} · ${request.duration}`],
                    ['Address', request.address],
                    ['Status', request.status],
                    ['Assigned employee', request.assignedEmployeeName || 'Unassigned'],
                    ['Message', request.notes || 'No notes provided']
                ];
            values.forEach(([label, value]) => {
                const line = document.createElement('p');
                const strong = document.createElement('strong');
                strong.textContent = `${label}: `;
                line.append(strong, document.createTextNode(String(value || '')));
                details.appendChild(line);
            });
            if (type === 'quote' && request.hasAttachment) {
                const attachment = document.createElement('a');
                attachment.href = `<?= site_url('quote-requests/attachment') ?>/${request.id}`;
                attachment.target = '_blank';
                attachment.rel = 'noopener';
                attachment.textContent = 'View attached photo or file';
                details.appendChild(attachment);
            }
            thread.textContent = 'Loading conversation…';
            dialog.showModal();
            await loadRequestMessages();
        }
        async function loadRequestMessages() {
            if (!activeRequestConversation) return;
            const { type, id } = activeRequestConversation;
            const response = await fetch(`<?= site_url('request-messages') ?>?type=${encodeURIComponent(type)}&id=${encodeURIComponent(id)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Could not load conversation.');
            const thread = document.getElementById('requestThread');
            thread.replaceChildren();
            if (!result.messages.length) { thread.textContent = 'No messages yet.'; return; }
            result.messages.forEach(message => {
                const item = document.createElement('article');
                item.className = 'request-message';
                const heading = document.createElement('strong');
                heading.textContent = `${message.senderName} · ${message.createdAt}`;
                const body = document.createElement('p');
                body.textContent = message.message;
                item.append(heading, body);
                thread.appendChild(item);
            });
        }
        document.getElementById('requestReplyForm').addEventListener('submit', async event => {
            event.preventDefault();
            if (!activeRequestConversation) return;
            const message = document.getElementById('requestReply');
            try {
                const response = await fetch('<?= site_url('request-messages') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams({ ...activeRequestConversation, message: message.value })
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not send message.');
                message.value = '';
                await loadRequestMessages();
            } catch (error) { showAlert('departmentAlert', error.message, 'error'); }
        });

        localStorage.setItem('currentUser', JSON.stringify({
            id: <?= (int) session()->get('userId') ?>,
            username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            role: <?= json_encode($role, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
        }));
    </script>
    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <?php if ($role === 'Marketing'): ?>
    <script>
        async function loadMarketingReviews() {
            const response = await fetch('<?= site_url('customer-reviews/manage') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Could not load customer stories.');
            const list = document.getElementById('marketingReviews');
            list.replaceChildren();
            if (!result.reviews.length) { list.textContent = 'No customer stories have been submitted.'; return; }
            result.reviews.forEach(review => {
                const row = document.createElement('article');
                row.className = 'dept-row';
                const heading = document.createElement('h3');
                heading.textContent = `${review.customerName} · ${review.serviceType} · ${review.status}`;
                const body = document.createElement('p');
                body.textContent = review.story;
                row.append(heading, body);
                ['Published', 'Declined'].forEach(nextStatus => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-secondary';
                    button.textContent = nextStatus === 'Published' ? (review.status === 'Published' ? 'Unpublish' : 'Publish review to the website') : 'Decline';
                    button.addEventListener('click', async () => {
                        button.disabled = true;
                        const status = nextStatus === 'Published' && review.status === 'Published' ? 'Pending' : nextStatus;
                        try {
                            const response = await fetch('<?= site_url('customer-reviews/status') ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                                body: new URLSearchParams({ id: review.id, status })
                            });
                            const result = await response.json();
                            if (!response.ok || !result.success) throw new Error(result.error || 'Could not update story publication.');
                            review.status = status;
                            await loadMarketingReviews();
                        } catch (error) { showAlert('departmentAlert', error.message, 'error'); button.disabled = false; }
                    });
                    row.appendChild(button);
                });
                list.appendChild(row);
            });
        }
        loadMarketingReviews().catch(error => showAlert('departmentAlert', error.message, 'error'));
    </script>
    <?php endif; ?>
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
        <?php if (in_array($role, ['IT', 'Marketing', 'Sales', 'Customer Service'], true)): ?>
        getDepartmentJson('<?= site_url('department/summary') ?>').then(({ summary }) => {
            const statusCounts = summary.bookingStatuses || {};
            <?php if ($role === 'IT'): ?>
            setMetric('departmentPrimaryMetric', 'Connected');
            setMetric('departmentSecondaryMetric', summary.users);
            <?php elseif ($role === 'Marketing'): ?>
            setMetric('departmentPrimaryMetric', summary.products + summary.services);
            setMetric('departmentSecondaryMetric', summary.products + ' products · ' + summary.services + ' services');
            <?php elseif ($role === 'Sales'): ?>
            setMetric('departmentPrimaryMetric', (summary.quoteStatuses || {}).Pending || 0);
            setMetric('departmentSecondaryMetric', summary.quoteRequests || 0);
            <?php else: ?>
            setMetric('departmentPrimaryMetric', summary.bookings);
            setMetric('departmentSecondaryMetric', statusCounts.Pending || 0);
            <?php endif; ?>
        }).catch(error => showDepartmentError(error.message));
        <?php endif; ?>
        <?php if ($role === 'HR'): ?>
        document.getElementById('hrEmployeeForm').addEventListener('submit', async event => {
            event.preventDefault();
            const form = event.currentTarget;
            const values = Object.fromEntries(new FormData(form).entries());
            const submit = form.querySelector('button[type="submit"]');
            const editing = values.id !== '';
            submit.disabled = true;
            try {
                const response = await fetch('<?= site_url('users/save') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(values)
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not save employee account.');
                resetHrEmployeeForm();
                showAlert('departmentAlert', editing ? 'Employee record updated.' : 'Employee account created and ready to log in.', 'success');
                await loadHrData();
            } catch (error) {
                showDepartmentError(error.message);
            } finally {
                submit.disabled = false;
            }
        });
        function resetHrEmployeeForm() {
            const form = document.getElementById('hrEmployeeForm');
            form.reset();
            document.getElementById('hrUserId').value = '';
            document.getElementById('hrEmployeePassword').required = true;
            form.querySelector('button[type="submit"]').textContent = 'Create employee account';
            document.getElementById('hrEmployeeCancel').hidden = true;
        }
        document.getElementById('hrEmployeeCancel').addEventListener('click', resetHrEmployeeForm);
        <?php endif; ?>
        <?php if ($role === 'IT'): ?>
        getDepartmentJson('<?= site_url('system/users') ?>').then(({ users }) => {
            const list = document.getElementById('systemUsers');
            list.replaceChildren();
            if (!users.length) { list.textContent = 'No user accounts found.'; return; }
            users.forEach(user => {
                const row = document.createElement('article');
                row.className = 'dept-row';
                const details = document.createElement('p');
                details.textContent = `${user.username} · ${user.role} · ${user.approvalStatus}${Number(user.isActive) === 1 ? ' · Active' : ' · Inactive'}`;
                row.appendChild(details);
                if (user.approvalStatus === 'Approved' && user.role !== 'Admin') {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = 'btn btn-secondary';
                    const nextActive = Number(user.isActive) !== 1;
                    button.textContent = nextActive ? 'Activate account' : 'Deactivate account';
                    button.addEventListener('click', async () => {
                        button.disabled = true;
                        try {
                            const response = await fetch('<?= site_url('system/users/status') ?>', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                    'X-Requested-With': 'XMLHttpRequest'
                                },
                                body: new URLSearchParams({ id: user.id, isActive: nextActive ? '1' : '0' })
                            });
                            const result = await response.json();
                            if (!response.ok || !result.success) throw new Error(result.error || 'Could not update account access.');
                            user.isActive = nextActive ? 1 : 0;
                            details.textContent = `${user.username} · ${user.role} · ${user.approvalStatus}${nextActive ? ' · Active' : ' · Inactive'}`;
                            button.textContent = nextActive ? 'Deactivate account' : 'Activate account';
                            showAlert('departmentAlert', 'Account access updated.', 'success');
                        } catch (error) {
                            showDepartmentError(error.message);
                        } finally {
                            button.disabled = false;
                        }
                    });
                    row.appendChild(button);
                }
                list.appendChild(row);
            });
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
                const view = document.createElement('button');
                view.type = 'button';
                view.className = 'btn btn-secondary';
                view.textContent = 'View details and reply';
                view.addEventListener('click', () => openRequestConversation('booking', booking).catch(error => showDepartmentError(error.message)));
                row.appendChild(view);
                <?php if ($role === 'Customer Service'): ?>
                const status = document.createElement('select');
                status.className = 'dept-search';
                status.setAttribute('aria-label', `Status for ${booking.customerName}'s request`);
                ['Pending', 'Confirmed', 'Completed', 'Cancelled'].forEach(value => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = value;
                    option.selected = value === booking.status;
                    status.appendChild(option);
                });
                status.addEventListener('change', async () => {
                    try {
                        const response = await fetch('<?= site_url('bookings/status') ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new URLSearchParams({ id: booking.id, status: status.value })
                        });
                        const result = await response.json();
                        if (!response.ok || !result.success) throw new Error(result.error || 'Could not update request status.');
                        booking.status = status.value;
                        detail.textContent = `${booking.date} ${booking.time} · ${booking.status}`;
                        showAlert('departmentAlert', 'Customer request status updated.', 'success');
                    } catch (error) {
                        showDepartmentError(error.message);
                    }
                });
                row.appendChild(status);
                <?php endif; ?>
                list.appendChild(row);
            });
        }
        getDepartmentJson('<?= site_url('bookings/list') ?>').then(result => {
            bookings = result.bookings;
            renderDepartmentBookings();
        }).catch(error => showDepartmentError(error.message));
        document.getElementById('bookingSearch').addEventListener('input', renderDepartmentBookings);
        <?php endif; ?>
        <?php if (in_array($role, ['Sales', 'Customer Service', 'Technician', 'Maintenance'], true)): ?>
        let quoteRequests = [];
        function renderDepartmentQuotes() {
            const list = document.getElementById('departmentQuotes');
            const query = document.getElementById('quoteSearch').value.trim().toLowerCase();
            const filtered = quoteRequests.filter(request => [request.fullName, request.serviceNeeded, request.preferredDateOrSchedule, request.status].some(value => String(value || '').toLowerCase().includes(query)));
            list.replaceChildren();
            if (!filtered.length) { list.textContent = 'No matching free quote requests.'; return; }
            filtered.forEach(request => {
                const row = document.createElement('article');
                row.className = 'dept-row';
                const heading = document.createElement('h3');
                heading.textContent = `${request.fullName} · ${request.serviceNeeded}`;
                const contact = document.createElement('p');
                contact.textContent = `${request.email} · ${request.phone}`;
                const details = document.createElement('p');
                details.textContent = `${request.propertyType} · ${request.preferredDateOrSchedule} · ${request.status}`;
                const address = document.createElement('p');
                address.textContent = request.propertyAddress;
                row.append(heading, contact, details, address);
                const view = document.createElement('button');
                view.type = 'button';
                view.className = 'btn btn-secondary';
                view.textContent = 'View full request and reply';
                view.addEventListener('click', () => openRequestConversation('quote', request).catch(error => showDepartmentError(error.message)));
                row.appendChild(view);
                <?php if (in_array($role, ['Sales', 'Customer Service', 'Technician', 'Maintenance'], true)): ?>
                const status = document.createElement('select');
                status.className = 'dept-search';
                status.setAttribute('aria-label', `Status for quote request from ${request.fullName}`);
                <?= in_array($role, ['Technician', 'Maintenance'], true) ? "['Pending', 'Contacted', 'In Progress', 'Quoted', 'Closed']" : "['Pending', 'Contacted', 'In Progress', 'Quoted', 'Closed', 'Declined']" ?>.forEach(value => {
                    const option = document.createElement('option');
                    option.value = value;
                    option.textContent = value;
                    option.selected = value === request.status;
                    status.appendChild(option);
                });
                status.addEventListener('change', async () => {
                    try {
                        const response = await fetch('<?= site_url('quote-requests/status') ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new URLSearchParams({ id: request.id, status: status.value })
                        });
                        const result = await response.json();
                        if (!response.ok || !result.success) throw new Error(result.error || 'Could not update quote status.');
                        request.status = status.value;
                        details.textContent = `${request.propertyType} · ${request.preferredDateOrSchedule} · ${request.status}`;
                        showAlert('departmentAlert', 'Quote request status updated.', 'success');
                    } catch (error) {
                        showDepartmentError(error.message);
                    }
                });
                row.appendChild(status);
                <?php endif; ?>
                <?php if ($role === 'Sales'): ?>
                const route = document.createElement('select');
                route.className = 'dept-search';
                route.setAttribute('aria-label', `Assign quote request from ${request.fullName}`);
                const initial = document.createElement('option');
                initial.value = '';
                initial.textContent = 'Route to a technical team member…';
                route.appendChild(initial);
                if (!window.quoteAssignableEmployees) window.quoteAssignableEmployees = [];
                window.quoteAssignableEmployees.forEach(employee => {
                    if (!['Designing', 'Installation', 'Repair', 'Monitoring', 'Maintenance'].includes(request.serviceNeeded)
                        || (request.serviceNeeded === 'Maintenance') !== (employee.role === 'Maintenance')) return;
                    const option = document.createElement('option');
                    option.value = String(employee.employeeId);
                    option.textContent = `${employee.firstName} ${employee.lastName} · ${employee.role}`;
                    option.selected = Number(request.assignedEmployeeId) === Number(employee.employeeId);
                    route.appendChild(option);
                });
                route.addEventListener('change', async () => {
                    try {
                        const response = await fetch('<?= site_url('quote-requests/assign') ?>', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                            body: new URLSearchParams({ id: request.id, employeeId: route.value })
                        });
                        const result = await response.json();
                        if (!response.ok || !result.success) throw new Error(result.error || 'Could not route quote request.');
                        request.assignedEmployeeId = result.assignedEmployeeId;
                        request.assignedDepartment = result.assignedDepartment;
                        const selectedEmployee = window.quoteAssignableEmployees.find(employee => Number(employee.employeeId) === Number(result.assignedEmployeeId));
                        request.assignedEmployeeName = selectedEmployee ? `${selectedEmployee.firstName} ${selectedEmployee.lastName}` : '';
                        showAlert('departmentAlert', 'Quote request routed to the selected team member.', 'success');
                    } catch (error) { showDepartmentError(error.message); }
                });
                row.appendChild(route);
                <?php endif; ?>
                list.appendChild(row);
            });
        }
        Promise.all([
            getDepartmentJson('<?= site_url('quote-requests/list') ?>'),
            <?= $role === 'Sales' ? "getDepartmentJson('" . site_url('quote-requests/assignable-employees') . "')" : "Promise.resolve({ employees: [] })" ?>
        ]).then(([result, assignable]) => {
            quoteRequests = result.requests;
            window.quoteAssignableEmployees = assignable.employees;
            renderDepartmentQuotes();
        }).catch(error => showDepartmentError(error.message));
        document.getElementById('quoteSearch').addEventListener('input', renderDepartmentQuotes);
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
                    const approval = document.createElement('p');
                    const isPending = employee.approvalStatus === 'Pending';
                    const isApproved = employee.approvalStatus === 'Approved';
                    approval.className = isApproved && Number(employee.isActive) === 1 ? 'dept-status' : '';
                    approval.textContent = isPending
                        ? 'Pending approval'
                        : isApproved && Number(employee.isActive) === 1
                            ? 'Approved'
                            : isApproved
                                ? 'Deactivated'
                                : 'Declined';
                    const record = attendance.employees.find(entry => Number(entry.userId) === Number(employee.id));
                    const status = document.createElement('p');
                    status.className = attendedIds.has(Number(record?.employeeId)) ? 'dept-status' : '';
                    status.textContent = isPending
                        ? 'Attendance available after approval'
                        : !isApproved || Number(employee.isActive) !== 1
                            ? 'Attendance unavailable while account access is disabled'
                        : record
                            ? (attendedIds.has(Number(record.employeeId)) ? 'Attendance recorded this month' : 'No attendance recorded this month')
                            : 'Employee profile not linked';
                    row.append(name, detail, approval, status);
                    const edit = document.createElement('button');
                    edit.type = 'button';
                    edit.className = 'btn btn-secondary';
                    edit.textContent = 'Edit employee';
                    edit.addEventListener('click', () => {
                        const form = document.getElementById('hrEmployeeForm');
                        document.getElementById('hrUserId').value = employee.id;
                        form.querySelector('[name="username"]').value = employee.username;
                        form.querySelector('[name="role"]').value = employee.role;
                        form.querySelector('[name="firstName"]').value = employee.firstName;
                        form.querySelector('[name="lastName"]').value = employee.lastName;
                        form.querySelector('[name="email"]').value = employee.email;
                        form.querySelector('[name="phone"]').value = employee.phone;
                        form.querySelector('[name="address"]').value = employee.address;
                        document.getElementById('hrEmployeePassword').required = false;
                        form.querySelector('button[type="submit"]').textContent = 'Save employee changes';
                        document.getElementById('hrEmployeeCancel').hidden = false;
                        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    });
                    row.appendChild(edit);
                    if (isPending) {
                        const approve = document.createElement('button');
                        approve.type = 'button';
                        approve.className = 'btn btn-primary';
                        approve.textContent = 'Approve employee';
                        approve.addEventListener('click', async () => {
                            try {
                                const response = await fetch('<?= site_url('employees/approve') ?>', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: new URLSearchParams({ id: employee.id })
                                });
                                const result = await response.json();
                                if (!response.ok || !result.success) throw new Error(result.error || 'Could not approve employee.');
                                await loadHrData();
                                showAlert('departmentAlert', 'Employee registration approved.', 'success');
                            } catch (error) {
                                showDepartmentError(error.message);
                            }
                        });
                        const decline = document.createElement('button');
                        decline.type = 'button';
                        decline.className = 'btn btn-secondary';
                        decline.textContent = 'Decline registration';
                        decline.addEventListener('click', async () => {
                            if (!confirm('Decline this employee registration? The account will remain disabled and marked declined.')) return;
                            try {
                                const response = await fetch('<?= site_url('employees/decline') ?>', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: new URLSearchParams({ id: employee.id })
                                });
                                const result = await response.json();
                                if (!response.ok || !result.success) throw new Error(result.error || 'Could not decline employee.');
                                await loadHrData();
                                showAlert('departmentAlert', 'Employee registration declined.', 'success');
                            } catch (error) {
                                showDepartmentError(error.message);
                            }
                        });
                        row.append(approve, decline);
                    } else if (isApproved) {
                        const setActive = document.createElement('button');
                        setActive.type = 'button';
                        setActive.className = 'btn btn-secondary';
                        const nextActive = Number(employee.isActive) === 1 ? 0 : 1;
                        setActive.textContent = nextActive ? 'Activate account' : 'Deactivate account';
                        setActive.addEventListener('click', async () => {
                            try {
                                const response = await fetch('<?= site_url('employees/status') ?>', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: new URLSearchParams({ id: employee.id, isActive: String(nextActive) })
                                });
                                const result = await response.json();
                                if (!response.ok || !result.success) throw new Error(result.error || 'Could not update employee access.');
                                await loadHrData();
                                showAlert('departmentAlert', nextActive ? 'Employee account activated.' : 'Employee account deactivated.', 'success');
                            } catch (error) {
                                showDepartmentError(error.message);
                            }
                        });
                        row.appendChild(setActive);
                    }
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
