<?php
$dashboardUrl = session()->get('isLoggedIn') === true
    ? site_url(match (session()->get('role')) {
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
    })
    : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Explore Sun Son Solar consultation, installation, and maintenance services. Learn about solar power and request a free quote.">
    <title>Sun Son Solar | Power Your Future with Solar</title>
    <style>
        :root { color-scheme: light; --navy: #123b6d; --blue: #1a73e8; --ink: #213547; --muted: #5c6d7e; --pale: #f3f7fb; --line: #dce6f0; }
        * { box-sizing: border-box; }
        html { scroll-behavior: smooth; }
        body { margin: 0; color: var(--ink); background: white; font-family: Arial, sans-serif; }
        a { color: #1557b0; }
        .site-header { position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; gap: 22px; padding: 15px max(24px, calc((100% - 1160px) / 2)); color: white; background: var(--navy); box-shadow: 0 4px 18px rgba(10, 36, 66, .18); }
        .brand { color: white; font-size: 1.13rem; font-weight: 750; text-decoration: none; white-space: nowrap; }
        .main-nav { display: flex; align-items: center; flex-wrap: wrap; justify-content: flex-end; gap: 18px; }
        .main-nav a { color: white; font-size: .94rem; font-weight: 600; text-decoration: none; }
        .main-nav a:hover, .main-nav a:focus-visible { color: #cfe5ff; text-decoration: underline; text-underline-offset: 4px; }
        .main-nav .nav-cta { padding: 10px 14px; border-radius: 7px; background: var(--blue); }
        .hero { position: relative; overflow: hidden; display: grid; min-height: 500px; place-items: center; padding: 78px 24px; color: white; text-align: center; background: radial-gradient(ellipse at 80% 5%, rgba(99, 177, 255, .58), transparent 34%), linear-gradient(130deg, #0e2e55, #123b6d 54%, #1a73e8); }
        .hero::before, .hero::after { position: absolute; content: ""; border: 1px solid rgba(255,255,255,.12); border-radius: 50%; pointer-events: none; }
        .hero::before { width: 520px; height: 520px; top: -310px; right: -85px; }
        .hero::after { width: 690px; height: 690px; top: -390px; right: -170px; }
        .hero-content { position: relative; z-index: 1; width: min(820px, 100%); }
        .eyebrow { margin: 0 0 14px; color: #d8ebff; font-size: .86rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .hero h1 { margin: 0 auto 18px; font-size: clamp(2.6rem, 7vw, 4.8rem); line-height: 1.04; }
        .hero p { max-width: 690px; margin: 0 auto 30px; color: #edf6ff; font-size: clamp(1rem, 2.4vw, 1.2rem); line-height: 1.7; }
        .actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; }
        .button { display: inline-flex; min-height: 46px; align-items: center; justify-content: center; padding: 12px 19px; border: 2px solid var(--blue); border-radius: 8px; background: var(--blue); color: white; font-weight: 700; text-decoration: none; transition: transform .15s ease, background .15s ease; }
        .button:hover { transform: translateY(-2px); background: #1557b0; }
        .button--light { border-color: white; background: transparent; }
        .button--light:hover { background: rgba(255,255,255,.13); }
        .section { width: min(1120px, calc(100% - 40px)); margin: 0 auto; padding: 74px 0; }
        .section--soft { width: 100%; padding-right: max(20px, calc((100% - 1120px) / 2)); padding-left: max(20px, calc((100% - 1120px) / 2)); background: var(--pale); }
        .section-heading { max-width: 720px; margin: 0 auto 34px; text-align: center; }
        .section-heading h2 { margin: 0 0 12px; color: var(--navy); font-size: clamp(1.8rem, 4vw, 2.4rem); }
        .section-heading p { margin: 0; color: var(--muted); line-height: 1.65; }
        .benefit-grid, .service-grid, .account-grid, .faq-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
        .feature-card { height: 100%; padding: 25px; border: 1px solid var(--line); border-radius: 12px; background: white; box-shadow: 0 8px 24px rgba(18, 59, 109, .055); }
        .feature-icon { display: grid; width: 44px; height: 44px; place-items: center; margin-bottom: 17px; border-radius: 12px; background: #eaf2fb; color: var(--blue); font-size: 1.35rem; font-weight: 700; }
        .feature-card h3 { margin: 0 0 10px; color: var(--navy); font-size: 1.17rem; }
        .feature-card p { margin: 0; color: var(--muted); line-height: 1.65; }
        .service-card { display: flex; flex-direction: column; min-height: 220px; }
        .service-card .text-link { margin-top: auto; padding-top: 18px; font-weight: 700; text-decoration: none; }
        .service-card .text-link:hover { text-decoration: underline; }
        .story-panel { display: grid; grid-template-columns: minmax(0, 1fr) auto; align-items: center; gap: 24px; padding: 30px; border: 1px solid var(--line); border-left: 5px solid var(--blue); border-radius: 12px; background: white; }
        .story-panel h3 { margin: 0 0 8px; color: var(--navy); }
        .story-panel p { margin: 0; color: var(--muted); line-height: 1.6; }
        .story-controls { display: flex; align-items: center; gap: 10px; margin-top: 20px; }
        .story-controls button { min-width: 44px; min-height: 42px; border: 1px solid var(--line); border-radius: 7px; background: #f3f7fb; color: var(--navy); font-size: 1.2rem; cursor: pointer; }
        .story-position { color: var(--muted); font-size: .9rem; }
        .account-card { display: flex; flex-direction: column; }
        .account-card .button { align-self: flex-start; margin-top: auto; }
        .account-card p { margin-bottom: 20px; }
        .faq-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        details { padding: 18px 20px; border: 1px solid var(--line); border-radius: 9px; background: white; }
        summary { color: var(--navy); font-weight: 700; cursor: pointer; }
        details p { margin: 12px 0 0; color: var(--muted); line-height: 1.6; }
        .contact-panel { display: flex; align-items: center; justify-content: space-between; gap: 22px; padding: 30px; border-radius: 14px; color: white; background: linear-gradient(120deg, var(--navy), var(--blue)); }
        .contact-panel h2 { margin: 0 0 8px; font-size: 1.8rem; }
        .contact-panel p { margin: 0; color: #e6f2ff; line-height: 1.6; }
        .site-footer { padding: 34px 20px 24px; color: #eaf2fb; background: #0d2d50; }
        .footer-inner { display: grid; width: min(1120px, 100%); grid-template-columns: 1.1fr 1fr 1.2fr; gap: 28px; margin: 0 auto; }
        .site-footer h2, .site-footer h3 { margin: 0 0 12px; color: white; font-size: 1.05rem; }
        .site-footer p { margin: 7px 0; color: #d2e1f0; font-size: .92rem; line-height: 1.55; }
        .site-footer p a { color: #9ecbff; }
        .footer-links { display: grid; align-content: start; gap: 9px; }
        .footer-links a { color: #eaf2fb; text-decoration: none; }
        .footer-links a:hover { text-decoration: underline; }
        .legal-note { width: min(1120px, 100%); margin: 26px auto 0; padding-top: 18px; border-top: 1px solid rgba(255,255,255,.2); color: #c6d6e6; font-size: .82rem; line-height: 1.6; }
        :focus-visible { outline: 3px solid #f5bd41; outline-offset: 3px; }
        @media (max-width: 760px) {
            .site-header { position: static; align-items: flex-start; flex-direction: column; padding: 16px 20px; }
            .main-nav { justify-content: flex-start; gap: 12px 16px; }
            .benefit-grid, .service-grid, .account-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .footer-inner { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        }
        @media (max-width: 540px) {
            .hero { min-height: 450px; padding: 60px 20px; }
            .section { width: min(100% - 28px, 1120px); padding: 52px 0; }
            .section--soft { width: 100%; padding-right: 14px; padding-left: 14px; }
            .benefit-grid, .service-grid, .account-grid, .faq-grid, .footer-inner { grid-template-columns: 1fr; }
            .story-panel, .contact-panel { align-items: flex-start; grid-template-columns: 1fr; flex-direction: column; }
            .contact-panel .button { align-self: flex-start; }
        }
        @media (prefers-reduced-motion: reduce) { html { scroll-behavior: auto; } *, *::before, *::after { transition-duration: .01ms !important; } }
    </style>
</head>
<body>
    <header class="site-header">
        <a class="brand" href="<?= site_url('/') ?>">Sun Son Solar</a>
        <nav class="main-nav" aria-label="Main navigation">
            <a href="<?= site_url('/') ?>#about">About</a>
            <a href="<?= site_url('/') ?>#services">Services</a>
            <a href="<?= site_url('/') ?>#stories">Stories</a>
            <a href="<?= site_url('/') ?>#insights">Insights</a>
            <a href="<?= site_url('/') ?>#faq">FAQ</a>
            <a href="<?= site_url('/') ?>#accounts">Account access</a>
            <a href="<?= site_url('products.php') ?>">Products</a>
            <?php if ($dashboardUrl !== null): ?>
                <a class="nav-cta" href="<?= esc($dashboardUrl) ?>">My dashboard</a>
                <a href="<?= site_url('logout') ?>">Log out</a>
            <?php else: ?>
                <a href="<?= site_url('login.php') ?>">Login</a>
                <a class="nav-cta" href="<?= site_url('quote.php') ?>">Free quote</a>
            <?php endif; ?>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <p class="eyebrow">Sun Son Solar</p>
                <h1>Power Your Future with Solar</h1>
                <p>Explore solar consultation, installation, and maintenance with Sun Son Solar. Start with clear information and a conversation about what may fit your property and energy needs.</p>
                <div class="actions">
                    <a class="button" href="<?= site_url('quote.php') ?>">Request a Free Quote</a>
                    <a class="button button--light" href="<?= site_url('services.php') ?>">Explore Services</a>
                </div>
            </div>
        </section>

        <section class="section" id="about" aria-labelledby="aboutHeading">
            <div class="section-heading">
                <h2 id="aboutHeading">A brighter way to explore your energy options</h2>
                <p>Sun Son Solar helps customers explore solar through consultation, installation, and maintenance. Every property is different, so system design, savings, and energy independence depend on your site, usage, equipment, and local conditions.</p>
            </div>
            <div class="benefit-grid">
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">₱</div>
                    <h3>Potential cost savings</h3>
                    <p>Solar may help reduce electricity purchases over time. Actual savings vary with system size, energy use, tariffs, financing, and sunlight.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">↗</div>
                    <h3>A more sustainable choice</h3>
                    <p>Generating electricity from sunlight can reduce reliance on fossil-fuel-based electricity, depending on the energy mix and system lifecycle.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">☀</div>
                    <h3>More energy independence</h3>
                    <p>On-site solar can provide another source of electricity. Backup power requires compatible storage and equipment and is not automatic.</p>
                </article>
            </div>
        </section>

        <section class="section section--soft" id="services" aria-labelledby="servicesHeading">
            <div class="section-heading">
                <h2 id="servicesHeading">Solar services for every step</h2>
                <p>Learn about the service that matches where you are in your solar journey. The team can discuss project needs after you get in touch.</p>
            </div>
            <div class="service-grid">
                <article class="feature-card service-card">
                    <div class="feature-icon" aria-hidden="true">◎</div>
                    <h3>Consultation</h3>
                    <p>Discuss your goals, property, energy use, and questions as a starting point for exploring solar options.</p>
                    <a class="text-link" href="<?= site_url('quote.php') ?>">Ask about a consultation →</a>
                </article>
                <article class="feature-card service-card">
                    <div class="feature-icon" aria-hidden="true">⌂</div>
                    <h3>Installation</h3>
                    <p>Explore the installation process and the project details that need to be assessed for your property.</p>
                    <a class="text-link" href="<?= site_url('quote.php') ?>">Discuss an installation →</a>
                </article>
                <article class="feature-card service-card">
                    <div class="feature-icon" aria-hidden="true">↻</div>
                    <h3>Maintenance</h3>
                    <p>Ask about maintaining a solar installation and the service support available for your system.</p>
                    <a class="text-link" href="<?= site_url('quote.php') ?>">Ask about maintenance →</a>
                </article>
            </div>
            <div class="actions" style="margin-top: 24px;">
                <a class="button" href="<?= site_url('services.php') ?>">Browse service details</a>
            </div>
        </section>

        <section class="section" id="stories" aria-labelledby="storiesHeading">
            <div class="section-heading">
                <h2 id="storiesHeading">Customer stories</h2>
                <p>Trust matters. We will only publish testimonials and project outcomes that have been provided and approved for sharing.</p>
            </div>
            <div class="story-panel" aria-live="polite">
                <div>
                    <h3 id="storyTitle">Stories coming soon</h3>
                    <p id="storyText">No customer-approved testimonials or success stories are currently available on this site. In the meantime, contact the team to discuss your project questions directly.</p>
                    <p id="storyAuthor"></p>
                    <div class="story-controls" id="storyControls" hidden>
                        <button type="button" id="previousStory" aria-label="Previous customer story">‹</button>
                        <span class="story-position" id="storyPosition"></span>
                        <button type="button" id="nextStory" aria-label="Next customer story">›</button>
                    </div>
                </div>
                <div>
                    <a class="button" href="<?= site_url('quote.php') ?>">Talk about your project</a>
                    <?php if (session()->get('role') === 'Customer'): ?>
                        <a class="button" href="<?= site_url('customer-review.php') ?>" style="margin-top:10px">Share your story</a>
                    <?php else: ?>
                        <a class="button" href="<?= site_url('login.php') ?>" style="margin-top:10px">Customer? Sign in to share your story</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="section" id="insights" aria-labelledby="insightsHeading">
            <div class="section-heading">
                <h2 id="insightsHeading">Solar insights</h2>
                <p>A few practical starting points for learning about solar. These are general guides, not project-specific engineering or financial advice.</p>
            </div>
            <div class="service-grid">
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">01</div>
                    <h3>Prepare for a solar consultation</h3>
                    <p>Gather your location, recent electricity usage, roof or site details, and the goals you want to discuss. An assessment can determine which details matter for your property.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">02</div>
                    <h3>Understand solar and backup power</h3>
                    <p>Solar panels and battery backup are different parts of a system. Ask whether a proposed design can supply backup power and what equipment that requires.</p>
                </article>
                <article class="feature-card">
                    <div class="feature-icon" aria-hidden="true">03</div>
                    <h3>Compare project proposals carefully</h3>
                    <p>Review the system design, equipment, installation scope, warranties, assumptions, and estimated performance with a qualified provider before deciding.</p>
                </article>
            </div>
        </section>

        <section class="section section--soft" id="accounts" aria-labelledby="accountsHeading">
            <div class="section-heading">
                <h2 id="accountsHeading">Secure account access</h2>
                <p>Customers and employees use the same secure login page. After authentication, the system sends each account to its role-appropriate area.</p>
            </div>
            <div class="account-grid">
                <article class="feature-card account-card">
                    <div class="feature-icon" aria-hidden="true">▣</div>
                    <h3>Customer account</h3>
                    <p>Sign in to access customer services. New customers can create their own account online.</p>
                    <a class="button" href="<?= site_url('login.php') ?>">Customer login</a>
                    <p style="margin: 16px 0 0;">New here? <a href="<?= site_url('register.php') ?>">Create a customer account</a>.</p>
                </article>
                <article class="feature-card account-card">
                    <div class="feature-icon" aria-hidden="true">◇</div>
                    <h3>Employee account</h3>
                    <p>Employees sign in with credentials created by HR or an administrator. Employee accounts cannot be created through public registration.</p>
                    <a class="button" href="<?= site_url('login.php') ?>">Employee login</a>
                </article>
                <article class="feature-card account-card">
                    <div class="feature-icon" aria-hidden="true">?</div>
                    <h3>Need help getting started?</h3>
                    <p>Send a quote or service inquiry and include the best way for the team to contact you.</p>
                    <a class="button" href="<?= site_url('quote.php') ?>">Contact the team</a>
                </article>
            </div>
        </section>

        <section class="section" id="faq" aria-labelledby="faqHeading">
            <div class="section-heading">
                <h2 id="faqHeading">Frequently asked questions</h2>
                <p>General information to help you start a conversation. The team can discuss details for your specific property.</p>
            </div>
            <div class="faq-grid">
                <details>
                    <summary>Will solar eliminate my electricity bill?</summary>
                    <p>Not necessarily. Results depend on your usage, system design, utility rules, weather, and whether the system includes storage. Ask for an assessment before making a decision.</p>
                </details>
                <details>
                    <summary>Can solar provide power during an outage?</summary>
                    <p>Grid-connected systems may shut down during an outage for safety. Backup power generally requires compatible battery storage and system equipment.</p>
                </details>
                <details>
                    <summary>What information should I prepare for a consultation?</summary>
                    <p>Your property location, recent electricity usage or bills, and project goals can help start a discussion. Avoid sending sensitive financial information through a general inquiry.</p>
                </details>
                <details>
                    <summary>How can I request a quote?</summary>
                    <p>Use the free quote form to share basic contact and project details. The form submits an inquiry for follow-up; it does not generate an instant price or commitment.</p>
                </details>
            </div>
        </section>

        <section class="section" id="contact" aria-label="Contact Sun Son Solar">
            <div class="contact-panel">
                <div>
                    <h2>Ready to explore solar?</h2>
                    <p>Tell us what you need and how you would like to be contacted. A quote request is an inquiry, not a confirmed price or service booking.</p>
                </div>
                <a class="button button--light" href="<?= site_url('quote.php') ?>">Request a free quote</a>
            </div>
        </section>
    </main>

    <footer class="site-footer">
        <div class="footer-inner">
            <section>
                <h2>Sun Son Solar</h2>
                <p>Consultation, installation, and maintenance information for customers exploring solar energy.</p>
            </section>
            <nav class="footer-links" aria-label="Footer navigation">
                <h3>Explore</h3>
                <a href="<?= site_url('services.php') ?>">Services</a>
                <a href="<?= site_url('products.php') ?>">Products</a>
                <a href="<?= site_url('/') ?>#faq">Frequently asked questions</a>
                <a href="<?= site_url('quote.php') ?>">Free quote request</a>
                <a href="<?= site_url('/') ?>#insights">Solar insights</a>
                <a href="<?= site_url('login.php') ?>">Customer and employee login</a>
            </nav>
            <section>
                <h3>Contact and notices</h3>
                <p>Use the <a href="<?= site_url('quote.php') ?>">inquiry form</a> to contact the team. A verified public phone number, email address, and social profiles have not been configured on this site.</p>
                <p>Review the consent notice on the quote form before submitting your information. Formal privacy and terms pages are not currently published.</p>
            </section>
        </div>
        <p class="legal-note">Solar performance, savings, and backup capability vary by property and system design. Information on this page is general and does not constitute a savings guarantee or a project-specific technical assessment. © <?= date('Y') ?> Sun Son Solar.</p>
    </footer>
    <script>
        (async function loadCustomerStories() {
            const response = await fetch('<?= site_url('customer-reviews/public') ?>');
            const result = await response.json();
            if (!response.ok || !result.success || !result.reviews.length) return;
            const reviews = result.reviews;
            let current = 0;
            const controls = document.getElementById('storyControls');
            const show = index => {
                current = (index + reviews.length) % reviews.length;
                const review = reviews[current];
                document.getElementById('storyTitle').textContent = `${review.serviceType} story`;
                document.getElementById('storyText').textContent = review.story;
                document.getElementById('storyAuthor').textContent = `— ${review.customerName}`;
                document.getElementById('storyPosition').textContent = `${current + 1} of ${reviews.length}`;
            };
            controls.hidden = false;
            document.getElementById('previousStory').addEventListener('click', () => show(current - 1));
            document.getElementById('nextStory').addEventListener('click', () => show(current + 1));
            show(0);
            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches && reviews.length > 1) {
                window.setInterval(() => show(current + 1), 8000);
            }
        })().catch(error => console.error('Could not load published customer stories.', error));
    </script>
</body>
</html>
