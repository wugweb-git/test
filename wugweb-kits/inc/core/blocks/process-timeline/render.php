<?php
/**
 * Process Timeline — the "approach" section of services/[slug].astro
 * (.approach-layout: stacked cards + standing CTA). Icons from step-icons.ts.
 * Data: service-data.json `approachTitle`, `steps[{t,p}]`, `cta{t,p,btn}`.
 */
defined('ABSPATH') || exit;
$pillar = sanitize_key($attributes['pillar'] ?? 'strategy');
$d = wugweb_core_service_data($pillar);
if (!$d) { return; }

$steps = array_slice(is_array($d['steps'] ?? null) ? $d['steps'] : array(), 0, 5);
if (!$steps) { return; }
$count = count($steps);
$title = !empty($d['approachTitle']) ? $d['approachTitle'] : 'From first conversation to handover.';
$cta = is_array($d['cta'] ?? null) ? $d['cta'] : array();
$cta_title = !empty($cta['t']) ? $cta['t'] : (ucfirst($pillar) . ' services you can rely on.');
$cta_body  = $cta['p'] ?? '';
$btns = is_array($cta['btn'] ?? null) ? $cta['btn'] : array();
$cta_btn  = $btns[0] ?? 'Start a project';
$cta_link = $btns[1] ?? 'See the work first';
$ad_image = isset($attributes['adImage']) ? esc_url($attributes['adImage']) : '';
$mark_rel = file_exists(get_theme_file_path('assets/brand/logo-small.svg')) ? 'assets/brand/logo-small.svg' : 'assets/brand/favicon.svg';

if (!function_exists('wugweb_pt_icon_for')) {
  function wugweb_pt_icon_for($text) {
    static $icons = null;
    if ($icons === null) {
      $icons = array(
        array('~kick.?off|handover|handoff|meeting|workshop|stakeholder|collabor~i', 'M3 13a4 4 0 0 1 8 0M7 5.5a2 2 0 1 1 0 4 2 2 0 0 1 0-4M11.5 13a3.2 3.2 0 0 1 4.5-2.9'),
        array('~interview|conversation|voice|survey|feedback|immersive~i', 'M2.5 4.5h11v7h-6l-3 2.5v-2.5h-2z'),
        array('~competitor|benchmark|market|audit|analys|analyz|result|measur|metric|report|analytic|data.?driven|data~i', 'M2.5 13.5h11M4.5 11V7M8 11V3.5M11.5 11V8.5'),
        array('~brain|storm|ideat|concept|idea|hypoth|principl~i', 'M8 2.2a4 4 0 0 1 2.4 7.2v1.6h-4.8V9.4A4 4 0 0 1 8 2.2ZM6.4 13.2h3.2'),
        array('~journey|flow|route|roadmap|path|mapping~i', 'M3 12.5c3 0 2-8 5-8s2 5 5 5M3 12.5h.01M13 9.5h.01'),
        array('~sitemap|architect|structure|taxonom|hierarch|design system|system~i', 'M8 2.5v3M4 13.5v-3M12 13.5v-3M4 10.5h8v-2H4zM6.5 5.5h3v3h-3z'),
        array('~wireframe|prototyp|layout|frame|draft|sketch~i', 'M2.5 3.5h11v9h-11zM2.5 6.5h11M6 6.5v6'),
        array('~usability|user test|testing|validat|qa|quality|review~i', 'M6.5 2.5h3v2h-3zM4 4.5h8v9H4zM6.2 9l1.3 1.3 2.4-2.6'),
        array('~accessib|inclusive|wcag~i', 'M8 2.6a1.1 1.1 0 1 1 0 2.2 1.1 1.1 0 0 1 0-2.2M4 6.4h8M8 6.4v3.2M8 9.6l-1.8 3.8M8 9.6l1.8 3.8'),
        array('~look and feel|visual|ui design|palette|identity|craft|art direction~i', 'M8 2.5a5.5 5.5 0 1 0 0 11c.9 0 1.2-.7.7-1.3-.5-.7-.2-1.7.8-1.7h1a3 3 0 0 0 3-3c0-2.8-2.5-5-5.5-5M5.5 7.5h.01M8 5.5h.01M10.5 7.5h.01'),
        array('~research|discover|explore|insight|persona|study~i', 'M7.2 2.6a4.6 4.6 0 1 1 0 9.2 4.6 4.6 0 0 1 0-9.2M10.6 10.6 13.5 13.5'),
        array('~build|develop|engineer|code|implement|integrat|automat~i', 'M6 5.5 3 8l3 2.5M10 5.5 13 8l-3 2.5'),
        array('~launch|ship|deploy|go.?live|scale|optimi|growth|iterat|refine|post.?launch~i', 'M8 2.5c2.4 1.6 3.4 4 3.4 6.4L8 12l-3.4-3.1c0-2.4 1-4.8 3.4-6.4ZM6.6 12.2 5.4 14M9.4 12.2l1.2 1.8'),
        array('~content|copy|narrative|messag|document|spec|guideline~i', 'M4 2.5h8v11H4zM6 5.5h4M6 8h4M6 10.5h2.5'),
        array('~govern|operat|process|training|enable|support|maintain~i', 'M8 5.9a2.1 2.1 0 1 1 0 4.2 2.1 2.1 0 0 1 0-4.2M8 2.5v1.6M8 11.9v1.6M13.5 8h-1.6M4.1 8H2.5M11.9 4.1l-1.1 1.1M5.2 10.8l-1.1 1.1M11.9 11.9l-1.1-1.1M5.2 5.2 4.1 4.1'),
      );
    }
    foreach ($icons as $row) { if (preg_match($row[0], (string) $text)) { return $row[1]; } }
    return 'M8 4.5a3.5 3.5 0 1 1 0 7 3.5 3.5 0 0 1 0-7';
  }
}

$wrap_args = array('class' => 'wrap approach-section', 'aria-labelledby' => 'approach-section-title');
if (empty($attributes['anchor'])) { $wrap_args['id'] = 'approach-section'; }
$wrap = get_block_wrapper_attributes($wrap_args);
?>
<section <?php echo $wrap; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
  <h2 data-reveal id="approach-section-title"><?php echo esc_html($title); ?></h2>

  <div class="approach-layout">
    <ol class="approach-cards" data-scroll="cardstack">
      <?php foreach ($steps as $i => $a) :
        $t = $a['t'] ?? '';
        $cls = 'approach-card' . ($i === 0 ? ' is-open' : '');
      ?>
        <li class="<?php echo esc_attr($cls); ?>" data-stack-card data-approach-card style="z-index:<?php echo (int) ($count - $i); ?>">
          <div class="approach-card-visual">
            <?php if (!empty($a['image'])) : ?>
              <img src="<?php echo esc_url($a['image']); ?>" alt="" loading="lazy" decoding="async" />
            <?php else : ?>
              <span class="approach-visual-mark" aria-hidden="true">
                <svg class="approach-icon" viewBox="0 0 16 16" focusable="false"><path d="<?php echo esc_attr(wugweb_pt_icon_for($t)); ?>" /></svg>
                <span class="approach-meter">
                  <?php for ($j = 0; $j < $count; $j++) : ?><span class="approach-seg<?php echo $j <= $i ? ' is-on' : ''; ?>"></span><?php endfor; ?>
                </span>
              </span>
            <?php endif; ?>
          </div>
          <div class="approach-card-body">
            <div class="approach-card-head">
              <span class="u-index"><?php echo esc_html(!empty($a['n']) ? $a['n'] : str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></span>
              <button type="button" class="u-chip approach-toggle" aria-expanded="<?php echo $i === 0 ? 'true' : 'false'; ?>" aria-label="<?php echo esc_attr('Show what happens during ' . $t); ?>" data-approach-toggle>
                <svg viewBox="0 0 16 16" aria-hidden="true" focusable="false"><path d="M8 1.5v13M1.5 8h13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" /></svg>
              </button>
            </div>
            <h3><?php echo esc_html($t); ?></h3>
            <div class="approach-card-desc"><p><?php echo esc_html($a['p'] ?? ''); ?></p></div>
          </div>
          <span class="approach-rule" aria-hidden="true"></span>
        </li>
      <?php endforeach; ?>
    </ol>

    <aside class="approach-cta" data-reveal>
      <?php if ($ad_image) : // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- esc_url() applied at assignment ?><img class="approach-cta-bg" src="<?php echo $ad_image; ?>" alt="" loading="lazy" decoding="async" /><?php endif; ?>
      <div class="approach-cta-inner">
        <img class="approach-cta-mark" src="<?php echo esc_url(get_theme_file_uri($mark_rel)); ?>" alt="Wugweb" width="36" height="36" />
        <h3><?php echo esc_html($cta_title); ?></h3>
        <?php if ($cta_body) : ?><p class="approach-cta-body"><?php echo esc_html($cta_body); ?></p><?php endif; ?>
        <a href="<?php echo esc_url('/contact-us'); ?>" class="btn btn-solid approach-cta-btn"><?php echo esc_html($cta_btn); ?></a>
        <a href="<?php echo esc_url('/work'); ?>" class="approach-cta-link"><?php echo esc_html($cta_link); ?></a>
      </div>
    </aside>
  </div>
</section>
