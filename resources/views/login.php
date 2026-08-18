<style>
/* ─── Login Shell ─────────────────────────────────────────────────────────── */
.login-shell {
    position: relative;
    display: grid;
    grid-template-columns: 1fr minmax(360px, 480px);
    min-height: 100vh;
    margin: -26px;
    overflow: hidden;
    background: #0d1f2d;
}

/* Left decorative panel */
.login-showcase {
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    padding: clamp(40px, 6vw, 80px);
    background:
        linear-gradient(160deg, rgba(13, 57, 74, 0.94) 0%, rgba(15, 40, 58, 0.88) 100%),
        radial-gradient(ellipse at 30% 60%, rgba(193, 116, 59, 0.22) 0%, transparent 55%);
    overflow: hidden;
}

.login-shell.has-login-background .login-showcase {
    background:
        linear-gradient(160deg, rgba(13, 57, 74, 0.9), rgba(15, 40, 58, 0.82)),
        var(--login-bg) center / cover no-repeat;
}

/* Subtle grid overlay */
.login-showcase::before {
    content: '';
    position: absolute;
    inset: 0;
    background-image:
        linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
    background-size: 60px 60px;
    pointer-events: none;
}

/* Large ambient circle */
.login-showcase::after {
    content: '';
    position: absolute;
    right: -120px;
    bottom: -120px;
    width: 480px;
    height: 480px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(31, 111, 139, 0.18) 0%, transparent 70%);
    pointer-events: none;
}

.login-brand {
    display: flex;
    align-items: center;
    gap: 14px;
    position: relative;
    z-index: 1;
}

.login-brand-mark {
    display: inline-grid;
    place-items: center;
    width: 52px;
    height: 52px;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(255,255,255,0.18);
    border-radius: 12px;
    backdrop-filter: blur(8px);
    flex-shrink: 0;
}

.login-brand-mark img {
    width: 36px;
    height: 36px;
    object-fit: contain;
}

.login-brand-mark-text {
    color: #fff;
    font-size: 18px;
    font-weight: 900;
    letter-spacing: -0.5px;
}

.login-brand-name {
    color: rgba(255,255,255,0.9);
    font-size: 15px;
    font-weight: 600;
    letter-spacing: 0.01em;
}

.login-brand-sub {
    color: rgba(255,255,255,0.45);
    font-size: 12px;
    margin-top: 1px;
}

/* Hero content */
.login-hero {
    position: relative;
    z-index: 1;
}

.login-hero-label {
    display: inline-block;
    margin-bottom: 20px;
    padding: 5px 12px;
    color: #f0c99a;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    background: rgba(193, 116, 59, 0.18);
    border: 1px solid rgba(193, 116, 59, 0.28);
    border-radius: 20px;
}

.login-hero h1 {
    margin: 0 0 16px;
    color: #fff;
    font-size: clamp(32px, 4.2vw, 56px);
    font-weight: 800;
    line-height: 1.06;
    letter-spacing: -0.02em;
}

.login-hero h1 em {
    font-style: normal;
    color: #f0c99a;
}

.login-hero-desc {
    color: rgba(255,255,255,0.5);
    font-size: 15px;
    line-height: 1.65;
    max-width: 460px;
    margin: 0;
}

/* Feature pills */
.login-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    position: relative;
    z-index: 1;
}

.login-pill {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(255,255,255,0.1);
    border-radius: 8px;
    backdrop-filter: blur(6px);
}

.login-pill-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #1f9e6e;
    flex-shrink: 0;
}

.login-pill strong {
    color: rgba(255,255,255,0.85);
    font-size: 13px;
    font-weight: 600;
}

.login-pill span {
    color: rgba(255,255,255,0.38);
    font-size: 13px;
}

/* ─── Right Panel ─────────────────────────────────────────────────────────── */
.login-panel {
    display: flex;
    flex-direction: column;
    justify-content: center;
    padding: clamp(32px, 5vw, 56px);
    background: #fff;
    position: relative;
    z-index: 1;
}

.login-shell.has-login-background .login-panel {
    background: rgba(255, 255, 255, 0.97);
    backdrop-filter: blur(16px);
}

.login-panel-inner {
    width: 100%;
    max-width: 380px;
    margin: auto;
}

/* Panel header */
.login-panel-eyebrow {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 10px;
    color: var(--brand, #1f6f8b);
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
}

.login-panel-eyebrow::before {
    content: '';
    display: block;
    width: 18px;
    height: 2px;
    background: currentColor;
    border-radius: 2px;
}

.login-panel-title {
    margin: 0 0 6px;
    color: #0d1f2d;
    font-size: 28px;
    font-weight: 800;
    letter-spacing: -0.03em;
    line-height: 1.1;
}

.login-panel-subtitle {
    margin: 0 0 28px;
    color: #687789;
    font-size: 14px;
    line-height: 1.6;
}

/* Alerts */
.alert-error {
    margin-bottom: 18px;
    padding: 12px 14px;
    color: #7a1f1f;
    font-size: 13.5px;
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 8px;
}

.alert-success {
    margin-bottom: 18px;
    padding: 12px 14px;
    color: #1f4d3a;
    font-size: 13.5px;
    background: #f0fdf6;
    border: 1px solid #bbf7d0;
    border-radius: 8px;
}

/* Form */
.login-form {
    display: grid;
    gap: 16px;
}

.login-form label {
    display: grid;
    gap: 6px;
}

.login-form label > span {
    color: #3d4f5d;
    font-size: 12px;
    font-weight: 700;
    letter-spacing: 0.04em;
    text-transform: uppercase;
}

.login-form input {
    width: 100%;
    height: 46px;
    padding: 0 14px;
    color: #0d1f2d;
    font-size: 14.5px;
    background: #f7fafb;
    border: 1.5px solid #dde6ed;
    border-radius: 8px;
    transition: border-color 0.15s, box-shadow 0.15s;
    outline: none;
}

.login-form input:focus {
    border-color: var(--brand, #1f6f8b);
    background: #fff;
    box-shadow: 0 0 0 3px rgba(31, 111, 139, 0.12);
}

.login-form input::placeholder {
    color: #a8b7c4;
}

.login-form button[type="submit"] {
    height: 48px;
    margin-top: 4px;
    padding: 0 20px;
    color: #fff;
    font-size: 14.5px;
    font-weight: 700;
    letter-spacing: 0.01em;
    background: var(--brand, #1f6f8b);
    border: 0;
    border-radius: 8px;
    cursor: pointer;
    transition: background 0.15s, transform 0.1s, box-shadow 0.15s;
    box-shadow: 0 4px 14px rgba(31, 111, 139, 0.3);
}

.login-form button[type="submit"]:hover {
    background: var(--brand-dark, #164d63);
    box-shadow: 0 6px 18px rgba(31, 111, 139, 0.38);
}

.login-form button[type="submit"]:active {
    transform: translateY(1px);
}

/* Demo users */
.demo-users {
    margin-top: 24px;
    padding: 14px 16px;
    background: #f7fafb;
    border: 1px solid #e8eff4;
    border-radius: 8px;
    font-size: 13px;
}

.demo-users strong {
    display: block;
    margin-bottom: 6px;
    color: #3d4f5d;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
}

.demo-users p {
    margin: 0 0 8px;
    color: #687789;
}

.demo-users code {
    padding: 1px 5px;
    color: var(--brand, #1f6f8b);
    background: rgba(31, 111, 139, 0.08);
    border-radius: 4px;
    font-size: 12px;
}

.demo-users ul {
    margin: 0;
    padding: 0;
    list-style: none;
    display: grid;
    gap: 4px;
}

.demo-users li {
    color: #4a5f6e;
    font-size: 12.5px;
    padding: 2px 0;
}

.demo-users li::before {
    content: '→ ';
    color: var(--brand, #1f6f8b);
    font-weight: 700;
}

/* Responsive */
@media (max-width: 820px) {
    .login-shell {
        grid-template-columns: 1fr;
        margin: 0;
    }
    .login-showcase {
        padding: 36px 28px;
        min-height: auto;
    }
    .login-hero h1 { font-size: 30px; }
    .login-panel {
        padding: 36px 28px;
    }
    .login-pills { display: none; }
}
</style>

<section class="login-shell <?= !empty($settings['login_background_path']) ? 'has-login-background' : '' ?>" <?php if (!empty($settings['login_background_path'])): ?>style="--login-bg: url('<?= e($settings['login_background_path']) ?>')"<?php endif; ?>>

    <!-- Left Showcase -->
    <div class="login-showcase">
        <div class="login-brand">
            <div class="login-brand-mark">
                <?php if (!empty($settings['logo_path'])): ?>
                    <img src="<?= e($settings['logo_path']) ?>" alt="<?= e($settings['school_name'] ?? 'School') ?>">
                <?php else: ?>
                    <span class="login-brand-mark-text">SP</span>
                <?php endif; ?>
            </div>
            <div>
                <div class="login-brand-name"><?= e($settings['school_name'] ?? 'Smapis School Portal') ?></div>
                <div class="login-brand-sub">School Management System</div>
            </div>
        </div>

        <div class="login-hero">
            <div class="login-hero-label">School Management</div>
            <h1>One portal for <em>every</em> role in your school</h1>
            <p class="login-hero-desc">Learning records, finance, communication, and HR — all in a single secure workspace built for African schools.</p>
        </div>

        <div class="login-pills">
            <div class="login-pill">
                <span class="login-pill-dot"></span>
                <strong>Students</strong>
                <span>Results, homework &amp; fees</span>
            </div>
            <div class="login-pill">
                <span class="login-pill-dot"></span>
                <strong>Parents</strong>
                <span>Live monitoring</span>
            </div>
            <div class="login-pill">
                <span class="login-pill-dot"></span>
                <strong>Staff</strong>
                <span>Classes &amp; operations</span>
            </div>
        </div>
    </div>

    <!-- Right Login Panel -->
    <article class="login-panel">
        <div class="login-panel-inner">
            <div class="login-panel-eyebrow">Secure Sign In</div>
            <h2 class="login-panel-title">Welcome back</h2>
            <p class="login-panel-subtitle">Enter your assigned credentials to access your portal.</p>

            <?php if ($error): ?>
                <div class="alert-error"><?= e($error) ?></div>
            <?php endif; ?>

            <?php if (!empty($_GET['installed'])): ?>
                <div class="alert-success">Installation complete — you can sign in now.</div>
            <?php endif; ?>

            <form class="login-form" method="post" action="/login">
                <label>
                    <span>Email or Username</span>
                    <input type="text" name="email" placeholder="name@school.edu" required autocomplete="username">
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" placeholder="••••••••" required autocomplete="current-password">
                </label>
                <button type="submit">Sign In</button>
            </form>

            <?php if (show_sample_login_accounts()): ?>
                <div class="demo-users">
                    <strong>Demo Access</strong>
                    <p>Password for all sample accounts: <code>password</code></p>
                    <ul>
                        <li>superadmin@school.test</li>
                        <li>admin@school.test</li>
                        <li>teacher@school.test</li>
                        <li>student_sample@school.test</li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </article>

</section>