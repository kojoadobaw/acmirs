<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/admin-layout.php';
require_admin();

$fields = [
    'services_title' => 'Services heading', 'services_intro' => 'Services introduction',
    'sectors_title' => 'Sectors heading', 'sectors_intro' => 'Sectors introduction',
    'videos_title' => 'Videos heading', 'experience_title' => 'Experience heading',
    'experience_intro' => 'Experience introduction', 'insights_title' => 'Insights heading',
    'insights_intro' => 'Insights introduction', 'lifecycle_title' => 'Lifecycle heading',
    'lifecycle_intro' => 'Lifecycle introduction', 'delivery_title' => 'Delivery heading',
    'delivery_intro' => 'Delivery introduction', 'cta_title' => 'Closing call-to-action heading',
    'cta_body' => 'Closing call-to-action text', 'contact_email' => 'Contact email',
    'contact_phone' => 'Contact phone',
    'metric_1_value' => 'Metric 1 value', 'metric_1_suffix' => 'Metric 1 suffix', 'metric_1_label' => 'Metric 1 label',
    'metric_2_value' => 'Metric 2 value', 'metric_2_suffix' => 'Metric 2 suffix', 'metric_2_label' => 'Metric 2 label',
    'metric_3_value' => 'Metric 3 value', 'metric_3_suffix' => 'Metric 3 suffix', 'metric_3_label' => 'Metric 3 label',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $statement = db()->prepare('INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($fields as $key => $label) {
        $statement->execute([$key, trim((string) ($_POST[$key] ?? ''))]);
    }
    flash('success', 'Section settings saved.');
    redirect('admin/settings.php');
}

$values = [];
foreach (fetch_all('SELECT setting_key, setting_value FROM site_settings') as $row) {
    $values[$row['setting_key']] = $row['setting_value'];
}

admin_header('Section settings');
?>
<div class="admin-heading"><div><p class="eyebrow-admin">Website copy</p><h1>Section settings</h1><p>Edit headings, introductions, metrics and contact details. The main hero remains fixed.</p></div></div>
<form method="post" class="admin-panel admin-form settings-grid">
  <?= csrf_field() ?>
  <?php foreach ($fields as $key => $label): ?>
    <label><?= e($label) ?>
      <?php if (strpos($key, 'intro') !== false || strpos($key, 'body') !== false): ?>
        <textarea name="<?= e($key) ?>" rows="3"><?= e($values[$key] ?? '') ?></textarea>
      <?php else: ?>
        <input name="<?= e($key) ?>" value="<?= e($values[$key] ?? '') ?>">
      <?php endif; ?>
    </label>
  <?php endforeach; ?>
  <div class="form-actions"><button class="admin-button" type="submit">Save settings</button></div>
</form>
<?php admin_footer(); ?>
