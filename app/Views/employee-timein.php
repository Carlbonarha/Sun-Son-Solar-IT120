<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Time In - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .timein-box { max-width: 560px; border-top: 5px solid #ffd400; }
        .timein-box h2 { margin-bottom: 8px; }
        .timein-box .intro { color: #666; margin-bottom: 24px; }
        .location-box { background: #f0f7ff; border-left: 4px solid #1a73e8; padding: 14px; margin-bottom: 18px; color: #333; }
        .location-box p { margin: 4px 0; font-size: 14px; }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">🌞 Sun Son Solar</div>
        <nav class="header-nav" aria-label="Employee navigation">
            <a href="<?= site_url(match (session()->get('role')) {
                'Admin' => 'admin-dashboard.php',
                'Dispatch', 'Dispatcher' => 'dispatcher-dashboard.php',
                'IT' => 'it-dashboard.php',
                'Accounting' => 'accounting-dashboard.php',
                'HR' => 'hr-dashboard.php',
                'Marketing' => 'marketing-dashboard.php',
                'Sales' => 'sales-dashboard.php',
                'Customer Service' => 'customer-service-dashboard.php',
                default => 'technician-dashboard.php',
            }) ?>">Dashboard</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>

    <main class="container">
        <form class="form-box timein-box" id="timeInForm">
            <h2>Employee Time In</h2>
            <p class="intro">Record your arrival at the service site. This entry will be visible to administrators.</p>
            <div id="timeInAlert" class="alert" role="alert" aria-live="polite"></div>
            <div class="form-group"><label for="siteName">Site Name *</label><input id="siteName" required placeholder="Customer or project name"></div>
            <div class="form-group"><label for="siteAddress">Site Address *</label><textarea id="siteAddress" required></textarea></div>
            <div class="form-group"><label for="timeInNote">Note</label><textarea id="timeInNote" placeholder="Optional arrival note"></textarea></div>
            <div class="location-box" id="locationBox"><p><strong>Location:</strong> Not captured</p></div>
            <button class="btn btn-secondary" type="button" id="locationButton">Capture My Location</button>
            <button class="btn btn-primary" type="submit">Time In Now</button>
        </form>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        const employee = { id: <?= (int) session()->get('userId') ?>, username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>, role: <?= json_encode((string) session()->get('role'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?> };
        localStorage.setItem('currentUser', JSON.stringify(employee));
        let capturedLocation = { latitude: null, longitude: null };

        document.getElementById('locationButton').addEventListener('click', () => {
            getCurrentLocation().then(location => {
                capturedLocation = location;
                document.getElementById('locationBox').innerHTML = `<p><strong>Latitude:</strong> ${location.latitude.toFixed(6)}</p><p><strong>Longitude:</strong> ${location.longitude.toFixed(6)}</p>`;
                showAlert('timeInAlert', 'Location captured', 'success');
            }).catch(error => showAlert('timeInAlert', `Could not capture location: ${error}`, 'error'));
        });

        document.getElementById('timeInForm').addEventListener('submit', async event => {
            event.preventDefault();
            const attendance = {
                siteName: document.getElementById('siteName').value.trim(),
                address: document.getElementById('siteAddress').value.trim(),
                latitude: capturedLocation.latitude,
                longitude: capturedLocation.longitude
            };
            try {
                const response = await fetch('<?= site_url('attendance/check-in') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
                    body: new URLSearchParams(attendance)
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not record attendance.');
                showAlert('timeInAlert', 'Time in recorded successfully', 'success');
                document.getElementById('timeInForm').reset();
                capturedLocation = { latitude: null, longitude: null };
                document.getElementById('locationBox').innerHTML = '<p><strong>Location:</strong> Not captured</p>';
            } catch (error) {
                showAlert('timeInAlert', error.message, 'error');
            }
        });
    </script>
</body>
</html>
