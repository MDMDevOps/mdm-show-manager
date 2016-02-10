<?php

/**
 * The public-facing functionality of the plugin.
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @author  Mid-West Family Marketing <author@email.com>
 * @package mdm_show_manager
 */

class Mdm_Show_Manager_Public {

    /**
     * The ID of this plugin.
     * @since  1.0.0
     * @access private
     * @var    (string) $plugin_name : The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     * @since  1.0.0
     * @access private
     * @var    (string) $version : The current version of this plugin.
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     * @since 1.0.0
     * @param (string) $plugin_name  : The name of this plugin.
     * @param (string) $version      : The current version of the plugin.
     * @param (string) $settings_key : The unique identifier used to get settings from the database.
     */
    public function __construct( $plugin_base, $plugin_name, $version, $settings_key  ) {
        $this->plugin_base  = $plugin_base;
        $this->plugin_name  = $plugin_name;
        $this->version      = $version;
        $this->settings_key = $settings_key;
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     * @since 1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( $this->plugin_base ) . 'assets/styles/public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( $this->plugin_base ) . 'assets/scripts/public.js', array( 'jquery' ), $this->version, false );
    }
}
