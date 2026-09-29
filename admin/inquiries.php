<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/admin-layout.php';
require_admin();

$categoryLabels = ['general' => 'General Inquiry', 'partnership' => 'Partnership', 'careers' => 'Careers', 'media' => 'Media & Press'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['form_action'] ?? '');

    if ($action === 'delete') {
        db()->prepare('DELETE FROM inquiries WHERE id = ?')->execute([$id]);
        flash('success', 'Inquiry deleted.');
    } elseif (in_array($action, ['read', 'archived', 'new'], true)) {
        db()->prepare('UPDATE inquiries SET status = ? WHERE id = ?')->execute([$action, $id]);
        flash('success', 'Inquiry updated.');
    }
    redirect('admin/inquiries.php');
}

$inquiries = fetch_all('SELECT * FROM inquiries ORDER BY created_at DESC, id DESC');

admin_header('Inquiries');
?>
<div class="admin-heading">
  <div><p class="eyebrow-admin">Contact form</p><h1>Inquiries</h1><p>Submissions from the website's contact form. Each one is also emailed to your contact address when SMTP is configured, but this list is the source of truth.</p></div>
</div>

<section class="admin-panel">
  <div class="panel-heading"><h2>All inquiries</h2><span><?= count($inquiries) ?> total</span></div>
  <div class="content-table-wrap">
    <table class="content-table">
      <thead><tr><th>Received</th><th>From</th><th>Category</th><th>Message</th><th>Status</th><th class="actions-column">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($inquiries as $inquiry): ?>
        <tr>
          <td><?= e(date('j M Y, g:ia', strtotime($inquiry['created_at']))) ?></td>
          <td>
            <strong><?= e($inquiry['name']) ?></strong>
            <small><a href="mailto:<?= e($inquiry['email']) ?>"><?= e($inquiry['email']) ?></a><?php if ($inquiry['company']): ?> · <?= e($inquiry['company']) ?><?php endif; ?><?php if ($inquiry['phone']): ?> · <?= e($inquiry['phone']) ?><?php endif; ?></small>
          </td>
          <td><?= e($categoryLabels[$inquiry['category']] ?? ucfirst($inquiry['category'])) ?></td>
          <td>
            <details><summary><?= e(mb_strimwidth($inquiry['message'], 0, 60, '…')) ?></summary><p><?= nl2br(e($inquiry['message'])) ?></p></details>
            <?php if (!$inquiry['email_sent']): ?><small>Email notification not sent<?php if (!empty($inquiry['email_error'])): ?>: <?= e($inquiry['email_error']) ?><?php endif; ?></small><?php endif; ?>
          </td>
          <td><span class="status status--<?= $inquiry['status'] === 'new' ? '' : 'hidden' ?>"><?= e(ucfirst($inquiry['status'])) ?></span></td>
          <td class="row-actions">
            <?php if ($inquiry['status'] !== 'read'): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $inquiry['id'] ?>"><input type="hidden" name="form_action" value="read"><button type="submit">Mark read</button></form>
            <?php endif; ?>
            <?php if ($inquiry['status'] !== 'archived'): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $inquiry['id'] ?>"><input type="hidden" name="form_action" value="archived"><button type="submit">Archive</button></form>
            <?php endif; ?>
            <form method="post" onsubmit="return confirm('Delete this inquiry? This cannot be undone.');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $inquiry['id'] ?>"><input type="hidden" name="form_action" value="delete"><button type="submit">Delete</button></form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$inquiries): ?><tr><td colspan="6">No inquiries yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
<?php admin_footer(); ?>
