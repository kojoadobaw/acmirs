<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/admin-layout.php';
require_admin();

function content_definitions(): array
{
    return [
        'services' => [
            'label' => 'Services', 'singular' => 'service', 'title_column' => 'title',
            'description' => 'Controls the service finder on the homepage.',
            'fields' => [
                'slug' => ['label' => 'Slug', 'type' => 'text', 'help' => 'Leave blank to generate from the title.'],
                'label' => ['label' => 'Tab label', 'type' => 'text', 'required' => true],
                'label_fr' => ['label' => 'Tab label (French)', 'type' => 'text'],
                'label_es' => ['label' => 'Tab label (Spanish)', 'type' => 'text'],
                'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
                'title_fr' => ['label' => 'Title (French)', 'type' => 'text'],
                'title_es' => ['label' => 'Title (Spanish)', 'type' => 'text'],
                'blurb' => ['label' => 'Introduction', 'type' => 'textarea', 'required' => true],
                'blurb_fr' => ['label' => 'Introduction (French)', 'type' => 'textarea'],
                'blurb_es' => ['label' => 'Introduction (Spanish)', 'type' => 'textarea'],
                'items_json' => ['label' => 'Capabilities', 'type' => 'lines', 'required' => true, 'help' => 'One item per line.'],
                'items_json_fr' => ['label' => 'Capabilities (French)', 'type' => 'lines', 'help' => 'One item per line.'],
                'items_json_es' => ['label' => 'Capabilities (Spanish)', 'type' => 'lines', 'help' => 'One item per line.'],
                'challenge' => ['label' => 'Client challenge', 'type' => 'textarea', 'required' => true],
                'challenge_fr' => ['label' => 'Client challenge (French)', 'type' => 'textarea'],
                'challenge_es' => ['label' => 'Client challenge (Spanish)', 'type' => 'textarea'],
                'response' => ['label' => 'ACMIRS response', 'type' => 'textarea', 'required' => true],
                'response_fr' => ['label' => 'ACMIRS response (French)', 'type' => 'textarea'],
                'response_es' => ['label' => 'ACMIRS response (Spanish)', 'type' => 'textarea'],
                'outcome' => ['label' => 'Client outcome', 'type' => 'textarea', 'required' => true],
                'outcome_fr' => ['label' => 'Client outcome (French)', 'type' => 'textarea'],
                'outcome_es' => ['label' => 'Client outcome (Spanish)', 'type' => 'textarea'],
                'sector_ids' => ['label' => 'Relevant sectors', 'type' => 'sectors', 'virtual' => true, 'help' => 'Hold Ctrl or Command to select more than one.'],
                'sort_order' => ['label' => 'Display order', 'type' => 'number'],
                'is_active' => ['label' => 'Visible on website', 'type' => 'checkbox'],
            ],
        ],
        'sectors' => [
            'label' => 'Sectors', 'singular' => 'sector', 'title_column' => 'name',
            'description' => 'Controls the sector cards, sector detail pages, and service relationships.',
            'fields' => [
                'slug' => ['label' => 'Slug', 'type' => 'text', 'help' => 'Leave blank to generate from the name. Used in the sector page URL.'],
                'name' => ['label' => 'Name', 'type' => 'text', 'required' => true],
                'name_fr' => ['label' => 'Name (French)', 'type' => 'text'],
                'name_es' => ['label' => 'Name (Spanish)', 'type' => 'text'],
                'description' => ['label' => 'Short description', 'type' => 'textarea', 'required' => true, 'help' => 'Shown on the homepage sector card.'],
                'description_fr' => ['label' => 'Short description (French)', 'type' => 'textarea'],
                'description_es' => ['label' => 'Short description (Spanish)', 'type' => 'textarea'],
                'body' => ['label' => 'Sector detail content', 'type' => 'richtext', 'help' => 'Formatted content shown on the sector detail page.'],
                'body_fr' => ['label' => 'Sector detail content (French)', 'type' => 'richtext'],
                'body_es' => ['label' => 'Sector detail content (Spanish)', 'type' => 'richtext'],
                'image_path' => ['label' => 'Image', 'type' => 'image', 'help' => 'Optional hero image for the sector detail page.'],
                'icon' => ['label' => 'Icon', 'type' => 'select', 'options' => ['energy' => 'Energy', 'transport' => 'Transport', 'water' => 'Water', 'oil-gas' => 'Oil & Gas', 'marine' => 'Marine', 'urban' => 'Urban', 'digital' => 'Digital', 'industry' => 'Industrial', 'agriculture' => 'Agriculture', 'aviation' => 'Aviation', 'default' => 'General']],
                'sort_order' => ['label' => 'Display order', 'type' => 'number'],
                'is_active' => ['label' => 'Visible on website', 'type' => 'checkbox'],
            ],
        ],
        'videos' => [
            'label' => 'Infrastructure videos', 'singular' => 'video', 'title_column' => 'title',
            'description' => 'Add a YouTube, Vimeo, direct MP4, or other public video URL.',
            'fields' => [
                'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
                'title_fr' => ['label' => 'Title (French)', 'type' => 'text'],
                'title_es' => ['label' => 'Title (Spanish)', 'type' => 'text'],
                'description' => ['label' => 'Description', 'type' => 'textarea'],
                'description_fr' => ['label' => 'Description (French)', 'type' => 'textarea'],
                'description_es' => ['label' => 'Description (Spanish)', 'type' => 'textarea'],
                'video_url' => ['label' => 'Video URL', 'type' => 'url', 'help' => 'Leave empty to retain a labelled placeholder.'],
                'thumbnail_path' => ['label' => 'Thumbnail', 'type' => 'image', 'help' => 'Optional.'],
                'sort_order' => ['label' => 'Display order', 'type' => 'number'],
                'is_active' => ['label' => 'Visible on website', 'type' => 'checkbox'],
            ],
        ],
        'projects' => [
            'label' => 'Experience and projects', 'singular' => 'project', 'title_column' => 'title',
            'description' => 'Selected mandates shown in Experience Across Africa, with optional detail pages.',
            'fields' => [
                'slug' => ['label' => 'Slug', 'type' => 'text', 'help' => 'Leave blank to generate from the title.'],
                'title' => ['label' => 'Project title', 'type' => 'text', 'required' => true],
                'title_fr' => ['label' => 'Project title (French)', 'type' => 'text'],
                'title_es' => ['label' => 'Project title (Spanish)', 'type' => 'text'],
                'infrastructure_class' => ['label' => 'Infrastructure class', 'type' => 'text', 'required' => true],
                'infrastructure_class_fr' => ['label' => 'Infrastructure class (French)', 'type' => 'text'],
                'infrastructure_class_es' => ['label' => 'Infrastructure class (Spanish)', 'type' => 'text'],
                'scale' => ['label' => 'Scale', 'type' => 'text', 'help' => 'Examples: 140 MW or 60,000 BOPD.'],
                'scale_fr' => ['label' => 'Scale (French)', 'type' => 'text'],
                'scale_es' => ['label' => 'Scale (Spanish)', 'type' => 'text'],
                'location' => ['label' => 'Location', 'type' => 'text'],
                'location_fr' => ['label' => 'Location (French)', 'type' => 'text'],
                'location_es' => ['label' => 'Location (Spanish)', 'type' => 'text'],
                'sector_name' => ['label' => 'Sector', 'type' => 'text'],
                'sector_name_fr' => ['label' => 'Sector (French)', 'type' => 'text'],
                'sector_name_es' => ['label' => 'Sector (Spanish)', 'type' => 'text'],
                'client' => ['label' => 'Client / partner', 'type' => 'text'],
                'client_fr' => ['label' => 'Client / partner (French)', 'type' => 'text'],
                'client_es' => ['label' => 'Client / partner (Spanish)', 'type' => 'text'],
                'summary' => ['label' => 'Summary', 'type' => 'textarea'],
                'summary_fr' => ['label' => 'Summary (French)', 'type' => 'textarea'],
                'summary_es' => ['label' => 'Summary (Spanish)', 'type' => 'textarea'],
                'body' => ['label' => 'Project detail', 'type' => 'richtext', 'rows' => 14, 'help' => 'Formatted content shown on the project detail page.'],
                'body_fr' => ['label' => 'Project detail (French)', 'type' => 'richtext', 'rows' => 14],
                'body_es' => ['label' => 'Project detail (Spanish)', 'type' => 'richtext', 'rows' => 14],
                'image_path' => ['label' => 'Image', 'type' => 'image'],
                'sort_order' => ['label' => 'Display order', 'type' => 'number'],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['published' => 'Published', 'draft' => 'Draft']],
            ],
        ],
        'insights' => [
            'label' => 'Insights', 'singular' => 'insight', 'title_column' => 'title',
            'description' => 'Articles, research, podcasts and other thought leadership.',
            'fields' => [
                'slug' => ['label' => 'Slug', 'type' => 'text', 'help' => 'Leave blank to generate from the title.'],
                'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
                'title_fr' => ['label' => 'Title (French)', 'type' => 'text'],
                'title_es' => ['label' => 'Title (Spanish)', 'type' => 'text'],
                'category' => ['label' => 'Category', 'type' => 'text', 'required' => true],
                'category_fr' => ['label' => 'Category (French)', 'type' => 'text'],
                'category_es' => ['label' => 'Category (Spanish)', 'type' => 'text'],
                'excerpt' => ['label' => 'Short summary', 'type' => 'textarea', 'required' => true],
                'excerpt_fr' => ['label' => 'Short summary (French)', 'type' => 'textarea'],
                'excerpt_es' => ['label' => 'Short summary (Spanish)', 'type' => 'textarea'],
                'body' => ['label' => 'Article content', 'type' => 'richtext', 'rows' => 14, 'help' => 'Formatted content shown on the insight detail page.'],
                'body_fr' => ['label' => 'Article content (French)', 'type' => 'richtext', 'rows' => 14],
                'body_es' => ['label' => 'Article content (Spanish)', 'type' => 'richtext', 'rows' => 14],
                'image_path' => ['label' => 'Image', 'type' => 'image'],
                'link_label' => ['label' => 'Link label', 'type' => 'text'],
                'link_label_fr' => ['label' => 'Link label (French)', 'type' => 'text'],
                'link_label_es' => ['label' => 'Link label (Spanish)', 'type' => 'text'],
                'published_at' => ['label' => 'Publication date', 'type' => 'datetime-local', 'required' => true],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['published' => 'Published', 'draft' => 'Draft']],
                'featured' => ['label' => 'Feature this insight', 'type' => 'checkbox'],
            ],
        ],
        'mandates' => [
            'label' => 'The ACMIRS Mandate menu', 'singular' => 'mandate item', 'title_column' => 'custom_label',
            'description' => 'Controls the menu shown inside the hero. Each item links to a service.',
            'fields' => [
                'service_id' => ['label' => 'Linked service', 'type' => 'services', 'required' => true],
                'custom_label' => ['label' => 'Menu label', 'type' => 'text', 'required' => true],
                'custom_label_fr' => ['label' => 'Menu label (French)', 'type' => 'text'],
                'custom_label_es' => ['label' => 'Menu label (Spanish)', 'type' => 'text'],
                'sort_order' => ['label' => 'Display order', 'type' => 'number'],
                'is_active' => ['label' => 'Visible in hero', 'type' => 'checkbox'],
            ],
        ],
        'testimonials' => [
            'label' => 'Testimonials', 'singular' => 'testimonial', 'title_column' => 'author_name',
            'description' => 'Client quotes shown as social proof on the homepage.',
            'fields' => [
                'quote' => ['label' => 'Quote', 'type' => 'textarea', 'required' => true],
                'quote_fr' => ['label' => 'Quote (French)', 'type' => 'textarea'],
                'quote_es' => ['label' => 'Quote (Spanish)', 'type' => 'textarea'],
                'author_name' => ['label' => 'Author name', 'type' => 'text', 'required' => true],
                'author_title' => ['label' => 'Author title', 'type' => 'text'],
                'author_title_fr' => ['label' => 'Author title (French)', 'type' => 'text'],
                'author_title_es' => ['label' => 'Author title (Spanish)', 'type' => 'text'],
                'company' => ['label' => 'Company', 'type' => 'text'],
                'logo_path' => ['label' => 'Company logo', 'type' => 'image', 'help' => 'Optional.'],
                'sort_order' => ['label' => 'Display order', 'type' => 'number'],
                'is_active' => ['label' => 'Visible on website', 'type' => 'checkbox'],
            ],
        ],
        'offices' => [
            'label' => 'Offices', 'singular' => 'office', 'title_column' => 'name',
            'description' => 'Controls the office locations shown in the footer.',
            'fields' => [
                'name' => ['label' => 'City', 'type' => 'text', 'required' => true],
                'sort_order' => ['label' => 'Display order', 'type' => 'number'],
                'is_active' => ['label' => 'Visible on website', 'type' => 'checkbox'],
            ],
        ],
        'staff' => [
            'label' => 'Our Team', 'singular' => 'team member', 'title_column' => 'name',
            'description' => 'Key staff shown on the team page, each with their own detail page.',
            'fields' => [
                'slug' => ['label' => 'Slug', 'type' => 'text', 'help' => 'Leave blank to generate from the name.'],
                'name' => ['label' => 'Name', 'type' => 'text', 'required' => true],
                'role' => ['label' => 'Title / role', 'type' => 'text', 'required' => true],
                'role_fr' => ['label' => 'Title / role (French)', 'type' => 'text'],
                'role_es' => ['label' => 'Title / role (Spanish)', 'type' => 'text'],
                'image_path' => ['label' => 'Photo', 'type' => 'image'],
                'short_bio' => ['label' => 'Short summary', 'type' => 'textarea', 'help' => 'Shown on the team listing page.'],
                'short_bio_fr' => ['label' => 'Short summary (French)', 'type' => 'textarea'],
                'short_bio_es' => ['label' => 'Short summary (Spanish)', 'type' => 'textarea'],
                'bio' => ['label' => 'Full biography', 'type' => 'richtext', 'rows' => 14, 'help' => 'Qualifications, expertise and leadership roles — shown on the detail page.'],
                'bio_fr' => ['label' => 'Full biography (French)', 'type' => 'richtext', 'rows' => 14],
                'bio_es' => ['label' => 'Full biography (Spanish)', 'type' => 'richtext', 'rows' => 14],
                'sort_order' => ['label' => 'Display order', 'type' => 'number'],
                'is_active' => ['label' => 'Visible on website', 'type' => 'checkbox'],
            ],
        ],
        'newsroom' => [
            'label' => 'Newsroom', 'singular' => 'newsroom item', 'title_column' => 'title',
            'description' => 'Careers, continental opportunities, bids for partnerships, and press.',
            'fields' => [
                'slug' => ['label' => 'Slug', 'type' => 'text', 'help' => 'Leave blank to generate from the title.'],
                'title' => ['label' => 'Title', 'type' => 'text', 'required' => true],
                'title_fr' => ['label' => 'Title (French)', 'type' => 'text'],
                'title_es' => ['label' => 'Title (Spanish)', 'type' => 'text'],
                'category' => ['label' => 'Category', 'type' => 'select', 'options' => ['career' => 'Career', 'bid' => 'Bid / Tender', 'partnership' => 'Partnership', 'press' => 'Press'], 'help' => 'The category label shown on the site is translated automatically; this choice does not need separate French/Spanish text.'],
                'excerpt' => ['label' => 'Short summary', 'type' => 'textarea', 'required' => true],
                'excerpt_fr' => ['label' => 'Short summary (French)', 'type' => 'textarea'],
                'excerpt_es' => ['label' => 'Short summary (Spanish)', 'type' => 'textarea'],
                'body' => ['label' => 'Full detail', 'type' => 'richtext', 'rows' => 14],
                'body_fr' => ['label' => 'Full detail (French)', 'type' => 'richtext', 'rows' => 14],
                'body_es' => ['label' => 'Full detail (Spanish)', 'type' => 'richtext', 'rows' => 14],
                'image_path' => ['label' => 'Image', 'type' => 'image'],
                'location' => ['label' => 'Location', 'type' => 'text'],
                'location_fr' => ['label' => 'Location (French)', 'type' => 'text'],
                'location_es' => ['label' => 'Location (Spanish)', 'type' => 'text'],
                'closing_date' => ['label' => 'Closing date', 'type' => 'date', 'help' => 'Optional application or bid deadline.'],
                'external_url' => ['label' => 'External application / bid link', 'type' => 'url', 'help' => 'Optional. Shown as a call-to-action on the detail page.'],
                'published_at' => ['label' => 'Publication date', 'type' => 'datetime-local', 'required' => true],
                'status' => ['label' => 'Status', 'type' => 'select', 'options' => ['published' => 'Published', 'draft' => 'Draft']],
                'featured' => ['label' => 'Feature this item', 'type' => 'checkbox'],
            ],
        ],
    ];
}

$definitions = content_definitions();
$type = (string) ($_GET['type'] ?? '');
if (!isset($definitions[$type])) {
    http_response_code(404);
    exit('Unknown content collection.');
}
$definition = $definitions[$type];
$table = $type;
$action = (string) ($_GET['action'] ?? 'list');
$id = max(0, (int) ($_GET['id'] ?? $_POST['id'] ?? 0));
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    if (($_POST['form_action'] ?? '') === 'delete') {
        $statement = db()->prepare('DELETE FROM `' . $table . '` WHERE id = ?');
        $statement->execute([$id]);
        flash('success', ucfirst($definition['singular']) . ' deleted.');
        redirect('admin/content.php?type=' . $type);
    }

    try {
        $columns = [];
        $values = [];
        foreach ($definition['fields'] as $column => $field) {
            if (!empty($field['virtual'])) {
                continue;
            }
            if ($field['type'] === 'checkbox') {
                $value = isset($_POST[$column]) ? 1 : 0;
            } else {
                $value = trim((string) ($_POST[$column] ?? ''));
            }
            if ($field['type'] === 'lines') {
                $lines = preg_split('/\R/', (string) $value) ?: [];
                $value = json_encode(array_values(array_filter(array_map('trim', $lines), static function ($line) {
                    return $line !== '';
                })), JSON_UNESCAPED_UNICODE);
            }
            if ($field['type'] === 'datetime-local') {
                $value = str_replace('T', ' ', (string) $value);
                if (strlen($value) === 16) {
                    $value .= ':00';
                }
            }
            if ($field['type'] === 'richtext') {
                $value = sanitize_html((string) $value);
            }
            if ($field['type'] === 'date' && $value === '') {
                $value = null;
            }
            if (!empty($field['required']) && $value === '') {
                throw new RuntimeException($field['label'] . ' is required.');
            }
            $columns[] = $column;
            $values[] = $value;
        }

        if (in_array('slug', $columns, true)) {
            $position = array_search('slug', $columns, true);
            if ($values[$position] === '') {
                $source = (string) ($_POST['title'] ?? $_POST['label'] ?? $_POST['name'] ?? 'item');
                $values[$position] = slugify($source);
            } else {
                $values[$position] = slugify((string) $values[$position]);
            }
        }

        if ($id > 0) {
            $assignments = implode(', ', array_map(static function ($column) {
                return '`' . $column . '` = ?';
            }, $columns));
            $statement = db()->prepare('UPDATE `' . $table . '` SET ' . $assignments . ' WHERE id = ?');
            $statement->execute(array_merge($values, [$id]));
        } else {
            $quoted = implode(', ', array_map(static function ($column) {
                return '`' . $column . '`';
            }, $columns));
            $placeholders = implode(', ', array_fill(0, count($columns), '?'));
            $statement = db()->prepare('INSERT INTO `' . $table . '` (' . $quoted . ') VALUES (' . $placeholders . ')');
            $statement->execute($values);
            $id = (int) db()->lastInsertId();
        }

        if ($type === 'services') {
            $selectedSectors = array_map('intval', (array) ($_POST['sector_ids'] ?? []));
            db()->prepare('DELETE FROM service_sectors WHERE service_id = ?')->execute([$id]);
            $join = db()->prepare('INSERT INTO service_sectors (service_id, sector_id) VALUES (?, ?)');
            foreach (array_unique($selectedSectors) as $sectorId) {
                if ($sectorId > 0) {
                    $join->execute([$id, $sectorId]);
                }
            }
        }

        flash('success', ucfirst($definition['singular']) . ' saved.');
        redirect('admin/content.php?type=' . $type . '&action=edit&id=' . $id);
    } catch (PDOException $exception) {
        $error = strpos($exception->getMessage(), 'Duplicate') !== false ? 'That slug is already in use. Choose a unique slug.' : 'The record could not be saved. Please review the fields and try again.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$record = [];
if ($action === 'edit' && $id > 0) {
    $statement = db()->prepare('SELECT * FROM `' . $table . '` WHERE id = ?');
    $statement->execute([$id]);
    $record = $statement->fetch() ?: [];
    if (!$record) {
        http_response_code(404);
        exit('Record not found.');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $error) {
    foreach ($definition['fields'] as $column => $field) {
        if ($field['type'] === 'checkbox') {
            $record[$column] = isset($_POST[$column]) ? 1 : 0;
        } elseif ($field['type'] === 'sectors') {
            $record[$column] = (array) ($_POST[$column] ?? []);
        } else {
            $record[$column] = (string) ($_POST[$column] ?? '');
        }
    }
}

$selectedSectorIds = [];
if ($type === 'services' && $id > 0 && !$error) {
    $statement = db()->prepare('SELECT sector_id FROM service_sectors WHERE service_id = ?');
    $statement->execute([$id]);
    $selectedSectorIds = array_map('intval', $statement->fetchAll(PDO::FETCH_COLUMN));
} elseif ($type === 'services' && isset($record['sector_ids'])) {
    $selectedSectorIds = array_map('intval', (array) $record['sector_ids']);
}

$sectorOptions = $type === 'services' ? fetch_all('SELECT id, name FROM sectors ORDER BY sort_order, name') : [];
$serviceOptions = $type === 'mandates' ? fetch_all('SELECT id, label FROM services WHERE is_active = 1 ORDER BY sort_order, label') : [];
$orderBy = in_array($type, ['insights', 'newsroom'], true) ? 'published_at DESC, id DESC' : 'sort_order ASC, id DESC';
$records = fetch_all('SELECT * FROM `' . $table . '` ORDER BY ' . $orderBy);

admin_header($definition['label']);
?>
<div class="admin-heading">
  <div><p class="eyebrow-admin">Content collection</p><h1><?= e($definition['label']) ?></h1><p><?= e($definition['description']) ?></p></div>
  <?php if (!in_array($action, ['new', 'edit'], true)): ?><a class="admin-button" href="<?= e(url('admin/content.php?type=' . $type . '&action=new')) ?>">Add <?= e($definition['singular']) ?></a><?php endif; ?>
</div>

<?php if (in_array($action, ['new', 'edit'], true)): ?>
  <section class="admin-panel editor-panel">
    <div class="panel-heading"><h2><?= $action === 'edit' ? 'Edit' : 'Add' ?> <?= e($definition['singular']) ?></h2><a href="<?= e(url('admin/content.php?type=' . $type)) ?>">Close editor</a></div>
    <?php if ($error): ?><div class="flash flash--error"><?= e($error) ?></div><?php endif; ?>
    <form method="post" class="admin-form content-form">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= $id ?>">
      <input type="hidden" name="form_action" value="save">
      <?php foreach ($definition['fields'] as $column => $field): ?>
        <?php
          $value = $record[$column] ?? '';
          if ($field['type'] === 'lines' && $value !== '') {
              $decoded = json_decode((string) $value, true);
              $value = is_array($decoded) ? implode("\n", $decoded) : $value;
          }
          if ($field['type'] === 'datetime-local' && $value !== '') {
              $value = date('Y-m-d\TH:i', strtotime((string) $value));
          }
          if ($action === 'new' && $field['type'] === 'checkbox' && !array_key_exists($column, $record)) {
              $value = 1;
          }
          if ($action === 'new' && $column === 'published_at' && $value === '') {
              $value = date('Y-m-d\TH:i');
          }
          if ($action === 'new' && $column === 'status' && $value === '') {
              $value = 'published';
          }
        ?>
        <?php if ($field['type'] === 'checkbox'): ?>
          <label class="checkbox-field"><input type="checkbox" name="<?= e($column) ?>" value="1" <?= (int) $value === 1 ? 'checked' : '' ?>><span><?= e($field['label']) ?></span></label>
        <?php elseif ($field['type'] === 'richtext'): ?>
          <label><?= e($field['label']) ?><textarea class="rte" name="<?= e($column) ?>" rows="<?= (int) ($field['rows'] ?? 12) ?>"><?= e((string) $value) ?></textarea><?php if (!empty($field['help'])): ?><small><?= e($field['help']) ?></small><?php endif; ?></label>
        <?php elseif ($field['type'] === 'image'): ?>
          <?php
            $inputId = 'field-' . $column;
            $previewSrc = $value !== '' ? (preg_match('~^https?://~i', (string) $value) ? (string) $value : url((string) $value)) : '';
          ?>
          <label><?= e($field['label']) ?>
            <div class="image-field">
              <input type="text" id="<?= e($inputId) ?>" name="<?= e($column) ?>" value="<?= e((string) $value) ?>" placeholder="Choose from gallery, or paste a path/URL">
              <button type="button" class="button-secondary" data-media-picker="<?= e($inputId) ?>">Choose from gallery</button>
            </div>
            <img class="image-field-preview" data-preview-for="<?= e($inputId) ?>" src="<?= e($previewSrc) ?>" alt="" style="<?= $previewSrc === '' ? 'display:none' : '' ?>">
            <?php if (!empty($field['help'])): ?><small><?= e($field['help']) ?></small><?php endif; ?>
          </label>
        <?php elseif ($field['type'] === 'textarea' || $field['type'] === 'lines'): ?>
          <label><?= e($field['label']) ?><textarea name="<?= e($column) ?>" rows="<?= (int) ($field['rows'] ?? 5) ?>" <?= !empty($field['required']) ? 'required' : '' ?>><?= e((string) $value) ?></textarea><?php if (!empty($field['help'])): ?><small><?= e($field['help']) ?></small><?php endif; ?></label>
        <?php elseif ($field['type'] === 'select'): ?>
          <label><?= e($field['label']) ?><select name="<?= e($column) ?>"><?php foreach ($field['options'] as $optionValue => $optionLabel): ?><option value="<?= e((string) $optionValue) ?>" <?= (string) $value === (string) $optionValue ? 'selected' : '' ?>><?= e($optionLabel) ?></option><?php endforeach; ?></select></label>
        <?php elseif ($field['type'] === 'sectors'): ?>
          <label><?= e($field['label']) ?><select name="sector_ids[]" multiple size="7"><?php foreach ($sectorOptions as $option): ?><option value="<?= (int) $option['id'] ?>" <?= in_array((int) $option['id'], $selectedSectorIds, true) ? 'selected' : '' ?>><?= e($option['name']) ?></option><?php endforeach; ?></select><?php if (!empty($field['help'])): ?><small><?= e($field['help']) ?></small><?php endif; ?></label>
        <?php elseif ($field['type'] === 'services'): ?>
          <label><?= e($field['label']) ?><select name="<?= e($column) ?>" required><option value="">Select a service</option><?php foreach ($serviceOptions as $option): ?><option value="<?= (int) $option['id'] ?>" <?= (int) $value === (int) $option['id'] ? 'selected' : '' ?>><?= e($option['label']) ?></option><?php endforeach; ?></select></label>
        <?php else: ?>
          <label><?= e($field['label']) ?><input type="<?= e($field['type']) ?>" name="<?= e($column) ?>" value="<?= e((string) $value) ?>" <?= !empty($field['required']) ? 'required' : '' ?>><?php if (!empty($field['help'])): ?><small><?= e($field['help']) ?></small><?php endif; ?></label>
        <?php endif; ?>
      <?php endforeach; ?>
      <div class="form-actions"><button class="admin-button" type="submit">Save <?= e($definition['singular']) ?></button><a class="button-secondary" href="<?= e(url('admin/content.php?type=' . $type)) ?>">Cancel</a></div>
    </form>
  </section>
<?php endif; ?>

<section class="admin-panel">
  <div class="panel-heading"><h2>All <?= e(strtolower($definition['label'])) ?></h2><span><?= count($records) ?> total</span></div>
  <div class="content-table-wrap">
    <table class="content-table">
      <thead><tr><th>Order</th><th>Title</th><th>Visibility</th><th class="actions-column">Actions</th></tr></thead>
      <tbody>
      <?php foreach ($records as $item): ?>
        <?php
          $visibility = array_key_exists('status', $item) ? ucfirst((string) $item['status']) : ((int) ($item['is_active'] ?? 1) === 1 ? 'Visible' : 'Hidden');
        ?>
        <tr>
          <td><?= (int) ($item['sort_order'] ?? 0) ?></td>
          <td><strong><?= e((string) $item[$definition['title_column']]) ?></strong><?php if (!empty($item['category'])): ?><small><?= e($item['category']) ?></small><?php endif; ?></td>
          <td><span class="status status--<?= strtolower($visibility) ?>"><?= e($visibility) ?></span></td>
          <td class="row-actions"><a href="<?= e(url('admin/content.php?type=' . $type . '&action=edit&id=' . (int) $item['id'])) ?>">Edit</a><form method="post" onsubmit="return confirm('Delete this <?= e($definition['singular']) ?>? This cannot be undone.');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $item['id'] ?>"><input type="hidden" name="form_action" value="delete"><button type="submit">Delete</button></form></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$records): ?><tr><td colspan="4">No content in this collection yet.</td></tr><?php endif; ?>
      </tbody>
    </table>
  </div>
</section>
<?php admin_footer(); ?>
