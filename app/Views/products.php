<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .catalog-page { display: block; width: min(1240px, calc(100% - 40px)); margin: 34px auto; padding: 0; animation: catalog-enter .7s cubic-bezier(.2, .75, .25, 1) both; }
        @keyframes catalog-enter { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        .catalog-intro { margin: 10px 0 24px; color: #64748b; }
        .catalog-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); grid-auto-rows: minmax(230px, auto); gap: 16px; }
        .product-card { padding: 0; overflow: hidden; display: flex; flex-direction: column; min-width: 0; }
        .product-card:nth-child(5n + 1) { grid-column: span 2; grid-row: span 2; }
        .product-card:nth-child(5n + 3) { grid-column: span 2; }
        .product-photo, .photo-placeholder { width: 100%; height: 180px; object-fit: cover; background: #e9f1f8; }
        .product-card:nth-child(5n + 1) .product-photo, .product-card:nth-child(5n + 1) .photo-placeholder { height: 300px; }
        .photo-placeholder { display: grid; place-items: center; color: #56718b; font-weight: 700; letter-spacing: .04em; background: linear-gradient(135deg, #eef5fa, #dce9f4); }
        .product-copy { padding: 18px; display: grid; align-content: start; gap: 8px; }
        .product-copy h2 { color: #123b6d; font-size: 20px; }
        .product-category { color: #1a73e8; font-size: 12px; text-transform: uppercase; letter-spacing: .08em; }
        .product-description { color: #64748b; line-height: 1.5; }
        .product-price { color: #123b6d; font-weight: 700; }
        .catalog-loading { grid-column: 1 / -1; display: flex; align-items: center; gap: 12px; color: #45617d; font-weight: 600; }
        .sun-loader { display: inline-block; color: #f4b400; font-size: 30px; line-height: 1; animation: sun-spin 1.8s linear infinite; }
        @keyframes sun-spin { to { transform: rotate(360deg); } }
        .product-skeleton { display: flex; flex-direction: column; overflow: hidden; min-width: 0; border: 1px solid #e8eef5; border-radius: 14px; background: #fff; box-shadow: 0 8px 24px rgba(18, 59, 109, .07); }
        .product-skeleton:nth-child(3n + 2) { grid-column: span 2; }
        .skeleton-shimmer { background: linear-gradient(100deg, #edf2f7 20%, #f9fbfd 38%, #e7eef5 55%); background-size: 220% 100%; animation: skeleton-shimmer 1.35s ease-in-out infinite; }
        @keyframes skeleton-shimmer { to { background-position-x: -220%; } }
        .skeleton-photo { height: 180px; }
        .skeleton-copy { display: grid; gap: 12px; padding: 18px; }
        .skeleton-line { height: 12px; border-radius: 8px; }
        .skeleton-line--short { width: 32%; height: 9px; }
        .skeleton-line--title { width: 72%; height: 19px; }
        .skeleton-line--price { width: 25%; margin-top: 4px; }
        .catalog-error { grid-column: 1 / -1; color: #b42318; }
        @media (max-width: 760px) { .product-skeleton:nth-child(n) { grid-column: span 1; } .product-skeleton:first-of-type { grid-column: 1 / -1; } }
        @media (max-width: 480px) { .product-skeleton:nth-child(n) { grid-column: auto; } }
        @media (max-width: 760px) { .catalog-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); grid-auto-rows: minmax(200px, auto); } .product-card:nth-child(5n + 1) { grid-column: span 2; grid-row: auto; } .product-card:nth-child(5n + 1) .product-photo, .product-card:nth-child(5n + 1) .photo-placeholder { height: 220px; } }
        @media (max-width: 480px) { .catalog-page { width: calc(100% - 24px); margin: 22px auto; } .catalog-grid { grid-template-columns: 1fr; } .product-card:nth-child(n) { grid-column: auto; } }
        @media (prefers-reduced-motion: reduce) { .catalog-page, .sun-loader, .skeleton-shimmer { animation: none; } }
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

    <main class="container catalog-page">
        <h1>Our Products</h1>
        <p class="catalog-intro">Explore product details and photo previews from Sun Son Solar.</p>
        <div class="catalog-grid" id="productsContainer" aria-live="polite" aria-busy="true">
            <div class="catalog-loading" role="status"><span class="sun-loader" aria-hidden="true">☀</span><span>Gathering your products…</span></div>
            <?php for ($i = 0; $i < 4; $i++): ?>
                <div class="product-skeleton" aria-hidden="true">
                    <div class="skeleton-photo skeleton-shimmer"></div>
                    <div class="skeleton-copy">
                        <div class="skeleton-line skeleton-line--short skeleton-shimmer"></div>
                        <div class="skeleton-line skeleton-line--title skeleton-shimmer"></div>
                        <div class="skeleton-line skeleton-shimmer"></div>
                        <div class="skeleton-line skeleton-line--price skeleton-shimmer"></div>
                    </div>
                </div>
            <?php endfor; ?>
        </div>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        const productsContainer = document.getElementById('productsContainer');

        async function loadProducts() {
            try {
                const response = await fetch('<?= site_url('catalog/products') ?>', { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Could not load products.');
                productsContainer.replaceChildren();
                productsContainer.setAttribute('aria-busy', 'false');
                if (!result.products.length) {
                    productsContainer.textContent = 'No products are listed yet.';
                    return;
                }
                result.products.forEach(product => {
                    const card = document.createElement('article');
                    card.className = 'card product-card';
                    if (product.photo) {
                        const image = document.createElement('img');
                        image.className = 'product-photo';
                        image.src = product.photo;
                        image.alt = product.name;
                        image.loading = 'lazy';
                        card.appendChild(image);
                    } else {
                        const placeholder = document.createElement('div');
                        placeholder.className = 'photo-placeholder';
                        placeholder.textContent = 'PRODUCT PHOTO';
                        placeholder.setAttribute('aria-label', `Photo placeholder for ${product.name}`);
                        card.appendChild(placeholder);
                    }
                    const copy = document.createElement('div');
                    copy.className = 'product-copy';
                    const category = document.createElement('span');
                    category.className = 'product-category';
                    category.textContent = product.category;
                    const name = document.createElement('h2');
                    name.textContent = product.name;
                    const description = document.createElement('p');
                    description.className = 'product-description';
                    description.textContent = product.description;
                    const price = document.createElement('p');
                    price.className = 'product-price';
                    price.textContent = product.price;
                    copy.append(category, name, description, price);
                    card.appendChild(copy);
                    productsContainer.appendChild(card);
                });
            } catch (error) {
                productsContainer.setAttribute('aria-busy', 'false');
                const message = document.createElement('p');
                message.className = 'catalog-error';
                message.textContent = error.message;
                productsContainer.replaceChildren(message);
            }
        }
        loadProducts();
    </script>
</body>
</html>
