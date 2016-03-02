<?php

/**
 * The admin-specific functionality of the plugin.
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */

class Mdm_Show_Manager_Utilities {

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

    public $onair_master;

    /**
     * Initialize the class and set its properties.
     * @since 1.0.0
     * @param (string) $plugin_name  : The name of this plugin.
     * @param (string) $version      : The current version of the plugin.
     * @param (string) $settings_key : The unique identifier used to get settings from the database.
     */
    public function __construct(  ) {
        // $this->plugin_base  = $plugin_base;
        // $this->plugin_name  = $plugin_name;
        // $this->version      = $version;
        // $this->settings_key = $settings_key;
    }

    /**
     * Set onair_master, sorted, from options table
     * @since 1.0.0
     */
    public function set_onair_master() {
        // Get master schedule from database
        $onair = ( is_array( get_option( 'onairmaster', false ) ) ) ? get_option( 'onairmaster' ) : array();
        for( $i = 0; $i < 8; $i++ ) {
            // If this day slot is not an array for some reason, set it to an empty array
            if( !isset( $onair[$i] ) || !is_array( $onair[$i] ) ) {
                $onair[$i] = array();
            }
            // Sort day slot by time
            ksort( $onair[$i] );
        }
        $this->onair_master = $onair;
    }

    public static function get_onairmaster() {
        // Get master schedule from database
        $onairmaster = get_option( 'onairmaster', array() );
        // Ensure correct structure of array and sort
        for( $i = 0; $i < 8; $i++ ) {
            // If this day slot is not an array for some reason, set it to an empty array
            if( !isset( $onair[$i] ) || !is_array( $onair[$i] ) ) {
                $onair[$i] = array();
            }
            // Sort day slot by time
            ksort( $onair[$i] );
        }
        return $onairmaster;
    }

    public function widget_onair_show( $onair ) {
        $show = array(
            'showid'    => $onair['showid'],
            'title'     => get_the_title( $onair['showid'] ),
            'permalink' => get_permalink( $onair['showid'] ),
            'stime'    => null,
            'etime'    => null,
        );
        return $show;
    }
    public function get_plugin_settings() {
        return get_option( 'mdm_show_manager_settings', array() );
    }

    public static function get_mdmsm_icon( $icon = null, $return = true ) {
        // If no icon request is passed in, just bail
        if( !$icon ) {
            return false;
        }
        // If user specifies return false, do that
        if( !$return )  {
            return null;
        }
        $icon_map = array(
            'twitter'   => 'font-icon mdmsm-icon-twitter',
            'facebook'  => 'font-icon mdmsm-icon-facebook',
            'gplus'     => 'font-icon mdmsm-icon-gplus',
            'youtube'   => 'font-icon mdmsm-icon-youtube',
            'pinterest' => 'font-icon icon fa fa-pinterest',
            'instagram' => 'font-icon mdmsm-icon-instagram',
        );

        if( isset( $icon_map[ strtolower( $icon ) ] ) ) {
            return $icon_map[ strtolower( $icon ) ];
        }
        return false;
    }
} // end class