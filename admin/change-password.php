<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/admin-layout.php';
$user = require_admin(true);

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $password = (string) ($_POST['password'] ?? '');
    $confirmation = (string) ($_POST['password_confirmation'] ?? '');

    if (strlen($password) < 12) {
        $error = 'Use at least 12 characters for the new password.';
    } elseif ($password !== $confirmation) {
        $error = 'The two passwords do not match.';
    } elseif (strtolower($password) === 'admin') {
        $error = 'Choose a password other than the temporary password.';
    } else {
        $statement = db()->prepare('UPDATE admins SET password_hash = ?, must_change_password = 0 WHERE id = ?');
        $statement->execute([password_hash($password, PASSWORD_DEFAULT), $user['id']]);
        session_regenerate_id(true);
        flash('success', 'Your password has been updated.');
        redirect('admin/index.php');
    }
}

admin_header((int) $user['must_change_password'] === 1 ? 'Set your password' : 'Change password');
?>
<section class="auth-card">
  <p class="eyebrow-admin"><?= (int) $user['must_change_password'] === 1 ? 'First sign-in' : 'Account security' ?></p>
  <h1><?= (int) $user['must_change_password'] === 1 ? 'Create your private password' : 'Change your password' ?></h1>
  <p>Use at least 12 characters. A long, memorable phrase works well.</p>
  <?php if ($error): ?><div class="flash flash--error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="admin-form">
    <?= csrf_field() ?>
    <label>New password<input type="password" name="password" autocomplete="new-password" minlength="12" required autofocus></label>
    <label>Confirm new password<input type="password" name="password_confirmation" autocomplete="new-password" minlength="12" required></label>
    <button class="admin-button" type="submit">Save password</button>
  </form>
</section>
<?php admin_footer(); ?>

