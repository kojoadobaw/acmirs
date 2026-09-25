<?php
declare(strict_types=1);

require_once __DIR__ . '/functions.php';

function admin_header(string $title)
{
    $user = admin_user();
    $types = [
        'services' => 'Services',
        'sectors' => 'Sectors',
        'videos' => 'Videos',
        'projects' => 'Experience',
        'insights' => 'Insights',
        'mandates' => 'Hero Mandate',
    ];
    ?>
    <!doctype html>
    <html lang="en">
    <head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <title><?= e($title) ?> · ACMIRS CMS</title>
      <link rel="stylesheet" href="<?= e(url('assets/css/admin.css')) ?>">
      <script src="https://cdn.tiny.cloud/1/fcl57xlaannxwafo0cd2pzcoirwveokva1gywtxbft6e35hm/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
    </head>
    <body class="admin-body">
      <header class="admin-topbar">
        <a class="admin-brand" href="<?= e(url('admin/index.php')) ?>">
          <img src="<?= e(url('assets/brand/Website/Logo-Footer.svg')) ?>" alt="ACMIRS">
          <span>Content management</span>
        </a>
        <?php if ($user): ?>
          <nav aria-label="Account">
            <a href="<?= e(url()) ?>" target="_blank">View website</a>
            <span><?= e($user['username']) ?></span>
            <a href="<?= e(url('admin/logout.php')) ?>">Sign out</a>
          </nav>
        <?php endif; ?>
      </header>
      <?php if ($user && (int) $user['must_change_password'] === 0): ?>
        <div class="admin-shell">
          <aside class="admin-sidebar">
            <a href="<?= e(url('admin/index.php')) ?>">Overview</a>
            <?php foreach ($types as $key => $label): ?>
              <a href="<?= e(url('admin/content.php?type=' . $key)) ?>"><?= e($label) ?></a>
            <?php endforeach; ?>
            <a href="<?= e(url('admin/settings.php')) ?>">Section settings</a>
            <a href="<?= e(url('admin/change-password.php')) ?>">Change password</a>
          </aside>
          <main class="admin-main">
      <?php else: ?>
        <main class="auth-main">
      <?php endif; ?>
        <?php foreach (pull_flashes() as $message): ?>
          <div class="flash flash--<?= e($message['type']) ?>"><?= e($message['message']) ?></div>
        <?php endforeach; ?>
    <?php
}

function admin_footer()
{
    $user = admin_user();
    if ($user && (int) $user['must_change_password'] === 0) {
        echo '</main></div>';
    } else {
        echo '</main>';
    }
    ?>
    <script>
      if (window.tinymce) {
        tinymce.init({
          selector: 'textarea.rte',
          plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
          toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | numlist bullist indent outdent | emoticons charmap | removeformat',
          menubar: false,
          branding: false,
          body_class: 'detail-content',
          content_css: [
            'https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap',
            <?= json_encode(url('assets/css/cms-overrides.css')) ?>
          ]
        });
      }
    </script>
    <?php
    echo '</body></html>';
}
