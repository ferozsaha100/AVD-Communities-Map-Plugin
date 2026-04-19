<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wrap avdc-wrap">

  <div class="avdc-header">
    <div class="avdc-header__logo">🗺️</div>
    <div>
      <h1>AVD Communities Map — Areas Embed</h1>
      <p>Each area = one photo card in the two-panel embed. Add a city name, state, lat/lng, photo, and optional link.</p>
    </div>
    <a href="<?php echo admin_url('admin.php?page=avdc-communities&action=new'); ?>" class="avdc-btn avdc-btn--primary avdc-header__cta">
      + Add New Area
    </a>
  </div>

  <?php if ( isset($_GET['saved']) )   : ?><div class="avdc-notice avdc-notice--success">✓ Area saved successfully.</div><?php endif; ?>
  <?php if ( isset($_GET['deleted']) ) : ?><div class="avdc-notice avdc-notice--success">✓ Area deleted.</div><?php endif; ?>
  <?php if ( isset($_GET['db_fixed']) ) : ?><div class="avdc-notice avdc-notice--success">✓ Database rebuilt successfully. You can now add areas.</div><?php endif; ?>

  <!-- DB Health Check -->
  <?php
  $columns  = AVDC_Areas_DB::get_columns();
  $required = array( 'id', 'area_name', 'state', 'lat', 'lng', 'zoom', 'image_url', 'custom_link', 'group_label', 'sort_order', 'active' );
  $missing  = array_diff( $required, $columns );
  ?>
  <?php if ( ! empty( $missing ) ) : ?>
  <div class="avdc-db-alert">
    <div class="avdc-db-alert__icon">⚠️</div>
    <div class="avdc-db-alert__body">
      <strong>Database schema is outdated</strong> — the following columns are missing from the <code>wp_avdc_areas</code> table:
      <code><?php echo implode( ', ', array_map( 'esc_html', $missing ) ); ?></code><br>
      <strong>This is why areas aren't saving.</strong> Click the button below to rebuild the table with the correct structure.
    </div>
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="flex-shrink:0;">
      <input type="hidden" name="action" value="avdc_fix_db">
      <?php wp_nonce_field('avdc_fix_db'); ?>
      <button type="submit" class="avdc-btn avdc-btn--danger" onclick="return confirm('This will rebuild the areas table. Any existing area data will be cleared. Continue?');">
        🔧 Fix Database Now
      </button>
    </form>
  </div>
  <?php endif; ?>

  <!-- Shortcode info bar -->
  <div class="avdc-info-bar">
    <div class="avdc-info-bar__left">
      <span class="avdc-info-bar__icon">📋</span>
      <div>
        <strong>Embed Shortcode</strong>
        <p>Paste into any page/post to display the full two-panel Areas section. Section title, subtitle and CTA are set in <a href="<?php echo admin_url('admin.php?page=avdc-settings'); ?>">Settings</a>.</p>
      </div>
    </div>
    <div class="avdc-info-bar__codes">
      <code class="avdc-shortcode" onclick="avdcCopyShortcode(this)">[avdc_areas]</code>
      <span style="font-size:.68rem;color:#aaa;margin-left:.25rem;">click to copy</span>
    </div>
  </div>

  <!-- How it works -->
  <div class="avdc-embed-diagram">
    <div class="avdc-embed-diagram__left">
      <strong>LEFT PANEL (photo cards)</strong>
      <ul>
        <li>🏷 <em>Area Name</em> — shown on card label</li>
        <li>📍 <em>State</em> — shown in map city overlay</li>
        <li>🌐 <em>Lat / Lng / Zoom</em> — pans map on hover</li>
        <li>🖼 <em>Image URL</em> — card background photo</li>
        <li>🔗 <em>Custom Link</em> — click card to navigate</li>
        <li>📂 <em>Group Label</em> — divider row heading</li>
      </ul>
    </div>
    <div class="avdc-embed-diagram__arrow">→</div>
    <div class="avdc-embed-diagram__right">
      <strong>RIGHT PANEL (map)</strong>
      <ul>
        <li>Hover any card → map pans to that city</li>
        <li>Map color set in <a href="<?php echo admin_url('admin.php?page=avdc-settings'); ?>">Settings → Map Bg Color</a></li>
        <li>API key set in <a href="<?php echo admin_url('admin.php?page=avdc-settings'); ?>">Settings → API Key</a></li>
        <li>CTA button set in <a href="<?php echo admin_url('admin.php?page=avdc-settings'); ?>">Settings → Areas Embed</a></li>
      </ul>
    </div>
  </div>

  <?php if ( empty( $areas ) ) : ?>
    <div class="avdc-empty">
      <div class="avdc-empty__icon">🏙️</div>
      <h3>No areas yet</h3>
      <p>Add your first area — it becomes the first photo card and the default map center.</p>
      <a href="<?php echo admin_url('admin.php?page=avdc-communities&action=new'); ?>" class="avdc-btn avdc-btn--primary">
        + Add First Area
      </a>
    </div>
  <?php else : ?>

    <div class="avdc-table-wrap">
      <table class="avdc-table">
        <thead>
          <tr>
            <th style="width:50px;">Order</th>
            <th style="width:72px;">Photo</th>
            <th>Area Name</th>
            <th>State</th>
            <th>Lat / Lng</th>
            <th>Group Label</th>
            <th>Link</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ( $areas as $area ) : ?>
          <tr>
            <td class="avdc-table__id"><?php echo esc_html( $area->sort_order ); ?></td>
            <td>
              <?php if ( $area->image_url ) : ?>
                <img src="<?php echo esc_url($area->image_url); ?>" alt="" style="width:62px;height:40px;object-fit:cover;border-radius:6px;border:1px solid #eee;">
              <?php else : ?>
                <div style="width:62px;height:40px;background:linear-gradient(135deg,#0C0C30,#302EB7);border-radius:6px;display:flex;align-items:center;justify-content:center;font-size:1.1rem;">🏙️</div>
              <?php endif; ?>
            </td>
            <td><strong><?php echo esc_html($area->area_name); ?></strong></td>
            <td><?php echo esc_html($area->state); ?></td>
            <td style="font-family:monospace;font-size:.72rem;color:#888;">
              <?php echo esc_html($area->lat); ?>,<br><?php echo esc_html($area->lng); ?>
              <span style="display:block;color:#ccc;">zoom <?php echo esc_html($area->zoom); ?></span>
            </td>
            <td style="font-size:.78rem;color:#666;"><?php echo esc_html($area->group_label ?: '—'); ?></td>
            <td>
              <?php if ( $area->custom_link ) : ?>
                <a href="<?php echo esc_url($area->custom_link); ?>" target="_blank" style="font-size:.72rem;color:#302EB7;">↗ link</a>
              <?php else : ?>
                <span style="color:#ccc;font-size:.72rem;">none</span>
              <?php endif; ?>
            </td>
            <td>
              <span class="avdc-status <?php echo $area->active ? 'avdc-status--active' : 'avdc-status--draft'; ?>">
                <?php echo $area->active ? 'Active' : 'Hidden'; ?>
              </span>
            </td>
            <td class="avdc-table__actions">
              <a href="<?php echo admin_url('admin.php?page=avdc-communities&action=edit&area_id=' . $area->id); ?>" class="avdc-btn avdc-btn--sm">Edit</a>
              <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" style="display:inline;" onsubmit="return confirm('Delete this area?');">
                <input type="hidden" name="action"  value="avdc_delete_community">
                <input type="hidden" name="area_id" value="<?php echo esc_attr($area->id); ?>">
                <?php wp_nonce_field('avdc_delete_community'); ?>
                <button type="submit" class="avdc-btn avdc-btn--sm avdc-btn--danger">Delete</button>
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

  <?php endif; ?>

  <div class="avdc-admin-footer">
    <span>AVD Communities Map v2.1.0 &nbsp;&middot;&nbsp; Designed by <a href="https://agentviewdigital.com" target="_blank">Feroj Hossain</a> &mdash; <a href="https://agentviewdigital.com" target="_blank">Agent View Digital</a></span>
    <span class="avdc-footer-badge">avd.mapguide</span>
  </div>

</div>

<style>
.avdc-db-alert {
  display: flex;
  align-items: flex-start;
  gap: 1rem;
  background: #fff3cd;
  border: 2px solid #ffc107;
  border-radius: 10px;
  padding: 1rem 1.25rem;
  margin-bottom: 1.5rem;
}
.avdc-db-alert__icon { font-size: 1.5rem; flex-shrink: 0; }
.avdc-db-alert__body { flex: 1; font-size: .84rem; color: #5d4037; line-height: 1.6; }
.avdc-db-alert__body code {
  background: rgba(0,0,0,.08); padding: .1rem .4rem;
  border-radius: 4px; font-size: .75rem; color: #c0392b;
}
.avdc-db-alert strong { color: #333; }
</style>
