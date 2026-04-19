<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/* ── Helpers (local to this view) ── */
if ( ! function_exists('ae_color') ) :
function ae_color( $key, $label, $s, $help = '' ) {
    $val = isset( $s[$key] ) ? $s[$key] : '#000000';
    echo '<div class="avdc-field avdc-field--inline">';
    echo '<label>' . esc_html($label) . '</label>';
    echo '<input type="text" name="' . esc_attr($key) . '" value="' . esc_attr($val) . '" class="avdc-color-picker">';
    if ($help) echo '<p class="avdc-help">' . esc_html($help) . '</p>';
    echo '</div>';
}
endif;

if ( ! function_exists('ae_fonts') ) :
function ae_fonts() {
    return array(
        'Bricolage Grotesque' => 'Bricolage Grotesque',
        'IBM Plex Sans'       => 'IBM Plex Sans',
        'Inter'               => 'Inter',
        'Poppins'             => 'Poppins',
        'Raleway'             => 'Raleway',
        'Montserrat'          => 'Montserrat',
        'Playfair Display'    => 'Playfair Display',
        'Lato'                => 'Lato',
        'Nunito'              => 'Nunito',
        'Open Sans'           => 'Open Sans',
    );
}
endif;
?>
<div class="wrap avdc-wrap">

  <div class="avdc-header">
    <div class="avdc-header__logo">📍</div>
    <div>
      <h1>AVD Communities Map — Settings</h1>
      <p>Configure API key, section content, and full visual styling for the Areas Embed.</p>
    </div>
  </div>

  <?php if ( $saved ) : ?>
    <div class="avdc-notice avdc-notice--success">✓ Settings saved.</div>
  <?php endif; ?>

  <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
    <input type="hidden" name="action" value="avdc_save_settings">
    <?php wp_nonce_field('avdc_save_settings'); ?>

    <!-- ════ TABS ════════════════════════════════════ -->
    <div class="avdc-tabs">
      <button type="button" class="avdc-tab avdc-tab--active" data-tab="api">🔑 API Keys</button>
      <button type="button" class="avdc-tab" data-tab="content">✏️ Content</button>
      <button type="button" class="avdc-tab" data-tab="style">🎨 Areas Style</button>
      <button type="button" class="avdc-tab" data-tab="mapcolors">🗺️ Map Colors</button>
    </div>

    <!-- ════════════════════════════════════════════════
         TAB 1 — API KEYS & MAP PROVIDER
    ════════════════════════════════════════════════════ -->
    <div class="avdc-tab-panel" id="tab-api">

      <!-- Provider selector -->
      <div class="avdc-card avdc-card--full">
        <div class="avdc-card__head"><span class="avdc-card__icon">🗺️</span><h2>Map Provider</h2></div>
        <div class="avdc-card__body">
          <div class="avdc-provider-toggle">
            <label class="avdc-provider-option <?php echo ($s['map_provider']==='google') ? 'avdc-provider-option--active' : ''; ?>">
              <input type="radio" name="map_provider" value="google" <?php checked($s['map_provider'], 'google'); ?>>
              <span class="avdc-provider-icon">G</span>
              <span class="avdc-provider-label">Google Maps</span>
              <span class="avdc-provider-desc">Custom styled map with precise color control</span>
            </label>
            <label class="avdc-provider-option <?php echo ($s['map_provider']==='mapbox') ? 'avdc-provider-option--active' : ''; ?>">
              <input type="radio" name="map_provider" value="mapbox" <?php checked($s['map_provider'], 'mapbox'); ?>>
              <span class="avdc-provider-icon avdc-provider-icon--mapbox">M</span>
              <span class="avdc-provider-label">Mapbox</span>
              <span class="avdc-provider-desc">Beautiful vector maps with smooth flyTo animations</span>
            </label>
          </div>
        </div>
      </div>

      <!-- Google API Key -->
      <div class="avdc-card avdc-card--full avdc-provider-panel" id="panel-google" style="<?php echo ($s['map_provider']==='mapbox') ? 'display:none;' : ''; ?>margin-top:16px;">
        <div class="avdc-card__head"><span class="avdc-card__icon">🔑</span><h2>Google Maps API Key</h2></div>
        <div class="avdc-card__body">
          <div class="avdc-field">
            <label>API Key</label>
            <input type="text" name="api_key" value="<?php echo esc_attr($s['api_key']); ?>" placeholder="AIzaSy…" class="avdc-input avdc-input--wide">
            <p class="avdc-help">Get yours at <a href="https://console.cloud.google.com/" target="_blank">console.cloud.google.com</a> — enable <strong>Maps JavaScript API</strong>. Also used by AVD Schools &amp; Reviews.</p>
          </div>
          <?php if ( $s['api_key'] ) : ?>
            <div class="avdc-api-status avdc-api-status--ok">✓ Google API key is set</div>
          <?php else : ?>
            <div class="avdc-api-status avdc-api-status--warn">⚠ No Google API key — map will not load</div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Mapbox API Key + Style -->
      <div class="avdc-card avdc-card--full avdc-provider-panel" id="panel-mapbox" style="<?php echo ($s['map_provider']==='google') ? 'display:none;' : ''; ?>margin-top:16px;">
        <div class="avdc-card__head"><span class="avdc-card__icon">🔑</span><h2>Mapbox Access Token</h2></div>
        <div class="avdc-card__body">
          <div class="avdc-field">
            <label>Access Token</label>
            <input type="text" name="mapbox_api_key" value="<?php echo esc_attr($s['mapbox_api_key']); ?>" placeholder="pk.eyJ1IjoiY…" class="avdc-input avdc-input--wide">
            <p class="avdc-help">Get a free token at <a href="https://account.mapbox.com/access-tokens/" target="_blank">account.mapbox.com</a> — create a new public token with default scopes.</p>
          </div>
          <?php if ( !empty($s['mapbox_api_key']) ) : ?>
            <div class="avdc-api-status avdc-api-status--ok">✓ Mapbox token is set</div>
          <?php else : ?>
            <div class="avdc-api-status avdc-api-status--warn">⚠ No Mapbox token — map will not load</div>
          <?php endif; ?>

          <!-- Style picker -->
          <div class="avdc-field" style="margin-top:1.5rem;padding-top:1.5rem;border-top:1px solid #f0f0f0;">
            <label>Map Style</label>
            <?php
            $mb_styles = array(
              'mapbox://styles/mapbox/dark-v11'              => '🌑 Dark (default)',
              'mapbox://styles/mapbox/light-v11'             => '☀️ Light',
              'mapbox://styles/mapbox/streets-v12'           => '🏙️ Streets',
              'mapbox://styles/mapbox/outdoors-v12'          => '🏔️ Outdoors',
              'mapbox://styles/mapbox/satellite-streets-v12' => '🛰️ Satellite Streets',
              'mapbox://styles/mapbox/navigation-day-v1'     => '🧭 Navigation Day',
              'mapbox://styles/mapbox/navigation-night-v1'   => '🌙 Navigation Night',
            );
            ?>
            <div class="avdc-mapbox-style-grid">
              <?php foreach ( $mb_styles as $val => $label ) :
                $active = ($s['mapbox_style'] === $val) ? ' avdc-style-tile--active' : '';
              ?>
              <label class="avdc-style-tile<?php echo $active; ?>">
                <input type="radio" name="mapbox_style" value="<?php echo esc_attr($val); ?>" <?php checked($s['mapbox_style'], $val); ?> style="position:absolute;opacity:0;">
                <span class="avdc-style-tile__label"><?php echo esc_html($label); ?></span>
              </label>
              <?php endforeach; ?>
            </div>
            <p class="avdc-help" style="margin-top:.5rem;">Note: Custom map colors in the <strong>Map Colors</strong> tab apply to Google Maps only. Mapbox uses its built-in style colors.</p>
          </div>
        </div>
      </div>

      <!-- Shortcode reference -->
      <div class="avdc-card avdc-card--full" style="margin-top:16px;">
        <div class="avdc-card__head"><span class="avdc-card__icon">📋</span><h2>Shortcode Reference</h2></div>
        <div class="avdc-card__body">
          <div style="padding:1rem 1.25rem;background:#f0f0ff;border:1px solid #c8c8ee;border-radius:10px;font-size:.82rem;color:#333;line-height:1.9;">
            <strong style="display:block;margin-bottom:.4rem;font-size:.84rem;color:#302EB7;">📍 Communities Map</strong>
            <code style="background:#fff;border:1px solid #ddd;padding:.15rem .5rem;border-radius:4px;font-size:.78rem;display:inline-block;margin:.2rem 0;">[avdc_areas]</code><br>
            <code style="background:#fff;border:1px solid #ddd;padding:.15rem .5rem;border-radius:4px;font-size:.78rem;display:inline-block;margin:.2rem 0;">[avdc_areas title="Our Markets" subtitle="Click any area to explore"]</code><br>
            <span style="color:#888;font-size:.76rem;">Map provider is set globally above. Add communities in <strong>Appearance → AVD Communities Map</strong>.</span>
          </div>
        </div>
      </div>

    </div>

    <!-- ════════════════════════════════════════════════
         TAB 2 — CONTENT
    ════════════════════════════════════════════════════ -->
    <div class="avdc-tab-panel avdc-tab-panel--hidden" id="tab-content">
      <div class="avdc-card avdc-card--full">
        <div class="avdc-card__head"><span class="avdc-card__icon">✏️</span><h2>Areas Embed — Section Content</h2></div>
        <div class="avdc-card__body">
          <div class="avdc-two-col">
            <div class="avdc-field">
              <label>Eyebrow Label</label>
              <input type="text" name="areas_eyebrow" value="<?php echo esc_attr($s['areas_eyebrow']); ?>" class="avdc-input avdc-input--wide">
              <p class="avdc-help">Small uppercase line above the title.</p>
            </div>
            <div class="avdc-field">
              <label>Section Title</label>
              <input type="text" name="areas_title" value="<?php echo esc_attr($s['areas_title']); ?>" class="avdc-input avdc-input--wide">
              <p class="avdc-help">Wrap a word in <code>&lt;em&gt;</code> for the gold italic style.</p>
            </div>
          </div>
          <div class="avdc-field">
            <label>Subtitle</label>
            <input type="text" name="areas_subtitle" value="<?php echo esc_attr($s['areas_subtitle']); ?>" class="avdc-input avdc-input--wide">
          </div>
          <div class="avdc-two-col">
            <div class="avdc-field">
              <label>CTA Button Text</label>
              <input type="text" name="areas_cta_text" value="<?php echo esc_attr($s['areas_cta_text']); ?>" class="avdc-input avdc-input--wide">
              <p class="avdc-help">Leave blank to hide button.</p>
            </div>
            <div class="avdc-field">
              <label>CTA Button URL</label>
              <input type="url" name="areas_cta_url" value="<?php echo esc_attr($s['areas_cta_url']); ?>" class="avdc-input avdc-input--wide">
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ════════════════════════════════════════════════
         TAB 3 — AREAS STYLE
    ════════════════════════════════════════════════════ -->
    <div class="avdc-tab-panel avdc-tab-panel--hidden" id="tab-style">

      <!-- FONTS -->
      <div class="avdc-card avdc-card--full">
        <div class="avdc-card__head"><span class="avdc-card__icon">🔤</span><h2>Font Family</h2></div>
        <div class="avdc-card__body">
          <div class="avdc-two-col">

            <div class="avdc-field">
              <label>Heading Font</label>
              <select name="ae_font_heading" class="avdc-input avdc-input--wide avdc-font-select" data-preview="preview-heading">
                <?php foreach ( ae_fonts() as $v => $l ) : ?>
                  <option value="<?php echo esc_attr($v); ?>" <?php selected($s['ae_font_heading'], $v); ?>><?php echo esc_html($l); ?></option>
                <?php endforeach; ?>
              </select>
              <p class="avdc-help">Title, card names, city overlay, CTA button.</p>
              <div class="avdc-font-preview" id="preview-heading" style="font-family:'<?php echo esc_attr($s['ae_font_heading']); ?>',sans-serif;">
                Areas of Expertise
              </div>
            </div>

            <div class="avdc-field">
              <label>Body Font</label>
              <select name="ae_font_body" class="avdc-input avdc-input--wide avdc-font-select" data-preview="preview-body">
                <?php foreach ( ae_fonts() as $v => $l ) : ?>
                  <option value="<?php echo esc_attr($v); ?>" <?php selected($s['ae_font_body'], $v); ?>><?php echo esc_html($l); ?></option>
                <?php endforeach; ?>
              </select>
              <p class="avdc-help">Subtitle paragraph and hint text.</p>
              <div class="avdc-font-preview avdc-font-preview--sm" id="preview-body" style="font-family:'<?php echo esc_attr($s['ae_font_body']); ?>',sans-serif;">
                Hover any area to explore on the map.
              </div>
            </div>

          </div>
        </div>
      </div>

      <div class="avdc-grid">

        <!-- LEFT PANEL COLORS -->
        <div class="avdc-card">
          <div class="avdc-card__head"><span class="avdc-card__icon">⬜</span><h2>Left Panel</h2></div>
          <div class="avdc-card__body">
            <?php ae_color('ae_color_left_bg',  'Panel Background', $s, 'White/light panel behind the photo cards.'); ?>
            <?php ae_color('ae_color_eyebrow',  'Eyebrow Text',     $s, 'Small uppercase label above the title.'); ?>
            <?php ae_color('ae_color_title',    'Title Text',       $s); ?>
            <?php ae_color('ae_color_subtitle', 'Subtitle Text',    $s); ?>
          </div>
        </div>

        <!-- BRAND / ACCENT -->
        <div class="avdc-card">
          <div class="avdc-card__head"><span class="avdc-card__icon">🎨</span><h2>Brand / Accent</h2></div>
          <div class="avdc-card__body">
            <?php ae_color('ae_color_accent',       'Primary Accent',   $s, 'Main brand colour — eyebrow line, pin, pulse ring.'); ?>
            <?php ae_color('ae_color_accent_light', 'Accent Light',     $s, 'Gradient end for title italic and sweep bar.'); ?>
            <?php ae_color('ae_color_dark',         'Dark / Base',      $s, 'Section background and card overlay base.'); ?>
            <?php ae_color('ae_color_card_bg',      'Card Fallback BG', $s, 'Card colour when no photo is set.'); ?>
          </div>
        </div>

        <!-- CARD HOVER -->
        <div class="avdc-card">
          <div class="avdc-card__head"><span class="avdc-card__icon">✨</span><h2>Card Hover / Active</h2></div>
          <div class="avdc-card__body">
            <?php ae_color('ae_color_hover_bar',      'Top Sweep Bar',      $s, 'Colour of the bar that animates on hover.'); ?>
            <?php ae_color('ae_color_hover_name',     'City Name on Hover', $s, 'Card city name colour when hovered.'); ?>
            <?php ae_color('ae_color_arrow_hover_bg', 'Arrow Circle',       $s, 'Arrow circle background on hover.'); ?>
          </div>
        </div>

        <!-- CTA BUTTON -->
        <div class="avdc-card">
          <div class="avdc-card__head"><span class="avdc-card__icon">🔘</span><h2>CTA Button</h2></div>
          <div class="avdc-card__body">
            <?php ae_color('ae_color_cta_bg',       'Background',       $s); ?>
            <?php ae_color('ae_color_cta_text',     'Text',             $s); ?>
            <?php ae_color('ae_color_cta_bg_hover', 'Hover Background', $s); ?>
            <!-- Live preview -->
            <div style="margin-top:1rem;padding:1rem;background:#12122e;border-radius:8px;display:flex;align-items:center;justify-content:center;">
              <span id="cta-preview-btn" style="
                font-size:.72rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;
                padding:.5rem 1.1rem;border-radius:100px;display:inline-block;
                background:<?php echo esc_attr($s['ae_color_cta_bg']); ?>;
                color:<?php echo esc_attr($s['ae_color_cta_text']); ?>;">
                <?php echo esc_html( $s['areas_cta_text'] ?: 'Book Strategy Session →' ); ?>
              </span>
            </div>
          </div>
        </div>

      </div><!-- /grid -->
    </div><!-- /tab-style -->

    <!-- ════════════════════════════════════════════════
         TAB 4 — MAP COLORS
    ════════════════════════════════════════════════════ -->
    <div class="avdc-tab-panel avdc-tab-panel--hidden" id="tab-mapcolors">
      <div class="avdc-card avdc-card--full">
        <div class="avdc-card__head">
          <span class="avdc-card__icon">🗺️</span>
          <h2>Areas Embed — Map Panel Colors</h2>
          <span class="avdc-card__sub">Right-side map for <code>[avdc_areas]</code> — applied when using <strong>Google Maps</strong> provider only</span>
        </div>
        <div class="avdc-card__body">
          <div class="avdc-two-col">
            <div>
              <?php ae_color('ae_map_bg',         'Map Base / Land',     $s, 'Background colour of all land/geometry.'); ?>
              <?php ae_color('ae_map_road',        'Roads',               $s); ?>
              <?php ae_color('ae_map_highway',     'Highways',            $s); ?>
              <?php ae_color('ae_map_water',       'Water',               $s); ?>
            </div>
            <div>
              <?php ae_color('ae_map_label',       'Label Text',          $s, 'Street and place name colour.'); ?>
              <?php ae_color('ae_map_border',      'Admin Boundaries',    $s, 'City/county border line colour.'); ?>
              <?php ae_color('ae_map_marker',      'Location Pin',        $s, 'Bouncing circle marker on city change.'); ?>
              <?php ae_color('ae_map_city_color',  'City Name Text',      $s, 'Overlay text at top of map panel.'); ?>
            </div>
          </div>
        </div>
      </div>
    </div><!-- /tab-mapcolors -->



    <div class="avdc-save-bar">
      <button type="submit" class="avdc-btn avdc-btn--primary">💾 Save All Settings</button>
      <span class="avdc-save-note">Changes apply immediately to all embeds.</span>
    </div>

  </form>

  <div class="avdc-admin-footer">
    <span>AVD Communities Map v1.0.2 &nbsp;&middot;&nbsp; Designed by <a href="https://agentviewdigital.com" target="_blank">Feroj Hossain</a> &mdash; <a href="https://agentviewdigital.com" target="_blank">Agent View Digital</a></span>
    <span class="avdc-footer-badge">avd.mapguide</span>
  </div>

</div><!-- /wrap -->

<script>
/* ── Tab switching ── */
document.querySelectorAll('.avdc-tab').forEach(function(btn) {
  btn.addEventListener('click', function() {
    document.querySelectorAll('.avdc-tab').forEach(function(b) { b.classList.remove('avdc-tab--active'); });
    document.querySelectorAll('.avdc-tab-panel').forEach(function(p) { p.classList.add('avdc-tab-panel--hidden'); });
    btn.classList.add('avdc-tab--active');
    document.getElementById('tab-' + btn.dataset.tab).classList.remove('avdc-tab-panel--hidden');
  });
});

/* ── Live font preview ── */
document.querySelectorAll('.avdc-font-select').forEach(function(sel) {
  sel.addEventListener('change', function() {
    var previewEl = document.getElementById(sel.dataset.preview);
    if (!previewEl) return;
    previewEl.style.fontFamily = "'" + sel.value + "', sans-serif";
    var link = document.createElement('link');
    link.rel  = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=' + encodeURIComponent(sel.value) + ':wght@400;700;800&display=swap';
    document.head.appendChild(link);
  });
});

/* ── Live CTA button preview ── */
document.addEventListener('input', function(e) {
  var btn = document.getElementById('cta-preview-btn');
  if (!btn) return;
  if (e.target.name === 'ae_color_cta_bg')   btn.style.background = e.target.value;
  if (e.target.name === 'ae_color_cta_text') btn.style.color       = e.target.value;
});

/* ── Map provider toggle ── */
document.querySelectorAll('input[name="map_provider"]').forEach(function(radio) {
  radio.addEventListener('change', function() {
    var isMapbox = this.value === 'mapbox';
    document.getElementById('panel-google').style.display = isMapbox ? 'none' : '';
    document.getElementById('panel-mapbox').style.display = isMapbox ? '' : 'none';
    // Update active styling on provider cards
    document.querySelectorAll('.avdc-provider-option').forEach(function(opt) {
      opt.classList.remove('avdc-provider-option--active');
    });
    this.closest('.avdc-provider-option').classList.add('avdc-provider-option--active');
  });
});

/* ── Mapbox style tile selector ── */
document.querySelectorAll('input[name="mapbox_style"]').forEach(function(radio) {
  radio.addEventListener('change', function() {
    document.querySelectorAll('.avdc-style-tile').forEach(function(t) {
      t.classList.remove('avdc-style-tile--active');
    });
    this.closest('.avdc-style-tile').classList.add('avdc-style-tile--active');
  });
});
</script>

<style>
/* ── SETTINGS TABS ───────────────────────────────── */
.avdc-tabs {
  display: flex;
  gap: .4rem;
  margin-bottom: 1.5rem;
  flex-wrap: wrap;
  border-bottom: 2px solid #e8e8f0;
  padding-bottom: .75rem;
}
.avdc-tab {
  padding: .5rem 1rem;
  border: 1px solid #e0e0ee;
  border-radius: 8px 8px 0 0;
  background: #f8f8fc;
  color: #666;
  font-size: .78rem;
  font-weight: 600;
  cursor: pointer;
  transition: all .2s;
  position: relative;
  bottom: -2px;
}
.avdc-tab:hover { background: #efefff; color: #302EB7; border-color: #c0c0ee; }
.avdc-tab--active {
  background: #fff;
  border-color: #302EB7;
  border-bottom-color: #fff;
  color: #302EB7;
  z-index: 2;
}
.avdc-tab-panel--hidden { display: none !important; }

/* ── FONT PREVIEW ────────────────────────────────── */
.avdc-font-preview {
  margin-top: .6rem;
  padding: .65rem 1rem;
  background: #f0f0f8;
  border-radius: 8px;
  font-size: 1.35rem;
  font-weight: 800;
  color: #0C0C30;
  line-height: 1.2;
  border: 1px solid #e0e0ee;
}
.avdc-font-preview--sm {
  font-size: .88rem;
  font-weight: 400;
}

/* ── PROVIDER TOGGLE ─────────────────────────── */
.avdc-provider-toggle {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 1rem;
}
@media (max-width: 600px) { .avdc-provider-toggle { grid-template-columns: 1fr; } }

.avdc-provider-option {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: .5rem;
  padding: 1.25rem 1rem;
  border: 2px solid #e0e0ee;
  border-radius: 12px;
  background: #fafafe;
  cursor: pointer;
  transition: all .2s;
  text-align: center;
}
.avdc-provider-option:hover { border-color: #302EB7; background: #f0f0ff; }
.avdc-provider-option--active {
  border-color: #302EB7;
  background: #ededff;
  box-shadow: 0 0 0 3px rgba(48,46,183,.12);
}
.avdc-provider-icon {
  width: 40px; height: 40px;
  border-radius: 50%;
  background: #4285F4;
  color: #fff;
  font-size: 1.1rem;
  font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.avdc-provider-icon--mapbox { background: #000; }
.avdc-provider-label {
  font-weight: 700;
  font-size: .9rem;
  color: #1a1a40;
}
.avdc-provider-desc {
  font-size: .75rem;
  color: #777;
  line-height: 1.4;
}

/* ── MAPBOX STYLE GRID ───────────────────────── */
.avdc-mapbox-style-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
  gap: .6rem;
  margin-top: .5rem;
}
.avdc-style-tile {
  position: relative;
  border: 2px solid #e0e0ee;
  border-radius: 8px;
  padding: .65rem .75rem;
  cursor: pointer;
  transition: all .18s;
  background: #fafafe;
  display: flex;
  align-items: center;
  gap: .4rem;
}
.avdc-style-tile:hover { border-color: #302EB7; background: #f0f0ff; }
.avdc-style-tile--active {
  border-color: #302EB7;
  background: #ededff;
  box-shadow: 0 0 0 3px rgba(48,46,183,.12);
}
.avdc-style-tile__label {
  font-size: .78rem;
  font-weight: 600;
  color: #333;
  line-height: 1.3;
}
</style>
