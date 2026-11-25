(function(){

  // ─────────────────────────────────────────────────────────────────────
  // CLIENT PORTAL INTERACTIONS & STEP CONTROLS
  // ─────────────────────────────────────────────────────────────────────
  
  // 1. HERO QUEUE CYCLE ANIMATION
  const queueElement = document.getElementById('hero-queue');
  if (queueElement) {
    const items = queueElement.querySelectorAll('.queue-item');
    let currentIndex = 0;

    setInterval(() => {
      // Deactivate current active
      const currentItem = items[currentIndex];
      currentItem.classList.remove('active');
      const currentStatus = currentItem.querySelector('.item-status');
      if (currentStatus) {
        currentStatus.textContent = '○ COMPLETED';
        currentStatus.className = 'item-status status-queued';
      }

      // Promote next
      currentIndex = (currentIndex + 1) % items.length;
      const nextItem = items[currentIndex];
      nextItem.classList.add('active');
      const nextStatus = nextItem.querySelector('.item-status');
      if (nextStatus) {
        nextStatus.textContent = '● ACTIVE';
        nextStatus.className = 'item-status status-active';
      }
    }, 4500);
  }

  // 2. HOW IT WORKS CARD FOCUS FADING
  const stepCards = document.querySelectorAll('.step-card');
  const stepsContainer = document.querySelector('.steps-container');
  if (stepsContainer && stepCards.length) {
    stepCards.forEach(card => {
      card.addEventListener('mouseenter', () => {
        stepsContainer.classList.add('has-focus');
        card.classList.add('focused');
      });
      card.addEventListener('mouseleave', () => {
        stepsContainer.classList.remove('has-focus');
        card.classList.remove('focused');
      });
    });
  }

  // 3. CORE RULE INTERACTIVE DEMO PIPELINE
  const btnNextDemo = document.getElementById('btn-next-demo');
  const iqActiveTitle = document.getElementById('iq-active-title');
  const iqActiveRow = document.getElementById('iq-active-row');
  const interactiveQueueBox = document.getElementById('interactive-queue-box');
  
  const upcomingDemoTasks = [
    'Sanity CMS Integration',
    'Figma Guidelines Exports',
    'Google Analytics Setup',
    'CRO Landing Testing',
    'Operations Monitoring Setup'
  ];
  let demoIdx = 0;

  if (btnNextDemo && iqActiveTitle && iqActiveRow && interactiveQueueBox) {
    btnNextDemo.addEventListener('click', () => {
      // 1. Convert current active row to completed row
      const oldTitle = iqActiveTitle.textContent;
      
      const completedRow = document.createElement('div');
      completedRow.className = 'iq-row completed';
      completedRow.innerHTML = `
        <span class="iq-status">✓</span>
        <span class="iq-title">${oldTitle}</span>
        <span class="iq-tag">COMPLETED</span>
      `;
      interactiveQueueBox.insertBefore(completedRow, iqActiveRow);

      // 2. Shift next queued row to active
      const nextQueuedRow = interactiveQueueBox.querySelector('.iq-row.queued');
      if (nextQueuedRow) {
        const nextTitle = nextQueuedRow.querySelector('.iq-title').textContent;
        iqActiveTitle.textContent = nextTitle;
        nextQueuedRow.remove();
      } else {
        iqActiveTitle.textContent = upcomingDemoTasks[demoIdx % upcomingDemoTasks.length];
        demoIdx++;
      }

      // 3. Add a new item to the bottom of the queue
      const newQueuedRow = document.createElement('div');
      newQueuedRow.className = 'iq-row queued';
      newQueuedRow.innerHTML = `
        <span class="iq-status">○</span>
        <span class="iq-title">${upcomingDemoTasks[(demoIdx + 2) % upcomingDemoTasks.length]}</span>
        <span class="iq-tag">QUEUED</span>
      `;
      interactiveQueueBox.appendChild(newQueuedRow);
    });
  }

  // 4. WHAT YOU GET - CAPABILITY SELECTION
  const capSelector = document.getElementById('cap-selector');
  const capPanes = document.querySelectorAll('.cap-content-pane');
  if (capSelector && capPanes.length) {
    const buttons = capSelector.querySelectorAll('.selector-btn');
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        buttons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const targetCap = btn.getAttribute('data-cap');
        capPanes.forEach(pane => {
          pane.classList.remove('active');
          if (pane.id === `cap-pane-${targetCap}`) {
            pane.classList.add('active');
          }
        });
      });
    });
  }

  // 5. REQUEST EXPLORER FILTERS & DRAWERS
  const explorerFilters = document.getElementById('explorer-filters');
  const explorerGrid = document.getElementById('explorer-grid');
  if (explorerFilters && explorerGrid) {
    const filterButtons = explorerFilters.querySelectorAll('.filter-btn');
    const items = explorerGrid.querySelectorAll('.explorer-item');

    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const filterVal = btn.getAttribute('data-filter');
        items.forEach(item => {
          const category = item.getAttribute('data-category');
          if (filterVal === 'all' || category === filterVal) {
            item.style.display = 'flex';
          } else {
            item.style.display = 'none';
          }
        });
      });
    });
  }

  // Request descriptions master config
  const requestDetails = {
    'landing-page': {
      title: 'Landing page redesign',
      desc: 'Design or improve a high-converting campaign, product or marketing landing page to maximize leads.',
      output: 'UX + UI wireframe + responsive design mockup + Figma handover files'
    },
    'website-page': {
      title: 'Website page design',
      desc: 'Add brand new subpages, services pages or features descriptions pages inline with your website stylesheet.',
      output: 'Responsive page designs + Figma system tokens link + developer notes'
    },
    'ux-improvement': {
      title: 'UX improvement integration',
      desc: 'Conduct localized UX reviews and modify user journeys, sign-up forms, and onboarding flows.',
      output: 'Detailed UX critique report + interactive prototypes + flow diagrams'
    },
    'ui-screen': {
      title: 'UI screen layout',
      desc: 'Complete dashboard interfaces or software views designed matching clean dark/light themes.',
      output: 'High fidelity UI screens outputs + design specifications notes'
    },
    'ds-component': {
      title: 'Design system component',
      desc: 'Create, update or modularize button components, form inputs, sliders or layout grids inside Figma.',
      output: 'Figma component library link + variants system configured'
    },
    'prototype': {
      title: 'Interactive prototype',
      desc: 'Build functional Figma flow prototypes to pitch features or execute usability reviews.',
      output: 'Figma clickable prototype link + transition flows mapping'
    },
    'cms-update': {
      title: 'CMS schema update',
      desc: 'Modify Sanity schemas, configure content blocks, add landing structures or build custom sections fields.',
      output: 'Sanity Studio schemas push + database migration script'
    },
    'frontend-feature': {
      title: 'Frontend web feature',
      desc: 'Implement responsive React/Astro views, write CSS layouts, or build custom animation wrappers.',
      output: 'Tested, production-ready code pull request'
    },
    'integration': {
      title: 'API / Platform Integration',
      desc: 'Connect databases, email platforms, webhooks or payment processors into your backend systems.',
      output: 'API logic implementation + webhook handler scripts + tests'
    },
    'seo-improvement': {
      title: 'SEO audit and fix',
      desc: 'Optimize meta tags, configure structured JSON-LD schemas, increase speed metrics and verify sitemap.',
      output: 'Google Search Console review + fixes deployment'
    },
    'analytics-setup': {
      title: 'Analytics tracking setup',
      desc: 'Configure Google Tag Manager events, setup GA4 conversion funnels or connect posthog tracking.',
      output: 'Tags and triggers implementation audit + dashboard custom reports'
    },
    'campaign-asset': {
      title: 'Digital campaign asset',
      desc: 'Produce social media layouts, marketing banners, or custom campaign assets.',
      output: 'Optimized SVG/PNG outputs + creative sources Figma links'
    },
    'product-audit': {
      title: 'Digital product audit',
      desc: 'Verify design compliance, check console bugs, inspect speed load benchmarks and outline fixes.',
      output: 'Comprehensive digital performance and design review PDF'
    },
    'conversion-improvement': {
      title: 'Conversion CRO review',
      desc: 'Implement localized A/B testing variations, streamline checkout gates, and optimize CTA weights.',
      output: 'CRO fixes recommendations + alternative UI assets'
    }
  };

  const drawerOverlay = document.getElementById('explorer-drawer-overlay');
  const drawerTitle = document.getElementById('drawer-title');
  const drawerDesc = document.getElementById('drawer-desc');
  const drawerOutput = document.getElementById('drawer-output');
  const btnCloseDrawer = document.getElementById('btn-close-drawer');

  if (explorerGrid && drawerOverlay && drawerTitle && drawerDesc && drawerOutput && btnCloseDrawer) {
    const items = explorerGrid.querySelectorAll('.explorer-item');
    items.forEach(item => {
      item.addEventListener('click', () => {
        const id = item.getAttribute('data-id');
        const detail = requestDetails[id];
        if (detail) {
          drawerTitle.textContent = detail.title;
          drawerDesc.textContent = detail.desc;
          drawerOutput.textContent = detail.output;
          drawerOverlay.classList.add('active');
        }
      });
    });

    const closeDrawer = () => {
      drawerOverlay.classList.remove('active');
    };

    btnCloseDrawer.addEventListener('click', closeDrawer);
    drawerOverlay.addEventListener('click', (e) => {
      if (e.target === drawerOverlay) closeDrawer();
    });

    window.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && drawerOverlay.classList.contains('active')) {
        closeDrawer();
      }
    });
  }

  // 6. ACCORDION PANELS (Boundaries & FAQ)
  const setupAccordion = (containerId) => {
    const container = document.getElementById(containerId);
    if (container) {
      const items = container.querySelectorAll('.accordion-item, .faq-item');
      items.forEach(item => {
        const header = item.querySelector('.accordion-header, .faq-header');
        if (header) {
          header.addEventListener('click', () => {
            const isActive = item.classList.contains('active');
            // Close all
            items.forEach(i => i.classList.remove('active'));
            // Toggle clicked
            if (!isActive) {
              item.classList.add('active');
            }
          });
        }
      });
    }
  };
  setupAccordion('boundaries-accordion');
  setupAccordion('faq-accordion');

  // 9. PAUSE & CANCEL SETTINGS MANAGEMENT INTERFACE
  const btnPause = document.getElementById('btn-pc-pause');
  const btnResume = document.getElementById('btn-pc-resume');
  const btnCancel = document.getElementById('btn-pc-cancel');
  const statusBadge = document.getElementById('workspace-status-badge');

  if (btnPause && btnResume && btnCancel && statusBadge) {
    btnPause.addEventListener('click', () => {
      statusBadge.textContent = 'PAUSED';
      statusBadge.style.backgroundColor = 'var(--neutral-6)';
      statusBadge.style.color = 'var(--color-text-secondary)';
      btnPause.style.display = 'none';
      btnResume.style.display = 'inline-block';
      alert('Subscription paused successfully.');
    });

    btnResume.addEventListener('click', () => {
      statusBadge.textContent = 'ACTIVE';
      statusBadge.style.backgroundColor = 'var(--color-accent-primary)';
      statusBadge.style.color = 'var(--neutral-10)';
      btnResume.style.display = 'none';
      btnPause.style.display = 'inline-block';
      alert('Subscription active.');
    });

    btnCancel.addEventListener('click', () => {
      if (confirm('Are you sure you want to cancel your subscription?')) {
        statusBadge.textContent = 'CANCELLED';
        statusBadge.style.backgroundColor = 'var(--color-feedback-error)';
        statusBadge.style.color = 'var(--color-surface-inverse)';
        btnPause.style.display = 'none';
        btnResume.style.display = 'inline-block';
        alert('Subscription scheduled for cancellation.');
      }
    });
  }

})();
