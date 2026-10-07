<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technician Dashboard - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .tech-layout { display: grid; grid-template-columns: minmax(280px, 380px) 1fr; gap: 20px; }
        .tech-card { background: #fff; padding: 22px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,.08); }
        .tech-booking { border-left: 4px solid #ffd400; margin: 12px 0; }
        .tech-booking-actions { display: flex; flex-wrap: wrap; align-items: center; gap: 10px; margin-top: 12px; }
        .tech-booking-actions select { min-width: 150px; padding: 8px; border: 1px solid #ccd3da; border-radius: 5px; }
        @media (max-width: 760px) { .tech-layout { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">Sun Son Solar · Technician</div>
        <nav class="header-nav" aria-label="Technician navigation">
            <span>@<?= esc((string) session()->get('username')) ?></span>
            <a href="<?= site_url('technician-dashboard.php') ?>">Dashboard</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <a href="<?= site_url('employee-timein.php') ?>">Time In</a>
            <a href="<?= site_url('profile.php') ?>">My Profile</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>

    <main class="container">
        <h1>Technician Workspace</h1>
        <p>Review customer bookings for installation, maintenance, repair, and monitoring work.</p>
        <div class="tech-layout">
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
            <section class="tech-card">
                <h2>Technician-Related Bookings</h2>
                <div id="technicianAlert" class="alert" role="alert" aria-live="polite"></div>
                <div id="technicianBookings" aria-live="polite">Loading bookings…</div>
            </section>
        </div>
    </main>

    <script>
        localStorage.setItem('currentUser', JSON.stringify({
            id: <?= (int) session()->get('userId') ?>,
            username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            role: 'Technician'
        }));
    </script>
    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        fetch('<?= site_url('bookings/list') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async response => {
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not load bookings.');
                return result.bookings;
            })
            .then(bookings => {
                const container = document.getElementById('technicianBookings');
                if (!bookings.length) {
                    container.textContent = 'No technician-related bookings are scheduled.';
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
                    card.append(title, details, address, actions);
                    container.appendChild(card);
                });
            })
            .catch(error => {
                document.getElementById('technicianBookings').textContent = error.message;
            });

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
