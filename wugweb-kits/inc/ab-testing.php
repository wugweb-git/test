<?php
/**
 * Minimal consent-aware A/B framework (no plugin). Experiments live in inc/ab-experiments.json and are OFF by default.
 * - A variant is assigned once and stored in the first-party cookie wugweb_ab ONLY after analytics consent
 *   (localStorage wugweb_cookie_consent_v1.analytics, set by the cookie banner). Without consent everyone sees variant A and no cookie is set.
 * - The variant is applied as a class on <html> (ab-<id>-<variant>) by a tiny inline head script, plus an optional text swap on a selector.
 * - GA4 events (only with consent, via gtag / the dataLayer Site Kit uses): ab_assign {experiment_id, variant} and ab_goal {experiment_id, variant, goal}.
 * - QA: add ?ab_force=<id>:<variant> to any URL to see a variant (no cookie, no events).
 * Prints nothing at all while no experiment is enabled.
 */
defined('ABSPATH') || exit;
add_action('wp_head', function () {
    if (is_admin() || is_feed() || is_embed()) { return; }
    $file = __DIR__ . '/ab-experiments.json';
    $cfg = is_readable($file) ? json_decode((string) file_get_contents($file), true) : array();
    $exps = array();
    foreach ((array) ($cfg['experiments'] ?? array()) as $e) {
        if (empty($e['enabled']) || empty($e['id']) || empty($e['variants'])) { continue; }
        $exps[] = array(
            'id' => sanitize_key($e['id']), 'paths' => array_values((array) ($e['paths'] ?? array())),
            'variants' => array_map(function ($v) { return array('id' => sanitize_key($v['id'] ?? ''), 'weight' => max(0, (int) ($v['weight'] ?? 1))); }, (array) $e['variants']),
            'selector' => (string) ($e['selector'] ?? ''), 'labels' => (object) ((array) ($e['labels'] ?? array())), 'goal' => (string) ($e['goal'] ?? ''),
        );
    }
    $exps = apply_filters('wugweb_ab_experiments', $exps);
    if (!$exps) { return; }
    $json = wp_json_encode($exps, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP);
    // phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped -- $json is wp_json_encode with HEX flags; the rest is static
    echo "<script>(function(){var E=" . $json . ",CK='wugweb_ab',KEY='wugweb_cookie_consent_v1',H=document.documentElement;
function consent(){try{var s=localStorage.getItem(KEY);return !!(s&&JSON.parse(s).analytics)}catch(e){return false}}
function rd(){var m=document.cookie.match(new RegExp('(?:^|; )'+CK+'=([^;]*)')),o={};if(m)decodeURIComponent(m[1]).split('|').forEach(function(p){var a=p.split(':');if(a[0]&&a[1])o[a[0]]=a[1]});return o}
function wr(o){var v=Object.keys(o).map(function(k){return k+':'+o[k]}).join('|');document.cookie=CK+'='+encodeURIComponent(v)+'; max-age=7776000; path=/; SameSite=Lax'+(location.protocol==='https:'?'; Secure':'')}
function pick(x){var t=0;x.variants.forEach(function(v){t+=v.weight});var r=Math.random()*t;for(var i=0;i<x.variants.length;i++){r-=x.variants[i].weight;if(r<0)return x.variants[i].id}return x.variants[0].id}
function ev(n,p){if(!consent())return;window.dataLayer=window.dataLayer||[];if(typeof window.gtag==='function'){window.gtag('event',n,p)}else{(function(){window.dataLayer.push(arguments)})('event',n,p)}}
var force={};(location.search.match(/[?&]ab_force=([^&]+)/)||[,''])[1].split(',').forEach(function(p){var a=decodeURIComponent(p).split(':');if(a[0]&&a[1])force[a[0]]=a[1]});
var on=[],ck=rd(),ok=consent(),dirty=false;
E.forEach(function(x){if(x.paths.length&&!x.paths.some(function(p){try{return new RegExp(p).test(location.pathname)}catch(e){return false}}))return;
var ids=x.variants.map(function(v){return v.id}),v=ids[0],seen=false;
if(force[x.id]&&ids.indexOf(force[x.id])>-1){v=force[x.id]}
else if(ok){if(ck[x.id]&&ids.indexOf(ck[x.id])>-1){v=ck[x.id]}else{v=pick(x);ck[x.id]=v;dirty=true}seen=true}
H.classList.add('ab-'+x.id+'-'+v);on.push({x:x,v:v,seen:seen})});
if(dirty)wr(ck);
// Consent given later on this page: assign and remember for the NEXT page (the visitor has only seen A so far, so no exposure event now).
window.addEventListener('wugweb-analytics-allowed',function(){var c=rd(),d=false;E.forEach(function(x){if(!c[x.id]){c[x.id]=pick(x);d=true}});if(d)wr(c)});
document.addEventListener('DOMContentLoaded',function(){on.forEach(function(o){var l=o.x.labels&&o.x.labels[o.v];if(o.x.selector&&typeof l==='string'){document.querySelectorAll(o.x.selector).forEach(function(el){el.textContent=l})}
if(o.seen)ev('ab_assign',{experiment_id:o.x.id,variant:o.v})});
document.addEventListener('click',function(e){var t=e.target&&e.target.closest?e.target:null;if(!t)return;on.forEach(function(o){if(!o.seen)return;var g=t.closest('[data-ab-goal]'),n=g?g.getAttribute('data-ab-goal'):'';if(!n&&o.x.goal&&t.closest(o.x.goal))n=o.x.id+'_click';if(n)ev('ab_goal',{experiment_id:o.x.id,variant:o.v,goal:n})})})});
})();</script>\n";
    // phpcs:enable
}, 1);
