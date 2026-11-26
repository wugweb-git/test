<?php
/** Contact Form: the enquiry form and the office / how-we-collaborate sidebar (pages/contact-us.astro). Posts to admin-post.php, see includes/contact-submit.php. */
$opt = wugweb_contact_options();
$pillars = array(
	'Strategy' => 'Product, Brand & Growth Architecture', 'Brand' => 'Identity Systems & Narrative Design', 'Experience' => 'UI/UX, Platforms & Design Systems',
	'Engineering' => 'Web, App & Platform Modernization', 'Growth' => 'Martech, CRO & Performance', 'Operations' => 'Managed Support & Maintenance',
);
// A failed server check sends the visitor back with their answers (kept 15 minutes) and what to fix.
$v = array( 'name' => '', 'company' => '', 'email' => '', 'phone' => '', 'topic' => '', 'budget' => '', 'timeline' => '', 'interests' => array(), 'newsletter' => false );
$token = isset( $_GET['cft'] ) ? sanitize_key( wp_unslash( $_GET['cft'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
if ( $token ) { $saved = get_transient( 'wg_cfv_' . $token ); if ( is_array( $saved ) ) { $v = array_merge( $v, $saved ); } }
$flags = isset( $_GET['cf'] ) ? array_filter( explode( ',', sanitize_text_field( wp_unslash( $_GET['cf'] ) ) ) ) : array(); // phpcs:ignore WordPress.Security.NonceVerification
$msgs = array(
	'missing-name' => array( 'name', 'Enter your name.' ), 'invalid-email' => array( 'email', 'Enter a work email, like name@company.com.' ),
	'message-too-short' => array( 'topic', 'Tell us a little more: at least ' . WUGWEB_CONTACT_MIN_MESSAGE . ' characters.' ),
);
$errs = array();
foreach ( $flags as $fl ) { if ( isset( $msgs[ $fl ] ) ) { $errs[ $msgs[ $fl ][0] ] = $msgs[ $fl ][1]; } }
$rate = in_array( 'rate', $flags, true );
$e = function ( $k ) use ( $errs ) { return isset( $errs[ $k ] ) ? ' aria-invalid="true" aria-describedby="err-' . esc_attr( $k ) . '"' : ''; };
$err = function ( $k ) use ( $errs ) { return isset( $errs[ $k ] ) ? '<p class="field-error" id="err-' . esc_attr( $k ) . '">' . esc_html( $errs[ $k ] ) . '</p>' : ''; };
$map = function ( $q ) { return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( $q ); };
$pin = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="item-icon" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>';
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core
?><div <?php echo get_block_wrapper_attributes( array( 'class' => 'contact-v2-body wrap', 'id' => 'enquiry' ) ); ?>><div class="contact-v2-grid">
<div class="form-card">
<?php if ( $errs || $rate ) : ?><div class="form-alert" role="alert" tabindex="-1" data-form-alert><p><strong><?php echo $rate ? 'Too many enquiries from this connection today.' : 'Check the highlighted fields.'; ?></strong> <?php echo $rate ? 'Email hello@wugweb.com and we will pick it up.' : 'Your answers are kept below.'; ?></p></div><?php endif; ?>
<form class="contact-form-v2" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate data-contact-form>
<input type="hidden" name="action" value="wugweb_contact"><input type="hidden" name="wg_ts" value="" data-ts>
<label class="hp-field" aria-hidden="true">Leave this empty<input type="text" name="website" tabindex="-1" autocomplete="off"></label>

<fieldset class="form-section"><legend class="section-title">01. What pillar are you focusing on?</legend>
<div class="pillar-grid">
<?php foreach ( $pillars as $n => $d ) : ?><label class="pillar-selector"><input type="checkbox" name="interests[]" value="<?php echo esc_attr( $n ); ?>"<?php checked( in_array( $n, $v['interests'], true ) ); ?>><span class="selector-content"><span class="pillar-name"><?php echo esc_html( $n ); ?></span><span class="pillar-desc"><?php echo esc_html( $d ); ?></span></span></label>
<?php endforeach; ?>
</div></fieldset>

<fieldset class="form-section"><legend class="section-title">02. Tell us about yourself</legend>
<div class="inputs-row">
<div class="field-group"><label for="cf-name">Your name <span aria-hidden="true">*</span></label><input type="text" id="cf-name" name="name" required autocomplete="name" placeholder="Jane Doe" value="<?php echo esc_attr( $v['name'] ); ?>"<?php echo $e( 'name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $err( 'name' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
<div class="field-group"><label for="cf-company">Company / organization</label><input type="text" id="cf-company" name="company" autocomplete="organization" placeholder="e.g. Acme Corp" value="<?php echo esc_attr( $v['company'] ); ?>"></div>
</div>
<div class="inputs-row">
<div class="field-group"><label for="cf-email">Work email <span aria-hidden="true">*</span></label><input type="email" id="cf-email" name="email" required autocomplete="email" placeholder="name@company.com" value="<?php echo esc_attr( $v['email'] ); ?>"<?php echo $e( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo $err( 'email' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
<div class="field-group"><label for="cf-phone">Phone number</label><input type="tel" id="cf-phone" name="phone" autocomplete="tel" placeholder="+1 555 000 0000" value="<?php echo esc_attr( $v['phone'] ); ?>"></div>
</div></fieldset>

<fieldset class="form-section"><legend class="section-title">03. Project details &amp; goals</legend>
<div class="field-group"><label for="cf-topic">Scope &amp; objectives <span aria-hidden="true">*</span></label><textarea id="cf-topic" name="topic" rows="5" required minlength="<?php echo (int) WUGWEB_CONTACT_MIN_MESSAGE; ?>" placeholder="Briefly describe your goals, challenge, or vision..."<?php echo $e( 'topic' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_textarea( $v['topic'] ); ?></textarea><?php echo $err( 'topic' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div></fieldset>

<fieldset class="form-section"><legend class="section-title">04. Investment &amp; timeline</legend>
<div class="range-group"><p class="group-label" id="cf-budget-l">Estimated budget</p><div class="chips-row" role="radiogroup" aria-labelledby="cf-budget-l">
<?php foreach ( $opt['budget'] as $b ) : ?><label class="radio-chip"><input type="radio" name="budget" value="<?php echo esc_attr( $b ); ?>"<?php checked( $v['budget'], $b ); ?>><span><?php echo esc_html( $b ); ?></span></label><?php endforeach; ?>
</div></div>
<div class="range-group"><p class="group-label" id="cf-timeline-l">Target timeline</p><div class="chips-row" role="radiogroup" aria-labelledby="cf-timeline-l">
<?php foreach ( $opt['timeline'] as $t ) : ?><label class="radio-chip"><input type="radio" name="timeline" value="<?php echo esc_attr( $t ); ?>"<?php checked( $v['timeline'], $t ); ?>><span><?php echo esc_html( $t ); ?></span></label><?php endforeach; ?>
</div></div></fieldset>

<div class="form-footer">
<label class="checkbox-optin"><input type="checkbox" name="newsletter" value="yes"<?php checked( $v['newsletter'] ); ?>><span>Subscribe to Wugweb insights &amp; editorial updates. Read our <a href="<?php echo esc_url( home_url( '/privacy-policy/' ) ); ?>">Privacy Policy</a>.</span></label>
<button type="submit" class="submit-btn"><span>Send enquiry</span><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg></button>
</div>
</form></div>

<aside class="sidebar-cards" aria-label="Offices and how we work">
<div class="info-card glass-panel"><h2 class="card-title">Our offices</h2><div class="info-list"><div class="info-addresses">
<div class="info-item"><span class="item-label">India office</span><a href="<?php echo esc_url( $map( 'Floor 19, WeWork Berger Delhi One, C-001/A2, Sector 16B, Noida, UP - 201301' ) ); ?>" target="_blank" rel="noopener noreferrer" class="item-value item-value-linked"><?php echo $pin; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Floor 19, WeWork Berger Delhi One, C-001/A2, Sector 16B, Noida, UP - 201301</a></div>
<div class="info-item"><span class="item-label">UAE presence</span><a href="<?php echo esc_url( $map( 'Dubai Silicon Oasis, DTEC Campus, Dubai, UAE' ) ); ?>" target="_blank" rel="noopener noreferrer" class="item-value item-value-linked"><?php echo $pin; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Dubai Silicon Oasis, DTEC Campus, Dubai, UAE</a></div>
</div></div></div>
<div class="info-card glass-panel"><h2 class="card-title">How we collaborate</h2><ol class="collab-steps">
<li><span class="step-num">01</span><div><strong>Discovery &amp; alignment</strong><p>We review your enquiry within 24h and schedule a 30-min strategy call.</p></div></li>
<li><span class="step-num">02</span><div><strong>Tailored proposal</strong><p>Transparent scope, deliverables, timeline, and value-based pricing.</p></div></li>
<li><span class="step-num">03</span><div><strong>Agile execution</strong><p>Dedicated squad, bi-weekly sprints, and direct Slack communication.</p></div></li>
</ol></div>
</aside></div></div>
