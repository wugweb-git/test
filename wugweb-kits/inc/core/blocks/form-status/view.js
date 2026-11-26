/* Move focus to the result so screen readers announce it and keyboard users start there. */
(function () { var c = document.querySelector('[data-form-status]'); if (c) c.focus({ preventScroll: false }); })();
