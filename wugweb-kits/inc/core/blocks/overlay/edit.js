(function (wp) {
  var el = wp.element.createElement, be = wp.blockEditor, c = wp.components;
  wp.blocks.registerBlockType('wugweb/overlay', {
    edit: function (p) {
      var a = p.attributes, set = p.setAttributes;
      var blockProps = be.useBlockProps({ className: 'wugweb-overlay-editor is-' + a.layout });
      return el(wp.element.Fragment, null,
        el(be.InspectorControls, null,
          el(c.PanelBody, { title: 'Overlay', initialOpen: true },
            el(c.SelectControl, { label: 'Layout', value: a.layout, options: [
              { label: 'Modal (centred)', value: 'modal' }, { label: 'Drawer right', value: 'drawer-right' }, { label: 'Drawer left', value: 'drawer-left' },
              { label: 'Drawer bottom (sheet)', value: 'drawer-bottom' }, { label: 'Banner bottom', value: 'banner-bottom' }], onChange: function (v) { set({ layout: v }); } }),
            el(c.TextControl, { label: 'Title', value: a.title, onChange: function (v) { set({ title: v }); } }),
            el(c.TextControl, { label: 'Close button label', value: a.closeLabel, onChange: function (v) { set({ closeLabel: v }); } }),
            el(c.ToggleControl, { label: 'Close on backdrop click', checked: a.backdropClose, onChange: function (v) { set({ backdropClose: v }); } }),
            el(c.ToggleControl, { label: 'Open on page load', checked: a.openOnLoad, onChange: function (v) { set({ openOnLoad: v }); } }),
            el('p', { style: { fontSize: 12 } }, 'Open with any button or link: data-overlay-target="' + (a.anchor || 'your-anchor') + '" (set the Anchor in Advanced) or href="#' + (a.anchor || 'your-anchor') + '".'))),
        el('div', blockProps,
          el('div', { className: 'wugweb-overlay-editor__label' }, 'Overlay · ' + a.layout + ' · ' + (a.title || '')),
          el(be.InnerBlocks, { templateLock: false, template: [['core/paragraph', { placeholder: 'Overlay content…' }]] })));
    },
    save: function () { return el(be.InnerBlocks.Content); }
  });
})(window.wp);
