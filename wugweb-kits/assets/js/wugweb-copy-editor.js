/* "Text & links" panel for every wugweb/* block: lists the copy and links the block renders and lets the editor override each.
 * Overrides live in the block's `copy` attribute { original: replacement }; PHP applies them at render. */
(function (wp) {
  var el = wp.element.createElement, useState = wp.element.useState, useEffect = wp.element.useEffect;
  var InspectorControls = wp.blockEditor.InspectorControls, PanelBody = wp.components.PanelBody, TextControl = wp.components.TextControl, Spinner = wp.components.Spinner, Button = wp.components.Button;
  function extract(html) {
    var doc = new DOMParser().parseFromString('<div>' + html + '</div>', 'text/html'), out = [], seen = {};
    var walker = doc.createTreeWalker(doc.body, NodeFilter.SHOW_TEXT);
    while (walker.nextNode()) {
      var n = walker.currentNode, p = n.parentElement;
      if (!p || /^(SCRIPT|STYLE|NOSCRIPT)$/.test(p.tagName)) continue;
      var t = n.nodeValue.replace(/\s+/g, ' ').trim();
      if (t.length < 2 || seen['t' + t]) continue; seen['t' + t] = 1; out.push({ kind: 'text', key: t });
    }
    doc.querySelectorAll('a[href]').forEach(function (a) { var h = a.getAttribute('href'); if (!h || h.charAt(0) === '#' || seen['h' + h]) return; seen['h' + h] = 1; out.push({ kind: 'link', key: h }); });
    return out;
  }
  function Panel(props) {
    var a = props.attributes, set = props.setAttributes, copy = a.copy || {};
    var s = useState(null), items = s[0], setItems = s[1];
    useEffect(function () {
      var attrs = Object.assign({}, a); delete attrs.copy;
      var q = Object.keys(attrs).map(function (k) { var v = attrs[k]; return typeof v === 'object' ? '' : 'attributes[' + encodeURIComponent(k) + ']=' + encodeURIComponent(v); }).filter(Boolean).join('&');
      var pid = wp.data.select('core/editor') && wp.data.select('core/editor').getCurrentPostId && wp.data.select('core/editor').getCurrentPostId();
      wp.apiFetch({ path: '/wp/v2/block-renderer/' + props.name + '?context=edit&' + (pid ? 'post_id=' + pid + '&' : '') + q }).then(function (r) { setItems(extract(r.rendered || '')); }).catch(function () { setItems([]); });
    }, [props.name]);
    function put(key, val) { var c = Object.assign({}, copy); if (val === '' || val === key) delete c[key]; else c[key] = val; set({ copy: c }); }
    if (items === null) return el(PanelBody, { title: 'Text & links', initialOpen: false }, el(Spinner));
    if (!items.length) return null;
    return el(PanelBody, { title: 'Text & links (' + items.length + ')', initialOpen: false },
      items.map(function (it) {
        var cur = copy[it.key] !== undefined ? copy[it.key] : it.key;
        return el(TextControl, { key: it.kind + it.key, label: it.kind === 'link' ? 'Link' : 'Text', value: cur, onChange: function (v) { put(it.key, v); }, __nextHasNoMarginBottom: true });
      }),
      Object.keys(copy).length ? el(Button, { variant: 'link', isDestructive: true, onClick: function () { set({ copy: {} }); } }, 'Reset all') : null
    );
  }
  wp.hooks.addFilter('editor.BlockEdit', 'wugweb/copy-editor', function (BlockEdit) {
    return function (props) {
      if (!props.isSelected || props.name.indexOf('wugweb/') !== 0 || !props.attributes || !('copy' in props.attributes)) return el(BlockEdit, props);
      return el(wp.element.Fragment, null, el(BlockEdit, props), el(InspectorControls, null, el(Panel, props)));
    };
  });
})(window.wp);
