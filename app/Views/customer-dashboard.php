<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .customer-dashboard { display: block; width: min(1120px, calc(100% - 40px)); margin: 36px auto; padding: 0; }
        .customer-dashboard > h1 { color: #123b6d; margin-bottom: 8px; }
        .customer-dashboard > p { color: #667085; margin-bottom: 24px; }
        .customer-dashboard .quick-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px; width: 100%; }
        .customer-dashboard .quick-stats .card { color: inherit; text-decoration: none; min-height: 132px; }
        .customer-dashboard .quick-stats .card h2 { color: #1a73e8; margin-bottom: 10px; }
        .customer-dashboard .booking-panel { margin-top: 24px; padding: 24px; width: 100%; }
        .customer-dashboard .booking-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 14px; margin-top: 16px; }
        .customer-dashboard .booking-item { min-width: 0; }
        @media (max-width: 520px) { .customer-dashboard { width: calc(100% - 24px); margin: 22px auto; } .customer-dashboard .booking-panel { padding: 18px; } }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">Sun Son Solar</div>
        <nav class="header-nav" aria-label="Customer navigation">
            <span>@<?= esc((string) session()->get('username')) ?></span>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <a href="<?= site_url('schedule.php') ?>">Schedule service</a>
            <a href="<?= site_url('customer-dashboard.php') ?>">Dashboard</a>
            <a href="<?= site_url('profile.php') ?>">My Profile</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>

    <main class="container customer-dashboard">
        <h1>Welcome, <?= esc((string) session()->get('username')) ?></h1>
        <p>Browse solar services and products, or review your bookings.</p>
        <div class="quick-stats">
            <a class="card" href="<?= site_url('services.php') ?>"><h2>Services</h2><p>Explore available solar services.</p></a>
            <a class="card" href="<?= site_url('products.php') ?>"><h2>Products</h2><p>Browse solar products available to everyone.</p></a>
            <a class="card" href="<?= site_url('schedule.php') ?>"><h2>Book a Service</h2><p>Request a solar service appointment.</p></a>
        </div>
        <section class="card booking-panel">
            <h2>My Bookings</h2>
            <div class="booking-list" id="customerBookings" aria-live="polite">Loading bookings…</div>
        </section>
    </main>

    <script>
        localStorage.setItem('currentUser', JSON.stringify({
            id: <?= (int) session()->get('userId') ?>,
            username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            role: 'Customer'
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
                const container = document.getElementById('customerBookings');
                if (!bookings.length) {
                    container.textContent = 'You have no bookings yet.';
                    return;
                }
                bookings.forEach(booking => {
                    const item = document.createElement('article');
                    item.className = 'card';
                    const title = document.createElement('h3');
                    title.textContent = booking.serviceType;
                    const details = document.createElement('p');
                    details.textContent = `${booking.date} at ${booking.time} · ${booking.status}`;
                    const address = document.createElement('p');
                    address.textContent = booking.address;
                    address.className = 'muted';
                    item.classList.add('booking-item');
                    item.append(title, details, address);
                    container.appendChild(item);
                });
            })
            .catch(error => {
                document.getElementById('customerBookings').textContent = error.message;
            });
    </script>
</body>
</html>
