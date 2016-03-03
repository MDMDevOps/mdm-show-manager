<?php

/**
 * Define the internationalization functionality
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */

class Mdm_Show_Manager_i18n {
    /**
     * The unique identifier of this plugin.
     * @since  1.0.0
     * @access protected
     * @var    (string) $plugin_name : The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * Load the plugin text domain for translation.
     * @see https://codex.wordpress.org/Function_Reference/load_plugin_textdomain
     * @since 1.0.0
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain( $this->plugin_name, false, dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/i18n/' );
    }



}
