<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/functions.php';

function column_exists(PDO $pdo, string $table, string $column): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
    );
    $statement->execute([$table, $column]);
    return (int) $statement->fetchColumn() > 0;
}

function index_exists(PDO $pdo, string $table, string $index): bool
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?'
    );
    $statement->execute([$table, $index]);
    return (int) $statement->fetchColumn() > 0;
}

function row_exists(PDO $pdo, string $table, string $slug): bool
{
    $statement = $pdo->prepare('SELECT COUNT(*) FROM `' . $table . '` WHERE slug = ?');
    $statement->execute([$slug]);
    return (int) $statement->fetchColumn() > 0;
}

$message = '';
$error = '';
$log = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    try {
        $pdo = db();

        // --- Schema: sectors gains slug/body/image_path so it can have its own detail page ---
        if (!column_exists($pdo, 'sectors', 'slug')) {
            $pdo->exec('ALTER TABLE sectors ADD COLUMN slug VARCHAR(180) NULL AFTER id');
            $log[] = 'Added sectors.slug column.';
        }
        if (!column_exists($pdo, 'sectors', 'body')) {
            $pdo->exec('ALTER TABLE sectors ADD COLUMN body MEDIUMTEXT NULL AFTER description');
            $log[] = 'Added sectors.body column.';
        }
        if (!column_exists($pdo, 'sectors', 'image_path')) {
            $pdo->exec('ALTER TABLE sectors ADD COLUMN image_path VARCHAR(500) NULL AFTER body');
            $log[] = 'Added sectors.image_path column.';
        }

        $missingSlugs = $pdo->query("SELECT id, name FROM sectors WHERE slug IS NULL OR slug = ''")->fetchAll();
        foreach ($missingSlugs as $row) {
            $base = slugify((string) $row['name']);
            $candidate = $base;
            $suffix = 2;
            $check = $pdo->prepare('SELECT COUNT(*) FROM sectors WHERE slug = ? AND id != ?');
            while (true) {
                $check->execute([$candidate, $row['id']]);
                if ((int) $check->fetchColumn() === 0) {
                    break;
                }
                $candidate = $base . '-' . $suffix;
                $suffix++;
            }
            $pdo->prepare('UPDATE sectors SET slug = ? WHERE id = ?')->execute([$candidate, $row['id']]);
        }
        if ($missingSlugs) {
            $log[] = 'Backfilled slugs for ' . count($missingSlugs) . ' existing sector(s).';
        }

        if (!index_exists($pdo, 'sectors', 'sectors_slug_unique')) {
            $pdo->exec('ALTER TABLE sectors ADD UNIQUE INDEX sectors_slug_unique (slug)');
            $log[] = 'Added unique index on sectors.slug.';
        }

        // --- Schema: new staff and newsroom tables ---
        $pdo->exec("CREATE TABLE IF NOT EXISTS staff (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          slug VARCHAR(180) NOT NULL UNIQUE,
          name VARCHAR(180) NOT NULL,
          role VARCHAR(180) NOT NULL,
          image_path VARCHAR(500) NULL,
          short_bio TEXT NULL,
          bio MEDIUMTEXT NULL,
          sort_order INT NOT NULL DEFAULT 0,
          is_active TINYINT(1) NOT NULL DEFAULT 1,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX staff_active_sort (is_active, sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS newsroom (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          slug VARCHAR(180) NOT NULL UNIQUE,
          title VARCHAR(240) NOT NULL,
          category VARCHAR(60) NOT NULL DEFAULT 'press',
          excerpt TEXT NOT NULL,
          body MEDIUMTEXT NULL,
          image_path VARCHAR(500) NULL,
          location VARCHAR(180) NULL,
          closing_date DATE NULL,
          external_url VARCHAR(700) NULL,
          published_at DATETIME NOT NULL,
          status ENUM('draft', 'published') NOT NULL DEFAULT 'published',
          featured TINYINT(1) NOT NULL DEFAULT 0,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX newsroom_status_date (status, published_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS offices (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          name VARCHAR(180) NOT NULL,
          sort_order INT NOT NULL DEFAULT 0,
          is_active TINYINT(1) NOT NULL DEFAULT 1,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          INDEX offices_active_sort (is_active, sort_order)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS media (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          filename VARCHAR(255) NOT NULL,
          original_name VARCHAR(255) NOT NULL,
          mime_type VARCHAR(100) NOT NULL,
          size INT UNSIGNED NOT NULL,
          width INT UNSIGNED NULL,
          height INT UNSIGNED NULL,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          INDEX media_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $pdo->exec("CREATE TABLE IF NOT EXISTS inquiries (
          id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
          category VARCHAR(40) NOT NULL DEFAULT 'general',
          name VARCHAR(180) NOT NULL,
          company VARCHAR(180) NULL,
          email VARCHAR(254) NOT NULL,
          phone VARCHAR(60) NULL,
          message TEXT NOT NULL,
          status ENUM('new', 'read', 'archived') NOT NULL DEFAULT 'new',
          email_sent TINYINT(1) NOT NULL DEFAULT 0,
          created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
          INDEX inquiries_status_created (status, created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
        $log[] = 'Ensured staff, newsroom, offices, media, and inquiries tables exist.';

        if ((int) $pdo->query('SELECT COUNT(*) FROM offices')->fetchColumn() === 0) {
            $officeStatement = $pdo->prepare('INSERT INTO offices (name, sort_order) VALUES (?, ?)');
            foreach (['New York', 'London', 'Beijing', 'Cape Town'] as $index => $office) {
                $officeStatement->execute([$office, ($index + 1) * 10]);
            }
            $log[] = 'Seeded the existing New York/London/Beijing/Cape Town offices.';
        }

        // --- Content: rename "Digital Infrastructure" sector, add "Aviation & Aerospace Advisory" ---
        $renamed = $pdo->prepare('UPDATE sectors SET slug = ?, name = ?, description = ? WHERE slug = ?');
        $renamed->execute([
            'fibre-and-digital-infrastructure',
            'Fibre and Digital Infrastructure',
            'Telecom, broadband, fibre, data centres and digital connectivity',
            'digital-infrastructure',
        ]);
        if ($renamed->rowCount() > 0) {
            $log[] = 'Renamed "Digital Infrastructure" sector to "Fibre and Digital Infrastructure".';
        }

        if (!row_exists($pdo, 'sectors', 'aviation-aerospace-advisory')) {
            $maxSort = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM sectors')->fetchColumn();
            $pdo->prepare('INSERT INTO sectors (slug, name, description, icon, sort_order) VALUES (?, ?, ?, ?, ?)')->execute([
                'aviation-aerospace-advisory',
                'Aviation & Aerospace Advisory',
                'Airport infrastructure, aviation services and aerospace sector advisory',
                'aviation',
                $maxSort + 10,
            ]);
            $log[] = 'Added "Aviation & Aerospace Advisory" sector.';
        }

        // --- Content: correct two mislabeled projects, add three missing ones ---
        $projectInsert = $pdo->prepare(
            'INSERT INTO projects (slug, title, infrastructure_class, scale, location, sector_name, client, summary, body, sort_order)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $newProjects = [
            ['solar-power-uganda', '20MW Solar Power', 'Economic infrastructure', '20 MW', 'Uganda', 'Energy & Power', '', 15],
            ['sgr-procurement-support', 'SGR Procurement Support', 'Economic infrastructure', '', 'Uganda', 'Transport', '', 70],
            ['sez-bankable-feasibility-study', 'Special Economic Zones Bankable Feasibility Study', 'Economic infrastructure', '', 'Continental', 'Industrial & Manufacturing', '', 90],
            ['pharma-medical-supplies-ppp', 'Pharmaceutical and Medical Supplies PPP Transaction Advisory', 'Social infrastructure', '', 'Continental', 'Health', '', 110],
            ['hospital-prisons-ppp-policy-framework', 'Policy Framework Development for Hospital and Prisons Services Infrastructure PPPs', 'Social infrastructure', '', 'Continental', 'Health & Justice', '', 120],
        ];
        foreach ($newProjects as $project) {
            if (row_exists($pdo, 'projects', $project[0])) {
                continue;
            }
            $meta = implode(' · ', array_filter([$project[4], $project[5], $project[6]]));
            $summary = $meta ? 'Selected ACMIRS experience: ' . $meta . '.' : 'Selected ACMIRS infrastructure advisory experience.';
            $projectInsert->execute([$project[0], $project[1], $project[2], $project[3], $project[4], $project[5], $project[6], $summary, $summary, $project[7]]);
            $log[] = 'Added project "' . $project[1] . '".';
        }

        $obsoleteSlugs = ['geothermal-power-development' => 'solar-power-uganda', 'scr-procurement-support' => 'sgr-procurement-support'];
        $deleteObsolete = $pdo->prepare('DELETE FROM projects WHERE slug = ?');
        foreach ($obsoleteSlugs as $oldSlug => $newSlug) {
            if (row_exists($pdo, 'projects', $oldSlug) && row_exists($pdo, 'projects', $newSlug)) {
                $deleteObsolete->execute([$oldSlug]);
                $log[] = 'Replaced mislabeled project "' . $oldSlug . '" with "' . $newSlug . '".';
            }
        }

        $pdo->prepare("UPDATE projects SET sector_name = 'Fibre and Digital Infrastructure' WHERE slug = 'digital-connectivity-acceleration' AND sector_name = 'Digital Infrastructure'")->execute();

        // --- Reorder Experience Across Africa: economic, then quasi-economic, then social ---
        $sortOrder = [
            'solar-power-uganda' => 10, 'ppp-geothermal-kenya' => 20, 'oil-refinery-uganda' => 30,
            'africa-power-interconnection' => 40, 'railways-diagnostic-study' => 50,
            'digital-connectivity-acceleration' => 60, 'sgr-procurement-support' => 70,
            'copper-kilembe-mines' => 80, 'sez-bankable-feasibility-study' => 90,
            'national-irrigation-master-plan' => 100, 'pharma-medical-supplies-ppp' => 110,
            'hospital-prisons-ppp-policy-framework' => 120,
        ];
        $sortStatement = $pdo->prepare('UPDATE projects SET sort_order = ? WHERE slug = ?');
        foreach ($sortOrder as $slug => $order) {
            $sortStatement->execute([$order, $slug]);
        }
        $log[] = 'Reordered Experience Across Africa (economic, quasi-economic, social).';

        $message = 'Migration complete.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>ACMIRS CMS — Content Update</title>
  <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
</head>
<body class="admin-body">
  <main class="auth-main">
    <section class="auth-card install-card">
      <img src="<?= e(url('assets/brand/Website/Logo-Header.svg')) ?>" alt="ACMIRS">
      <p class="eyebrow-admin">One-time content update</p>
      <h1>Sectors, Experience &amp; new content types</h1>
      <p>Adds sector detail pages, the Staff and Newsroom sections, and applies the sector/experience content fixes. Safe to run more than once.</p>
      <?php if ($message): ?><div class="flash flash--success"><?= e($message) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="flash flash--error"><?= e($error) ?></div><?php endif; ?>
      <?php if ($log): ?>
        <ul class="config-summary">
          <?php foreach ($log as $entry): ?><li><?= e($entry) ?></li><?php endforeach; ?>
        </ul>
      <?php endif; ?>
      <?php if ($message): ?>
        <a class="admin-button" href="<?= e(url('admin/content.php?type=sectors')) ?>">Go to Sectors</a>
      <?php else: ?>
        <form method="post">
          <?= csrf_field() ?>
          <button class="admin-button" type="submit">Run content update</button>
        </form>
      <?php endif; ?>
    </section>
  </main>
</body>
</html>
