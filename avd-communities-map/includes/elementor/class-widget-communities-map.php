<?php
if ( ! defined( 'ABSPATH' ) ) exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;

class AVDC_Widget_Communities_Map extends Widget_Base {

    // ── Identity ──────────────────────────────────────────────────────────

    public function get_name()       { return 'avdc_communities_map'; }
    public function get_title()      { return __( 'Communities Map', 'avd-communities-map' ); }
    public function get_icon()       { return 'eicon-map-pin'; }
    public function get_categories() { return array( 'avd-communities' ); }
    public function get_keywords()   { return array( 'map', 'communities', 'areas', 'avd', 'locations' ); }

    // ── Controls ──────────────────────────────────────────────────────────

    protected function register_controls() {
        $d = AVDC_Settings::defaults();

        /* ═══════════════════════════════════════════════════════════
         *  TAB: CONTENT
         * ═══════════════════════════════════════════════════════════ */

        // ── Areas Source ─────────────────────────────────────────────
        $this->start_controls_section( 'section_source', array(
            'label' => __( 'Areas Source', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'areas_source', array(
            'label'   => __( 'Source', 'avd-communities-map' ),
            'type'    => Controls_Manager::SELECT,
            'default' => 'db',
            'options' => array(
                'db'     => __( 'Plugin Database (admin-managed)', 'avd-communities-map' ),
                'custom' => __( 'Custom Areas (defined here)', 'avd-communities-map' ),
            ),
        ) );

        $this->end_controls_section();

        // ── Custom Areas Repeater ─────────────────────────────────────
        $this->start_controls_section( 'section_custom_areas', array(
            'label'     => __( 'Custom Areas', 'avd-communities-map' ),
            'tab'       => Controls_Manager::TAB_CONTENT,
            'condition' => array( 'areas_source' => 'custom' ),
        ) );

        $repeater = new Repeater();

        $repeater->add_control( 'area_name', array(
            'label'       => __( 'Area / City Name', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'e.g. Dearborn',
            'label_block' => true,
        ) );

        $repeater->add_control( 'state', array(
            'label'       => __( 'State / Region', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'e.g. Michigan',
        ) );

        $repeater->add_control( 'group_label', array(
            'label'       => __( 'Group Label', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => 'e.g. Metro Detroit',
            'description' => __( 'Areas with the same group label appear under one divider.', 'avd-communities-map' ),
        ) );

        $repeater->add_control( 'lat', array(
            'label'       => __( 'Latitude', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => '42.3223',
        ) );

        $repeater->add_control( 'lng', array(
            'label'       => __( 'Longitude', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => '-83.1763',
        ) );

        $repeater->add_control( 'zoom', array(
            'label'   => __( 'Zoom Level', 'avd-communities-map' ),
            'type'    => Controls_Manager::NUMBER,
            'min'     => 1,
            'max'     => 20,
            'default' => 13,
        ) );

        $repeater->add_control( 'image', array(
            'label'       => __( 'Card Image', 'avd-communities-map' ),
            'type'        => Controls_Manager::MEDIA,
            'label_block' => true,
        ) );

        $repeater->add_control( 'custom_link', array(
            'label'         => __( 'Click-through URL', 'avd-communities-map' ),
            'type'          => Controls_Manager::URL,
            'placeholder'   => 'https://',
            'label_block'   => true,
        ) );

        $repeater->add_control( 'active', array(
            'label'        => __( 'Visible', 'avd-communities-map' ),
            'type'         => Controls_Manager::SWITCHER,
            'default'      => 'yes',
            'label_on'     => __( 'Yes', 'avd-communities-map' ),
            'label_off'    => __( 'No', 'avd-communities-map' ),
        ) );

        $this->add_control( 'areas_list', array(
            'label'       => __( 'Areas', 'avd-communities-map' ),
            'type'        => Controls_Manager::REPEATER,
            'fields'      => $repeater->get_controls(),
            'default'     => array(
                array(
                    'area_name'   => 'Dearborn',
                    'state'       => 'Michigan',
                    'group_label' => 'Metro Detroit',
                    'lat'         => '42.3223',
                    'lng'         => '-83.1763',
                    'zoom'        => 13,
                    'active'      => 'yes',
                ),
            ),
            'title_field' => '{{{ area_name }}}{{ state ? ", " + state : "" }}',
        ) );

        $this->end_controls_section();

        // ── Header Content ────────────────────────────────────────────
        $this->start_controls_section( 'section_header', array(
            'label' => __( 'Header', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'eyebrow', array(
            'label'       => __( 'Eyebrow Text', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => $d['areas_eyebrow'],
            'label_block' => true,
        ) );

        $this->add_control( 'title', array(
            'label'       => __( 'Title', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => $d['areas_title'],
            'label_block' => true,
            'description' => __( 'Wrap a word in &lt;em&gt; for the gold gradient highlight.', 'avd-communities-map' ),
        ) );

        $this->add_control( 'subtitle', array(
            'label'       => __( 'Subtitle', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXTAREA,
            'rows'        => 3,
            'placeholder' => $d['areas_subtitle'],
        ) );

        $this->end_controls_section();

        // ── Call to Action ────────────────────────────────────────────
        $this->start_controls_section( 'section_cta', array(
            'label' => __( 'Call to Action', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'cta_text', array(
            'label'       => __( 'Button Label', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => $d['areas_cta_text'],
            'label_block' => true,
        ) );

        $this->add_control( 'cta_url', array(
            'label'         => __( 'Button URL', 'avd-communities-map' ),
            'type'          => Controls_Manager::URL,
            'placeholder'   => 'https://',
            'label_block'   => true,
        ) );

        $this->end_controls_section();

        // ── Map Provider ──────────────────────────────────────────────
        $this->start_controls_section( 'section_map_provider', array(
            'label' => __( 'Map Provider & API', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_CONTENT,
        ) );

        $this->add_control( 'map_provider', array(
            'label'   => __( 'Map Provider', 'avd-communities-map' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '',
            'options' => array(
                ''       => __( '— Use plugin setting —', 'avd-communities-map' ),
                'google' => __( 'Google Maps', 'avd-communities-map' ),
                'mapbox' => __( 'Mapbox GL JS', 'avd-communities-map' ),
            ),
        ) );

        $this->add_control( 'google_api_key', array(
            'label'       => __( 'Google Maps API Key', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => __( 'Leave blank to use plugin setting', 'avd-communities-map' ),
            'label_block' => true,
            'condition'   => array( 'map_provider' => array( '', 'google' ) ),
        ) );

        $this->add_control( 'mapbox_api_key', array(
            'label'       => __( 'Mapbox Access Token', 'avd-communities-map' ),
            'type'        => Controls_Manager::TEXT,
            'placeholder' => __( 'Leave blank to use plugin setting', 'avd-communities-map' ),
            'label_block' => true,
            'condition'   => array( 'map_provider' => 'mapbox' ),
        ) );

        $this->add_control( 'mapbox_style', array(
            'label'     => __( 'Mapbox Style', 'avd-communities-map' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => '',
            'options'   => array(
                ''                                         => __( '— Use plugin setting —', 'avd-communities-map' ),
                'mapbox://styles/mapbox/dark-v11'          => 'Dark',
                'mapbox://styles/mapbox/light-v11'         => 'Light',
                'mapbox://styles/mapbox/streets-v12'       => 'Streets',
                'mapbox://styles/mapbox/satellite-v9'      => 'Satellite',
                'mapbox://styles/mapbox/satellite-streets-v12' => 'Satellite + Streets',
                'mapbox://styles/mapbox/navigation-night-v1'  => 'Navigation Night',
                'mapbox://styles/mapbox/outdoors-v12'      => 'Outdoors',
            ),
            'condition' => array( 'map_provider' => 'mapbox' ),
        ) );

        $this->end_controls_section();

        /* ═══════════════════════════════════════════════════════════
         *  TAB: STYLE
         * ═══════════════════════════════════════════════════════════ */

        // ── Layout & Left Panel ────────────────────────────────────────
        $this->start_controls_section( 'section_style_layout', array(
            'label' => __( 'Left Panel', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'left_bg', array(
            'label'   => __( 'Left Panel Background', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_left_bg'],
        ) );

        $this->add_control( 'card_bg', array(
            'label'   => __( 'Card Fallback Background', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_card_bg'],
            'description' => __( 'Used when a card has no photo.', 'avd-communities-map' ),
        ) );

        $this->add_control( 'color_dark', array(
            'label'   => __( 'Dark Base Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_dark'],
            'description' => __( 'Used for card text overlays and section background.', 'avd-communities-map' ),
        ) );

        $this->end_controls_section();

        // ── Typography ────────────────────────────────────────────────
        $this->start_controls_section( 'section_style_typography', array(
            'label' => __( 'Typography', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $font_options = array(
            ''                    => __( '— Use plugin setting —', 'avd-communities-map' ),
            'Bricolage Grotesque'  => 'Bricolage Grotesque',
            'IBM Plex Sans'        => 'IBM Plex Sans',
            'Inter'                => 'Inter',
            'Poppins'              => 'Poppins',
            'Raleway'              => 'Raleway',
            'Montserrat'           => 'Montserrat',
            'Playfair Display'     => 'Playfair Display',
            'Lato'                 => 'Lato',
            'Nunito'               => 'Nunito',
            'Open Sans'            => 'Open Sans',
        );

        $this->add_control( 'font_heading', array(
            'label'   => __( 'Heading Font', 'avd-communities-map' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '',
            'options' => $font_options,
        ) );

        $this->add_control( 'font_body', array(
            'label'   => __( 'Body Font', 'avd-communities-map' ),
            'type'    => Controls_Manager::SELECT,
            'default' => '',
            'options' => $font_options,
        ) );

        $this->end_controls_section();

        // ── Text Colors ────────────────────────────────────────────────
        $this->start_controls_section( 'section_style_text', array(
            'label' => __( 'Text Colors', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'color_eyebrow', array(
            'label'   => __( 'Eyebrow Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_eyebrow'],
        ) );

        $this->add_control( 'color_title', array(
            'label'   => __( 'Title Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_title'],
        ) );

        $this->add_control( 'color_subtitle', array(
            'label'   => __( 'Subtitle Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_subtitle'],
        ) );

        $this->add_control( 'color_accent', array(
            'label'   => __( 'Accent Color (Gold)', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_accent'],
            'description' => __( 'Used for the title gradient highlight and pulse pin.', 'avd-communities-map' ),
        ) );

        $this->add_control( 'color_accent_light', array(
            'label'   => __( 'Accent Light', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_accent_light'],
        ) );

        $this->end_controls_section();

        // ── Card Hover ─────────────────────────────────────────────────
        $this->start_controls_section( 'section_style_hover', array(
            'label' => __( 'Card Hover Effects', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'color_hover_bar', array(
            'label'   => __( 'Hover Bar Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_hover_bar'],
            'description' => __( 'The gold sweep bar at the top of an active card.', 'avd-communities-map' ),
        ) );

        $this->add_control( 'color_hover_name', array(
            'label'   => __( 'Hover Name Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_hover_name'],
        ) );

        $this->add_control( 'color_arrow_hover_bg', array(
            'label'   => __( 'Arrow Button Hover Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_arrow_hover_bg'],
        ) );

        $this->end_controls_section();

        // ── CTA Button ─────────────────────────────────────────────────
        $this->start_controls_section( 'section_style_cta', array(
            'label' => __( 'CTA Button (Map Overlay)', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'color_cta_bg', array(
            'label'   => __( 'Button Background', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_cta_bg'],
        ) );

        $this->add_control( 'color_cta_text', array(
            'label'   => __( 'Button Text Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_cta_text'],
        ) );

        $this->add_control( 'color_cta_bg_hover', array(
            'label'   => __( 'Button Hover Background', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_color_cta_bg_hover'],
        ) );

        $this->end_controls_section();

        // ── Map Colors ─────────────────────────────────────────────────
        $this->start_controls_section( 'section_style_map', array(
            'label' => __( 'Map Colors', 'avd-communities-map' ),
            'tab'   => Controls_Manager::TAB_STYLE,
        ) );

        $this->add_control( 'map_bg', array(
            'label'   => __( 'Map Background / Base', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_map_bg'],
        ) );

        $this->add_control( 'map_road', array(
            'label'   => __( 'Road Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_map_road'],
        ) );

        $this->add_control( 'map_highway', array(
            'label'   => __( 'Highway Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_map_highway'],
        ) );

        $this->add_control( 'map_water', array(
            'label'   => __( 'Water Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_map_water'],
        ) );

        $this->add_control( 'map_label', array(
            'label'   => __( 'Label / Text Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_map_label'],
        ) );

        $this->add_control( 'map_border', array(
            'label'   => __( 'Administrative Border Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_map_border'],
        ) );

        $this->add_control( 'map_marker', array(
            'label'   => __( 'Marker Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_map_marker'],
        ) );

        $this->add_control( 'map_city_color', array(
            'label'   => __( 'City Overlay Text Color', 'avd-communities-map' ),
            'type'    => Controls_Manager::COLOR,
            'default' => $d['ae_map_city_color'],
        ) );

        $this->end_controls_section();
    }

    // ── Render ────────────────────────────────────────────────────────────

    protected function render() {
        $settings = $this->get_settings_for_display();
        $plugin_s = AVDC_Settings::get();

        // ── Resolve active settings (widget overrides plugin defaults) ──
        $s = $this->resolve_settings( $settings, $plugin_s );

        // ── Enqueue assets ─────────────────────────────────────────────
        $this->enqueue_assets( $s );

        // ── Build areas array ───────────────────────────────────────────
        if ( $settings['areas_source'] === 'custom' && ! empty( $settings['areas_list'] ) ) {
            $areas = $this->repeater_to_objects( $settings['areas_list'] );
        } else {
            $areas = AVDC_Areas_DB::get_all( true );
        }

        if ( empty( $areas ) ) {
            echo '<div class="avdc-no-areas"><p>&#9888; No communities configured. '
               . 'Switch to "Custom Areas" above or add areas in '
               . '<strong>Appearance &#8594; AVD Communities Map</strong>.</p></div>';
            return;
        }

        // ── Build grouped data for template ────────────────────────────
        $grouped = array();
        foreach ( $areas as $area ) {
            $g = $area->group_label ?: 'Communities';
            if ( ! isset( $grouped[ $g ] ) ) $grouped[ $g ] = array();
            $grouped[ $g ][] = $area;
        }
        $first_area = $areas[0];

        // ── Text content ────────────────────────────────────────────────
        $eyebrow  = $settings['eyebrow']  ?: $plugin_s['areas_eyebrow'];
        $title    = $settings['title']    ?: $plugin_s['areas_title'];
        $subtitle = $settings['subtitle'] ?: $plugin_s['areas_subtitle'];
        $cta_text = $settings['cta_text'] ?: $plugin_s['areas_cta_text'];
        $cta_url  = ! empty( $settings['cta_url']['url'] ) ? $settings['cta_url']['url'] : $plugin_s['areas_cta_url'];
        $api_key  = $s['api_key'];
        $map_bg   = $s['ae_map_bg'];

        // ── Elementor widget instance ID ────────────────────────────────
        $instance_id = $this->get_id();

        // ── JS config ───────────────────────────────────────────────────
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

        $provider  = $s['map_provider'];
        $js_config = array(
            'areas'        => $areas_js,
            'map_provider' => $provider,
            'mapbox_key'   => ( $provider === 'mapbox' ) ? $s['mapbox_api_key'] : '',
            'mapbox_style' => $s['mapbox_style'],
            'map_bg'       => $s['ae_map_bg'],
            'map_road'     => $s['ae_map_road'],
            'map_highway'  => $s['ae_map_highway'],
            'map_water'    => $s['ae_map_water'],
            'map_label'    => $s['ae_map_label'],
            'map_border'   => $s['ae_map_border'],
            'map_marker'   => $s['ae_map_marker'],
            'has_key'      => ( $provider === 'mapbox' )
                                ? ! empty( $s['mapbox_api_key'] )
                                : ! empty( $s['api_key'] ),
        );

        // ── Inline styles (scoped to this widget instance) ──────────────
        $css_selector = '.avdc-areas[data-avdc-id="' . esc_attr( $instance_id ) . '"]';
        echo '<style>' . $css_selector . '{' . $this->build_css_vars( $s ) . '}</style>' . "\n";

        // ── Inline JS config ────────────────────────────────────────────
        $iid_js = esc_js( $instance_id );
        $inline  = 'window.avdcAreasInstances=window.avdcAreasInstances||{};'
                 . 'window.avdcAreasInstances["' . $iid_js . '"]=' . wp_json_encode( $js_config ) . ';';

        if ( $provider === 'google' ) {
            $inline .= 'window.avdGoogleQueue=window.avdGoogleQueue||[];'
                     . 'window.avdGoogleQueue.push(function(){'
                     . 'if(typeof window.avdcInitMap==="function")window.avdcInitMap();'
                     . '});'
                     . 'if(!window.avdGoogleMapsReady){'
                     . 'window.avdGoogleMapsReady=function(){'
                     . '(window.avdGoogleQueue||[]).forEach(function(fn){try{fn();}catch(e){}});'
                     . '};'
                     . '}';
        }
        echo '<script>' . $inline . '</script>' . "\n";

        // ── Render the shared template ──────────────────────────────────
        include AVDC_PATH . 'templates/areas.php';
    }

    // ── Helpers ───────────────────────────────────────────────────────────

    private function enqueue_assets( $s ) {
        $heading = $s['ae_font_heading'] ?: 'Bricolage Grotesque';
        $body    = $s['ae_font_body']    ?: 'IBM Plex Sans';

        wp_enqueue_style(
            'avdc-fonts',
            AVDC_Areas_Shortcode::build_font_url( $heading, $body ),
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

        $provider   = $s['map_provider'];
        $google_key = $s['api_key'];
        $mapbox_key = $s['mapbox_api_key'];

        if ( $provider === 'mapbox' && ! empty( $mapbox_key ) ) {
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
        } elseif ( $provider === 'google' && ! empty( $google_key ) ) {
            wp_enqueue_script(
                'avd-google-maps-api',
                'https://maps.googleapis.com/maps/api/js?key='
                    . esc_attr( $google_key )
                    . '&callback=avdGoogleMapsReady&loading=async',
                array( 'avdc-areas' ),
                null,
                true
            );
        }
    }

    private function resolve_settings( $settings, $plugin_s ) {
        $p = $plugin_s;

        // Map provider / API
        $provider = $settings['map_provider'] ?: $p['map_provider'];

        return array(
            // API
            'api_key'               => ( $settings['google_api_key'] ?: $p['api_key'] ),
            'map_provider'          => $provider,
            'mapbox_api_key'        => ( $settings['mapbox_api_key'] ?: $p['mapbox_api_key'] ),
            'mapbox_style'          => ( $settings['mapbox_style']   ?: $p['mapbox_style'] ),

            // Left panel colors
            'ae_color_accent'          => ( $settings['color_accent']       ?: $p['ae_color_accent'] ),
            'ae_color_accent_light'    => ( $settings['color_accent_light'] ?: $p['ae_color_accent_light'] ),
            'ae_color_dark'            => ( $settings['color_dark']         ?: $p['ae_color_dark'] ),
            'ae_color_left_bg'         => ( $settings['left_bg']            ?: $p['ae_color_left_bg'] ),
            'ae_color_card_bg'         => ( $settings['card_bg']            ?: $p['ae_color_card_bg'] ),
            'ae_color_eyebrow'         => ( $settings['color_eyebrow']      ?: $p['ae_color_eyebrow'] ),
            'ae_color_title'           => ( $settings['color_title']        ?: $p['ae_color_title'] ),
            'ae_color_subtitle'        => ( $settings['color_subtitle']     ?: $p['ae_color_subtitle'] ),
            'ae_color_hover_bar'       => ( $settings['color_hover_bar']    ?: $p['ae_color_hover_bar'] ),
            'ae_color_hover_name'      => ( $settings['color_hover_name']   ?: $p['ae_color_hover_name'] ),
            'ae_color_arrow_hover_bg'  => ( $settings['color_arrow_hover_bg'] ?: $p['ae_color_arrow_hover_bg'] ),
            'ae_color_cta_bg'          => ( $settings['color_cta_bg']       ?: $p['ae_color_cta_bg'] ),
            'ae_color_cta_text'        => ( $settings['color_cta_text']     ?: $p['ae_color_cta_text'] ),
            'ae_color_cta_bg_hover'    => ( $settings['color_cta_bg_hover'] ?: $p['ae_color_cta_bg_hover'] ),

            // Map colors
            'ae_map_bg'          => ( $settings['map_bg']         ?: $p['ae_map_bg'] ),
            'ae_map_road'        => ( $settings['map_road']        ?: $p['ae_map_road'] ),
            'ae_map_highway'     => ( $settings['map_highway']     ?: $p['ae_map_highway'] ),
            'ae_map_water'       => ( $settings['map_water']       ?: $p['ae_map_water'] ),
            'ae_map_label'       => ( $settings['map_label']       ?: $p['ae_map_label'] ),
            'ae_map_border'      => ( $settings['map_border']      ?: $p['ae_map_border'] ),
            'ae_map_marker'      => ( $settings['map_marker']      ?: $p['ae_map_marker'] ),
            'ae_map_city_color'  => ( $settings['map_city_color']  ?: $p['ae_map_city_color'] ),

            // Typography
            'ae_font_heading' => ( $settings['font_heading'] ?: $p['ae_font_heading'] ),
            'ae_font_body'    => ( $settings['font_body']    ?: $p['ae_font_body'] ),
        );
    }

    private function build_css_vars( $s ) {
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

        return implode( '', $lines );
    }

    private function repeater_to_objects( $items ) {
        $objects = array();
        foreach ( $items as $index => $item ) {
            if ( empty( $item['area_name'] ) ) continue;
            if ( isset( $item['active'] ) && $item['active'] !== 'yes' ) continue;

            $obj              = new stdClass();
            $obj->id          = $index;
            $obj->area_name   = $item['area_name'];
            $obj->state       = $item['state'] ?? '';
            $obj->group_label = $item['group_label'] ?? '';
            $obj->lat         = $item['lat'] ?? '0';
            $obj->lng         = $item['lng'] ?? '0';
            $obj->zoom        = intval( $item['zoom'] ?: 13 );
            $obj->image_url   = $item['image']['url']        ?? '';
            $obj->custom_link = $item['custom_link']['url']  ?? '';

            $objects[] = $obj;
        }
        return $objects;
    }
}
