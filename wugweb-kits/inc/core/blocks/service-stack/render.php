<?php
/** Service Stack — port of the .svc-stack panels in services/index.astro. */
defined('ABSPATH') || exit;

$template = array(
  array('n'=>'01','slug'=>'strategy','title'=>'Strategy','sub'=>'Define the future before building it.','bg'=>'var(--neutral-8)','fg'=>'var(--fg)','accent'=>'var(--color-accent-primary)','tiers'=>array(array('id'=>'sprint','name'=>'Sprint','body'=>'A focused strategy sprint to sharpen positioning, pressure-test the opportunity and turn complex thinking into a direction your teams and your audiences can actually act on.'),array('id'=>'partnership','name'=>'Partnership','body'=>'An embedded strategy partnership for the bigger bets — transformation programmes, new market entry, and roadmaps that need dedicated thinking over quarters, not weeks.')),'gallery'=>array('axis-bank','metcred','sadhan','indusninja')),
  array('n'=>'02','slug'=>'engineering','title'=>'Engineering','sub'=>'Engineer products built to scale.','bg'=>'var(--neutral-7)','fg'=>'var(--fg)','accent'=>'var(--color-accent-primary)','tiers'=>array(array('id'=>'build','name'=>'Build','body'=>'A production-grade web or app build on modern foundations — performant, accessible, secure by default, and handed over with the documentation to keep it that way.'),array('id'=>'partnership','name'=>'Partnership','body'=>'A long-run engineering partnership: a dedicated squad on your roadmap, covering platform work, integrations, cloud and the reliability that comes after launch.')),'gallery'=>array('metcred','indusninja','airtel-xsafe','visione')),
  array('n'=>'03','slug'=>'brand','title'=>'Brand','sub'=>'Create brands people remember.','bg'=>'var(--neutral-8)','fg'=>'var(--fg)','accent'=>'var(--color-accent-primary)','tiers'=>array(array('id'=>'foundations','name'=>'Foundations','body'=>'A complete brand foundation built to hold its own alongside organisations ten times your size — from visual identity to voice, delivered as a system your team can actually run with.'),array('id'=>'partnership','name'=>'Partnership','body'=>'A bespoke brand partnership with dedicated creative resource for the work that needs more room: a full rebrand, a campaign identity, or something nobody has done before.')),'gallery'=>array('inkompass','visione','sadhan','axis-bank')),
  array('n'=>'04','slug'=>'growth','title'=>'Growth','sub'=>'Turn products into businesses.','bg'=>'var(--neutral-7)','fg'=>'var(--fg)','accent'=>'var(--color-accent-primary)','tiers'=>array(array('id'=>'foundations','name'=>'Foundations','body'=>'A martech and analytics foundation that turns scattered tooling into one measurable acquisition engine — tracking, automation and reporting you can trust.'),array('id'=>'partnership','name'=>'Partnership','body'=>'A retained growth partnership running lifecycle automation, AI-assisted campaigns and experimentation against targets we agree upfront and report on monthly.')),'gallery'=>array('indusninja','sadhan','axis-bank','airtel-xsafe')),
  array('n'=>'05','slug'=>'operations','title'=>'Operations','sub'=>'Keep everything running and improving.','bg'=>'var(--neutral-8)','fg'=>'var(--fg)','accent'=>'var(--color-accent-primary)','tiers'=>array(array('id'=>'essentials','name'=>'Essentials','body'=>'A QA, no-code and automation pass that removes the manual work slowing delivery down — test coverage, workflow automation and the tooling to keep releases boring.'),array('id'=>'partnership','name'=>'Partnership','body'=>'An embedded operations partnership covering continuous QA, release engineering and site reliability so your products ship faster and scale without friction.')),'gallery'=>array('visione','sadhan','inkompass','indusninja')),
  array('n'=>'06','slug'=>'experience','title'=>'Experience','sub'=>'Design products people love using.','bg'=>'var(--neutral-7)','fg'=>'var(--fg)','accent'=>'var(--color-accent-primary)','tiers'=>array(array('id'=>'foundations','name'=>'Foundations','body'=>'A research-led UX and UI build designed to convert, communicate and hold up under scrutiny — journeys, interfaces and a design system that ships with the product.'),array('id'=>'partnership','name'=>'Partnership','body'=>'An ongoing experience partnership for multi-product estates: continuous research, a governed design system, and a design team that stays with the roadmap.')),'gallery'=>array('airtel-xsafe','inkompass','metcred','visione')),
);
$panels = (!empty($attributes['panels']) && is_array($attributes['panels'])) ? $attributes['panels'] : $template;

$norm = function ($s) { return preg_replace('/[^a-z0-9]+/', '', strtolower((string) $s)); };
$cs_all = get_posts(array('post_type' => 'case_study', 'numberposts' => 200, 'post_status' => 'publish', 'orderby' => 'date', 'order' => 'DESC'));
$resolve = function ($key) use ($cs_all, $norm) {
  $kn = $norm($key);
  foreach ($cs_all as $p) {
    $slug = $norm($p->post_name);
    $client = $norm(get_post_meta($p->ID, 'wugweb_cs_client', true));
    if (($slug !== '' && strpos($slug, $kn) !== false) || ($client !== '' && (strpos($client, $kn) !== false || strpos($kn, substr($client, 0, 8)) !== false))) {
      $f = wugweb_fields_case_study($p->ID);
      return array('media' => $f['image'], 'label' => $f['client'] ?: $key, 'url' => $f['url'], 'id' => $p->ID);
    }
  }
  return array('media' => '', 'label' => ucwords(str_replace('-', ' ', $key)), 'url' => home_url('/work/'));
};
$wrap = get_block_wrapper_attributes(array('class' => 'svc-stack'));
?>
<div <?php echo $wrap; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core ?>>
<?php foreach ($panels as $s) :
  $slug = sanitize_key($s['slug']); ?>
  <section class="svc-panel" id="<?php echo esc_attr($slug); ?>" style="--svc-bg:<?php echo esc_attr($s['bg']); ?>;--svc-fg:<?php echo esc_attr($s['fg']); ?>;--svc-accent:<?php echo esc_attr($s['accent']); ?>" aria-label="<?php echo esc_attr($s['title']); ?>">
    <div class="wrap svc-inner">
      <div class="svc-copy">
        <span class="svc-num"><?php echo esc_html($s['n']); ?></span>
        <h2 class="svc-title"><?php echo esc_html($s['title']); ?></h2>
        <?php if (!empty($s['sub'])) : ?><p class="svc-sub"><?php echo esc_html($s['sub']); ?></p><?php endif; ?>
        <div class="svc-tabs" role="tablist">
          <?php foreach ($s['tiers'] as $ti => $t) : ?>
          <button type="button" role="tab" class="svc-tab<?php echo $ti === 0 ? ' active' : ''; ?>" data-tier-btn="<?php echo esc_attr($t['id']); ?>" aria-selected="<?php echo $ti === 0 ? 'true' : 'false'; ?>"><?php echo esc_html($t['name']); ?></button>
          <?php endforeach; ?>
        </div>
        <?php foreach ($s['tiers'] as $ti => $t) : ?>
        <div class="svc-tier" role="tabpanel" data-tier-panel="<?php echo esc_attr($t['id']); ?>"<?php echo $ti !== 0 ? ' hidden' : ''; ?>>
          <p class="svc-desc"><?php echo esc_html($t['body']); ?></p>
          <div class="svc-btns">
            <?php if ($ti === 0) : ?><a href="<?php echo esc_url(home_url('/services/' . $slug . '/')); ?>" class="btn svc-btn">Learn More</a><a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn svc-btn">Get in Touch</a>
            <?php else : ?><a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn svc-btn">Contact Us</a><?php endif; ?>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="svc-gallery">
        <?php $svc_ids = array(); foreach ($s['gallery'] as $g) : $item = $resolve($g); if (!empty($item['id'])) { $svc_ids[] = $item['id']; }
          $is_video = (bool) preg_match('/\.(mp4|webm)(\?|$)/i', $item['media']); ?>
        <a href="<?php echo esc_url($item['url']); ?>" aria-label="<?php echo esc_attr($item['label'] . ' — case study'); ?>">
          <?php if ($is_video) : ?><video aria-hidden="true" muted loop playsinline preload="none" data-src="<?php echo esc_url($item['media']); ?>"></video>
          <?php elseif ($item['media']) : ?><img src="<?php echo esc_url($item['media']); ?>" alt="" loading="lazy" decoding="async">
          <?php else : ?><span class="wugweb-ph" aria-hidden="true"></span><?php endif; ?>
          <span class="svc-gallery-label"><?php echo esc_html($item['label']); ?></span>
        </a>
        <?php endforeach; ?>
      </div>
      <?php $svc_tools = $svc_ids ? wugweb_tool_chips_html($svc_ids, 'tool', 8) : ''; if ($svc_tools !== '') : ?><div class="svc-tools"><span class="svc-tools-label">Tools used on this work</span><?php echo $svc_tools; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built with esc_url/esc_html ?></div><?php endif; ?>
    </div>
  </section>
<?php endforeach; ?>
</div>
