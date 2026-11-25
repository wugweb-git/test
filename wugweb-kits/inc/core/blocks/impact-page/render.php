<?php
/** Impact Page — verbatim markup of footer/impact.astro. */
defined('ABSPATH') || exit;
?>
<div <?php echo get_block_wrapper_attributes(array('class' => 'impact-page')); ?>>
    
    <!-- 01 — HERO -->
    <section class="hero-section wrap" id="hero-section" aria-labelledby="hero-section-title">
      <div class="hero-content">
        
        <h1 class="page-title" data-reveal id="hero-section-title">Supporting meaningful work.</h1>
        <p class="hero-lead" data-reveal>
          Support for meaningful work has never mattered more. We believe organisations, campaigns and people working towards positive change should have access to the expertise and capabilities they need to move their work forward.
        </p>
        <p class="hero-body" data-reveal>
          That is where we can help. Wugweb contributes through technology, design, digital support, mentorship and partnerships—using what we know to support work that matters.
        </p>
        <div class="hero-actions" data-reveal>
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid">Request support</a>
        </div>
      </div>
      
      <div class="hero-visual-slot" data-reveal>
        <div class="visual-container ratio-16-9">
          <div class="placeholder-graphic"></div>
        </div>
      </div>
    </section>

    <!-- 02 — OUR APPROACH -->
    <section class="approach-section wrap" id="approach-section" aria-labelledby="approach-section-title">
      <div class="section-header">
        
        <h2 data-reveal id="approach-section-title">We give capability, not just support.</h2>
      </div>
      
      <div class="approach-grid">
        <div class="approach-copy" data-reveal>
          <p>
            Sometimes it means building technology for a campaign. Sometimes it means supporting an organisation's digital presence. Sometimes it means sharing design expertise, mentoring emerging talent or helping solve a difficult problem.
          </p>
          <p>
            We support work where our capabilities can make a meaningful difference.
          </p>
        </div>
        
        <div class="approach-visual" data-reveal>
          <div class="capability-sequence">
            <div class="capability-node">
              <span class="node-n">01</span>
              <h3>Design</h3>
              <p>Visual expression & UX</p>
            </div>
            <div class="capability-arrow" aria-hidden="true">→</div>
            <div class="capability-node">
              <span class="node-n">02</span>
              <h3>Technology</h3>
              <p>Engineering & scale</p>
            </div>
            <div class="capability-arrow" aria-hidden="true">→</div>
            <div class="capability-node">
              <span class="node-n">03</span>
              <h3>Strategy</h3>
              <p>Positioning & launch</p>
            </div>
            <div class="capability-arrow" aria-hidden="true">→</div>
            <div class="capability-node">
              <span class="node-n">04</span>
              <h3>Mentorship</h3>
              <p>Sharing & guidance</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 03 — REQUEST SUPPORT -->
    <section class="request-banner-section wrap" data-reveal id="request-banner-section" aria-labelledby="request-banner-section-title">
      <div class="banner-inner">
        <div class="banner-content">
          <h2 id="request-banner-section-title">Working on something that matters?</h2>
          <p>
            If you're a purpose-driven organisation, campaign or initiative and need help with design, technology or digital support, tell us what you're working on.
          </p>
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid btn-light">Request support &rarr;</a>
        </div>
        <div class="banner-visual-slot">
          <div class="visual-container ratio-4-3">
            <div class="placeholder-graphic light-lines"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 04 — ADVOCACY & GIVING -->
    <section class="advocacy-section wrap" id="advocacy-section" aria-labelledby="advocacy-section-title">
      <div class="section-header">
        
        <h2 data-reveal id="advocacy-section-title">Where we've put our capabilities to work.</h2>
      </div>

      <!-- FEATURED STORY -->
      <div class="featured-story" data-reveal>
        <div class="featured-visual-matrix">
          <div class="primary-visual">
            <div class="visual-container ratio-3-2">
              <div class="placeholder-graphic"></div>
            </div>
          </div>
          
          <div class="secondary-visuals">
            <div class="visual-container ratio-1-1">
              <div class="placeholder-graphic"></div>
            </div>
            <div class="visual-container ratio-1-1">
              <div class="placeholder-graphic"></div>
            </div>
          </div>
        </div>

        <div class="featured-content">
          <span class="meta-label">Campaign · Digital platform · Advocacy</span>
          <h3>ANIMAL WRITES</h3>
          <p>
            We helped create and run the digital platform behind Animal Writes, supporting its campaign and helping it build momentum across India.
          </p>
          <a href="https://animalwrites.in/" target="_blank" rel="noopener noreferrer" class="link-cta">
            Visit Animal Writes
          </a>
        </div>
      </div>
    </section>

    <!-- 05 — OTHER CONTRIBUTIONS -->
    <section class="other-contributions-section wrap" id="other-contributions-section" aria-labelledby="other-contributions-section-title">
      <div class="section-header">
        
      </div>

      <div class="contributions-list">
        <!-- Contribution Row 1 -->
        <div class="contribution-row" data-reveal>
          <div class="row-main-content">
            <span class="meta-label">Digital sponsorship · Animal welfare</span>
            <h3 id="other-contributions-section-title">Nawabi Tails Rescue</h3>
            <p>
              We provide digital sponsorship and support to an organisation working in animal rescue and welfare.
            </p>
            <a href="https://nawabitailsrescue.org/" target="_blank" rel="noopener noreferrer" class="link-cta">
              Visit
            </a>
          </div>
          
          <div class="row-visuals">
            <div class="visual-container ratio-4-3">
              <div class="placeholder-graphic"></div>
            </div>
          </div>
        </div>

        <!-- Contribution Row 2 -->
        <div class="contribution-row" data-reveal>
          <div class="row-main-content">
            <span class="meta-label">#ExtendedGuestList · Technology Partner</span>
            <h3>Robin Hood Army</h3>
            <p>
              Wugweb provided technology support for the 2022 campaign with Dentsu Creative and Robin Hood Army.
            </p>
            <a href="https://bestmediainfo.com/2022/12/why-did-dentsu-creative-bring-robin-hood-army-to-actor-hansika-motwani-s-wedding" target="_blank" rel="noopener noreferrer" class="link-cta">
              Read about the campaign
            </a>
          </div>
          
          <div class="row-visuals">
            <div class="visual-container ratio-4-3">
              <div class="placeholder-graphic"></div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 06 — MENTORSHIP & KNOWLEDGE -->
    <section class="mentorship-section wrap" id="mentorship-section" aria-labelledby="mentorship-section-title">
      <div class="mentorship-grid">
        <div class="mentorship-content" data-reveal>
          
          <h2 id="mentorship-section-title">Expertise should be shared.</h2>
          <p>
            Through design mentorship, professional guidance and knowledge sharing, we support emerging designers, founders and professionals as they develop their ideas, products and careers.
          </p>
          <p>
            We also participate in India's startup ecosystem through Startup India and its mentorship initiatives.
          </p>
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-ghost">Learn more &rarr;</a>
        </div>
        
        <div class="mentorship-visual-slot" data-reveal>
          <div class="visual-container ratio-3-2">
            <div class="placeholder-graphic"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 07 — SUPPORT THE WORK -->
    <section class="support-work-section wrap" id="support-work-section" aria-labelledby="support-work-section-title">
      <div class="section-header text-center">
        
        <h2 data-reveal id="support-work-section-title">You can help too.</h2>
        <p class="section-lede" data-reveal>
          You don't have to work with us to contribute. If the work of organisations we support matters to you, you can help them directly.
        </p>
      </div>

      <div class="action-cards-grid">
        <!-- Card 1 -->
        <div class="action-card" data-reveal>
          <div class="card-visual-slot">
            <div class="visual-container ratio-16-9">
              <div class="placeholder-graphic"></div>
            </div>
          </div>
          <div class="card-content">
            <h3>Animal Writes</h3>
            <p>Support animal-welfare advocacy and help the campaign continue its work.</p>
            <a href="https://animalwrites.in/" target="_blank" rel="noopener noreferrer" class="btn btn-solid card-btn">
              Support Animal Writes
            </a>
          </div>
        </div>

        <!-- Card 2 -->
        <div class="action-card" data-reveal>
          <div class="card-visual-slot">
            <div class="visual-container ratio-16-9">
              <div class="placeholder-graphic"></div>
            </div>
          </div>
          <div class="card-content">
            <h3>Nawabi Tails Rescue</h3>
            <p>Support rescue, rehabilitation and animal welfare.</p>
            <a href="https://nawabitailsrescue.org/" target="_blank" rel="noopener noreferrer" class="btn btn-solid card-btn">
              Support Nawabi Tails
            </a>
          </div>
        </div>
      </div>
    </section>

    <!-- 08 — RECOGNISED -->
    <section class="recognised-section wrap" data-reveal id="recognised-section" aria-labelledby="recognised-section-title">
      <div class="recognised-inner">
        <div class="recognised-content">
          
          <h2 id="recognised-section-title">DPIIT Recognised Startup</h2>
          <p>
            Wugweb is recognised under India's Startup India ecosystem through the Department for Promotion of Industry and Internal Trade (DPIIT).
          </p>
          <a href="https://www.startupindia.gov.in/" target="_blank" rel="noopener noreferrer" class="btn btn-ghost text-left">
            Startup India
          </a>
        </div>
        
        <div class="recognised-visual">
          <div class="visual-container ratio-4-3">
            <div class="placeholder-graphic"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 09 — FINAL CTA -->
    <section class="final-cta-section wrap" data-reveal id="final-cta-section" aria-labelledby="final-cta-section-title">
      <div class="final-cta-inner-impact">
        <h2 id="final-cta-section-title">Have meaningful work that needs support?</h2>
        <p>
          Tell us what you're working on, who you're helping, and where Wugweb could contribute.
        </p>
        <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid">Request support &rarr;</a>
      </div>
    </section>

  </div>
