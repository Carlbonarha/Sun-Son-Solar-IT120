<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Share your story - Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .review-panel { width: min(720px, calc(100% - 32px)); margin: 36px auto; padding: 28px; }
        .review-panel h1 { color: #123b6d; }
        .review-panel textarea, .review-panel select { width: 100%; padding: 11px; border: 1px solid #cbd5e1; border-radius: 6px; font: inherit; }
        .review-panel textarea { min-height: 180px; }
        .review-panel .consent { display: flex; gap: 10px; align-items: flex-start; margin: 18px 0; }
        #reviewMessage[data-state="success"] { color: #167242; }
        #reviewMessage[data-state="error"] { color: #a61b1b; }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">Sun Son Solar</div>
        <nav class="header-nav" aria-label="Customer navigation">
            <a href="<?= site_url('customer-dashboard.php') ?>">Dashboard</a>
            <a href="<?= site_url('/') ?>#stories">Customer stories</a>
            <a href="<?= site_url('profile.php') ?>">My Profile</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>
    <main class="card review-panel">
        <h1>Share your Sun Son Solar story</h1>
        <p>Submissions are reviewed by Marketing. A story appears publicly only after it is published.</p>
        <form id="reviewForm">
            <div class="form-group">
                <label for="serviceType">Service you are reviewing *</label>
                <select name="serviceType" id="serviceType" required>
                    <option value="">Choose a service</option>
                    <option>Consultation</option>
                    <option>Designing</option>
                    <option>Installation</option>
                    <option>Repair</option>
                    <option>Monitoring</option>
                    <option>Maintenance</option>
                </select>
            </div>
            <div class="form-group">
                <label for="story">Your story *</label>
                <textarea name="story" id="story" maxlength="5000" required placeholder="Tell us about your experience. Please do not include private financial or other sensitive details."></textarea>
            </div>
            <label class="consent">
                <input name="privacyConsent" type="checkbox" value="1" required>
                <span>I give Sun Son Solar permission to review this story for possible publication. I understand it will not appear on the website unless Marketing publishes it.</span>
            </label>
            <button class="btn btn-primary" id="submitReview" type="submit">Submit story for review</button>
            <p id="reviewMessage" role="status" aria-live="polite"></p>
        </form>
    </main>
    <script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
    <script>
        document.getElementById('reviewForm').addEventListener('submit', async event => {
            event.preventDefault();
            const form = event.currentTarget;
            const button = document.getElementById('submitReview');
            const message = document.getElementById('reviewMessage');
            button.disabled = true;
            message.textContent = '';
            try {
                const response = await fetch('<?= site_url('customer-reviews') ?>', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: new FormData(form)
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Your story could not be submitted.');
                message.dataset.state = 'success';
                message.textContent = 'Thank you. Your story is awaiting Marketing review.';
                form.reset();
            } catch (error) {
                message.dataset.state = 'error';
                message.textContent = error instanceof Error ? error.message : 'Your story could not be submitted.';
            } finally { button.disabled = false; }
        });
    </script>
</body>
</html>
