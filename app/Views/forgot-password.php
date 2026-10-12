<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
</head>
<body>
    <header>
        <a class="header-logo" href="<?= site_url('/') ?>" style="color: inherit; text-decoration: none;">Sun Son Solar</a>
        <nav class="header-nav" aria-label="Main navigation">
            <a href="<?= site_url('/') ?>">Home</a>
            <a href="<?= site_url('login.php') ?>">Login</a>
            <a href="<?= site_url('register.php') ?>">Create Customer Account</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
        </nav>
    </header>

    <main class="container">
        <form class="form-box" id="forgotPasswordForm">
            <h1>Reset Your Password</h1>
            <p>Enter your username and the email address on your account, then choose a new password.</p>
            <div id="forgotAlert" class="alert" role="alert" aria-live="polite"></div>

            <div class="form-group">
                <label for="resetUsername">Username</label>
                <input id="resetUsername" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="resetEmail">Account email</label>
                <input id="resetEmail" type="email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="resetPassword">New password</label>
                <input id="resetPassword" type="password" minlength="6" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirmResetPassword">Confirm new password</label>
                <input id="confirmResetPassword" type="password" minlength="6" required autocomplete="new-password">
            </div>

            <button class="btn btn-primary" type="submit">Reset Password</button>
            <a class="btn btn-secondary text-center" href="<?= site_url('login.php') ?>">Back to Login</a>
        </form>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=8') ?>"></script>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = event.currentTarget;
            const password = document.getElementById('resetPassword').value;
            const confirmation = document.getElementById('confirmResetPassword').value;
            if (password !== confirmation) {
                showAlert('forgotAlert', 'New passwords do not match.', 'error');
                return;
            }

            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;
            try {
                const response = await fetch('<?= site_url('password/forgot') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        username: document.getElementById('resetUsername').value.trim(),
                        email: document.getElementById('resetEmail').value.trim(),
                        password
                    })
                });
                const result = await response.json();
                if (!response.ok || !result.success) {
                    showAlert('forgotAlert', result.error || 'Password could not be reset. Please try again.', 'error');
                    return;
                }

                showAlert('forgotAlert', result.message || 'Password reset successfully. You can now log in.', 'success');
                form.reset();
            } catch (error) {
                console.error('Password reset error:', error);
                showAlert('forgotAlert', 'Could not connect to the server. Please try again.', 'error');
            } finally {
                button.disabled = false;
            }
        });
    </script>
</body>
</html>
