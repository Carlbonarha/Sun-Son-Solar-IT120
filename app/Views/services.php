<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .page-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 48px 20px;
        }

        .content-box {
            width: 100%;
            max-width: 900px;
            background: white;
            padding: 40px;
            border-radius: 12px;
            border-top: 5px solid #ffd400;
            box-shadow: 0 8px 24px rgba(26, 115, 232, 0.12);
            animation: catalog-enter .7s cubic-bezier(.2, .75, .25, 1) both;
        }

        @keyframes catalog-enter { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        .content-box h1 {
            color: #1a73e8;
            margin-bottom: 12px;
        }

        .content-box > p {
            color: #666;
            margin-bottom: 28px;
        }

        .service-list {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            grid-auto-rows: minmax(210px, auto);
            gap: 16px;
        }

        .service-item {
            padding: 0;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 14px rgba(18, 59, 109, .08);
        }

        .service-item:nth-child(4n + 1) { grid-column: span 2; grid-row: span 2; }
        .service-item:nth-child(4n + 3) { grid-column: span 2; }
        .service-photo, .service-photo-placeholder { width: 100%; height: 150px; object-fit: cover; background: #e9f1f8; }
        .service-item:nth-child(4n + 1) .service-photo, .service-item:nth-child(4n + 1) .service-photo-placeholder { height: 250px; }
        .service-photo-placeholder { display: grid; place-items: center; color: #56718b; font-weight: 700; letter-spacing: .04em; background: linear-gradient(135deg, #eef5fa, #dce9f4); }
        .service-copy { padding: 18px; }
        .service-copy h2 {
            color: #1a73e8;
            font-size: 18px;
            margin-bottom: 8px;
        }

        .service-copy p {
            color: #666;
            line-height: 1.5;
        }

        .catalog-loading { grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; color: #45617d; font-weight: 600; }
        .sun-loader { display: inline-block; color: #f4b400; font-size: 30px; line-height: 1; animation: sun-spin 1.8s linear infinite; }
        @keyframes sun-spin { to { transform: rotate(360deg); } }
        .service-skeleton { display: flex; flex-direction: column; overflow: hidden; min-width: 0; border: 1px solid #e8eef5; border-radius: 8px; background: #fff; box-shadow: 0 4px 14px rgba(18, 59, 109, .08); }
        .service-skeleton:nth-child(3n + 2) { grid-column: span 2; }
        .skeleton-shimmer { background: linear-gradient(100deg, #edf2f7 20%, #f9fbfd 38%, #e7eef5 55%); background-size: 220% 100%; animation: skeleton-shimmer 1.35s ease-in-out infinite; }
        @keyframes skeleton-shimmer { to { background-position-x: -220%; } }
        .skeleton-photo { height: 150px; }
        .skeleton-copy { display: grid; gap: 12px; padding: 18px; }
        .skeleton-line { height: 12px; border-radius: 8px; }
        .skeleton-line--short { width: 32%; height: 9px; }
        .skeleton-line--title { width: 72%; height: 19px; }
        .catalog-error { grid-column: 1 / -1; color: #b42318; }
        @media (max-width: 760px) { .service-skeleton:nth-child(n) { grid-column: span 1; } .service-skeleton:first-of-type { grid-column: 1 / -1; } }
        @media (max-width: 480px) { .service-skeleton:nth-child(n) { grid-column: auto; } }
        @media (prefers-reduced-motion: reduce) { .content-box, .sun-loader, .skeleton-shimmer { animation: none; } }

        @media (max-width: 760px) {
            .service-list { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .service-item:nth-child(4n + 1) { grid-column: span 2; grid-row: auto; }
            .service-item:nth-child(4n + 1) .service-photo, .service-item:nth-child(4n + 1) .service-photo-placeholder { height: 200px; }
        }
        @media (max-width: 480px) {
            .service-list { grid-template-columns: 1fr; }
            .service-item:nth-child(n) { grid-column: auto; }
            .content-box {
                padding: 26px 20px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">Sun Son Solar</div>
        <nav class="header-nav" aria-label="Main navigation">
            <?php if (session()->get('isLoggedIn') === true): ?>
                <a href="<?= site_url(match (session()->get('role')) {
                    'Admin' => 'admin-dashboard.php',
                    'Dispatch', 'Dispatcher' => 'dispatcher-dashboard.php',
                    'Technician' => 'technician-dashboard.php',
                    'IT' => 'it-dashboard.php',
                    'Accounting' => 'accounting-dashboard.php',
                    'HR' => 'hr-dashboard.php',
                    'Marketing' => 'marketing-dashboard.php',
                    'Sales' => 'sales-dashboard.php',
                    'Customer Service' => 'customer-service-dashboard.php',
                    default => 'customer-dashboard.php',
                }) ?>">Dashboard</a>
                <a href="<?= site_url('profile.php') ?>">My Profile</a>
                <button class="logout-btn" type="button" onclick="logout()">Logout</button>
            <?php else: ?>
                <a href="<?= site_url('login.php') ?>">Login</a>
                <a href="<?= site_url('register.php') ?>">Create Account</a>
            <?php endif; ?>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
        </nav>
    </header>

    <main class="page-wrapper">
        <section class="content-box">
            <h1>Our Services</h1>
            <p>Explore the solar services available from Sun Son Solar.</p>
            <div class="service-list" id="servicesContainer" aria-live="polite" aria-busy="true">
                <div class="catalog-loading" role="status"><span class="sun-loader" aria-hidden="true">☀</span><span>Gathering your services…</span></div>
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="service-skeleton" aria-hidden="true">
                        <div class="skeleton-photo skeleton-shimmer"></div>
                        <div class="skeleton-copy">
                            <div class="skeleton-line skeleton-line--short skeleton-shimmer"></div>
                            <div class="skeleton-line skeleton-line--title skeleton-shimmer"></div>
                            <div class="skeleton-line skeleton-shimmer"></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
        </section>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        const servicesContainer = document.getElementById('servicesContainer');
        async function loadServices() {
            try {
                const response = await fetch('<?= site_url('catalog/services') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not load services.');
                servicesContainer.replaceChildren();
                servicesContainer.setAttribute('aria-busy', 'false');
                if (!result.services.length) {
                    servicesContainer.textContent = 'No services are listed yet.';
                    return;
                }
                result.services.forEach(service => {
                    const item = document.createElement('article');
                    item.className = 'service-item';
                    if (service.photo) {
                        const image = document.createElement('img');
                        image.className = 'service-photo';
                        image.src = service.photo;
                        image.alt = service.name;
                        image.loading = 'lazy';
                        item.appendChild(image);
                    } else {
                        const placeholder = document.createElement('div');
                        placeholder.className = 'service-photo-placeholder';
                        placeholder.textContent = 'SERVICE PHOTO';
                        placeholder.setAttribute('aria-label', `Photo placeholder for ${service.name}`);
                        item.appendChild(placeholder);
                    }
                    const copy = document.createElement('div');
                    copy.className = 'service-copy';
                    const title = document.createElement('h2');
                    title.textContent = service.name;
                    const description = document.createElement('p');
                    description.textContent = service.description;
                    copy.append(title, description);
                    item.appendChild(copy);
                    servicesContainer.appendChild(item);
                });
            } catch (error) {
                servicesContainer.setAttribute('aria-busy', 'false');
                const message = document.createElement('p');
                message.className = 'catalog-error';
                message.textContent = error.message;
                servicesContainer.replaceChildren(message);
            }
        }
        loadServices();
    </script>
</body>
</html>
