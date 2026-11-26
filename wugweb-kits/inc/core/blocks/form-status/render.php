<?php
/** Form Status: what happens after the enquiry form is sent. state = success | error. */
$ok = ( $attributes['state'] ?? 'success' ) !== 'error';
$mail = 'hello@wugweb.com';
$icon = $ok
	? '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>'
	: '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v5M12 16.5v.01"/></svg>';
// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- get_block_wrapper_attributes() output is escaped by core
?><section <?php echo get_block_wrapper_attributes( array( 'class' => 'form-status wrap ' . ( $ok ? 'is-success' : 'is-error' ) ) ); ?>>
<div class="form-status-card" role="<?php echo $ok ? 'status' : 'alert'; ?>" tabindex="-1" data-form-status>
<span class="form-status-icon"><?php echo $icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
<?php if ( $ok ) : ?>
<h2 class="form-status-title">Your enquiry is in.</h2>
<p class="form-status-lede">We have it and our team has been notified. Here is what happens next.</p>
<ol class="form-status-steps">
<li><span class="step-num">01</span><div><strong>We review it</strong><p>Within one business day, someone reads your enquiry and the pillars you picked.</p></div></li>
<li><span class="step-num">02</span><div><strong>We reply by email</strong><p>You will hear from us at the address you gave, with a time for a 30-minute strategy call.</p></div></li>
<li><span class="step-num">03</span><div><strong>You get a proposal</strong><p>Scope, deliverables, timeline and pricing, after we have talked.</p></div></li>
</ol>
<p class="form-status-note">Nothing in your inbox after a day? Check spam, or write to <a href="mailto:<?php echo esc_attr( $mail ); ?>"><?php echo esc_html( $mail ); ?></a>.</p>
<div class="form-status-actions"><a class="btn btn-solid" href="<?php echo esc_url( home_url( '/work/' ) ); ?>">See our work</a><a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/blog/' ) ); ?>">Read the blog</a></div>
<?php else : ?>
<h2 class="form-status-title">We could not save your enquiry.</h2>
<p class="form-status-lede">This was a problem on our side, not with what you entered. Nothing was sent. Try again, or reach us directly and we will pick it up from there.</p>
<div class="form-status-actions"><a class="btn btn-solid" href="<?php echo esc_url( home_url( '/contact-us/#enquiry' ) ); ?>">Try again</a><a class="btn btn-ghost" href="mailto:<?php echo esc_attr( $mail ); ?>">Email <?php echo esc_html( $mail ); ?></a><a class="btn btn-ghost" href="https://wa.me/919205996219" target="_blank" rel="noopener noreferrer">WhatsApp us</a></div>
<?php endif; ?>
</div></section>
