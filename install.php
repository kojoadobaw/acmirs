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

        $seed = require __DIR__ . '/database/seed.php';
        $pdo->beginTransaction();

        if ((int) $pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn() === 0) {
            $statement = $pdo->prepare('INSERT INTO admins (username, password_hash, must_change_password) VALUES (?, ?, 1)');
            $statement->execute(['admin', password_hash('admin', PASSWORD_DEFAULT)]);
        }

        if ((int) $pdo->query('SELECT COUNT(*) FROM site_settings')->fetchColumn() === 0) {
            $statement = $pdo->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?)');
            foreach ($seed['settings'] as $key => $value) {
                $statement->execute([$key, $value]);
            }
        }

        if ((int) $pdo->query('SELECT COUNT(*) FROM sectors')->fetchColumn() === 0) {
            $statement = $pdo->prepare('INSERT INTO sectors (name, description, icon, sort_order) VALUES (?, ?, ?, ?)');
            foreach ($seed['sectors'] as $index => $sector) {
                $statement->execute([$sector[0], $sector[1], $sector[2], ($index + 1) * 10]);
            }
        }

        if ((int) $pdo->query('SELECT COUNT(*) FROM services')->fetchColumn() === 0) {
            $serviceStatement = $pdo->prepare('INSERT INTO services (slug, label, title, blurb, items_json, challenge, response, outcome, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
            $sectorLookup = $pdo->query('SELECT name, id FROM sectors')->fetchAll(PDO::FETCH_KEY_PAIR);
            $joinStatement = $pdo->prepare('INSERT IGNORE INTO service_sectors (service_id, sector_id) VALUES (?, ?)');
            foreach ($seed['services'] as $index => $service) {
                $serviceStatement->execute([$service[0], $service[1], $service[1], $service[2], json_encode($service[3], JSON_UNESCAPED_UNICODE), $service[4], $service[5], $service[6], ($index + 1) * 10]);
                $serviceId = (int) $pdo->lastInsertId();
                foreach ($service[7] as $sectorName) {
                    if (isset($sectorLookup[$sectorName])) {
                        $joinStatement->execute([$serviceId, $sectorLookup[$sectorName]]);
                    }
                }
            }
        }

        if ((int) $pdo->query('SELECT COUNT(*) FROM mandates')->fetchColumn() === 0) {
            $services = $pdo->query('SELECT id, slug, label FROM services ORDER BY sort_order LIMIT 6')->fetchAll();
            $statement = $pdo->prepare('INSERT INTO mandates (service_id, custom_label, sort_order) VALUES (?, ?, ?)');
            foreach ($services as $index => $service) {
                $statement->execute([$service['id'], $service['label'], ($index + 1) * 10]);
            }
        }

        if ((int) $pdo->query('SELECT COUNT(*) FROM videos')->fetchColumn() === 0) {
            $statement = $pdo->prepare('INSERT INTO videos (title, description, sort_order) VALUES (?, ?, ?)');
            foreach ($seed['videos'] as $index => $video) {
                $statement->execute([$video[0], $video[1], ($index + 1) * 10]);
            }
        }

        if ((int) $pdo->query('SELECT COUNT(*) FROM projects')->fetchColumn() === 0) {
            $statement = $pdo->prepare('INSERT INTO projects (slug, title, infrastructure_class, scale, location, sector_name, client, summary, body, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
            foreach ($seed['projects'] as $project) {
                $meta = implode(' · ', array_filter([$project[4], $project[5], $project[6]]));
                $summary = $meta ? 'Selected ACMIRS experience: ' . $meta . '.' : 'Selected ACMIRS infrastructure advisory experience.';
                $statement->execute([$project[0], $project[1], $project[2], $project[3], $project[4], $project[5], $project[6], $summary, $summary, $project[7]]);
            }
        }

        if ((int) $pdo->query('SELECT COUNT(*) FROM insights')->fetchColumn() === 0) {
            $statement = $pdo->prepare('INSERT INTO insights (slug, title, category, excerpt, body, link_label, published_at, status) VALUES (?, ?, ?, ?, ?, ?, ?, "published")');
            foreach ($seed['insights'] as $index => $insight) {
                $date = date('Y-m-d H:i:s', strtotime('2026-01-01 +' . $index . ' month'));
                $statement->execute([$insight[0], $insight[1], $insight[2], $insight[3], $insight[3] . '.', $insight[4], $date]);
            }
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
      <p>This creates the MySQL tables and migrates the current website content. It is safe to run again; existing records are not overwritten.</p>
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
