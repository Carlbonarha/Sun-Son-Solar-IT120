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
        .customer-dashboard .request-conversation { grid-column: 1 / -1; width: 100%; }
        .customer-dashboard .request-conversation textarea { width: 100%; min-height: 70px; margin: 8px 0; }
        .customer-dashboard .request-thread { display: grid; gap: 7px; margin: 10px 0; }
        .customer-dashboard .request-thread p { padding: 10px; border-radius: 6px; background: #f3f7fb; white-space: pre-wrap; }
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
            <a href="<?= site_url('quote.php') ?>">Free quote</a>
            <a href="<?= site_url('schedule.php') ?>">Schedule service</a>
            <a href="<?= site_url('customer-review.php') ?>">Share your story</a>
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
            <a class="card" href="<?= site_url('quote.php') ?>"><h2>Request a Free Quote</h2><p>Share your project details with Sun Son Solar.</p></a>
            <a class="card" href="<?= site_url('schedule.php') ?>"><h2>Book a Service</h2><p>Request a solar service appointment.</p></a>
            <a class="card" href="<?= site_url('customer-review.php') ?>"><h2>Share your story</h2><p>Submit a customer story for Marketing to review before publication.</p></a>
        </div>
        <section class="card booking-panel">
            <h2>My Bookings</h2>
            <div class="booking-list" id="customerBookings" aria-live="polite">Loading bookings…</div>
        </section>
        <section class="card booking-panel">
            <h2>My Quote Requests</h2>
            <div class="booking-list" id="customerQuotes" aria-live="polite">Loading quote requests…</div>
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
        async function addCustomerConversation(item, type, id) {
            const details = document.createElement('details');
            details.className = 'request-conversation';
            const summary = document.createElement('summary');
            summary.textContent = 'View conversation and reply';
            const thread = document.createElement('div');
            thread.className = 'request-thread';
            const form = document.createElement('form');
            const input = document.createElement('textarea');
            input.maxLength = 5000;
            input.required = true;
            input.placeholder = 'Write a reply or follow-up message';
            const button = document.createElement('button');
            button.className = 'btn btn-primary';
            button.type = 'submit';
            button.textContent = 'Send message';
            form.append(input, button);
            details.append(summary, thread, form);
            item.appendChild(details);
            summary.addEventListener('click', async () => {
                if (details.dataset.loaded === '1') return;
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
                    details.dataset.loaded = '1';
                } catch (error) { thread.textContent = error.message; }
            });
            form.addEventListener('submit', async event => {
                event.preventDefault();
                button.disabled = true;
                try {
                    const response = await fetch('<?= site_url('request-messages') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                        body: new URLSearchParams({ type, id: String(id), message: input.value })
                    });
                    const result = await response.json();
                    if (!response.ok || !result.success) throw new Error(result.error || 'Could not send message.');
                    input.value = '';
                    details.dataset.loaded = '';
                    details.open = false;
                    summary.click();
                } catch (error) { thread.textContent = error.message; }
                finally { button.disabled = false; }
            });
        }

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
                    const notes = document.createElement('p');
                    notes.textContent = booking.notes || 'No additional notes.';
                    item.appendChild(notes);
                    addCustomerConversation(item, 'booking', booking.id);
                    container.appendChild(item);
                });
            })
            .catch(error => {
                document.getElementById('customerBookings').textContent = error.message;
            });
        fetch('<?= site_url('quote-requests/list') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(async response => {
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not load quote requests.');
                return result.requests;
            })
            .then(requests => {
                const container = document.getElementById('customerQuotes');
                container.replaceChildren();
                if (!requests.length) { container.textContent = 'You have no quote requests linked to this account.'; return; }
                requests.forEach(request => {
                    const item = document.createElement('article');
                    item.className = 'card booking-item';
                    const title = document.createElement('h3');
                    title.textContent = request.serviceNeeded;
                    const status = document.createElement('p');
                    status.textContent = `Submitted ${request.createdAt} · ${request.status}`;
                    const message = document.createElement('p');
                    message.textContent = request.additionalDetails || 'No additional message.';
                    item.append(title, status, message);
                    addCustomerConversation(item, 'quote', request.id);
                    container.appendChild(item);
                });
            })
            .catch(error => { document.getElementById('customerQuotes').textContent = error.message; });
    </script>
</body>
</html>
