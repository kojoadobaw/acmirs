<?php
declare(strict_types=1);

function detail_header(string $title, string $description = '')
{
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width,initial-scale=1">
      <meta name="description" content="<?= e($description) ?>">
      <title><?= e($title) ?> | ACMIRS</title>
      <link rel="preconnect" href="https://fonts.googleapis.com">
      <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
      <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="<?= e(url('assets/css/cms-overrides.css')) ?>">
      <link rel="icon" type="image/svg+xml" href="<?= e(url('assets/brand/Website/Favicon.svg')) ?>">
    </head>
    <body class="detail-page">
      <header class="detail-header"><a href="<?= e(url()) ?>" aria-label="ACMIRS home"><img src="<?= e(url('assets/brand/Website/Logo-Footer.svg')) ?>" alt="ACMIRS"></a><a href="<?= e(url()) ?>">← Back to website</a></header>
      <main class="detail-main">
    <?php
}

function detail_footer()
{
    ?>
      </main>
      <footer class="detail-footer">© <?= date('Y') ?> ACMIRS · Global expertise to drive Africa's infrastructure solutions.</footer>
    </body>
    </html>
    <?php
}
