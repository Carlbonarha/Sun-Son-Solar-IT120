<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - 🌞 Sun Son Solar</title>
    <link rel="stylesheet" href="<?= base_url('CSS/styles.css') ?>">
    <style>
        .page-wrapper {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 40px 20px;
        }

        .form-box {
            max-width: 620px;
            border-top: 5px solid #1a73e8;
        }

        .form-box h2 {
            margin-bottom: 8px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 24px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .form-actions .btn {
            flex: 1;
        }

        .password-heading {
            color: #1a73e8;
            margin: 24px 0 14px;
            font-size: 18px;
        }

        @media (max-width: 620px) {
            .form-row,
            .form-actions {
                grid-template-columns: 1fr;
                display: grid;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-logo">🌞 Sun Son Solar</div>
        <nav class="header-nav" aria-label="Account navigation">
            <a href="<?= site_url(match (session()->get('role')) {
                'Admin' => 'admin-dashboard.php',
                'Dispatch', 'Dispatcher' => 'dispatcher-dashboard.php',
                'Technician' => 'technician-dashboard.php',
                'Maintenance' => 'maintenance-dashboard.php',
                'IT' => 'it-dashboard.php',
                'Accounting' => 'accounting-dashboard.php',
                'HR' => 'hr-dashboard.php',
                'Marketing' => 'marketing-dashboard.php',
                'Sales' => 'sales-dashboard.php',
                'Customer Service' => 'customer-service-dashboard.php',
                default => 'customer-dashboard.php',
            }) ?>">Dashboard</a>
            <a href="<?= site_url('services.php') ?>">Services</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <button class="logout-btn" type="button" onclick="logout()">Logout</button>
        </nav>
    </header>

    <main class="page-wrapper">
        <form class="form-box" id="profileForm">
            <h2>My Profile</h2>
            <p class="subtitle">Update your account and contact information.</p>
            <div id="profileAlert" class="alert" role="alert" aria-live="polite"></div>

            <div class="form-row">
                <div class="form-group">
                    <label for="firstName">First Name *</label>
                    <input id="firstName" name="firstName" value="<?= esc($profile['firstName'] ?? '') ?>" maxlength="255" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name *</label>
                    <input id="lastName" name="lastName" value="<?= esc($profile['lastName'] ?? '') ?>" maxlength="255" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="middleName">Middle Name</label>
                    <input id="middleName" name="middleName" value="<?= esc($profile['middleName'] ?? '') ?>" maxlength="255">
                </div>
                <div class="form-group">
                    <label for="birthdate">Birthdate</label>
                    <input id="birthdate" name="birthdate" type="date" value="<?= esc($profile['birthdate'] ?? '') ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="gender">Gender</label>
                <select id="gender" name="gender">
                    <option value="">Select gender</option>
                    <option value="Male" <?= ($profile['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                    <option value="Female" <?= ($profile['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
                    <option value="Other" <?= ($profile['gender'] ?? '') === 'Other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>

            <div class="form-group">
                <label for="profileEmail">Email Address *</label>
                <input id="profileEmail" name="email" type="email" value="<?= esc($profile['email'] ?? '') ?>" maxlength="255" required>
            </div>

            <div class="form-group">
                <label for="phone">Phone Number</label>
                <input id="phone" name="phone" type="tel" value="<?= esc($profile['phone'] ?? '') ?>" maxlength="50">
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address"><?= esc($profile['address'] ?? '') ?></textarea>
            </div>

            <div class="form-group">
                <label for="profileUsername">Username *</label>
                <input id="profileUsername" name="username" value="<?= esc($accountUsername) ?>" minlength="3" maxlength="255" required>
            </div>

            <h3 class="password-heading">Change Password</h3>
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input id="currentPassword" type="password" autocomplete="current-password">
            </div>
            <div class="form-group">
                <label for="newPassword">New Password</label>
                <input id="newPassword" type="password" autocomplete="new-password" minlength="6">
            </div>
            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input id="confirmPassword" type="password" autocomplete="new-password">
            </div>

            <div class="form-actions">
                <button class="btn btn-primary" type="submit">Save Changes</button>
                <a class="btn btn-secondary text-center" href="<?= site_url(match (session()->get('role')) {
                    'Admin' => 'admin-dashboard.php',
                    'Dispatch', 'Dispatcher' => 'dispatcher-dashboard.php',
                    'Technician' => 'technician-dashboard.php',
                    'Maintenance' => 'maintenance-dashboard.php',
                    'IT' => 'it-dashboard.php',
                    'Accounting' => 'accounting-dashboard.php',
                    'HR' => 'hr-dashboard.php',
                    'Marketing' => 'marketing-dashboard.php',
                    'Sales' => 'sales-dashboard.php',
                    'Customer Service' => 'customer-service-dashboard.php',
                    default => 'customer-dashboard.php',
                }) ?>">Cancel</a>
            </div>
        </form>
    </main>

    <script src="<?= base_url('JavaScript/script.js?v=8') ?>"></script>
    <script>
        const profileUser = {
            id: <?= (int) session()->get('userId') ?>,
            username: <?= json_encode((string) session()->get('username'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>,
            role: <?= json_encode((string) session()->get('role'), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>
        };
        localStorage.setItem('currentUser', JSON.stringify(profileUser));

        document.getElementById('profileForm').addEventListener('submit', async function(event) {
            event.preventDefault();
            const newPassword = document.getElementById('newPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (newPassword && newPassword !== confirmPassword) {
                showAlert('profileAlert', 'New passwords do not match.', 'error');
                return;
            }

            const button = this.querySelector('button[type="submit"]');
            button.disabled = true;
            try {
                const values = Object.fromEntries(new FormData(this).entries());
                values.currentPassword = document.getElementById('currentPassword').value;
                values.newPassword = newPassword;
                const response = await fetch('<?= site_url('profile/update') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams(values)
                });
                const result = await response.json();
                if (!response.ok || !result.success) {
                    showAlert('profileAlert', result.error || 'Profile could not be saved. Please try again.', 'error');
                    return;
                }

                localStorage.setItem('currentUser', JSON.stringify(result.user));
                document.getElementById('currentPassword').value = '';
                document.getElementById('newPassword').value = '';
                document.getElementById('confirmPassword').value = '';
                showAlert('profileAlert', result.message || 'Profile updated successfully.', 'success');
            } catch (error) {
                console.error('Profile update error:', error);
                showAlert('profileAlert', 'Could not connect to the server. Please try again.', 'error');
            } finally {
                button.disabled = false;
            }
        });
    </script>
</body>
</html>
