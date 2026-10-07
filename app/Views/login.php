<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login -🌞 Sun Son Solar</title>
<style>
  * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: Arial, sans-serif;
  }

  body {
    background: #f4f6f8;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header {
    background: #1a73e8;
    color: white;
    padding: 16px 24px 12px;
    font-size: 20px;
    font-weight: bold;
    position: relative;
    min-height: 68px;
  }

  .header-nav {
    position: absolute;
    right: 24px;
    bottom: 10px;
    display: flex;
    align-items: center;
    gap: 18px;
    font-size: 14px;
  }

  .header-nav a {
    color: white;
    text-decoration: none;
    font-weight: bold;
  }

  .header-nav a:hover {
    color: #ffd400;
  }

  .wrapper {
    --arc-top: 18%;
    --arc-width: 125%;
    --arc-height: 620px;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    gap: 10px;
    padding: 12px;
    position: relative;
    isolation: isolate;
    overflow: hidden;
  }

  .login-box {
    background: rgba(243, 245, 125, 0.466);
    padding: 22px;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(224, 206, 38, 0.575);
    width: 100%;
    max-width: 360px;
    position: relative;
    z-index: 2;
  }

  .login-box h2 {
    text-align: center;
    margin-bottom: 24px;
    color: #f4f4f7;
  }

  .login-box input {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #0f0808;
    border-radius: 8px;
    font-size: 14px;
  }

  .login-box button {
    width: 100%;
    padding: 12px;
    margin-top: 12px;
    border: none;
    border-radius: 8px;
    background: #1a73e8;
    color: white;
    font-size: 15px;
    cursor: pointer;
  }

  .login-box button:hover {
    background: #3e4708ce;
  }

  .forgot {
    text-align: center;
    margin: 12px 0;
    font-size: 13px;
  }

  .forgot a {
    color: #1a73e8;
    text-decoration: none;
  }

  .divider {
    text-align: center;
    margin: 16px 0;
    color: #888;
    font-size: 13px;
  }

  .create-account {
    display: block;
    width: 100%;
    padding: 12px;
    border: 1px solid #1a73e8;
    border-radius: 8px;
    color: #1a73e8;
    text-align: center;
    text-decoration: none;
    font-size: 15px;
  }

  .arc-line {
    position: absolute;
    top: var(--arc-top);
    left: 50%;
    transform: translateX(-50%);
    width: var(--arc-width);
    height: var(--arc-height);
    border-top: 8px solid #ffd400;
    border-radius: 50% 50% 0 0;
    pointer-events: none;
    z-index: 1;
  }

  .sun-rays {
    position: absolute;
    inset: -8% -12% -4%;
    pointer-events: none;
    z-index: -1;
  }

  .sun-rays svg {
    width: 100%;
    height: 100%;
    overflow: visible;
  }

  .sun-rays path {
    fill: #ffd400;
    stroke: none;
  }

  .sun-rays .s-curve {
    opacity: 0.36;
  }

  .sun-rays .ray {
    opacity: 0.6;
  }

  .create-account:hover {
    background: #f0f7ff;
  }

.solar-gallery {
  position: absolute;
  top: var(--arc-top);
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  justify-content: center;
  gap: 0;
  width: var(--arc-width);
  max-width: none;
  height: var(--arc-height);
  border-radius: 50% 50% 0 0;
  overflow: hidden;
  z-index: 0;
  pointer-events: none;
}

.solar-gallery img {
  flex: 1 1 0;
  min-width: 0;
  height: 100%;
  object-fit: cover;
  border-radius: 0;
  border: 0;
  opacity: 0.72;
  filter: saturate(1.15) brightness(0.8);
}

@media (max-width: 620px) {
  header {
    padding-bottom: 48px;
  }

  .header-nav {
    right: 24px;
    bottom: 12px;
    gap: 12px;
    font-size: 12px;
  }

  .solar-gallery {
    top: var(--arc-top);
    width: var(--arc-width);
    height: var(--arc-height);
  }

  .solar-gallery img {
    height: 100%;
  }
}

</style>
</head>
<body>

<header>
  🌞 Sun-Son Solar inc.
  <nav class="header-nav" aria-label="Main navigation">
    <a href="<?= site_url('register.php') ?>">Register</a>
    <a href="<?= site_url('services.php') ?>">Services</a>
    <a href="<?= site_url('products.php') ?>">Products</a>
    <a href="#loginForm">Login</a>
  </nav>
</header>

<div class="wrapper">
  <div class="sun-rays" aria-hidden="true">
    <svg viewBox="0 0 1000 700" preserveAspectRatio="none">
      <path class="s-curve" d="M12 170 C215 5 365 42 470 182 S735 360 988 112 L958 153 C735 394 585 275 450 218 S220 66 12 214 Z" />
      <path class="ray" d="M214 248 C140 150 66 96 0 70 L28 124 C92 132 150 172 214 248 Z" />
      <path class="ray" d="M302 168 C266 80 228 28 182 0 L222 74 C250 98 275 128 302 168 Z" />
      <path class="ray" d="M407 130 C394 62 382 18 374 -12 L422 62 C420 84 415 108 407 130 Z" />
      <path class="ray" d="M600 153 C650 70 688 28 742 0 L694 76 C660 97 630 122 600 153 Z" />
      <path class="ray" d="M712 210 C808 126 884 94 1000 88 L932 126 C850 135 780 164 712 210 Z" />
      <path class="ray" d="M780 302 C878 267 952 270 1020 298 L942 306 C886 296 834 296 780 302 Z" />
    </svg>
  </div>

  <form class="login-box" id="loginForm">
    <h2>Log in</h2>

    <div id="loginAlert" class="alert" role="alert" aria-live="polite"></div>
    
    <label for="loginUsername">Username</label>
    <input type="text" id="loginUsername" name="username" placeholder="Username" autocomplete="username" required />
    <label for="loginPassword">Password</label>
    <input type="password" id="loginPassword" name="password" placeholder="Password" autocomplete="current-password" required />
    
    <div class="forgot">
      <a href="<?= site_url('forgot-password.php') ?>">Forgot password?</a>
    </div>
    
    <button type="submit">Log in</button>

    <div class="divider">New to Sun Son Solar?</div>
    <a class="create-account" href="<?= site_url('register.php') ?>">Create Account</a>
  </form>
  <div class="arc-line"></div>

  <div class="solar-gallery" aria-label="Solar panel images">
    <img src="https://images.unsplash.com/photo-1508514177221-188b1cf16e9d?auto=format&fit=crop&w=500&q=80" alt="Solar panels in a field">
    <img src="https://images.unsplash.com/photo-1497435334941-8c899ee9e8e9?auto=format&fit=crop&w=500&q=80" alt="Solar panels on a rooftop">
    <img src="https://tse1.mm.bing.net/th/id/OIP.s1VI2SYvtDC0ieWMjoZJmgHaEK?r=0&rs=1&pid=ImgDetMain&o=7&rm=3" alt="Solar panels under sunlight">
  </div>
</div>
<script src="<?= base_url('JavaScript/script.js?v=7') ?>"></script>
<script>
document.getElementById('loginForm').addEventListener('submit', async function(event) {
    event.preventDefault();

    const username = document.getElementById('loginUsername').value.trim();
    const password = document.getElementById('loginPassword').value;
    const submitButton = this.querySelector('button[type="submit"]');
    submitButton.disabled = true;

    try {
        const response = await fetch('<?= site_url('login/check') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({ username, password })
        });
        const result = await response.json();

        if (!response.ok || !result.success) {
            showAlert('loginAlert', result.error || 'Hindi matagumpay ang pag-login.', 'error');
            return;
        }

        localStorage.setItem('currentUser', JSON.stringify(result.user));
        window.location.href = result.redirect;
    } catch (error) {
        console.error('Error:', error);
        showAlert('loginAlert', 'May problema sa koneksyon sa server.', 'error');
    } finally {
        submitButton.disabled = false;
    }
});
</script>
</body>
</html>