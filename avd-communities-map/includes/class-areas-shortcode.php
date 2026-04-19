<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AVDC_Areas_Shortcode {

    public static function init() {
        add_shortcode( 'avdc_areas', array( __CLASS__, 'render' ) );
        add_action( 'wp_enqueue_scripts', array( __CLASS__, 'maybe_enqueue' ) );
    }

    public static function maybe_enqueue() {
        global $post;
        if ( is_a( $post, 'WP_Post' ) && has_shortcode( $post->post_content, 'avdc_areas' ) ) {
            self::enqueue_files();
        }
    }

    public static function enqueue_files() {
        $s = AVDC_Settings::get();

        wp_enqueue_style(
            'avdc-fonts',
            self::build_font_url( $s['ae_font_heading'], $s['ae_font_body'] ),
            array(),
            null
        );

        wp_enqueue_style(
            'avdc-areas',
            AVDC_URL . 'public/css/areas.css',
            array( 'avdc-fonts' ),
            AVDC_VERSION
        );

        wp_enqueue_script(
            'avdc-areas',
            AVDC_URL . 'public/js/areas.js',
            array(),
            AVDC_VERSION,
            true
        );

        $provider = $s['map_provider'] ?? 'google';

        if ( $provider === 'mapbox' && ! empty( $s['mapbox_api_key'] ) ) {
            // ── Mapbox GL JS ──────────────────────────────────────────────
            wp_enqueue_style(
                'avdc-mapbox-gl',
                'https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.css',
                array(),
                null
            );
            wp_enqueue_script(
                'avdc-mapbox-gl',
                'https://api.mapbox.com/mapbox-gl-js/v3.3.0/mapbox-gl.js',
                array( 'avdc-areas' ),
                null,
                true
            );

        } elseif ( $provider === 'google' && ! empty( $s['api_key'] ) ) {
            // ── Google Maps — shared handle with AVD Schools and Reviews ──
            wp_enqueue_script(
                'avd-google-maps-api',
                'https://maps.googleapis.com/maps/api/js?key='
                    . esc_attr( $s['api_key'] )
                    . '&callback=avdGoogleMapsReady&loading=async',
                array( 'avdc-areas' ),
                null,
                true
            );
        }
    }

    public static function render( $atts ) {
        $s = AVDC_Settings::get();

        $atts = shortcode_atts( array(
            'eyebrow'  => '',
            'title'    => '',
            'subtitle' => '',
            'cta_text' => '',
            'cta_url'  => '',
        ), $atts, 'avdc_areas' );

        $eyebrow  = $atts['eyebrow']  ?: $s['areas_eyebrow'];
        $title    = $atts['title']    ?: $s['areas_title'];
        $subtitle = $atts['subtitle'] ?: $s['areas_subtitle'];
        $cta_text = $atts['cta_text'] ?: $s['areas_cta_text'];
        $cta_url  = $atts['cta_url']  ?: $s['areas_cta_url'];
        $api_key  = $s['api_key'];

        // Enqueue files for page builders that bypass has_shortcode()
        self::enqueue_files();

        $areas = AVDC_Areas_DB::get_all( true );

        if ( empty( $areas ) ) {
            return '<div class="avdc-no-areas"><p>&#9888; No communities configured. '
                 . 'Go to <strong>Appearance &#8594; AVD Communities Map</strong> '
                 . 'to add communities.</p></div>';
        }

        // Build JS config
        $areas_js = array();
        foreach ( $areas as $area ) {
            $areas_js[ $area->area_name ] = array(
                'lat'   => (float) $area->lat,
                'lng'   => (float) $area->lng,
                'zoom'  => (int)   $area->zoom,
                'state' => $area->state,
                'link'  => $area->custom_link,
            );
        }

        $provider   = $s['map_provider'] ?? 'google';
        $js_config = array(
            'areas'        => $areas_js,
            'map_provider' => $provider,
            'mapbox_key'   => ( $provider === 'mapbox' ) ? $s['mapbox_api_key'] : '',
            'mapbox_style' => $s['mapbox_style'] ?? 'mapbox://styles/mapbox/dark-v11',
            'map_bg'       => $s['ae_map_bg'],
            'map_road'     => $s['ae_map_road'],
            'map_highway'  => $s['ae_map_highway'],
            'map_water'    => $s['ae_map_water'],
            'map_label'    => $s['ae_map_label'],
            'map_border'   => $s['ae_map_border'],
            'map_marker'   => $s['ae_map_marker'],
            'has_key'      => ( $provider === 'mapbox' )
                                ? ! empty( $s['mapbox_api_key'] )
                                : ! empty( $api_key ),
        );

        // Group areas by group_label
        $grouped = array();
        foreach ( $areas as $area ) {
            $g = $area->group_label ?: 'Communities';
            if ( ! isset( $grouped[ $g ] ) ) $grouped[ $g ] = array();
            $grouped[ $g ][] = $area;
        }

        $first_area  = $areas[0];
        $instance_id = '';                      // empty = legacy shortcode IDs
        $map_bg      = $s['ae_map_bg'];

        ob_start();

        // Inline CSS vars — must be a <style> block in HTML output because
        // wp_add_inline_style() is too late when called from inside a shortcode.
        echo '<style>' . self::build_css_vars( $s ) . '</style>' . "\n";

        // Inline JS config.
        // For Google: also wire the shared queue dispatcher (shared with AVD Schools & Reviews).
        // For Mapbox: avdcInitMapbox() is called directly from DOMContentLoaded in areas.js.
        $inline  = 'window.avdcAreasConfig=' . wp_json_encode( $js_config ) . ';';
        if ( ( $s['map_provider'] ?? 'google' ) === 'google' ) {
            $inline .= 'window.avdGoogleQueue=window.avdGoogleQueue||[];'
                     . 'window.avdGoogleQueue.push(function(){'
                     . 'if(typeof window.avdcInitMap==="function")window.avdcInitMap();'
                     . '});'
                     . 'window.avdGoogleMapsReady=function(){'
                     . '(window.avdGoogleQueue||[]).forEach(function(fn){try{fn();}catch(e){}});'
                     . '};';
        }
        echo '<script>' . $inline . '</script>' . "\n";

        include AVDC_PATH . 'templates/areas.php';

        return ob_get_clean();
    }

    // ── HELPERS ──────────────────────────────────────────────────────────

    public static function build_font_url( $heading, $body ) {
        $weights = array(
            'Bricolage Grotesque' => 'opsz,wght@12..96,400;12..96,700;12..96,800',
            'IBM Plex Sans'       => 'wght@400;500;600',
            'Inter'               => 'wght@400;500;600;700',
            'Poppins'             => 'wght@400;500;600;700;800',
            'Raleway'             => 'wght@400;600;700;800',
            'Montserrat'          => 'wght@400;600;700;800',
            'Playfair Display'    => 'wght@400;700;800',
            'Lato'                => 'wght@400;700',
            'Nunito'              => 'wght@400;600;700;800',
            'Open Sans'           => 'wght@400;600;700',
        );

        $families = array();
        foreach ( array_unique( array( $heading, $body ) ) as $font ) {
            if ( empty( $font ) ) continue;
            $w          = isset( $weights[ $font ] ) ? $weights[ $font ] : 'wght@400;600;700;800';
            $families[] = urlencode( $font ) . ':' . $w;
        }

        return 'https://fonts.googleapis.com/css2?family='
             . implode( '&family=', $families )
             . '&display=swap';
    }

    private static function build_css_vars( $s ) {
        $h = esc_attr( $s['ae_font_heading'] ?: 'Bricolage Grotesque' );
        $b = esc_attr( $s['ae_font_body']    ?: 'IBM Plex Sans' );

        $vars = array(
            '--ae-accent'         => $s['ae_color_accent'],
            '--ae-accent-light'   => $s['ae_color_accent_light'],
            '--ae-dark'           => $s['ae_color_dark'],
            '--ae-left-bg'        => $s['ae_color_left_bg'],
            '--ae-card-bg'        => $s['ae_color_card_bg'],
            '--ae-eyebrow-color'  => $s['ae_color_eyebrow'],
            '--ae-title-color'    => $s['ae_color_title'],
            '--ae-subtitle-color' => $s['ae_color_subtitle'],
            '--ae-font-heading'   => "'{$h}', sans-serif",
            '--ae-font-body'      => "'{$b}', -apple-system, sans-serif",
            '--ae-hover-bar'      => $s['ae_color_hover_bar'],
            '--ae-hover-name'     => $s['ae_color_hover_name'],
            '--ae-arrow-hover-bg' => $s['ae_color_arrow_hover_bg'],
            '--ae-cta-bg'         => $s['ae_color_cta_bg'],
            '--ae-cta-text'       => $s['ae_color_cta_text'],
            '--ae-cta-bg-hover'   => $s['ae_color_cta_bg_hover'],
            '--ae-map-bg'         => $s['ae_map_bg'],
            '--ae-map-city-color' => $s['ae_map_city_color'],
        );

        $lines = array();
        foreach ( $vars as $prop => $val ) {
            $lines[] = $prop . ':' . esc_attr( $val ) . ';';
        }

        return '.avdc-areas{' . implode( '', $lines ) . '}';
    }
}
