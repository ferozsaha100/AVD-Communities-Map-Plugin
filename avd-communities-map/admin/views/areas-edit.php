<?php
if ( ! defined( 'ABSPATH' ) ) exit;
$is_edit    = ( $action === 'edit' && $area );
$page_title = $is_edit ? 'Edit Area' : 'Add New Area';
/* Helper: get field value or default */
$v = function( $field, $default = '' ) use ( $area, $is_edit ) {
    return ( $is_edit && isset( $area->$field ) ) ? $area->$field : $default;
};
?>
<div class="wrap avdc-wrap">

  <div class="avdc-header">
    <div class="avdc-header__logo">🏙️</div>
    <div>
      <h1><?php echo esc_html( $page_title ); ?></h1>
      <p>This area becomes one photo card in the <code>[avdc_areas]</code> embed. On hover it pans the map to the coordinates you set.</p>
    </div>
    <a href="<?php echo admin_url('admin.php?page=avdc-communities'); ?>" class="avdc-btn avdc-btn--ghost">← Back to Areas</a>
  </div>

  <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
    <input type="hidden" name="action"  value="avdc_save_community">
    <input type="hidden" name="area_id" value="<?php echo $is_edit ? esc_attr($area->id) : '0'; ?>">
    <?php wp_nonce_field('avdc_save_community'); ?>

    <div class="avdc-grid">

      <!-- ── AREA IDENTITY ──────────────────────── -->
      <div class="avdc-card avdc-card--full">
        <div class="avdc-card__head">
          <span class="avdc-card__icon">📝</span>
          <h2>Area Identity</h2>
        </div>
        <div class="avdc-card__body">
          <div class="avdc-two-col">

            <div class="avdc-field">
              <label>Area / City Name <span class="req">*</span></label>
              <input type="text" name="area_name" value="<?php echo esc_attr($v('area_name')); ?>" placeholder="e.g. Dearborn" class="avdc-input avdc-input--wide" required>
              <p class="avdc-help">Displayed on the card label and in the map overlay (e.g. "Dearborn").</p>
            </div>

            <div class="avdc-field">
              <label>State / Region <span class="req">*</span></label>
              <input type="text" name="state" value="<?php echo esc_attr($v('state')); ?>" placeholder="e.g. Michigan" class="avdc-input avdc-input--wide" required>
              <p class="avdc-help">Shown next to the city name in the map overlay — "Dearborn, <em>Michigan</em>".</p>
            </div>

            <div class="avdc-field">
              <label>Group Label</label>
              <input type="text" name="group_label" value="<?php echo esc_attr($v('group_label')); ?>" placeholder="e.g. 🏭 Michigan Cities" class="avdc-input avdc-input--wide">
              <p class="avdc-help">Areas sharing the same group label are placed under one divider row. Add an emoji prefix for style.</p>
            </div>

            <div class="avdc-field">
              <label>Sort Order</label>
              <input type="number" name="sort_order" value="<?php echo esc_attr($v('sort_order', 0)); ?>" min="0" max="999" class="avdc-input avdc-input--sm">
              <p class="avdc-help">Lower number = appears first. The very first active area is also the default map center.</p>
            </div>

          </div>

          <div class="avdc-field" style="margin-top:.5rem;">
            <label class="avdc-toggle-switch">
              <input type="checkbox" name="active" value="1" <?php checked( $v('active', 1), 1 ); ?>>
              <span class="avdc-toggle-slider"></span>
              <span class="avdc-toggle-label">Active — show this card in the embed</span>
            </label>
          </div>

        </div>
      </div>

      <!-- ── MAP COORDINATES ───────────────────── -->
      <div class="avdc-card">
        <div class="avdc-card__head">
          <span class="avdc-card__icon">📍</span>
          <h2>Map Coordinates</h2>
        </div>
        <div class="avdc-card__body">

          <div class="avdc-field">
            <label>Latitude <span class="req">*</span></label>
            <input type="text" name="lat" value="<?php echo esc_attr($v('lat')); ?>" placeholder="42.3223" class="avdc-input avdc-input--wide" required>
          </div>
          <div class="avdc-field">
            <label>Longitude <span class="req">*</span></label>
            <input type="text" name="lng" value="<?php echo esc_attr($v('lng')); ?>" placeholder="-83.1763" class="avdc-input avdc-input--wide" required>
          </div>
          <div class="avdc-field">
            <label>Zoom Level</label>
            <div class="avdc-range-wrap">
              <input type="range" name="zoom" id="zoom-range" min="10" max="17" value="<?php echo esc_attr($v('zoom', 13)); ?>" oninput="document.getElementById('zoom-val').textContent=this.value">
              <span class="avdc-range-val" id="zoom-val"><?php echo esc_attr($v('zoom', 13)); ?></span>
            </div>
            <p class="avdc-help">10 = wide city view &nbsp;·&nbsp; 13 = neighborhood &nbsp;·&nbsp; 16 = street level</p>
          </div>

          <div class="avdc-latlong-tip">
            <strong>💡 Find coordinates:</strong><br>
            Right-click any location on <a href="https://maps.google.com" target="_blank">Google Maps</a> → click the numbers at the top of the menu to copy lat/lng.
          </div>

        </div>
      </div>

      <!-- ── AREA IMAGE ────────────────────────── -->
      <div class="avdc-card">
        <div class="avdc-card__head">
          <span class="avdc-card__icon">🖼️</span>
          <h2>Area Photo</h2>
        </div>
        <div class="avdc-card__body">

          <div class="avdc-field">
            <label>Image URL</label>
            <div class="avdc-media-row">
              <input type="text" name="image_url" id="avdc-image-url" value="<?php echo esc_attr($v('image_url')); ?>" placeholder="https://…" class="avdc-input avdc-input--wide">
              <button type="button" class="avdc-btn avdc-btn--sm avdc-media-btn" id="avdc-media-upload">
                📁 Choose
              </button>
            </div>
            <p class="avdc-help">Card background photo. Paste any URL or use the WordPress Media Library. Best size: 900×500px, 16:9 ratio.</p>
          </div>

          <div id="avdc-img-preview" style="<?php echo $v('image_url') ? '' : 'display:none;'; ?>">
            <div class="avdc-img-preview">
              <img id="avdc-img-preview-img" src="<?php echo esc_url($v('image_url')); ?>" alt="">
              <button type="button" class="avdc-img-remove" id="avdc-img-remove">✕</button>
            </div>
          </div>
          <div id="avdc-img-placeholder" style="<?php echo $v('image_url') ? 'display:none;' : ''; ?>">
            <div class="avdc-img-placeholder">
              <span>🏙️</span>
              <p>No image — a dark gradient will be used</p>
            </div>
          </div>

        </div>
      </div>

      <!-- ── CUSTOM LINK ───────────────────────── -->
      <div class="avdc-card">
        <div class="avdc-card__head">
          <span class="avdc-card__icon">🔗</span>
          <h2>Area Detail Page Link</h2>
        </div>
        <div class="avdc-card__body">

          <div class="avdc-field">
            <label>Custom Link URL</label>
            <input type="url" name="custom_link" value="<?php echo esc_attr($v('custom_link')); ?>" placeholder="https://yoursite.com/areas/dearborn/" class="avdc-input avdc-input--wide">
            <p class="avdc-help">When a visitor <strong>clicks</strong> this card they will be taken to this page. Leave blank to disable click navigation.</p>
          </div>

        </div>
      </div>

      <?php if ( $is_edit ) : ?>
      <!-- ── LIVE CARD PREVIEW ──────────────────── -->
      <div class="avdc-card avdc-card--full">
        <div class="avdc-card__head">
          <span class="avdc-card__icon">👁️</span>
          <h2>Card Preview</h2>
        </div>
        <div class="avdc-card__body">
          <div class="avdc-card-preview">

            <!-- Mimics the exact .avdc-areas__card structure -->
            <div class="avdc-areas__card avdc-active" style="width:260px;flex-shrink:0;">
              <div class="avdc-areas__bar"></div>
              <div class="avdc-areas__bg" style="<?php
                echo $area->image_url
                  ? 'background-image:url(' . esc_url($area->image_url) . ');'
                  : 'background:linear-gradient(135deg,#1a1a40,#302EB7);';
              ?>"></div>
              <div class="avdc-areas__label">
                <span class="avdc-areas__name"><?php echo esc_html($area->area_name ?: 'Area Name'); ?></span>
                <span class="avdc-areas__arrow">→</span>
              </div>
            </div>

            <div style="margin-left:1.5rem;">
              <p style="font-size:.82rem;color:#555;line-height:1.7;max-width:320px;">
                This is how the card looks in the embed.<br>
                The map will pan to <strong><?php echo esc_html($area->area_name); ?>, <?php echo esc_html($area->state); ?></strong> when a visitor hovers this card.
                <?php if ($area->custom_link) : ?>
                  <br>Clicking navigates to <a href="<?php echo esc_url($area->custom_link); ?>" target="_blank"><?php echo esc_html($area->custom_link); ?></a>.
                <?php endif; ?>
              </p>
              <p style="font-size:.72rem;color:#aaa;margin-top:.5rem;">
                Coordinates: <?php echo esc_html($area->lat); ?>, <?php echo esc_html($area->lng); ?> · Zoom: <?php echo esc_html($area->zoom); ?>
              </p>
            </div>

          </div>
        </div>
      </div>
      <?php endif; ?>

    </div><!-- /grid -->

    <div class="avdc-save-bar">
      <button type="submit" class="avdc-btn avdc-btn--primary">
        <?php echo $is_edit ? '✓ Update Area' : '+ Create Area'; ?>
      </button>
      <a href="<?php echo admin_url('admin.php?page=avdc-communities'); ?>" class="avdc-btn avdc-btn--ghost-dark">Cancel</a>
    </div>

  </form>

  <!-- Embed the avdc-areas card CSS just for the preview box -->
  <style>
    .avdc-card-preview { display:flex; align-items:flex-start; gap:1.5rem; flex-wrap:wrap; }
    .avdc-latlong-tip {
      margin-top:1rem; padding:.75rem 1rem;
      background:#f8f8ff; border:1px dashed #c8c8ee; border-radius:8px;
      font-size:.75rem; color:#555; line-height:1.65;
    }
    .avdc-latlong-tip a { color:#302EB7; }
  </style>

  <div class="avdc-admin-footer">
    <span>AVD Communities Map v2.0.0 &nbsp;&middot;&nbsp; Designed by <a href="https://agentviewdigital.com" target="_blank">Feroj Hossain</a> &mdash; <a href="https://agentviewdigital.com" target="_blank">Agent View Digital</a></span>
    <span class="avdc-footer-badge">avd.mapguide</span>
  </div>

</div>
