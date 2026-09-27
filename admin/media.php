<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/admin-layout.php';
require_admin();

if (($_GET['format'] ?? '') === 'json') {
    $items = fetch_all('SELECT id, filename, original_name FROM media ORDER BY created_at DESC, id DESC');
    header('Content-Type: application/json');
    echo json_encode(array_map(static function (array $item): array {
        return [
            'id' => (int) $item['id'],
            'path' => 'uploads/' . $item['filename'],
            'name' => $item['original_name'],
        ];
    }, $items), JSON_UNESCAPED_SLASHES);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (($_POST['form_action'] ?? '') === 'delete') {
        $id = (int) ($_POST['id'] ?? 0);
        $statement = db()->prepare('SELECT filename FROM media WHERE id = ?');
        $statement->execute([$id]);
        $row = $statement->fetch();
        if ($row) {
            $path = PROJECT_ROOT . '/uploads/' . $row['filename'];
            if (is_file($path)) {
                unlink($path);
            }
            db()->prepare('DELETE FROM media WHERE id = ?')->execute([$id]);
            flash('success', 'Image deleted.');
        }
        redirect('admin/media.php');
    }

    try {
        if (empty($_FILES['file']['name'])) {
            throw new RuntimeException('Choose an image to upload.');
        }
        handle_media_upload($_FILES['file']);
        flash('success', 'Image uploaded.');
        redirect('admin/media.php');
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$images = fetch_all('SELECT * FROM media ORDER BY created_at DESC, id DESC');

admin_header('Media Library');
?>
<div class="admin-heading">
  <div><p class="eyebrow-admin">Content</p><h1>Media Library</h1><p>Upload images here, then use "Choose from gallery" on any image field across the CMS to pick them.</p></div>
</div>

<section class="admin-panel">
  <?php if ($error): ?><div class="flash flash--error"><?= e($error) ?></div><?php endif; ?>
  <form method="post" enctype="multipart/form-data" class="admin-form">
    <?= csrf_field() ?>
    <label>Upload an image<input type="file" name="file" accept="image/jpeg,image/png,image/gif,image/webp" required></label>
    <div class="form-actions"><button class="admin-button" type="submit">Upload</button></div>
  </form>
</section>

<section class="admin-panel">
  <div class="panel-heading"><h2>All images</h2><span><?= count($images) ?> total</span></div>
  <div class="media-grid">
    <?php foreach ($images as $image): ?>
      <div class="media-item">
        <img src="<?= e(url('uploads/' . $image['filename'])) ?>" alt="<?= e($image['original_name']) ?>" loading="lazy">
        <div class="media-item-meta">
          <small><?= e($image['original_name']) ?></small>
          <form method="post" onsubmit="return confirm('Delete this image? This cannot be undone.');">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (int) $image['id'] ?>">
            <input type="hidden" name="form_action" value="delete">
            <button type="submit">Delete</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (!$images): ?><p>No images uploaded yet.</p><?php endif; ?>
  </div>
</section>
<?php admin_footer(); ?>
