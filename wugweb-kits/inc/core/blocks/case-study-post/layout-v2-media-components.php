<?php
/**
 * Case-study layout v2, media block markup: split, mosaic, wall, rail, scrollshot, loop, All screens.
 * Mirrors src/components/work/Case{Split,Mosaic,Wall,Rail,Loop,Figure,Screens}.astro: same class names, same ARIA.
 * Loaded only when the media blocks are on. Every dynamic value is escaped here; the returned strings are safe to echo.
 */
defined('ABSPATH') || exit;

/** <img> for a media item with width/height from img-dims.json, else the manifest. Always lazy: only the hero is eager. */
function wugweb_cl3_img(array $it, $class) {
    $dims = wugweb_cl3_dims_attr($it['src']);
    if ($dims === '' && !empty($it['w']) && !empty($it['h'])) { $dims = ' width="' . (int) $it['w'] . '" height="' . (int) $it['h'] . '"'; }
    return '<img class="' . esc_attr($class) . '" src="' . esc_url($it['src']) . '" alt="' . esc_attr($it['alt']) . '"' . $dims . ' loading="lazy" decoding="async">';
}

/** Lightbox data attributes for a link around an image. $group keeps one set per viewer sequence; $set is the name read out in "3 of 8, Member". */
function wugweb_cl3_lb_attrs($src, $alt, $set, $group) {
    return ' data-case-lightbox-src="' . esc_url($src) . '" data-case-lightbox-caption="" data-case-lightbox-alt="' . esc_attr($alt) . '" data-case-lightbox-set="' . esc_attr($set) . '" data-case-lightbox-group="' . esc_attr($group) . '"';
}

function wugweb_cl3_split($b) {
    $o = '<div class="case-split" data-reveal>';
    foreach (array($b['left'], $b['right']) as $it) { $o .= '<figure class="case-split-item">' . wugweb_cl3_img($it, 'case-split-media') . '</figure>'; }
    return $o . '</div>';
}

function wugweb_cl3_mosaic($b) {
    $o = '<div class="case-mosaic" data-reveal>';
    foreach ($b['items'] as $it) { $o .= '<figure class="case-mosaic-item">' . wugweb_cl3_img($it, 'case-mosaic-media') . '</figure>'; }
    return $o . '</div>';
}

/** Wall: fixed 4/3 frames showing each file whole; a click opens the viewer (the grammar's "wall with lightbox"). */
function wugweb_cl3_wall($b) {
    $group = 'wall-' . substr(md5($b['chapterId'] . '|' . count($b['items'])), 0, 8);
    $set = $b['title'] !== '' ? $b['title'] : 'Gallery';
    $o = '<div class="case-wall" data-reveal>';
    foreach ($b['items'] as $it) {
        $o .= '<figure class="case-wall-item"><a class="case-wall-link" href="' . esc_url($it['src']) . '" aria-label="' . esc_attr('View image: ' . $it['alt']) . '"' . wugweb_cl3_lb_attrs($it['src'], $it['alt'], $set, $group) . '>'
            . wugweb_cl3_img($it, 'case-wall-media') . '</a></figure>';
    }
    return $o . '</div>';
}

/** Rail: W3C carousel pattern. Prev/next buttons, a live counter, arrow/Home/End keys (layout-v2-blocks.js). Never auto-advances. */
function wugweb_cl3_rail($b) {
    static $n = 0; $n++;
    $total = count($b['items']);
    $title = $b['flow'] !== '' ? $b['flow'] : 'Screens';
    $label = $b['flow'] !== '' ? $b['flow'] . ' flow' : 'Screens';
    $o = '<section class="case-rail" role="region" aria-roledescription="carousel" aria-label="' . esc_attr($label) . '" data-case-rail data-rail-id="case-rail-' . (int) $n . '" data-reveal>'
        . '<div class="case-rail-head"><h3 class="case-rail-title">' . esc_html($title) . '</h3>'
        . '<div class="case-rail-controls">'
        . '<button type="button" class="case-rail-btn" data-rail-prev aria-label="Previous screen">&larr;</button>'
        . '<span class="case-rail-counter" data-rail-counter aria-live="polite" aria-atomic="true">1 of ' . (int) $total . '</span>'
        . '<button type="button" class="case-rail-btn" data-rail-next aria-label="Next screen">&rarr;</button>'
        . '</div></div><ol class="case-rail-track" data-rail-track>';
    foreach ($b['items'] as $i => $it) {
        $o .= '<li class="case-rail-slide" aria-roledescription="slide" aria-label="' . (int) ($i + 1) . ' of ' . (int) $total . '" role="group"><figure class="case-rail-item">'
            . wugweb_cl3_img($it, 'case-rail-media') . '</figure></li>';
    }
    return $o . '</ol></section>';
}

/** Loop (CaseLoop.astro): muted, playsinline, loop, aria-hidden. No src until it is on screen (data-src), poster first, paused under reduced motion (layout-v2-blocks.js). */
function wugweb_cl3_loop($b) {
    $style = ($b['w'] && $b['h']) ? ' style="aspect-ratio:' . (int) $b['w'] . ' / ' . (int) $b['h'] . '"' : '';
    $dims = ($b['w'] && $b['h']) ? ' width="' . (int) $b['w'] . '" height="' . (int) $b['h'] . '"' : '';
    return '<figure class="case-loop" data-reveal><video class="case-loop-media" data-case-loop data-src="' . esc_url($b['src']) . '"'
        . ($b['poster'] !== '' ? ' poster="' . esc_url($b['poster']) . '"' : '') . $dims . $style
        . ' muted playsinline loop preload="none" disablepictureinpicture aria-hidden="true" tabindex="-1"></video></figure>';
}

/** Scrollshot (CaseFigure.astro, tall): a fixed-height window on the top of a long page; "View full page" opens the whole file in the viewer. */
function wugweb_cl3_scrollshot($b) {
    $it = $b['item'];
    $set = $b['title'] !== '' ? $b['title'] : 'Full page';
    return '<figure class="case-figure case-figure--tall" data-reveal>' . wugweb_cl3_img($it, 'case-figure-media')
        . '<a class="case-figure-full" href="' . esc_url($it['src']) . '"' . wugweb_cl3_lb_attrs($it['src'], $it['alt'], $set, 'tall-' . substr(md5($it['src']), 0, 8)) . ' aria-label="' . esc_attr('View full page: ' . $it['alt']) . '">View full page</a></figure>';
}

/** All screens (CaseScreens.astro): set chips with counts, one set of thumbnails at a time, the viewer on a thumbnail. Without JS every set is stacked and each thumbnail is a plain link. */
function wugweb_cl3_screens($b) {
    $sets = $b['sets'];
    if (!$sets) { return ''; }
    $total = 0; foreach ($sets as $s) { $total += count($s['items']); }
    $word = function ($n) { return $n . ' ' . ($n === 1 ? 'screen' : 'screens'); };
    $o = '<section class="case-screens" id="all-screens" aria-labelledby="all-screens-head" data-case-screens data-reveal>'
        . '<header class="case-screens-top"><h2 class="case-screens-head" id="all-screens-head">All screens</h2><p class="case-screens-total">' . esc_html($word($total) . (count($sets) > 1 ? ' in ' . count($sets) . ' sets' : '')) . '</p></header>';
    if (count($sets) > 1) {
        $o .= '<div class="case-screens-chips" role="group" aria-label="Screen sets" data-screens-chips hidden>';
        foreach ($sets as $i => $s) {
            $c = count($s['items']);
            $o .= '<button type="button" class="case-screens-chip" aria-pressed="' . ($i === 0 ? 'true' : 'false') . '" aria-controls="' . esc_attr($s['id']) . '" aria-label="' . esc_attr($s['label'] . ', ' . $word($c)) . '" data-screens-chip="' . esc_attr($s['id']) . '">'
                . '<span class="case-screens-chip-name">' . esc_html($s['label']) . '</span><span class="case-screens-chip-count">' . (int) $c . '</span></button>';
        }
        $o .= '</div>';
    }
    foreach ($sets as $s) {
        $c = count($s['items']);
        $o .= '<section class="case-screens-set" id="' . esc_attr($s['id']) . '" aria-labelledby="' . esc_attr($s['id']) . '-head" data-screens-set data-shape="' . esc_attr($s['shape']) . '">'
            . '<h3 class="case-screens-set-head" id="' . esc_attr($s['id']) . '-head"><span>' . esc_html($s['label']) . '</span><span class="case-screens-set-count">' . (int) $c . '</span></h3><ul class="case-screens-grid">';
        foreach ($s['items'] as $i => $it) {
            $pos = ($i + 1) . ' of ' . $c . ', ' . $s['label'];
            $o .= '<li class="case-screens-item"><figure class="case-screens-figure"><a class="case-screens-link" id="' . esc_attr($s['id'] . '-' . ($i + 1)) . '" href="' . esc_url($it['src']) . '" aria-label="' . esc_attr('View ' . $it['alt'] . ': ' . $pos) . '"'
                . wugweb_cl3_lb_attrs($it['src'], $it['alt'], $s['label'], $s['id']) . '>' . wugweb_cl3_img($it, 'case-screens-media') . '</a></figure></li>';
        }
        $o .= '</ul></section>';
    }
    return $o . '</section>';
}

/** Dispatcher for the media block types; '' for anything else. */
function wugweb_cl3_block($b) {
    switch ($b['type']) {
        case 'split': return wugweb_cl3_split($b);
        case 'mosaic': return wugweb_cl3_mosaic($b);
        case 'wall': return wugweb_cl3_wall($b);
        case 'rail': return wugweb_cl3_rail($b);
        case 'loop': return wugweb_cl3_loop($b);
        case 'scrollshot': return wugweb_cl3_scrollshot($b);
        case 'screens': return wugweb_cl3_screens($b);
    }
    return '';
}
