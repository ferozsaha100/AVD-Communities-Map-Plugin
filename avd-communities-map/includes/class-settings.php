<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class AVDC_Settings {

    const OPTION_KEY = 'avdc_settings';

    public static function defaults() {
        return array(
            // ── API ───────────────────────────────────────────────────────
            'api_key'                  => '',
            'map_provider'             => 'google',  // 'google' | 'mapbox'
            'mapbox_api_key'           => '',
            'mapbox_style'             => 'mapbox://styles/mapbox/dark-v11',

            // ── Areas Embed — Content ─────────────────────────────────────
            'areas_eyebrow'            => 'Markets We Know Inside & Out',
            'areas_title'              => 'Areas of Expertise',
            'areas_subtitle'           => 'Hover any area to explore on the map.',
            'areas_cta_text'           => 'Book Strategy Session →',
            'areas_cta_url'            => '',

            // ── Areas Embed — Colors ──────────────────────────────────────
            'ae_color_accent'          => '#B87F0D',
            'ae_color_accent_light'    => '#D4AF37',
            'ae_color_dark'            => '#0C0C30',
            'ae_color_left_bg'         => '#FFFFFF',
            'ae_color_card_bg'         => '#1a1a40',
            'ae_color_eyebrow'         => '#B87F0D',
            'ae_color_title'           => '#0C0C30',
            'ae_color_subtitle'        => '#58595B',
            'ae_color_hover_bar'       => '#B87F0D',
            'ae_color_hover_name'      => '#B87F0D',
            'ae_color_arrow_hover_bg'  => '#B87F0D',
            'ae_color_cta_bg'          => '#B87F0D',
            'ae_color_cta_text'        => '#0C0C30',
            'ae_color_cta_bg_hover'    => '#D4AF37',

            // ── Areas Embed — Map Colors ──────────────────────────────────
            'ae_map_bg'                => '#0e0e2e',
            'ae_map_road'              => '#1a1a50',
            'ae_map_highway'           => '#302e60',
            'ae_map_water'             => '#060620',
            'ae_map_label'             => '#8a8ab0',
            'ae_map_border'            => '#302eb7',
            'ae_map_marker'            => '#B87F0D',
            'ae_map_city_color'        => '#FFFFFF',

            // ── Areas Embed — Typography ──────────────────────────────────
            'ae_font_heading'          => 'Bricolage Grotesque',
            'ae_font_body'             => 'IBM Plex Sans',
        );
    }

    public static function get( $key = null ) {
        $saved    = get_option( self::OPTION_KEY, array() );
        $settings = array_merge( self::defaults(), (array) $saved );
        if ( $key !== null ) {
            return isset( $settings[ $key ] ) ? $settings[ $key ] : null;
        }
        return $settings;
    }

    public static function save( $post ) {
        $defaults = self::defaults();
        $data     = array();
        foreach ( $defaults as $key => $default ) {
            if ( isset( $post[ $key ] ) ) {
                $data[ $key ] = sanitize_text_field( $post[ $key ] );
            } else {
                $data[ $key ] = $default;
            }
        }
        update_option( self::OPTION_KEY, $data );
    }
}
