<?php
/** Team Card: components/TeamGrid.astro single member card. eyebrow = role, title = name, meta = suffix. */
$name = (string) ($attributes['title'] ?? '');
$role = (string) ($attributes['eyebrow'] ?? '');
$suffix = (string) ($attributes['meta'] ?? '');
if ($suffix === '') { $suffix = 'at wugweb'; }
$img = (string) ($attributes['imageUrl'] ?? '');
$alt = (string) ($attributes['imageAlt'] ?? '');
$initials = '';
foreach (array_slice(preg_split('/\s+/', trim($name), -1, PREG_SPLIT_NO_EMPTY), 0, 2) as $w) { $initials .= mb_substr($w, 0, 1); }
?><div <?php echo get_block_wrapper_attributes(array('class' => 'team-card', 'data-reveal' => '')); ?>><div class="team-photo"><?php if ($img): ?><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($alt !== '' ? $alt : $name); ?>" loading="lazy" decoding="async"><?php else: ?><span class="team-initials" aria-hidden="true"><?php echo esc_html($initials); ?></span><?php endif; ?></div><div class="team-meta"><p class="team-name"><?php echo esc_html($name); ?></p><?php if ($role !== ''): ?><p class="team-role"><?php echo esc_html($role); ?></p><?php endif; ?><p class="team-suffix"><?php echo esc_html($suffix); ?></p></div></div>
