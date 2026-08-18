<?php require __DIR__ . '/partials/header.php'; ?>
<main id="main-content">
    <section class="website-pro-page-hero"><span class="website-pro-eyebrow">We would love to hear from you</span><h1><?= e($sections['contact_title'] ?? 'Contact Our School') ?></h1><p><?= e($sections['contact_intro'] ?? 'Send us a message or contact the school office directly.') ?></p></section>
    <section class="website-pro-contact-page">
        <aside><span class="website-pro-eyebrow">School information</span><h2>Let us answer your questions.</h2><p>Our school office is available to help with admissions, visits, enrolment and general enquiries.</p><dl><?php if (!empty($sections['contact_address'])): ?><div><dt>Address</dt><dd><?= e($sections['contact_address']) ?></dd></div><?php endif; ?><?php if (!empty($sections['contact_phone'])): ?><div><dt>Telephone</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', $sections['contact_phone'])) ?>"><?= e($sections['contact_phone']) ?></a></dd></div><?php endif; ?><?php if (!empty($sections['contact_email'])): ?><div><dt>Email</dt><dd><a href="mailto:<?= e($sections['contact_email']) ?>"><?= e($sections['contact_email']) ?></a></dd></div><?php endif; ?><?php if (!empty($sections['opening_hours'])): ?><div><dt>Office hours</dt><dd><?= e($sections['opening_hours']) ?></dd></div><?php endif; ?></dl></aside>
        <form class="website-pro-contact-form" method="post" action="/website/contact-submit">
            <?= csrf_field() ?>
            <div><span class="website-pro-eyebrow">Send an enquiry</span><h2>How can we help?</h2></div>
            <?php if (!empty($success)): ?><div class="alert-success" role="status"><?= e($success) ?></div><?php endif; ?>
            <?php if (!empty($error)): ?><div class="alert-error" role="alert"><?= e($error) ?></div><?php endif; ?>
            <label><span>Full name</span><input name="full_name" autocomplete="name" required></label>
            <div class="website-pro-form-row"><label><span>Email address</span><input type="email" name="email" autocomplete="email" required></label><label><span>Phone number</span><input name="phone" autocomplete="tel"></label></div>
            <label><span>Subject</span><input name="subject"></label>
            <label><span>Your message</span><textarea name="message" rows="6" required></textarea></label>
            <button class="website-pro-button website-pro-button-primary" type="submit">Send message <span aria-hidden="true">→</span></button>
        </form>
    </section>
</main>
<?php require __DIR__ . '/partials/footer.php'; ?>
