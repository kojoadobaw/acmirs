<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/detail-header.php';

$categories = [
    'general' => 'General Inquiry',
    'partnership' => 'Partnership',
    'careers' => 'Careers',
    'media' => 'Media & Press',
];

$error = '';
$success = false;
$values = ['category' => 'general', 'name' => '', 'company' => '', 'email' => '', 'phone' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (trim((string) ($_POST['website'] ?? '')) !== '') {
        // Honeypot field: real visitors never fill this in. Pretend success
        // without touching the database so bots get no useful signal.
        $success = true;
    } else {
        $values['category'] = (string) ($_POST['category'] ?? 'general');
        if (!isset($categories[$values['category']])) {
            $values['category'] = 'general';
        }
        $values['name'] = trim((string) ($_POST['name'] ?? ''));
        $values['company'] = trim((string) ($_POST['company'] ?? ''));
        $values['email'] = trim((string) ($_POST['email'] ?? ''));
        $values['phone'] = trim((string) ($_POST['phone'] ?? ''));
        $values['message'] = trim((string) ($_POST['message'] ?? ''));

        $validEmail = filter_var($values['email'], FILTER_VALIDATE_EMAIL);

        if ($values['name'] === '' || !$validEmail || $values['message'] === '') {
            $error = 'Please fill in your name, a valid email address, and your message.';
        } else {
            $statement = db()->prepare(
                'INSERT INTO inquiries (category, name, company, email, phone, message) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $statement->execute([
                $values['category'], $values['name'], $values['company'],
                $values['email'], $values['phone'], $values['message'],
            ]);
            $inquiryId = (int) db()->lastInsertId();

            $emailResult = send_inquiry_email([
                'category_label' => $categories[$values['category']],
                'name' => $values['name'],
                'company' => $values['company'],
                'email' => $values['email'],
                'phone' => $values['phone'],
                'message' => $values['message'],
            ]);
            db()->prepare('UPDATE inquiries SET email_sent = ?, email_error = ? WHERE id = ?')->execute([
                $emailResult['sent'] ? 1 : 0,
                $emailResult['sent'] ? null : $emailResult['error'],
                $inquiryId,
            ]);

            $success = true;
        }
    }
}

detail_header('Contact', 'Start a conversation with the ACMIRS team.', 'contact-page');
?>
<section class="collection-hero"><div class="container collection-hero-grid">
  <div><p class="editorial-kicker">Get in touch</p><h1>Let's talk<br><em>infrastructure.</em></h1></div>
  <div class="collection-intro"><span class="intro-rule" aria-hidden="true"></span><p>Tell us what you're working on.</p><p class="collection-description">Whether it's a project, a partnership, a career move or a press inquiry — we'll route it to the right person.</p></div>
</div></section>

<section class="contact-section container">
  <?php if ($success): ?>
    <div class="contact-success">
      <p class="editorial-kicker">Message sent</p>
      <h2>Thank you — we'll be in touch.</h2>
      <p>A member of the ACMIRS team will follow up shortly. In the meantime, explore our <a href="<?= e(url('index.php#experience')) ?>">experience across Africa</a>.</p>
    </div>
  <?php else: ?>
    <?php if ($error): ?><div class="public-message public-message--error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="inquiry-form" data-inquiry-form novalidate>
      <?= csrf_field() ?>
      <input type="text" name="website" value="" tabindex="-1" autocomplete="off" class="hp-field" aria-hidden="true">

      <fieldset class="form-step" data-step data-step-label="What can we help with?">
        <legend>What can we help with?</legend>
        <div class="category-grid">
          <?php foreach ($categories as $key => $label): ?>
            <label class="category-card">
              <input type="radio" name="category" value="<?= e($key) ?>" <?= $values['category'] === $key ? 'checked' : '' ?> required>
              <span><?= e($label) ?></span>
            </label>
          <?php endforeach; ?>
        </div>
      </fieldset>

      <fieldset class="form-step" data-step data-step-label="Your details">
        <legend>Your details</legend>
        <label>Full name<input type="text" name="name" value="<?= e($values['name']) ?>" required></label>
        <label>Company / organisation<input type="text" name="company" value="<?= e($values['company']) ?>"></label>
        <label>Email<input type="email" name="email" value="<?= e($values['email']) ?>" required></label>
        <label>Phone<input type="tel" name="phone" value="<?= e($values['phone']) ?>"></label>
      </fieldset>

      <fieldset class="form-step" data-step data-step-label="Your message">
        <legend>Your message</legend>
        <label>Tell us more<textarea name="message" rows="6" required><?= e($values['message']) ?></textarea></label>
      </fieldset>

      <button type="submit" class="btn btn--copper btn-large">Send message</button>
    </form>
  <?php endif; ?>
</section>
<script src="<?= e(url('assets/js/contact-form.js')) ?>"></script>
<?php detail_footer(); ?>
