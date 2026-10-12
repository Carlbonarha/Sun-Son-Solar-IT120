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
    background: #f3f7fb;
    color: #213547;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }

  header {
    background: #123b6d;
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
    color: #d7e8fb;
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
    background: rgba(255, 255, 255, 0.96);
    padding: 22px;
    border-radius: 12px;
    box-shadow: 0 12px 36px rgba(18, 59, 109, 0.12);
    width: 100%;
    max-width: 360px;
    position: relative;
    z-index: 2;
  }

  .login-box h2 {
    text-align: center;
    margin-bottom: 24px;
    color: #123b6d;
  }

  .login-box input {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 1px solid #c8d3df;
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
    background: #1557b0;
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
    border-top: 8px solid #d3e81a;
    border-radius: 50% 50% 0 0;
    pointer-events: none;
    z-index: 1;
  }

  .sun-rays {
    position: absolute;
    top: calc(var(--arc-top) - 260px);
    left: 50%;
    width: min(680px, 120vw);
    height: 260px;
    transform: translateX(-50%);
    pointer-events: none;
    z-index: 0;
  }

  .sun-rays svg {
    width: 100%;
    height: 100%;
    overflow: visible;
  }

  .sun-rays .rotating-rays {
    transform-box: view-box;
    transform-origin: 340px 260px;
    animation: sunray-rotation 18s linear infinite;
  }

  .sun-rays line {
    stroke: #e5e81a;
    stroke-linecap: round;
    opacity: 0.48;
  }

  .sun-rays .sun-core {
    fill: #e8b81a;
    opacity: 0.72;
  }

  @keyframes sunray-rotation {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }

  @media (prefers-reduced-motion: reduce) {
    .sun-rays .rotating-rays { animation: none; }
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

.brand {
  text-decoration: none;
  color: white;
  font-size: 20px;
  font-weight: bold;
}

</style>
</head>
<body>

<header>
  <a class="brand" href="http://localhost/ci4/public/index.php/">🌞 Sun-Son Solar inc.</a>
  <nav class="header-nav" aria-label="Main navigation">
    <a href="<?= site_url('register.php') ?>">Register</a>
    <a href="<?= site_url('services.php') ?>">Services</a>
    <a href="<?= site_url('products.php') ?>">Products</a>
    <a href="#loginForm">Login</a>
  </nav>
</header>

<div class="wrapper">
  <div class="sun-rays" aria-hidden="true">
    <svg viewBox="0 0 680 260" preserveAspectRatio="none">
      <g class="rotating-rays">
        <line x1="340" y1="260" x2="340" y2="34" stroke-width="13" />
        <line x1="340" y1="260" x2="340" y2="72" stroke-width="9" transform="rotate(30 340 260)" />
        <line x1="340" y1="260" x2="340" y2="34" stroke-width="13" transform="rotate(60 340 260)" />
        <line x1="340" y1="260" x2="340" y2="72" stroke-width="9" transform="rotate(90 340 260)" />
        <line x1="340" y1="260" x2="340" y2="34" stroke-width="13" transform="rotate(120 340 260)" />
        <line x1="340" y1="260" x2="340" y2="72" stroke-width="9" transform="rotate(150 340 260)" />
        <line x1="340" y1="260" x2="340" y2="34" stroke-width="13" transform="rotate(180 340 260)" />
        <line x1="340" y1="260" x2="340" y2="72" stroke-width="9" transform="rotate(210 340 260)" />
        <line x1="340" y1="260" x2="340" y2="34" stroke-width="13" transform="rotate(240 340 260)" />
        <line x1="340" y1="260" x2="340" y2="72" stroke-width="9" transform="rotate(270 340 260)" />
        <line x1="340" y1="260" x2="340" y2="34" stroke-width="13" transform="rotate(300 340 260)" />
        <line x1="340" y1="260" x2="340" y2="72" stroke-width="9" transform="rotate(330 340 260)" />
      </g>
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