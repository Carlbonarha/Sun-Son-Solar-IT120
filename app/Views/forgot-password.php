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
        <div class="header-logo">Sun Son Solar</div>
        <nav class="header-nav" aria-label="Main navigation">
            <a href="login.html">Login</a>
            <a href="register.html">Create Account</a>
            <a href="services.html">Services</a>
        </nav>
    </header>

    <main class="container">
        <form class="form-box" id="forgotPasswordForm">
            <h2>Reset Your Password</h2>
            <p>Verify your username and account email, then choose a new password.</p>
            <div id="forgotAlert" class="alert" role="alert" aria-live="polite"></div>

            <div class="form-group">
                <label for="resetUsername">Username</label>
                <input id="resetUsername" required autocomplete="username">
            </div>
            <div class="form-group">
                <label for="resetEmail">Account Email</label>
                <input id="resetEmail" type="email" required autocomplete="email">
            </div>
            <div class="form-group">
                <label for="resetPassword">New Password</label>
                <input id="resetPassword" type="password" minlength="6" required autocomplete="new-password">
            </div>
            <div class="form-group">
                <label for="confirmResetPassword">Confirm New Password</label>
                <input id="confirmResetPassword" type="password" minlength="6" required autocomplete="new-password">
            </div>

            <button class="btn btn-primary" type="submit">Reset Password</button>
            <a class="btn btn-secondary text-center" href="login.html">Back to Login</a>
        </form>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const newPassword = document.getElementById('resetPassword').value;
            const confirmation = document.getElementById('confirmResetPassword').value;

            if (newPassword !== confirmation) {
                showAlert('forgotAlert', 'New passwords do not match', 'error');
                return;
            }

            const result = resetPassword(
                document.getElementById('resetUsername').value.trim(),
                document.getElementById('resetEmail').value.trim(),
                newPassword
            );

            if (!result.success) {
                showAlert('forgotAlert', result.error, 'error');
                return;
            }

            showAlert('forgotAlert', 'Password reset successfully. You can now log in.', 'success');
            document.getElementById('forgotPasswordForm').reset();
        });
    </script>
</body>
</html>
