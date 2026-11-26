<?php
/**
 * Case-study layout v2: component markup (one function per Astro component). Loaded once via require_once from layout-v2.php.
 * Every dynamic value is escaped here; callers may echo the returned strings.
 */
defined('ABSPATH') || exit;

/** <img> with the file's own width/height (inc/core/img-dims.json, R2 files) so the browser reserves the space. Returns escaped HTML. */
function wugweb_cl2_img($src, $alt, $class, $eager = false) {
    $dims = '';
    if (function_exists('wugweb_r2_img_dims') && function_exists('wugweb_r2_key_from_url')) {
        $k = wugweb_r2_key_from_url($src); $dm = wugweb_r2_img_dims();
        if ($k !== '' && !empty($dm[$k][0]) && !empty($dm[$k][1])) { $dims = ' width="' . (int) $dm[$k][0] . '" height="' . (int) $dm[$k][1] . '"'; }
    }
    return '<img class="' . esc_attr($class) . '" src="' . esc_url($src) . '" alt="' . esc_attr($alt) . '"' . $dims . ($eager ? ' loading="eager" fetchpriority="high"' : ' loading="lazy"') . ' decoding="async">';
}

/** figure.case-video: muted, no autoplay, one play/pause button (CaseVideo.astro). Returns escaped HTML. */
function wugweb_cl2_video($src, $label, $poster) {
    $vsrc = $poster ? $src : $src . '#t=0.1';
    return '<figure class="case-video" data-case-video data-reveal><div class="case-video-frame">'
        . '<video class="case-video-media" src="' . esc_url($vsrc) . '"' . ($poster ? ' poster="' . esc_url($poster) . '"' : '') . ' muted playsinline preload="' . ($poster ? 'none' : 'metadata') . '" disablepictureinpicture aria-label="' . esc_attr($label) . '"></video>'
        . '<button type="button" class="case-video-toggle" data-video-toggle aria-pressed="false" aria-label="' . esc_attr('Play video: ' . $label) . '" hidden><span class="case-video-icon" aria-hidden="true">&#9654;</span></button>'
        . '</div></figure>';
}

function wugweb_cl2_stats($b, $type) {
    $full = $type === 'results';
    $h = $type . '-head';
    $o = '<section class="case-stats" aria-labelledby="' . esc_attr($h) . '" data-reveal><h2 class="case-stats-head" id="' . esc_attr($h) . '">' . ($full ? 'Results' : 'Stats') . '</h2><ul class="case-stats-list">';
    foreach ($b['items'] as $it) {
        $o .= '<li class="case-stat"><p class="case-stat-value">' . esc_html($it['value']) . '</p><p class="case-stat-what">' . esc_html($it['what']) . '</p>';
        if ($full && ($it['source'] !== '' || $it['asOf'] !== '')) {
            $o .= '<p class="case-stat-source">' . ($it['source'] !== '' ? '<span>' . esc_html($it['source']) . '</span>' : '') . ($it['source'] !== '' && $it['asOf'] !== '' ? '<span class="case-stat-sep">,</span>' : '') . ($it['asOf'] !== '' ? '<span>as of ' . esc_html($it['asOf']) . '</span>' : '') . '</p>';
        }
        $o .= '</li>';
    }
    return $o . '</ul></section>';
}

function wugweb_cl2_related($b) {
    $next = $b['type'] === 'next';
    $items = $next ? array($b['item']) : $b['items'];
    $h = $b['type'] . '-head';
    $o = '<section class="' . ($next ? 'case-related case-related--next' : 'case-related') . '" aria-labelledby="' . esc_attr($h) . '" data-reveal><h2 class="case-related-head" id="' . esc_attr($h) . '">' . ($next ? 'Up next' : 'Related case studies') . '</h2><ul class="case-related-list">';
    foreach ($items as $it) {
        $o .= '<li class="case-related-item"><a class="case-related-link" href="' . esc_url($it['url']) . '">'
            . ($it['img'] !== '' ? wugweb_cl2_img($it['img'], '', 'case-related-media') : '')
            . '<div class="case-related-text"><h3 class="case-related-title">' . esc_html($it['title']) . '</h3>'
            . ($it['industry'] !== '' ? '<p class="case-related-industry">' . esc_html($it['industry']) . '</p>' : '')
            . '</div></a></li>';
    }
    return $o . '</ul></section>';
}

/** One non-chapter or chapter-item block -> HTML. Every dynamic value is escaped inside; the result is safe to echo. */
function wugweb_cl2_block($b) {
    switch ($b['type']) {
        case 'hero':
            $o = '<header class="case-hero" data-reveal><div class="case-hero-text">';
            if ($b['clientName'] !== '') { $o .= '<p class="case-hero-client">' . esc_html($b['clientName']) . '</p>'; }
            $o .= '<h1 class="case-hero-title">' . esc_html($b['title']) . '</h1>';
            if ($b['subtitle'] !== '') { $o .= '<p class="case-hero-subtitle">' . wugweb_cl2_inline($b['subtitle']) . '</p>'; }
            if ($b['tags']) { $o .= '<ul class="case-hero-tags" aria-label="Tags">'; foreach ($b['tags'] as $t) { $o .= '<li class="case-hero-tag">' . esc_html($t) . '</li>'; } $o .= '</ul>'; }
            if ($b['meta']) { $o .= '<dl class="case-hero-meta">'; foreach ($b['meta'] as $r) { $o .= '<dt>' . esc_html($r[0]) . '</dt><dd>' . wugweb_cl2_inline($r[1]) . '</dd>'; } $o .= '</dl>'; }
            $o .= '</div>';
            if ($b['cover'] !== '') { $o .= '<div class="case-hero-cover" id="work-cover">' . wugweb_cl2_img($b['cover'], $b['coverAlt'], 'case-hero-media', true) . '</div>'; }
            return $o . '</header>';
        case 'summary':
            return '<p class="case-summary" data-reveal>' . esc_html($b['text']) . '</p>';
        case 'stats': return wugweb_cl2_stats($b, 'stats');
        case 'results': return wugweb_cl2_stats($b, 'results');
        case 'points':
            $o = '<ul class="case-points" data-reveal>';
            foreach ($b['items'] as $t) { $o .= '<li class="case-point">' . ($t[0] !== '' ? '<h3 class="case-point-title">' . wugweb_cl2_inline($t[0]) . '</h3>' : '') . '<p class="case-point-text">' . wugweb_cl2_inline($t[1]) . '</p></li>'; }
            return $o . '</ul>';
        case 'figure':
            return '<figure class="case-figure" data-reveal>' . wugweb_cl2_img($b['src'], $b['alt'], 'case-figure-media') . '</figure>';
        case 'pair':
            return '<div class="case-pair" data-reveal>'
                . '<figure class="case-pair-item">' . wugweb_cl2_img($b['left']['src'], $b['left']['alt'], 'case-pair-media') . '</figure>'
                . '<figure class="case-pair-item">' . wugweb_cl2_img($b['right']['src'], $b['right']['alt'], 'case-pair-media') . '</figure></div>';
        case 'video': return wugweb_cl2_video($b['src'], $b['label'], $b['poster']);
        case 'closing':
            // WordPress-only stand-in for the Astro "All screens" block: the files no chapter claimed, as plain figures (no viewer script).
            $fig = function ($md) use ($b) {
                return $md['video'] ? wugweb_cl2_video($md['src'], $b['vlabel'], wugweb_cl2_poster($md['src'])) : '<figure class="case-figure" data-reveal>' . wugweb_cl2_img($md['src'], $b['label'], 'case-figure-media') . '</figure>';
            };
            $first = array_slice($b['items'], 0, 6); $more = array_slice($b['items'], 6);
            $o = '<section class="case-closing" aria-label="More from the project"><div class="case-closing-grid">';
            foreach ($first as $md) { $o .= $fig($md); }
            $o .= '</div>';
            if ($more) { $o .= '<details class="case-closing-more"><summary class="case-closing-summary">Show all ' . (int) count($b['items']) . ' images</summary><div class="case-closing-grid">'; foreach ($more as $md) { $o .= $fig($md); } $o .= '</div></details>'; }
            return $o . '</section>';
        case 'credits':
            $o = '<section class="case-credits" aria-labelledby="credits-head" data-reveal><h2 class="case-credits-head" id="credits-head">Credits</h2><dl class="case-credits-list">';
            foreach ($b['items'] as $k => $v) { $o .= '<dt>' . esc_html($k) . '</dt><dd>' . wugweb_cl2_inline($v) . '</dd>'; }
            return $o . '</dl></section>';
        case 'presence':
            $o = '<section class="case-presence" aria-labelledby="presence-head" data-reveal><h2 class="case-presence-head" id="presence-head">Where it lives</h2><ul class="case-presence-list">';
            foreach ($b['links'] as $l) { $o .= '<li class="case-presence-item"><a class="case-presence-link" href="' . esc_url($l['href']) . '" target="_blank" rel="noopener noreferrer"><span class="case-presence-key">' . esc_html($l['value']) . '</span><span class="case-presence-text">' . esc_html($l['text']) . '</span></a></li>'; }
            return $o . '</ul></section>';
        case 'related':
        case 'next': return wugweb_cl2_related($b);
    }
    return '';
}
