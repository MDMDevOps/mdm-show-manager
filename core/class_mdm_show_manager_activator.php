<?php

/**
 * Fired during plugin activation
 * Registeres custom post types & taxonomies, adds default terms, and flushes rewrite rules
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */

class Mdm_Show_Manager_Activator {

    /**
     * Handles all of the activation
     * Register post types, Registers taxonomies, sets default terms, flushes rewrite rules, and sets default plugin settings
     * @param (array) $plugin_args : array that holds plugin arguments
     * @since 1.0.0
     */
    public static function activate( $plugin_args = array() ) {
        $default_args = array (
            'plugin_name'    => null,
            'plugin_slug'    => null,
            'plugin_version' => null,
            'plugin_base'    => null,
            'settings_key'   => null,
            'network'        => null,
        );
        $plugin_args = ( !empty( $plugin_args ) ) ? array_merge( $default_args, $plugin_args ) : $default_args;
        self::activate_content_types( $plugin_args['plugin_name'], $plugin_args['settings_key'] );
        self::activate_settings( $plugin_args['plugin_name'], $plugin_args['settings_key'], $plugin_args['network'] );
        update_option( 'showschedid', rand( 100, 1000 ), true );
        self::flush_permalinks();
    }

    /**
     * Activates post types, taxonomies, and flushes rewrite rules
     * @param (string) $plugin_name : name of the plugin
     * @since 1.0.0
     */
    public static function activate_content_types( $plugin_name, $settings_key ) {
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'content/class_mdm_show_manager_content.php';
        $content = new Mdm_Show_Manager_Content( $plugin_name, $settings_key );
        $content->register_post_types();
        $content->register_taxonomies();
        $content->set_default_terms();
    }

    /**
     * Activates the default settings in the database
     * @param (string) $plugin_name  : name of the plugin
     * @param (string) $settings_key : unique key used to store plugin settings
     * @since 1.0.0
     */
    public static function activate_settings( $plugin_name, $settings_key, $network ) {
        include_once plugin_dir_path( dirname( __FILE__ ) ) . 'settings/class_mdm_show_manager_settings.php';
        $settings = new Mdm_Show_Manager_Settings( $plugin_name, $settings_key, $network );
        // $settings->set_defaults( true );
    }

    public static function flush_permalinks() {
        global $wp_rewrite;
        $wp_rewrite->init(); //important...
        $wp_rewrite->flush_rules();
    }
} // end class
