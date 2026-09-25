<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/admin-layout.php';

if (admin_user()) {
    redirect('admin/index.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $statement = db()->prepare('SELECT id, password_hash, must_change_password FROM admins WHERE username = ? LIMIT 1');
        $statement->execute([$username]);
        $user = $statement->fetch();

        if (!$user || !password_verify($password, $user['password_hash'])) {
            throw new RuntimeException('The username or password is incorrect.');
        }

        session_regenerate_id(true);
        $_SESSION['admin_id'] = (int) $user['id'];
        if ((int) $user['must_change_password'] === 1) {
            redirect('admin/change-password.php');
        }
        redirect('admin/index.php');
    } catch (PDOException $exception) {
        $error = 'The CMS has not been installed yet, or the database is unavailable.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

admin_header('Sign in');
?>
<section class="auth-card">
  <img src="<?= e(url('assets/brand/Website/Logo-Header.svg')) ?>" alt="ACMIRS">
  <p class="eyebrow-admin">Secure administration</p>
  <h1>Sign in to manage content</h1>
  <?php if ($error): ?><div class="flash flash--error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" class="admin-form">
    <?= csrf_field() ?>
    <label>Username<input name="username" autocomplete="username" required autofocus></label>
    <label>Password<input type="password" name="password" autocomplete="current-password" required></label>
    <button class="admin-button" type="submit">Sign in</button>
  </form>
  <p class="auth-help">First use? <a href="<?= e(url('install.php')) ?>">Run the installer</a>. The temporary sign-in is <strong>admin / admin</strong> and must be changed immediately.</p>
</section>
<?php admin_footer(); ?>
