<?php
/** Taxonomy Filter: work/index.astro .work-filters + .filter-chip (blog/index.astro .blog-filters for blog taxonomies). Chips are links, so filtering is server-side. */
$taxonomy = sanitize_key($attributes['taxonomy'] ?? 'discipline');
$terms = get_terms(array('taxonomy' => $taxonomy, 'hide_empty' => true));
if (is_wp_error($terms)) { $terms = array(); }
$base = get_post_type_archive_link('case_study');
$current = isset($_GET[$taxonomy]) ? sanitize_title(wp_unslash($_GET[$taxonomy])) : '';
$wrap = in_array($taxonomy, array('category', 'post_tag'), true) ? 'blog-filters' : 'work-filters';
?><nav <?php echo get_block_wrapper_attributes(array('class' => $wrap, 'role' => 'toolbar', 'aria-label' => ucfirst($taxonomy) . ' filters')); ?>><a class="filter-chip<?php echo $current === '' ? ' active' : ''; ?>" href="<?php echo esc_url($base); ?>"<?php echo $current === '' ? ' aria-current="true"' : ''; ?>>All</a><?php foreach ($terms as $term): $on = ($current === $term->slug); ?><a class="filter-chip<?php echo $on ? ' active' : ''; ?>" href="<?php echo esc_url(add_query_arg($taxonomy, $term->slug, $base)); ?>"<?php echo $on ? ' aria-current="true"' : ''; ?>><?php echo esc_html($term->name); ?></a><?php endforeach; ?></nav>
