<?php
/**
 * Template: Areas Embed
 *
 * STRUCTURE:
 *   <section.avdc-areas>                  — CSS grid: 1fr 1fr
 *     <div.avdc-areas__left>              — white left panel
 *     <div.avdc-areas__right>             — sticky map panel
 *       <div.avdc-areas__map>             — map fills 100%
 *       <div.avdc-areas__map-top>         — city overlay (position:absolute)
 *       <div.avdc-areas__pin>             — pulse pin (position:absolute)
 *       <div.avdc-areas__map-bot>         — hint + CTA (position:absolute)
 *
 * Variables expected from including scope:
 *   $eyebrow, $title, $subtitle, $cta_text, $cta_url
 *   $api_key, $map_bg, $grouped, $first_area
 *   $instance_id  (optional — empty string for shortcode, widget ID for Elementor)
 */
if ( ! defined( 'ABSPATH' ) ) exit;

// Scoped element IDs — keeps multiple widget instances independent on the same page.
$iid        = isset( $instance_id ) && $instance_id !== '' ? $instance_id : '';
$map_el_id  = $iid ? 'avdc-map-' . $iid : 'avdc-map';
$city_el_id = $iid ? 'avdcMapCity-' . $iid : 'avdcMapCity';
$ph_el_id   = $iid ? 'avdcPlaceholder-' . $iid : 'avdcPlaceholder';
$ph_city_id = $iid ? 'avdcPhCity-' . $iid : 'avdcPhCity';
?>

<section class="avdc-areas" data-avdc-id="<?php echo esc_attr( $iid ); ?>">

  <!-- ══ LEFT: Photo Card Grid ══════════════════════ -->
  <div class="avdc-areas__left">

    <p class="avdc-areas__eyebrow"><?php echo esc_html( $eyebrow ); ?></p>
    <h2 class="avdc-areas__title"><?php echo wp_kses( $title, array( 'em' => array() ) ); ?></h2>
    <p class="avdc-areas__subtitle"><?php echo esc_html( $subtitle ); ?></p>

    <div class="avdc-areas__grid">

      <?php foreach ( $grouped as $group_label => $group_areas ) : ?>

        <div class="avdc-areas__divider">
          <span><?php echo esc_html( $group_label ); ?></span>
        </div>

        <?php foreach ( $group_areas as $area ) :
          $is_first = ( $area->id === $first_area->id );
          $bg_style = ! empty( $area->image_url )
            ? 'background-image:url(' . esc_url( $area->image_url ) . ');'
            : 'background:linear-gradient(135deg,#1a1a40,#302EB7);';
        ?>

        <div class="avdc-areas__card<?php echo $is_first ? ' avdc-active' : ''; ?>"
             data-city="<?php echo esc_attr( $area->area_name ); ?>"
             data-state="<?php echo esc_attr( $area->state ); ?>"
             data-lat="<?php echo esc_attr( $area->lat ); ?>"
             data-lng="<?php echo esc_attr( $area->lng ); ?>"
             data-zoom="<?php echo esc_attr( $area->zoom ); ?>"
             data-link="<?php echo esc_url( $area->custom_link ); ?>">
          <div class="avdc-areas__bar"></div>
          <div class="avdc-areas__bg" style="<?php echo $bg_style; ?>"></div>
          <div class="avdc-areas__label">
            <span class="avdc-areas__name"><?php echo esc_html( $area->area_name ); ?></span>
            <span class="avdc-areas__arrow">→</span>
          </div>
        </div>

        <?php endforeach; ?>
      <?php endforeach; ?>

    </div><!-- /avdc-areas__grid -->
  </div><!-- /avdc-areas__left -->


  <!-- ══ RIGHT: Sticky Map ══════════════════════════ -->
  <div class="avdc-areas__right">

    <div class="avdc-areas__map-inner">

      <!-- Map renders here — .avdc-areas__map class used by CSS instead of the ID -->
      <div id="<?php echo esc_attr( $map_el_id ); ?>"
           class="avdc-areas__map"
           style="background:<?php echo esc_attr( $map_bg ); ?>;">
        <!-- Placeholder until API loads -->
        <div class="avdc-areas__map-placeholder" id="<?php echo esc_attr( $ph_el_id ); ?>">
          <div class="avdc-ph-icon">🗺️</div>
          <div class="avdc-ph-city" id="<?php echo esc_attr( $ph_city_id ); ?>">
            <?php echo esc_html( $first_area->area_name . ', ' . $first_area->state ); ?>
          </div>
          <?php if ( empty( $api_key ) ) : ?>
            <p>Google Maps loads here.<br>Add your API key in Settings to activate.</p>
            <code>Appearance → AVD Map Guide → Settings</code>
          <?php else : ?>
            <p>Loading map…</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- City name overlay — top -->
      <div class="avdc-areas__map-top">
        <div class="avdc-areas__map-city avdc-vis" id="<?php echo esc_attr( $city_el_id ); ?>">
          <?php echo esc_html( $first_area->area_name . ', ' . $first_area->state ); ?>
        </div>
      </div>

      <!-- Pulse pin — decorative center dot -->
      <div class="avdc-areas__pin">
        <div class="avdc-areas__pin-dot"></div>
      </div>

      <!-- Bottom bar — hover hint + CTA -->
      <div class="avdc-areas__map-bot">
        <span class="avdc-areas__map-hint">Hover any area to explore on map</span>
        <?php if ( $cta_url && $cta_text ) : ?>
          <a href="<?php echo esc_url( $cta_url ); ?>" class="avdc-areas__map-cta">
            <?php echo esc_html( $cta_text ); ?>
          </a>
        <?php endif; ?>
      </div>

    </div><!-- /avdc-areas__map-inner -->
  </div><!-- /avdc-areas__right -->

</section>
