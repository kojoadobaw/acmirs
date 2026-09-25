<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    try {
        $installed = (int) db()->query('SELECT COUNT(*) FROM admins')->fetchColumn() > 0;
        if ($installed) {
            $message = 'The ACMIRS CMS is already installed. Continue to the administrator sign-in.';
        }
    } catch (Throwable $exception) {
        // A missing database is expected before the first installation.
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $message === '') {
    verify_csrf();
    try {
        if (!preg_match('/^[A-Za-z0-9_]+$/', DB_NAME)) {
            throw new RuntimeException('The database name may contain only letters, numbers, and underscores.');
        }

        $server = server_db();
        $server->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
        $pdo = db();

        $schema = (string) file_get_contents(__DIR__ . '/database/schema.sql');
        $statements = preg_split('/;\s*(?:\r?\n|$)/', $schema) ?: [];
        foreach ($statements as $statement) {
            if (trim($statement) !== '') {
                $pdo->exec($statement);
            }
        }

        $pdo->beginTransaction();

        if ((int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() === 0) {
            $statement = $pdo->prepare('INSERT INTO admins (username, password_hash, must_change_password) VALUES (?, ?, 1)');
            $statement->execute(['admin', password_hash('admin', PASSWORD_DEFAULT)]);
        }

        $pdo->commit();
        $message = 'Installation complete. Sign in with username admin and temporary password admin. You will be required to choose a new password immediately.';
    } catch (Throwable $exception) {
        if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $error = $exception->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Install ACMIRS CMS</title>
  <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>
<body class="admin-body">
  <main class="auth-main">
    <section class="auth-card install-card">
      <img src="<?= e(url('assets/brand/Website/Logo-Header.svg')) ?>" alt="ACMIRS">
      <p class="eyebrow-admin">Initial setup</p>
      <h1>Install the ACMIRS content system</h1>
      <p>This creates the MySQL tables and the default administrator account. It is safe to run again; existing records are not overwritten.</p>
      <dl class="config-summary">
        <div><dt>Database server</dt><dd><?= e(DB_HOST . ':' . DB_PORT) ?></dd></div>
        <div><dt>Database name</dt><dd><?= e(DB_NAME) ?></dd></div>
        <div><dt>Database user</dt><dd><?= e(DB_USER) ?></dd></div>
      </dl>
      <?php if ($message): ?><div class="flash flash--success"><?= e($message) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="flash flash--error"><?= e($error) ?></div><?php endif; ?>
      <?php if ($message): ?>
        <a class="admin-button" href="<?= e(url('admin/login.php')) ?>">Continue to sign in</a>
      <?php else: ?>
        <form method="post">
          <?= csrf_field() ?>
          <button class="admin-button" type="submit">Create database and migrate content</button>
        </form>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
