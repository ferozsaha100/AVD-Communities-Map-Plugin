/* AVD Map Guide — Admin JS */
jQuery(document).ready(function ($) {

  /* ══════════════════════════════════════════════════
     COLOR PICKERS
     wpColorPicker uses the Iris library — changes fire
     a custom 'iris-change' event, NOT a standard input
     event. We hook both to catch all updates.
  ══════════════════════════════════════════════════ */
  function initColorPickers( context ) {
    var $ctx = context ? $(context) : $(document);
    $ctx.find('.avdc-color-picker').each(function () {
      if ( $(this).hasClass('wp-color-picker') ) return; // already init'd
      $(this).wpColorPicker({
        change: function ( event, ui ) {
          // Fire a synthetic event so live previews pick it up
          var name = $(event.target).attr('name');
          var val  = ui.color.toString();
          updatePreview( name, val );
        },
        clear: function () {}
      });
    });
  }

  // Init all pickers on load
  initColorPickers();


  /* ══════════════════════════════════════════════════
     LIVE PREVIEWS
     Called whenever any color picker changes.
  ══════════════════════════════════════════════════ */
  function updatePreview( name, val ) {
    var $btn = $('#cta-preview-btn');

    if ( name === 'ae_color_cta_bg' && $btn.length ) {
      $btn.css('background', val);
    }
    if ( name === 'ae_color_cta_text' && $btn.length ) {
      $btn.css('color', val);
    }
    if ( name === 'ae_color_cta_bg_hover' && $btn.length ) {
      $btn.data('hover-bg', val);
    }

    // Live left-panel BG preview
    if ( name === 'ae_color_left_bg' ) {
      $('.avdc-left-preview').css('background', val);
    }
  }

  // Also catch direct hex typing in the text field
  $(document).on('input', '.avdc-color-picker', function () {
    var val = $(this).val();
    if ( /^#[0-9a-f]{3,6}$/i.test(val) ) {
      updatePreview( $(this).attr('name'), val );
    }
  });


  /* ══════════════════════════════════════════════════
     SETTINGS TABS
     Re-initialize color pickers when a tab is shown,
     because pickers in hidden elements don't render.
  ══════════════════════════════════════════════════ */
  $(document).on('click', '.avdc-tab', function () {
    var tab = $(this).data('tab');

    // Switch active tab button
    $('.avdc-tab').removeClass('avdc-tab--active');
    $(this).addClass('avdc-tab--active');

    // Switch visible panel
    $('.avdc-tab-panel').addClass('avdc-tab-panel--hidden');
    $('#tab-' + tab).removeClass('avdc-tab-panel--hidden');

    // Init any pickers inside the newly-visible panel
    initColorPickers('#tab-' + tab);
  });


  /* ══════════════════════════════════════════════════
     FONT PREVIEW — live font family swap + Google
     Fonts dynamic load when dropdown changes
  ══════════════════════════════════════════════════ */
  $(document).on('change', '.avdc-font-select', function () {
    var font      = $(this).val();
    var previewId = $(this).data('preview');
    var $preview  = $('#' + previewId);

    if ( $preview.length ) {
      $preview.css('font-family', "'" + font + "', sans-serif");
    }

    // Dynamically load the chosen font so preview renders immediately
    var href = 'https://fonts.googleapis.com/css2?family='
             + encodeURIComponent(font)
             + ':wght@400;600;700;800&display=swap';
    if ( !$('link[href="' + href + '"]').length ) {
      $('<link rel="stylesheet">').attr('href', href).appendTo('head');
    }
  });


  /* ══════════════════════════════════════════════════
     COPY SHORTCODE
  ══════════════════════════════════════════════════ */
  window.avdcCopyShortcode = function (el) {
    var text = el.textContent.trim();
    if (navigator.clipboard) {
      navigator.clipboard.writeText(text).then(function () { avdcFlash(el); });
    } else {
      var ta = document.createElement('textarea');
      ta.value = text; document.body.appendChild(ta);
      ta.select(); document.execCommand('copy');
      document.body.removeChild(ta); avdcFlash(el);
    }
  };

  function avdcFlash(el) {
    var orig = el.textContent, origBg = el.style.background, origColor = el.style.color;
    el.textContent = '✓ Copied!';
    el.style.background = '#d4edda'; el.style.color = '#155724';
    setTimeout(function () {
      el.textContent = orig; el.style.background = origBg; el.style.color = origColor;
    }, 1800);
  }


  /* ══════════════════════════════════════════════════
     WP MEDIA UPLOADER (Areas edit page)
  ══════════════════════════════════════════════════ */
  var mediaUploader;

  $(document).on('click', '#avdc-media-upload', function (e) {
    e.preventDefault();
    if (mediaUploader) { mediaUploader.open(); return; }

    mediaUploader = wp.media({
      title:    'Choose Area Photo',
      button:   { text: 'Use This Image' },
      multiple: false,
      library:  { type: 'image' },
    });

    mediaUploader.on('select', function () {
      var attach = mediaUploader.state().get('selection').first().toJSON();
      $('#avdc-image-url').val(attach.url);
      $('#avdc-img-preview-img').attr('src', attach.url);
      $('#avdc-img-preview').show();
      $('#avdc-img-placeholder').hide();
    });

    mediaUploader.open();
  });

  $(document).on('click', '#avdc-img-remove', function () {
    $('#avdc-image-url').val('');
    $('#avdc-img-preview').hide();
    $('#avdc-img-placeholder').show();
  });

  $(document).on('change blur', '#avdc-image-url', function () {
    var url = $(this).val().trim();
    if (url) {
      $('#avdc-img-preview-img').attr('src', url);
      $('#avdc-img-preview').show();
      $('#avdc-img-placeholder').hide();
    } else {
      $('#avdc-img-preview').hide();
      $('#avdc-img-placeholder').show();
    }
  });


  /* ══════════════════════════════════════════════════
     AUTO-DISMISS NOTICES
  ══════════════════════════════════════════════════ */
  setTimeout(function () { $('.avdc-notice').fadeOut(400); }, 4000);

});
