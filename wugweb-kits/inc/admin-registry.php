<?php
/** Appearance → Wugweb Registry: every block and pattern with its number, name, category, keywords and root class. Read-only; numbers live in inc/core/registry-numbers.json. */
defined('ABSPATH') || exit;
add_action('admin_menu', function () {
    add_theme_page('Wugweb Registry', 'Wugweb Registry', 'edit_theme_options', 'wugweb-registry', 'wugweb_registry_page');
});
function wugweb_registry_page() {
    if (!current_user_can('edit_theme_options')) return;
    $nums = json_decode((string) @file_get_contents(get_theme_file_path('inc/core/registry-numbers.json')), true) ?: array('blocks' => array(), 'patterns' => array());
    $cats = array(); foreach (get_default_block_categories() as $c) { $cats[$c['slug']] = $c['title']; }
    foreach (apply_filters('block_categories_all', array(), get_post() ?: new WP_Block_Editor_Context()) as $c) { $cats[$c['slug']] = $c['title']; }
    $blocks = array(); foreach (WP_Block_Type_Registry::get_instance()->get_all_registered() as $name => $t) { if (strpos($name, 'wugweb/') === 0) { $blocks[] = $t; } }
    usort($blocks, function ($a, $b) use ($nums) { return strcmp($nums['blocks'][$a->name] ?? 'Z', $nums['blocks'][$b->name] ?? 'Z'); });
    $pats = array(); foreach (WP_Block_Patterns_Registry::get_instance()->get_all_registered() as $p) { if (strpos($p['name'], 'wugweb-kits/') === 0) { $pats[] = $p; } }
    usort($pats, function ($a, $b) use ($nums) { return strcmp($nums['patterns'][$a['name']] ?? 'Z', $nums['patterns'][$b['name']] ?? 'Z'); });
    echo '<div class="wrap"><h1>Wugweb Registry</h1><p>Blocks and patterns with their reference numbers. Conventions: block name <code>wugweb/&lt;folder&gt;</code>, root class <code>wp-block-wugweb-&lt;folder&gt;</code>, one unique id per element per page, accessible names from <code>inc/a11y-names.php</code> (override per block with <em>Accessible label</em>). See <code>docs/naming.md</code>.</p>';
    echo '<h2>Blocks (' . count($blocks) . ')</h2><table class="widefat striped"><thead><tr><th>No.</th><th>Title</th><th>Name</th><th>Category</th><th>Keywords</th><th>Root class</th></tr></thead><tbody>';
    foreach ($blocks as $t) {
        printf('<tr><td>%s</td><td>%s</td><td><code>%s</code></td><td>%s</td><td>%s</td><td><code>wp-block-wugweb-%s</code></td></tr>',
            esc_html($nums['blocks'][$t->name] ?? '—'), esc_html($t->title), esc_html($t->name), esc_html($cats[$t->category] ?? (string) $t->category), esc_html(implode(', ', (array) $t->keywords)), esc_html(substr($t->name, 7)));
    }
    echo '</tbody></table><h2>Patterns (' . count($pats) . ')</h2><table class="widefat striped"><thead><tr><th>No.</th><th>Title</th><th>Slug</th><th>Categories</th><th>Keywords</th><th>Description</th></tr></thead><tbody>';
    foreach ($pats as $p) {
        printf('<tr><td>%s</td><td>%s</td><td><code>%s</code></td><td>%s</td><td>%s</td><td>%s</td></tr>',
            esc_html($nums['patterns'][$p['name']] ?? '—'), esc_html($p['title']), esc_html($p['name']), esc_html(implode(', ', (array) ($p['categories'] ?? array()))), esc_html(implode(', ', (array) ($p['keywords'] ?? array()))), esc_html((string) ($p['description'] ?? '')));
    }
    echo '</tbody></table></div>';
}
