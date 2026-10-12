<?php
$employeeRole = $employeeRole ?? 'Technician';
$dashboard = $dashboard ?? 'technician-dashboard.php';
$canCheckIn = $canCheckIn ?? true;
$isMaintenance = $employeeRole === 'Maintenance';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($employeeRole) ?> Dashboard - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .tech-layout { display: grid; grid-template-columns: minmax(280px, 380px) 1fr; gap: 20px; }
        .tech-card { background: #fff; padding: 22px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .tech-booking { border-left: 4px solid #1a73e8; margin: 12px 0; }
        .tech-booking-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: 12px; }
        .tech-booking-actions select { min-width: 150px; padding: 8px; border: 1px solid #ccd3da; border-radius: 5px; }
        @media (max-width: 760px) { .tech-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">Sun Son Solar · <?= esc($employeeRole) ?></div>
        <nav class="header-nav" aria-label="<?= esc($employeeRole) ?> navigation">
            <span>@<?= esc((string) session()->get('username')) ?></span>
            <a href="<?= site_url($dashboard) ?>">Dashboard</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <a href="<?= site_url('employee-timein.php') ?>">Time In</a>
            <a href="<?= site_url('profile.php') ?>">My Profile</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>

    <main class="container">
        <h1><?= esc($employeeRole) ?> Workspace</h1>
        <p><?= $isMaintenance ? 'Review and update maintenance jobs assigned to you.' : 'Review and update technical installation jobs assigned to you.' ?></p>
        <div class="tech-layout">
            <?php if ($canCheckIn): ?>
            <section class="tech-card">
                <h2>Record Attendance</h2>
                <form id="technicianAttendanceForm">
                    <div id="attendanceAlert" class="alert" role="alert" aria-live="polite"></div>
                    <div class="form-group">
                        <label for="attendanceSite">Site or customer name</label>
                        <input id="attendanceSite" required>
                    </div>
                    <div class="form-group">
                        <label for="attendanceAddress">Site address</label>
                        <textarea id="attendanceAddress" required></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Check In</button>
                </form>
            </section>
            <?php endif; ?>
            <section class="tech-card">
                <h2><?= esc($employeeRole) ?> Assignments</h2>
                <div id="technicianAlert" class="alert" role="alert" aria-live="polite"></div>
                <div id="technicianBookings" aria-live="polite">Loading bookings…</div>
            </section>
        </div>
        <section class="tech-card" style="margin-top:20px">
            <h2>Assigned quote requests</h2>
            <p>Review requests routed to your department and reply with progress or technical follow-up.</p>
            <div id="technicianQuotes" aria-live="polite">Loading assigned requests…</div>
        </section>
    </main>

    <script>
        localStorage.setItem('currentUser', JSON.stringify({
            id: <?= (int) session()->get('userId') ?>,
            username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            role: <?= json_encode($employeeRole, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
        }));
    </script>
    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        function appendConversation(card, type, id) {
            const details = document.createElement('details');
            const summary = document.createElement('summary');
            summary.textContent = 'View full details and conversation';
            const thread = document.createElement('div');
            thread.className = 'tech-thread';
            const form = document.createElement('form');
            const input = document.createElement('textarea');
            input.maxLength = 5000;
            input.required = true;
            input.placeholder = 'Write a message or progress update';
            const send = document.createElement('button');
            send.type = 'submit';
            send.className = 'btn btn-secondary';
            send.textContent = 'Send reply';
            form.append(input, send);
            details.append(summary, thread, form);
            card.appendChild(details);
            summary.addEventListener('click', async () => {
                if (details.dataset.loaded === '1') return;
                details.dataset.loaded = '1';
                try {
                    const response = await fetch(`<?= site_url('request-messages') ?>?type=${type}&id=${id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.error || 'Could not load conversation.');
                    thread.replaceChildren();
                    if (!result.messages.length) thread.textContent = 'No messages yet.';
                    result.messages.forEach(message => {
                        const entry = document.createElement('p');
                        entry.textContent = `${message.senderName} · ${message.createdAt}: ${message.message}`;
                        thread.appendChild(entry);
                    });
                } catch (error) { thread.textContent = error.message; }
            });
            form.addEventListener('submit', async event => {
                event.preventDefault();
                send.disabled = true;
                try {
                    const response = await fetch('<?= site_url('request-messages') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                        body: new URLSearchParams({ type, id: String(id), message: input.value })
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.error || 'Could not send reply.');
                    input.value = '';
                    details.dataset.loaded = '';
                    details.open = false;
                    summary.click();
                } catch (error) { showAlert('technicianAlert', error.message, 'error'); }
                finally { send.disabled = false; }
            });
        }

        fetch('<?= site_url('bookings/list') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async response => {
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not load bookings.');
                return result.bookings;
            })
            .then(bookings => {
                const container = document.getElementById('technicianBookings');
                if (!bookings.length) {
                    container.textContent = 'No assigned service jobs are scheduled.';
                    return;
                }
                bookings.forEach(booking => {
                    const card = document.createElement('article');
                    card.className = 'tech-card tech-booking';
                    const title = document.createElement('h3');
                    title.textContent = `${booking.serviceType} · ${booking.customerName}`;
                    const details = document.createElement('p');
                    details.textContent = `${booking.date} at ${booking.time} · ${booking.status}`;
                    details.dataset.bookingStatus = String(booking.id);
                    const address = document.createElement('p');
                    address.textContent = booking.address;
                    const contact = document.createElement('p');
                    contact.textContent = `${booking.email} · ${booking.phone}`;
                    const notes = document.createElement('p');
                    notes.textContent = booking.notes || 'No additional notes.';
                    const actions = document.createElement('div');
                    actions.className = 'tech-booking-actions';
                    const statusLabel = document.createElement('label');
                    statusLabel.textContent = 'Update status';
                    statusLabel.htmlFor = `booking-status-${booking.id}`;
                    const status = document.createElement('select');
                    status.id = `booking-status-${booking.id}`;
                    status.setAttribute('aria-label', `Status for ${booking.serviceType} booking`);
                    ['Pending', 'Confirmed', 'Completed', 'Cancelled'].forEach(value => {
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = value;
                        option.selected = value === booking.status;
                        status.appendChild(option);
                    });
                    const saveStatus = document.createElement('button');
                    saveStatus.type = 'button';
                    saveStatus.className = 'btn btn-primary';
                    saveStatus.textContent = 'Save status';
                    saveStatus.addEventListener('click', async () => {
                        saveStatus.disabled = true;
                        try {
                            const response = await fetch('<?= site_url('bookings/status') ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                                body: new URLSearchParams({ id: booking.id, status: status.value })
                            });
                            const result = await response.json();
                            if (!response.ok || !result.success) throw new Error(result.error || 'Could not update booking status.');
                            details.textContent = `${booking.date} at ${booking.time} · ${status.value}`;
                            showAlert('technicianAlert', 'Booking status updated.', 'success');
                        } catch (error) {
                            showAlert('technicianAlert', error.message, 'error');
                        } finally {
                            saveStatus.disabled = false;
                        }
                    });
                    actions.append(statusLabel, status, saveStatus);
                    card.append(title, details, address, contact, notes, actions);
                    appendConversation(card, 'booking', booking.id);
                    container.appendChild(card);
                });
            })
            .catch(error => {
                document.getElementById('technicianBookings').textContent = error.message;
            });

        fetch('<?= site_url('quote-requests/list') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async response => {
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not load assigned quote requests.');
                return result.requests;
            })
            .then(requests => {
                const container = document.getElementById('technicianQuotes');
                container.replaceChildren();
                if (!requests.length) { container.textContent = 'No quote requests are assigned to you.'; return; }
                requests.forEach(request => {
                    const card = document.createElement('article');
                    card.className = 'tech-card tech-booking';
                    const title = document.createElement('h3');
                    title.textContent = `${request.serviceNeeded} · ${request.fullName}`;
                    const contact = document.createElement('p');
                    contact.textContent = `${request.email} · ${request.phone}`;
                    const property = document.createElement('p');
                    property.textContent = `${request.propertyType} · ${request.propertyAddress}`;
                    const schedule = document.createElement('p');
                    schedule.textContent = `Preferred date: ${request.preferredDateOrSchedule} · Status: ${request.status}`;
                    const message = document.createElement('p');
                    message.textContent = request.additionalDetails || 'No additional message.';
                    card.append(title, contact, property, schedule, message);
                    if (request.hasAttachment) {
                        const attachment = document.createElement('a');
                        attachment.href = `<?= site_url('quote-requests/attachment') ?>/${request.id}`;
                        attachment.target = '_blank';
                        attachment.rel = 'noopener';
                        attachment.textContent = 'View attached photo or file';
                        card.appendChild(attachment);
                    }
                    const status = document.createElement('select');
                    ['Pending', 'In Progress', 'Quoted', 'Closed'].forEach(value => {
                        const option = document.createElement('option');
                        option.value = value;
                        option.textContent = value;
                        option.selected = value === request.status;
                        status.appendChild(option);
                    });
                    const update = document.createElement('button');
                    update.type = 'button';
                    update.className = 'btn btn-secondary';
                    update.textContent = 'Update request status';
                    update.addEventListener('click', async () => {
                        try {
                            const response = await fetch('<?= site_url('quote-requests/status') ?>', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                                body: new URLSearchParams({ id: request.id, status: status.value })
                            });
                            const result = await response.json();
                            if (!response.ok || !result.success) throw new Error(result.error || 'Could not update status.');
                            request.status = status.value;
                            schedule.textContent = `Preferred date: ${request.preferredDateOrSchedule} · Status: ${request.status}`;
                            showAlert('technicianAlert', 'Quote request status updated.', 'success');
                        } catch (error) { showAlert('technicianAlert', error.message, 'error'); }
                    });
                    card.append(status, update);
                    appendConversation(card, 'quote', request.id);
                    container.appendChild(card);
                });
            })
            .catch(error => { document.getElementById('technicianQuotes').textContent = error.message; });

        document.getElementById('technicianAttendanceForm').addEventListener('submit', async event => {
            event.preventDefault();
            const fields = {
                siteName: document.getElementById('attendanceSite').value.trim(),
                address: document.getElementById('attendanceAddress').value.trim()
            };
            try {
                const response = await fetch('<?= site_url('attendance/check-in') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams(fields)
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not record attendance.');
                showAlert('attendanceAlert', 'Attendance recorded successfully.', 'success');
                event.target.reset();
            } catch (error) {
                showAlert('attendanceAlert', error.message, 'error');
            }
        });
    </script>
</body>
</html>
