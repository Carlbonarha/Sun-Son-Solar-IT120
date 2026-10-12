<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Customer Account - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .register-page { width: min(680px, calc(100% - 32px)); margin: 38px auto; }
        .register-card { padding: clamp(22px, 5vw, 40px); }
        .register-card h1 { margin-bottom: 8px; color: #123b6d; }
        .register-intro { margin-bottom: 24px; color: #5c6d7e; line-height: 1.55; }
        .register-form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .register-field { display: grid; align-content: start; gap: 7px; }
        .register-field--wide, .register-actions, #registerAlert { grid-column: 1 / -1; }
        .register-field label { font-weight: 600; color: #213547; }
        .register-field input, .register-field select, .register-field textarea { width: 100%; padding: 11px 12px; border: 1px solid #c8d3df; border-radius: 7px; font: inherit; }
        .register-field textarea { min-height: 90px; resize: vertical; }
        .register-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 12px; }
        .register-actions button { padding: 12px 18px; }
        .employee-note { margin: 22px 0 0; padding: 14px 16px; border-left: 4px solid #1a73e8; background: #eef5fc; color: #334155; line-height: 1.5; }
        .register-login { margin-top: 20px; color: #5c6d7e; text-align: center; }
        .register-login a { color: #1557b0; font-weight: 700; }
        @media (max-width: 560px) {
            .register-page { margin: 22px auto; }
            .register-form { grid-template-columns: 1fr; }
            .register-field--wide, .register-actions, #registerAlert { grid-column: auto; }
        }
    </style>
</head>
<body>
    <header>
        <a class="header-logo" href="<?= site_url('/') ?>" style="color: inherit; text-decoration: none;">Sun Son Solar</a>
        <nav class="header-nav" aria-label="Main navigation">
            <a href="<?= site_url('/') ?>">Home</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('quote.php') ?>">Free Quote</a>
            <a href="<?= site_url('login.php') ?>">Login</a>
        </nav>
    </header>

    <main class="container register-page">
        <section class="card register-card" aria-labelledby="registerHeading">
            <h1 id="registerHeading">Create a customer account</h1>
            <p class="register-intro">Register to access your Sun Son Solar customer account. Employee accounts are created by HR or an administrator.</p>
            <div id="registerAlert" class="alert" role="alert" aria-live="polite"></div>

            <form id="registerForm" class="register-form">
                <?= csrf_field() ?>
                <div class="register-field">
                    <label for="firstName">First name *</label>
                    <input id="firstName" name="firstName" type="text" maxlength="255" autocomplete="given-name" required>
                </div>
                <div class="register-field">
                    <label for="lastName">Last name *</label>
                    <input id="lastName" name="lastName" type="text" maxlength="255" autocomplete="family-name" required>
                </div>
                <div class="register-field">
                    <label for="middleName">Middle name</label>
                    <input id="middleName" name="middleName" type="text" maxlength="255" autocomplete="additional-name">
                </div>
                <div class="register-field">
                    <label for="birthdate">Birthdate</label>
                    <input id="birthdate" name="birthdate" type="date" autocomplete="bday">
                </div>
                <div class="register-field">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="">Prefer not to specify</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="register-field">
                    <label for="email">Email address *</label>
                    <input id="email" name="email" type="email" maxlength="255" autocomplete="email" required>
                </div>
                <div class="register-field">
                    <label for="phone">Phone number</label>
                    <input id="phone" name="phone" type="tel" maxlength="50" autocomplete="tel">
                </div>
                <div class="register-field register-field--wide">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" autocomplete="street-address"></textarea>
                </div>
                <div class="register-field">
                    <label for="regUsername">Username *</label>
                    <input id="regUsername" name="username" type="text" minlength="3" maxlength="255" autocomplete="username" required>
                </div>
                <div class="register-field">
                    <label for="regPassword">Password *</label>
                    <input id="regPassword" name="password" type="password" minlength="6" autocomplete="new-password" required>
                </div>
                <div class="register-actions">
                    <button class="btn btn-primary" type="submit">Create customer account</button>
                    <a class="btn btn-secondary" href="<?= site_url('login.php') ?>">Back to login</a>
                </div>
            </form>

            <p class="employee-note"><strong>Are you an employee?</strong> For account security, employee accounts are set up by HR or an administrator. Contact your department’s HR representative or administrator for access.</p>
            <p class="register-login">Already have an account? <a href="<?= site_url('login.php') ?>">Log in</a>.</p>
        </section>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=8') ?>"></script>
    <script>
        const registerForm = document.getElementById('registerForm');
        registerForm.addEventListener('submit', async event => {
            event.preventDefault();
            const data = Object.fromEntries(new FormData(registerForm).entries());
            const validation = validateFormData({
                firstName: String(data.firstName || '').trim(),
                lastName: String(data.lastName || '').trim(),
                email: String(data.email || '').trim(),
                username: String(data.username || '').trim(),
                password: String(data.password || '')
            }, 'customer');

            if (!validation.isValid) {
                showAlert('registerAlert', validation.errors.join(', '), 'error');
                return;
            }

            const submitButton = registerForm.querySelector('button[type="submit"]');
            submitButton.disabled = true;
            try {
                const response = await fetch('<?= site_url('register/check') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        ...data,
                        username: String(data.username).trim(),
                        email: String(data.email).trim(),
                        role: 'Customer'
                    })
                });
                const result = await response.json();
                if (!response.ok || !result.success) {
                    showAlert('registerAlert', result.error || 'Account registration failed. Please try again.', 'error');
                    return;
                }

                showAlert('registerAlert', result.message || 'Customer account created. Redirecting to login…', 'success');
                registerForm.reset();
                window.setTimeout(() => window.location.assign('<?= site_url('login.php') ?>'), 1200);
            } catch (error) {
                console.error('Registration error:', error);
                showAlert('registerAlert', 'Could not connect to the server. Please try again.', 'error');
            } finally {
                submitButton.disabled = false;
            }
        });

        if (isLoggedIn()) {
            window.location.assign(getRoleHome(getCurrentUser().role));
        }
    </script>
</body>
</html>
