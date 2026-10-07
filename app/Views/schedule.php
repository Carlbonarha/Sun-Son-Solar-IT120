<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule a Service - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .schedule-layout {
            display: grid;
            grid-template-columns: minmax(320px, 1fr) minmax(320px, 1fr);
            gap: 24px;
            width: min(1100px, 100%);
        }

        .schedule-panel {
            background: white;
            padding: 28px;
            border-radius: 10px;
            border-top: 4px solid #ffd400;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .schedule-panel h1,
        .schedule-panel h2 {
            color: #1a73e8;
            margin-bottom: 10px;
        }

        .schedule-panel > p {
            color: #666;
            margin-bottom: 22px;
        }

        .booking-list {
            display: grid;
            gap: 12px;
        }

        .booking-item {
            border: 1px solid #e0e0e0;
            border-left: 4px solid #ffd400;
            border-radius: 6px;
            padding: 14px;
        }

        .booking-item h3 {
            color: #1a73e8;
            font-size: 16px;
            margin-bottom: 6px;
        }

        .booking-item p {
            color: #666;
            font-size: 14px;
            margin: 4px 0;
        }

        .status {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 8px;
            border-radius: 12px;
            background: #fff3cd;
            color: #856404;
            font-size: 12px;
            font-weight: bold;
        }

        @media (max-width: 760px) {
            .schedule-layout {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">Sun Son Solar</div>
        <nav class="header-nav" aria-label="Customer navigation">
            <a href="<?= site_url('customer-dashboard.php') ?>">Dashboard</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <a href="<?= site_url('profile.php') ?>">My Profile</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>

    <main class="container">
        <div class="schedule-layout">
            <section class="schedule-panel">
                <h1>Schedule a Service</h1>
                <p>Tell us what you need and when our team should visit.</p>
                <form id="scheduleForm">
                    <div id="scheduleAlert" class="alert" role="alert" aria-live="polite"></div>
                    <div class="form-group">
                        <label for="serviceType">Service Type *</label>
                        <select id="serviceType" required>
                            <option value="">Select a service</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="bookingDate">Preferred Date *</label>
                            <input id="bookingDate" type="date" required>
                        </div>
                        <div class="form-group">
                            <label for="bookingTime">Preferred Time *</label>
                            <input id="bookingTime" type="time" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="duration">Estimated Duration</label>
                        <select id="duration">
                            <option>1 hour</option>
                            <option>2 hours</option>
                            <option>Half day</option>
                            <option>Full day</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName">First Name *</label>
                            <input id="firstName" required>
                        </div>
                        <div class="form-group">
                            <label for="lastName">Last Name *</label>
                            <input id="lastName" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bookingEmail">Email *</label>
                        <input id="bookingEmail" type="email" required>
                    </div>
                    <div class="form-group">
                        <label for="bookingPhone">Phone *</label>
                        <input id="bookingPhone" type="tel" required>
                    </div>
                    <div class="form-group">
                        <label for="bookingAddress">Service Address *</label>
                        <textarea id="bookingAddress" required placeholder="Where should the service take place?"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="bookingNotes">Additional Notes</label>
                        <textarea id="bookingNotes" placeholder="Share access details, system concerns, or other helpful information."></textarea>
                    </div>
                    <button class="btn btn-primary" type="submit">Submit Booking</button>
                </form>
            </section>

            <section class="schedule-panel">
                <h2>My Bookings</h2>
                <p>Track the requests you have sent to Sun Son Solar.</p>
                <div class="booking-list" id="bookingList"></div>
            </section>
        </div>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        const scheduleUser = { id: <?= (int) session()->get('userId') ?>, username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>, role: 'Customer' };
        localStorage.setItem('currentUser', JSON.stringify(scheduleUser));

        function loadCustomerDetails() {
            const account = getAccountRecord(scheduleUser.id);
            if (!account) return;
            const record = account.record;
            document.getElementById('firstName').value = record.firstName || '';
            document.getElementById('lastName').value = record.lastName || '';
            document.getElementById('bookingEmail').value = record.email || '';
            document.getElementById('bookingPhone').value = record.phone || '';
            document.getElementById('bookingAddress').value = record.address || '';
        }

        async function loadAvailableServices() {
            const select = document.getElementById('serviceType');
            const response = await fetch('<?= site_url('catalog/services') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Could not load available services.');
            result.services.forEach(service => {
                const option = document.createElement('option');
                option.value = service.type;
                option.textContent = service.name;
                select.appendChild(option);
            });
            if (!result.services.length) select.disabled = true;
        }

        async function loadBookings() {
            const container = document.getElementById('bookingList');
            container.innerHTML = '';
            const response = await fetch('<?= site_url('bookings/list') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const result = await response.json();
            if (!response.ok || !result.success) throw new Error(result.error || 'Could not load bookings.');
            const bookings = result.bookings;
            if (!bookings.length) {
                container.innerHTML = '<p>No bookings yet.</p>';
                return;
            }
            bookings.slice().reverse().forEach(booking => {
                const item = document.createElement('article');
                item.className = 'booking-item';
                const title = document.createElement('h3');
                title.textContent = booking.serviceType;
                const details = document.createElement('p');
                details.textContent = `When: ${booking.date} at ${booking.time}`;
                const address = document.createElement('p');
                address.textContent = `Address: ${booking.address}`;
                const status = document.createElement('span');
                status.className = 'status';
                status.textContent = booking.status;
                item.append(title, details, address, status);
                container.appendChild(item);
            });
        }

        loadCustomerDetails();
        loadAvailableServices().catch(error => showAlert('scheduleAlert', error.message, 'error'));
        loadBookings().catch(error => showAlert('scheduleAlert', error.message, 'error'));
        document.getElementById('bookingDate').min = new Date().toISOString().split('T')[0];

        document.getElementById('scheduleForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const bookingData = {
                serviceType: document.getElementById('serviceType').value,
                date: document.getElementById('bookingDate').value,
                time: document.getElementById('bookingTime').value,
                duration: document.getElementById('duration').value,
                firstName: document.getElementById('firstName').value.trim(),
                lastName: document.getElementById('lastName').value.trim(),
                email: document.getElementById('bookingEmail').value.trim(),
                phone: document.getElementById('bookingPhone').value.trim(),
                address: document.getElementById('bookingAddress').value.trim(),
                notes: document.getElementById('bookingNotes').value.trim()
            };

            try {
                const response = await fetch('<?= site_url('bookings/create') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams({
                        firstName: bookingData.firstName,
                        lastName: bookingData.lastName,
                        email: bookingData.email,
                        phone: bookingData.phone,
                        address: bookingData.address,
                        serviceType: bookingData.serviceType,
                        date: bookingData.date,
                        time: bookingData.time,
                        duration: bookingData.duration,
                        notes: bookingData.notes
                    })
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not submit booking.');

                showAlert('scheduleAlert', 'Booking submitted successfully', 'success');
                document.getElementById('serviceType').value = '';
                document.getElementById('bookingDate').value = '';
                document.getElementById('bookingTime').value = '';
                document.getElementById('bookingNotes').value = '';
                await loadBookings();
            } catch (error) {
                showAlert('scheduleAlert', error.message, 'error');
            }
        });
    </script>
</body>
</html>
