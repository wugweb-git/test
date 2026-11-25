<?php
/** Subscription Page — markup of footer/subscription.astro (checkout modal omitted). */
defined('ABSPATH') || exit;
?>

  <div <?php echo get_block_wrapper_attributes(array('class' => 'sub-page')); ?>>

    <!-- 01 — HERO -->
    <section class="hero-section wrap" id="hero-section" aria-labelledby="hero-section-title">
      <div class="hero-content">
        
        <h1 class="page-title" data-reveal id="hero-section-title">Your digital team, on subscription.</h1>
        <p class="hero-lead" data-reveal>
          Design, technology, strategy and growth support — continuously, without the overhead of hiring another team.
        </p>
        
        <div class="hero-price-box" data-reveal>
          <div class="price-row">
            <span class="price-val">₹95,000</span>
            <span class="price-period">/ month</span>
          </div>
          <p class="price-support">
            One active request · Unlimited queue · Pause anytime · No long-term contract
          </p>
        </div>

        <div class="hero-actions" data-reveal>
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid trigger-checkout">Start subscription</a>
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-ghost">Talk to us</a>
        </div>
      </div>

      <div class="hero-visual" data-reveal>
        <div class="queue-panel">
          <div class="panel-header">
            <div class="dot-indicators">
              <span></span><span></span><span></span>
            </div>
            <span class="panel-title">REQUEST QUEUE</span>
          </div>
          <div class="queue-list" id="hero-queue">
            <div class="queue-item active" data-index="0">
              <span class="item-n">01</span>
              <span class="item-title">Landing page redesign</span>
              <span class="item-status status-active">● ACTIVE</span>
            </div>
            <div class="queue-item" data-index="1">
              <span class="item-n">02</span>
              <span class="item-title">Product UX improvements</span>
              <span class="item-status status-queued">○ QUEUED</span>
            </div>
            <div class="queue-item" data-index="2">
              <span class="item-n">03</span>
              <span class="item-title">SEO audit</span>
              <span class="item-status status-queued">○ QUEUED</span>
            </div>
            <div class="queue-item" data-index="3">
              <span class="item-n">04</span>
              <span class="item-title">CMS improvements</span>
              <span class="item-status status-queued">○ QUEUED</span>
            </div>
            <div class="queue-item" data-index="4">
              <span class="item-n">05</span>
              <span class="item-title">Campaign landing page</span>
              <span class="item-status status-queued">○ QUEUED</span>
            </div>
          </div>
          <div class="panel-footer">
            <span class="panel-footer-text">Auto-processing simulated workspace</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 02 — HOW IT WORKS -->
    <section class="how-it-works wrap" id="how-it-works" aria-labelledby="how-it-works-title">
      <div class="section-header">
        
        <h2 data-reveal id="how-it-works-title">Simple by design.</h2>
      </div>

      <div class="steps-container">
        <!-- Step 1 -->
        <div class="step-card" data-reveal data-step="0">
          <div class="step-header">
            <span class="step-num">01</span>
            <div class="step-icon">
              <!-- plus/inbox -->
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            </div>
          </div>
          <h3>REQUEST</h3>
          <p class="step-desc">Add as many requests as you need.</p>
          
          <!-- Infinite scrolling category tags marquee like Designjoy -->
          <div class="sub-marquee-wrap">
            <div class="sub-marquee-inner">
              <span class="sub-pill">Mobile Apps</span>
              <span class="sub-pill">UX Systems</span>
              <span class="sub-pill">Figma Files</span>
              <span class="sub-pill">Webflow Dev</span>
              <span class="sub-pill">Brand Guide</span>
              <span class="sub-pill">Slide Decks</span>
              <span class="sub-pill">Strategy Audits</span>
              <span class="sub-pill">Pitch Decks</span>
              <!-- Repeat for seamless scrolling -->
              <span class="sub-pill">Mobile Apps</span>
              <span class="sub-pill">UX Systems</span>
              <span class="sub-pill">Figma Files</span>
              <span class="sub-pill">Webflow Dev</span>
            </div>
          </div>
        </div>

        <!-- Step 2 -->
        <div class="step-card" data-reveal data-step="1">
          <div class="step-header">
            <span class="step-num">02</span>
            <div class="step-icon">
              <!-- sort/priority -->
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"></line><line x1="3" y1="6" x2="21" y2="6"></line><line x1="3" y1="18" x2="21" y2="18"></line></svg>
            </div>
          </div>
          <h3>PRIORITISE</h3>
          <p class="step-desc">Decide what matters most.</p>
          
          <div class="sub-board-preview">
            <div class="board-lane">
              <span class="lane-title">Active</span>
              <div class="board-card active">Hero Overhaul</div>
            </div>
            <div class="board-lane">
              <span class="lane-title">Queue</span>
              <div class="board-card">SEO Audit</div>
              <div class="board-card">Webflow Launch</div>
            </div>
          </div>
        </div>

        <!-- Step 3 -->
        <div class="step-card" data-reveal data-step="2">
          <div class="step-header">
            <span class="step-num">03</span>
            <div class="step-icon">
              <!-- cursor/tools -->
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            </div>
          </div>
          <h3>WORK</h3>
          <p class="step-desc">We work on one active request at a time.</p>
          
          <div class="sub-delivery-bar">
            <div class="bar-progress"></div>
            <span class="bar-eta">Avg. 48 hour delivery</span>
          </div>
        </div>

        <!-- Step 4 -->
        <div class="step-card" data-reveal data-step="3">
          <div class="step-header">
            <span class="step-num">04</span>
            <div class="step-icon">
              <!-- check/arrow -->
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
            </div>
          </div>
          <h3>DELIVER</h3>
          <p class="step-desc">Review, refine and move to the next.</p>
          
          <!-- Layered Design Stack elements that fan out on hover -->
          <div class="sub-stack-wrap">
            <div class="sub-stack-card card-back"></div>
            <div class="sub-stack-card card-mid"></div>
            <div class="sub-stack-card card-front"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- 03 — THE CORE RULE -->
    <section class="core-rule-section wrap" id="core-rule-section" aria-labelledby="core-rule-section-title">
      <div class="core-rule-grid">
        <div class="core-rule-copy" data-reveal>
          <h2 id="core-rule-section-title">Unlimited requests.<br>One active request.</h2>
          <div class="oversized-text">
            <div class="word-group">
              <span class="huge-word text-accent">Unlimited</span>
              <span class="sub-word">requests.</span>
            </div>
            <div class="word-group">
              <span class="huge-word">One</span>
              <span class="sub-word">active request.</span>
            </div>
          </div>
        </div>

        <div class="core-rule-visual" data-reveal>
          <div class="queue-interactive-panel">
            <div class="interactive-panel-header">
              <span>WORK PIPELINE INTERACTIVE DEMO</span>
              <button class="btn-demo-trigger" id="btn-next-demo">Simulate Next Deliverable</button>
            </div>
            <div class="interactive-queue-box" id="interactive-queue-box">
              <div class="iq-row completed">
                <span class="iq-status">✓</span>
                <span class="iq-title">Homepage Launch</span>
                <span class="iq-tag">COMPLETED</span>
              </div>
              <div class="iq-row active" id="iq-active-row">
                <span class="iq-status">●</span>
                <span class="iq-title" id="iq-active-title">Product UX Overhaul</span>
                <span class="iq-tag">ACTIVE</span>
              </div>
              <div class="iq-row queued" data-queue-idx="0">
                <span class="iq-status">○</span>
                <span class="iq-title">SEO Optimization Audit</span>
                <span class="iq-tag">QUEUED</span>
              </div>
              <div class="iq-row queued" data-queue-idx="1">
                <span class="iq-status">○</span>
                <span class="iq-title">Campaign Landing Page</span>
                <span class="iq-tag">QUEUED</span>
              </div>
              <div class="iq-row queued" data-queue-idx="2">
                <span class="iq-status">○</span>
                <span class="iq-title">Sanity CMS Integration</span>
                <span class="iq-tag">QUEUED</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 04 — WHAT YOU GET -->
    <section class="capabilities-section wrap" id="capabilities-section" aria-labelledby="capabilities-section-title">
      <div class="section-header">
        
        <h2 data-reveal id="capabilities-section-title">One team. Six capabilities.</h2>
      </div>

      <div class="capabilities-interactive">
        <div class="capabilities-selector" id="cap-selector" data-reveal>
          <button class="selector-btn active" data-cap="strategy">STRATEGY</button>
          <button class="selector-btn" data-cap="brand">BRAND</button>
          <button class="selector-btn" data-cap="experience">EXPERIENCE</button>
          <button class="selector-btn" data-cap="engineering">ENGINEERING</button>
          <button class="selector-btn" data-cap="growth">GROWTH</button>
          <button class="selector-btn" data-cap="operations">OPERATIONS</button>
        </div>

        <div class="capabilities-viewport" id="cap-viewport" data-reveal>
          <div class="cap-content-pane active" id="cap-pane-strategy">
            <div class="pane-copy">
              <h3>Strategy Support</h3>
              <p>Product thinking, audits, roadmaps, research synthesis and strategic problem solving.</p>
            </div>
            <div class="pane-visual">
              <div class="visual-container ratio-4-3">
                <div class="placeholder-graphic"></div>
              </div>
            </div>
          </div>

          <div class="cap-content-pane" id="cap-pane-brand">
            <div class="pane-copy">
              <h3>Brand Support</h3>
              <p>Brand applications, campaigns, messaging, identity extensions and creative production.</p>
            </div>
            <div class="pane-visual">
              <div class="visual-container ratio-4-3">
                <div class="placeholder-graphic"></div>
              </div>
            </div>
          </div>

          <div class="cap-content-pane" id="cap-pane-experience">
            <div class="pane-copy">
              <h3>Experience Support</h3>
              <p>UX, UI, design systems, prototypes and experience improvements.</p>
            </div>
            <div class="pane-visual">
              <div class="visual-container ratio-4-3">
                <div class="placeholder-graphic"></div>
              </div>
            </div>
          </div>

          <div class="cap-content-pane" id="cap-pane-engineering">
            <div class="pane-copy">
              <h3>Engineering Support</h3>
              <p>Web development, CMS, frontend work, integrations and ongoing improvements.</p>
            </div>
            <div class="pane-visual">
              <div class="visual-container ratio-4-3">
                <div class="placeholder-graphic"></div>
              </div>
            </div>
          </div>

          <div class="cap-content-pane" id="cap-pane-growth">
            <div class="pane-copy">
              <h3>Growth Support</h3>
              <p>SEO, CRO, analytics, landing pages and growth experimentation.</p>
            </div>
            <div class="pane-visual">
              <div class="visual-container ratio-4-3">
                <div class="placeholder-graphic"></div>
              </div>
            </div>
          </div>

          <div class="cap-content-pane" id="cap-pane-operations">
            <div class="pane-copy">
              <h3>Operations Support</h3>
              <p>QA, optimisation, maintenance, monitoring and continuous improvement.</p>
            </div>
            <div class="pane-visual">
              <div class="visual-container ratio-4-3">
                <div class="placeholder-graphic"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 05 — REQUEST EXPLORER -->
    <section class="explorer-section wrap" id="explorer-section" aria-labelledby="explorer-section-title">
      <div class="section-header">
        
        <h2 data-reveal id="explorer-section-title">If it moves your digital work forward, it can probably go in the queue.</h2>
      </div>

      <div class="explorer-container">
        <!-- Filter Selector -->
        <div class="explorer-filters" id="explorer-filters" data-reveal>
          <button class="filter-btn active" data-filter="all">ALL</button>
          <button class="filter-btn" data-filter="design">DESIGN</button>
          <button class="filter-btn" data-filter="technology">TECHNOLOGY</button>
          <button class="filter-btn" data-filter="growth">GROWTH</button>
          <button class="filter-btn" data-filter="strategy">STRATEGY</button>
        </div>

        <!-- Requests Grid -->
        <div class="explorer-grid" id="explorer-grid" data-reveal>
          <button class="explorer-item" data-category="design" data-id="landing-page">
            <span>Landing page</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="design" data-id="website-page">
            <span>Website page</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="design" data-id="ux-improvement">
            <span>UX improvement</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="design" data-id="ui-screen">
            <span>UI screen</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="design" data-id="ds-component">
            <span>Design system component</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="design" data-id="prototype">
            <span>Prototype</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="technology" data-id="cms-update">
            <span>CMS update</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="technology" data-id="frontend-feature">
            <span>Frontend feature</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="technology" data-id="integration">
            <span>Integration</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="growth" data-id="seo-improvement">
            <span>SEO improvement</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="growth" data-id="analytics-setup">
            <span>Analytics setup</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="growth" data-id="campaign-asset">
            <span>Campaign asset</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="strategy" data-id="product-audit">
            <span>Product audit</span>
            <span class="item-arrow">&rarr;</span>
          </button>
          <button class="explorer-item" data-category="growth" data-id="conversion-improvement">
            <span>Conversion improvement</span>
            <span class="item-arrow">&rarr;</span>
          </button>
        </div>
      </div>

      <!-- Detail Slide-out Drawer Overlay -->
      <div class="explorer-drawer-overlay" id="explorer-drawer-overlay">
        <div class="explorer-drawer" id="explorer-drawer">
          <div class="drawer-header">
            <h3 id="drawer-title">Request Type</h3>
            <button class="btn-close-drawer" id="btn-close-drawer">&times;</button>
          </div>
          <div class="drawer-body">
            <p id="drawer-desc" class="drawer-desc">Description of work goes here.</p>
            
            <div class="drawer-meta-section">
              <h4>Typical output</h4>
              <p id="drawer-output">UX + UI + responsive design + handoff</p>
            </div>
            
            <div class="drawer-meta-section">
              <h4>Typical turnaround</h4>
              <p id="drawer-turnaround">1–3 business days*</p>
            </div>

            <p class="drawer-disclaimer">*Turnaround depends on complexity and request scope.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 06 — SPEED -->
    <section class="speed-section wrap" id="speed-section" aria-labelledby="speed-section-title">
      <div class="speed-inner" data-reveal>
        
        <h2 id="speed-section-title">Fast by default. Thoughtful when it matters.</h2>
        
        <p class="speed-lede">
          <strong>Most standard requests are delivered within 1–3 business days.</strong>
        </p>
        <p class="speed-body">
          Small, clearly defined requests move quickly. Larger pieces of work are broken into manageable deliverables and progressed sequentially.
        </p>

        <div class="timeline-visual">
          <div class="timeline-node">
            <span class="node-bullet"></span>
            <h3>REQUEST</h3>
          </div>
          <div class="timeline-line"></div>
          <div class="timeline-node">
            <span class="node-bullet"></span>
            <h3>UNDERSTAND</h3>
          </div>
          <div class="timeline-line"></div>
          <div class="timeline-node">
            <span class="node-bullet"></span>
            <h3>CREATE</h3>
          </div>
          <div class="timeline-line"></div>
          <div class="timeline-node">
            <span class="node-bullet"></span>
            <h3>DELIVER / REVIEW</h3>
          </div>
        </div>
      </div>
    </section>

    <!-- 07 — INCLUDED -->
    <section class="included-section wrap" id="included-section" aria-labelledby="included-section-title">
      <div class="included-grid">
        <div class="included-header" data-reveal>
          
          <h2 id="included-section-title">Everything you need to keep moving.</h2>
          <p class="included-lede">
            One subscription. Multiple capabilities. No need to assemble a different team for every task.
          </p>
        </div>

        <div class="included-checklist" data-reveal>
          <ul>
            <li><span class="check-mark">✓</span> One active request</li>
            <li><span class="check-mark">✓</span> Unlimited request queue</li>
            <li><span class="check-mark">✓</span> Senior Wugweb team</li>
            <li><span class="check-mark">✓</span> Strategy + Brand + Experience</li>
            <li><span class="check-mark">✓</span> Engineering + Growth + Operations</li>
            <li><span class="check-mark">✓</span> Revisions included</li>
            <li><span class="check-mark">✓</span> Async collaboration</li>
            <li><span class="check-mark">✓</span> Priority workflow</li>
            <li><span class="check-mark">✓</span> Pause anytime</li>
            <li><span class="check-mark">✓</span> No long-term contract</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- 08 — NOT INCLUDED (Boundaries) -->
    <section class="boundaries-section wrap" id="boundaries-section" aria-labelledby="boundaries-section-title">
      <div class="section-header">
        
        <h2 data-reveal id="boundaries-section-title">Some work deserves its own project.</h2>
      </div>

      <div class="boundaries-accordion" id="boundaries-accordion" data-reveal>
        <div class="accordion-item">
          <button class="accordion-header">
            <span>Large standalone transformations</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>Complex visual audits or complete business restructuring fall outside the quick iterative scope of our monthly queue, and are handled as standalone scoped projects.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header">
            <span>Complete enterprise platforms</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>High-security portal systems or global database systems requiring custom compliance certification are sized as fixed-scope architectures.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header">
            <span>Large mobile applications</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>Native Swift/Kotlin application pipelines with specific app store submissions are best served by focused project timelines.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header">
            <span>Major migrations</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>Migrating multi-tenant databases or millions of system profiles require dedicated sandbox testing environments outside regular subscription iterations.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header">
            <span>Full-scale rebrands</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>Full rebrand assignments involving global signage, packaging, and guideline releases are scoped as enterprise project work.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header">
            <span>Large campaign productions</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>High-end multimedia video shoots or large external advertising layouts with strict deadlines fit project pricing architectures.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header">
            <span>Dedicated full-time staffing</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>Dedicated developer seats or full-time, 40-hours-a-week standup attendance require traditional client staffing agreements.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header">
            <span>Third-party software / production costs</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>Subscriptions to CMS systems, web hosting platforms, fonts, assets, and APIs are billed directly to your billing account.</p>
          </div>
        </div>

        <div class="accordion-item">
          <button class="accordion-header">
            <span>Advertising or media spend</span>
            <span class="accordion-icon">+</span>
          </button>
          <div class="accordion-panel">
            <p>Payments for Google Ads, Facebook Ads, or LinkedIn sponsored campaigns are funded directly by your campaign balance.</p>
          </div>
        </div>
      </div>

      <div class="boundaries-footer" data-reveal>
        <p>These aren't necessarily things we can't do. If something falls outside the subscription model, we'll scope it separately.</p>
      </div>
    </section>

    <!-- 09 — PRICING -->
    <section class="pricing-section wrap" id="pricing-section" aria-label="Pricing section">
      <div class="pricing-card" data-reveal>
        <span class="pricing-eyebrow">WUGWEB SUBSCRIPTION</span>
        <div class="pricing-row-main">
          <span class="price-val">₹95,000</span>
          <span class="price-period">/ month</span>
        </div>
        
        <ul class="pricing-benefits">
          <li>One active request</li>
          <li>Unlimited queue</li>
          <li>Senior multidisciplinary team</li>
          <li>Six Wugweb capabilities</li>
          <li>1–3 business-day target for standard requests</li>
          <li>Revisions included</li>
          <li>Pause anytime</li>
          <li>No long-term contract</li>
        </ul>

        <div class="pricing-cta-box">
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid trigger-checkout">Start subscription</a>
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-ghost">Talk to us</a>
        </div>

        <div class="pricing-footer-link">
          <span>Need something larger or more specialised?</span>
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>">Talk to us &rarr;</a>
        </div>
      </div>
    </section>

    <!-- 13 — PAUSE / CANCEL STATUS CONTROLS -->
    <section class="pause-cancel-section wrap" data-reveal id="pause-cancel-section" aria-labelledby="pause-cancel-section-title">
      <div class="pc-inner">
        <div class="pc-header">
          <h3 id="pause-cancel-section-title">Manage Subscription State</h3>
          <p>Test the workspace pause and cancellation flow interface directly.</p>
        </div>
        <div class="pc-status-display">
          <span>Current active status: </span>
          <span class="status-badge" id="workspace-status-badge">ACTIVE</span>
        </div>
        <div class="pc-actions">
          <button class="btn btn-ghost" id="btn-pc-pause">Pause subscription</button>
          <button class="btn btn-ghost" id="btn-pc-resume" style="display:none;">Resume subscription</button>
          <button class="btn btn-ghost" id="btn-pc-cancel">Cancel subscription</button>
        </div>
        <p class="pc-notice">
          Your subscription is designed around your workload. Pause when things slow down and resume when you're ready.
        </p>
      </div>
    </section>

    <!-- 14 — FAQ -->
    <section class="faq-section wrap" id="faq-section" aria-labelledby="faq-section-title">
      <div class="section-header">
        
        <h2 data-reveal id="faq-section-title">Frequently Asked Questions</h2>
      </div>

      <div class="faq-accordion" id="faq-accordion" data-reveal>
        <div class="faq-item">
          <button class="faq-header">
            <span>How does one request at a time work?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>You can add as many requests as you need. We work on the highest-priority active request before moving to the next.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>Can I submit unlimited requests?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>Yes. Your queue can contain as many requests as you need.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>How quickly will my request be completed?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>Most standard requests are delivered within 1–3 business days. Larger work takes longer and is broken into sequential deliverables.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>Can I request development?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>Yes. Engineering is part of the Wugweb subscription.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>Can I request strategy?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>Yes, where the work is appropriate for the subscription model.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>Are revisions included?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>Yes, within the scope of the active request.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>Can I pause?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>Yes. You can pause your subscription at the end of the billing period and resume anytime.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>Can I cancel?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>Yes. There is no long-term contract.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>How do we submit requests?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>Through your Wugweb workspace/client portal.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>Who works on my requests?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>A senior Wugweb team, with the appropriate specialists involved based on the request.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>What happens with large projects?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>We determine whether the work can be broken into subscription requests. If not, we'll scope it separately.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-header">
            <span>Can I subscribe from outside India?</span>
            <span class="faq-icon">+</span>
          </button>
          <div class="faq-panel">
            <p>International subscriptions can be supported where payment and billing requirements allow. Keep this answer configurable based on Wugweb's actual payment setup.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 15 — FINAL CTA -->
    <section class="final-cta wrap" data-reveal id="final-cta" aria-labelledby="final-cta-title">
      <div class="final-inner">
        <h2 id="final-cta-title">Keep your digital work moving.</h2>
        <p>A senior team across strategy, design, technology and growth — available when you need it.</p>
        <div class="final-actions">
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-solid trigger-checkout">Start subscription</a>
          <a href="<?php echo esc_url(home_url('/contact-us/')); ?>" class="btn btn-ghost">Talk to us</a>
        </div>
      </div>
    </section>

  </div>
