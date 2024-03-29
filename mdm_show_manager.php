<?php
/**
 * The plugin bootstrap file
 * This file is read by WordPress to generate the plugin information in the plugin admin area.
 * This file also defines plugin parameters, registers the activation and deactivation functions, and defines a function that starts the plugin.
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 *
 * @wordpress-plugin
 * Plugin Name: MDM Show Manager
 * Plugin URI:  http://midwestfamilymarketing.com
 * Description: Manage Radio Show Schedules
 * Version:     1.2.1
 * Author:      Mid-West Family Marketing
 * Author URI:  http://midwestfamilymarketing.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: mdm_show_manager
 * Domain Path: /i18n
 */

// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
    die( 'Bugger Off Script Kiddies!' );
}

/**
 * Define plugin parameters required throughout the plugin
 * @since 1.0.0
 */

 if( !function_exists( 'mdmsm_plugin_config' ) ) {
     function mdmsm_plugin_config() {
        // Makes sure the plugin is defined before trying to use it
        if ( !function_exists( 'is_plugin_active_for_network' ) ) {
            require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
        }
        $plugin_config = array (
            // 1. Reference to the name to use internally for the plugin
            'plugin_name'    => 'mdm_show_manager',
            // 2. Reference to root directory NAME ( what Wordpress uses internally as the "plugin slug" )
            'plugin_slug'    => dirname( plugin_basename( __FILE__ ) ),
            // 3. Reference to the current version number, used internally
            'plugin_version' => '1.2.1',
            // 4: Reference to this file, the main plugin file
            'plugin_base'    => __FILE__,
            // 5: Reference to setting key used to get stuff out of the database
            'settings_key'   => 'mdm_show_manager_settings',
            // 6: Network Activated
            'network'        => is_plugin_active_for_network( plugin_basename( __FILE__ ) ),
        );
        return $plugin_config;
     }
 }

/**
 * The code that runs during plugin activation.
 * @since 1.0.0
 */
if( !function_exists( 'activate_mdm_show_manager' ) ) {
    function activate_mdm_show_manager() {
        include_once plugin_dir_path( __FILE__ ) . 'core/class_mdm_show_manager_activator.php';
        Mdm_Show_Manager_Activator::activate( mdmsm_plugin_config() );
    }
    register_activation_hook( __FILE__, 'activate_mdm_show_manager' );
}

/**
 * The code that runs during plugin deactivation.
 * @since 1.0.0
 */
if( !function_exists( 'deactivate_mdm_show_manager' ) ) {
    function deactivate_mdm_show_manager() {
        include_once plugin_dir_path( __FILE__ ) . 'core/class_mdm_show_manager_deactivator.php';
        Mdm_Show_Manager_Deactivator::deactivate( mdmsm_plugin_config() );
    }
    register_deactivation_hook( __FILE__, 'deactivate_mdm_show_manager' );
}

/**
 * The core plugin class
 * Controls internationalization, registers all action & filter hooks, and loads plugin functionality
 * @since 1.0.0
 */
require plugin_dir_path( __FILE__ ) . 'core/class_mdm_show_manager.php';

/**
 * Begins execution of the plugin.
 * @since 1.0.0
 */
if( !function_exists( 'run_mdm_show_manager' ) ) {
    function run_mdm_show_manager() {
        $plugin = new Mdm_Show_Manager( mdmsm_plugin_config() );
        $plugin->run();
    }
    run_mdm_show_manager();
}
