<?php

/**
 * The widget specific functionality of the plugin
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */

class Mdm_Show_Manager_Widgets {

    /**
     * The base file of the plugin
     * @since  1.0.0
     * @access protected
     * @var    (string) $plugin_base : The base file to get relative urls to assets
     */
    protected $plugin_base;

    /**
     * The unique identifier of this plugin.
     * @since  1.0.0
     * @access protected
     * @var    (string) $plugin_name : The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The version of this plugin.
     * @since  1.0.0
     * @access private
     * @var    (string) $version : The current version of this plugin.
     */
    private $version;

    /**
     * The settings key to store plugin settings
     * @since  1.1.0
     * @access private
     * @var    (string) $settings_key : The settings key
     */
    private $settings_key;

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
        $this->include_widgets();
    }

    public function include_widgets() {
        include plugin_dir_path( __FILE__ ) . 'now_playing_show/class_mdm_show_manager_now_playing_widget.php';
    }

    public function register_widgets() {
        register_widget( 'Mdm_Show_Manager_Now_Playing_Widget' );
    }
} // end class