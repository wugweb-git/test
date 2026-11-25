(function (wp) {
  var el = wp.element.createElement, be = wp.blockEditor, c = wp.components, SSR = wp.serverSideRender;
  var TYPES = [['count', 'Count-up number / stat'], ['bar-h', 'Bar (horizontal)'], ['bar-v', 'Bar (vertical)'], ['progress', 'Progress bars'], ['line', 'Line'], ['area', 'Area'], ['sparkline', 'Sparkline'], ['donut', 'Donut'], ['ring', 'Progress ring'], ['compare', 'Before / after']];
  var COLORS = [['', 'Default (by position)'], ['fg', 'White'], ['neutral-3', 'Light grey'], ['neutral-5', 'Mid grey'], ['neutral-6', 'Dark grey'], ['accent', 'Accent']];
  wp.blocks.registerBlockType('wugweb/chart', {
    edit: function (p) {
      var a = p.attributes, set = p.setAttributes, rows = a.rows || [];
      function upd(i, k, v) { var r = rows.map(function (x, j) { return j === i ? Object.assign({}, x, (function () { var o = {}; o[k] = v; return o; })()) : x; }); set({ rows: r }); }
      return el(wp.element.Fragment, null,
        el(be.InspectorControls, null,
          el(c.PanelBody, { title: 'Chart', initialOpen: true },
            el(c.SelectControl, { label: 'Type', value: a.type, options: TYPES.map(function (t) { return { value: t[0], label: t[1] }; }), onChange: function (v) { set({ type: v }); } }),
            el(c.TextControl, { label: 'Title', value: a.title, onChange: function (v) { set({ title: v }); } }),
            el(c.TextControl, { label: 'Caption', value: a.caption, onChange: function (v) { set({ caption: v }); } }),
            el(c.TextControl, { label: 'Unit (e.g. %, +, k)', value: a.unit, onChange: function (v) { set({ unit: v }); } }),
            el(c.TextControl, { label: 'Max value (0 = auto)', type: 'number', value: a.max, onChange: function (v) { set({ max: parseFloat(v) || 0 }); } }),
            el(c.ToggleControl, { label: 'Show legend', checked: a.showLegend, onChange: function (v) { set({ showLegend: v }); } }),
            el(c.TextareaControl, { label: 'Screen-reader summary (optional)', value: a.summary, onChange: function (v) { set({ summary: v }); } })),
          el(c.PanelBody, { title: 'Data rows (' + rows.length + ')', initialOpen: true },
            rows.map(function (r, i) {
              return el('div', { key: i, style: { borderBottom: '1px solid #ddd', paddingBottom: 8, marginBottom: 8 } },
                el(c.TextControl, { label: 'Label', value: r.label || '', onChange: function (v) { upd(i, 'label', v); } }),
                el(c.TextControl, { label: 'Value', type: 'number', value: r.value, onChange: function (v) { upd(i, 'value', parseFloat(v) || 0); } }),
                el(c.TextControl, { label: 'Unit (optional)', value: r.unit || '', onChange: function (v) { upd(i, 'unit', v); } }),
                el(c.SelectControl, { label: 'Colour', value: r.color || '', options: COLORS.map(function (o) { return { value: o[0], label: o[1] }; }), onChange: function (v) { upd(i, 'color', v); } }),
                el(c.Button, { variant: 'link', isDestructive: true, onClick: function () { set({ rows: rows.filter(function (_, j) { return j !== i; }) }); } }, 'Remove row'));
            }),
            el(c.Button, { variant: 'secondary', onClick: function () { set({ rows: rows.concat([{ label: 'New', value: 50, unit: '', color: '' }]) }); } }, 'Add row'))),
        el('div', be.useBlockProps(), el(SSR, { block: 'wugweb/chart', attributes: a })));
    },
    save: function () { return null; }
  });
})(window.wp);
