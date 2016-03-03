<?php
/**
 * The content-specific functionality of the plugin.
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */

 class Mdm_Show_Manager_Settings {

    /**
     * The ID of this plugin.
     * @since  1.0.0
     * @access private
     * @var    (string) $plugin_name : The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The settings key to store plugin settings
     * @since  1.1.0
     * @access private
     * @var    (string) $settings_key : The settings key
     */
    private $settings_key;

    /**
     * The settings
     * @since  1.1.0
     * @access private
     * @var    (array) $settings : array of all of the settings
     */
    private $settings;

    /**
     * The default settings
     * @since  1.1.0
     * @access private
     * @var    (array) $defaults : array of all of the setting defaults
     */
    private $defaults;
    private $network_defaults;

    /**
     * The setting options
     * @since  1.1.0
     * @access private
     * @var    (array) $options : list of options to display the settings
     */
    private $options;
    private $network_options;
    private $network; // whether this is network activated or not

    /**
     * Initialize the class and set its properties.
     * @since 1.0.0
     * @param (string) $plugin_name : The name of this plugin.
     * @param (string) $version     : The version of this plugin.
     */
    public function __construct( $plugin_name, $settings_key, $network ) {
        $this->plugin_name  = $plugin_name;
        $this->settings_key = $settings_key;
        $this->network      = $network;
        $this->set_options();
        $this->set_defaults();
        $this->set_network_options();
        $this->set_network_defaults();
    }

    /**
     * Set plugin options, including default settings
     * @since 1.0.0
     * @see https://codex.wordpress.org/Function_Reference/update_option
     * @access private
     */
    private function set_options() {
         $this->options = array(
            'api_key'     => array(
               'title'       => __( 'API Key', $this->plugin_name ),
               'label'       => null,
               'type'        => 'text',
               'class'       => 'widefat',
               'section'     => $this->settings_key . '_general',
               'id'          => 'api_key',
               'description' => sprintf( '%s%s %s%s', __( 'Enter your GitHub', $this->plugin_name ), '<a href="https://github.com/blog/1509-personal-api-tokens" target="_blank">', __( 'Personal Access Token', $this->plugin_name ), '</a>' ),
               'placeholder' => null,
               'default'     => null,
               'restrict'    => 'network',
            ),
            'css_disable' => array(
                'title'         => __( 'Disable CSS Output', $this->plugin_name ),
                'label'         => __( 'Check to disable front end CSS Output', $this->plugin_name ),
                'type'          => 'checkbox',
                'class'         => null,
                'id'            => null,
                'section'       => $this->settings_key . '_general',
                'description'   => sprintf( '<em>%s</em>', __( 'Allows you to output front-end CSS in your own theme instead of being called by the plugin', $this->plugin_name ) ),
                'checked_value' => true,
                'default'       => false
            ),
            'js_disable' => array(
                'title'         => __( 'Disable Javascript Output', $this->plugin_name ),
                'label'         => __( 'Check to disable front end Javascript Output', $this->plugin_name ),
                'type'          => 'checkbox',
                'class'         => null,
                'id'            => null,
                'section'       => $this->settings_key . '_general',
                'description'   => sprintf( '<em>%s</em>', __( 'Allows you to output required JS on your own - use carefully', $this->plugin_name ) ),
                'checked_value' => true,
                'default'       => false
            ),
            'archive_disable' => array(
                'title'         => __( 'Disable Archive Page', $this->plugin_name ),
                'label'         => __( 'Check to use a "PAGE" in place of post type archives', $this->plugin_name ),
                'type'          => 'checkbox',
                'class'         => null,
                'id'            => null,
                'section'       => $this->settings_key . '_general',
                'description'   => sprintf( '<em>%s</em>', __( 'Allows you to replace the archive with a static page', $this->plugin_name ) ),
                'checked_value' => true,
                'default'       => false
            ),
            'thumbnail' => array(
                'title'       => __( 'Featured Image Options', $this->plugin_name ),
                'type'        => 'mixed',
                'section'     => $this->settings_key . '_general',
                'id'          => 'thumbnail',
                'description' => null,
                'placeholder' => null,
                'path'        => 'partials/input_image_size.php',
                'options'     => array(
                    'width'   => array(
                        'label' => 'Height',
                        'class' => 'small-text',
                        'type'  => 'number',
                        'step'  => 1,
                        'min'   => 0,
                        'default' => 150
                    ),
                    'height'   => array(
                        'label' => 'Width',
                        'class' => 'small-text',
                        'type'  => 'number',
                        'step'  => 1,
                        'min'   => 0,
                        'default' => 150
                    ),
                    'crop' => array(
                        'label'         => __( 'Crop thumbnail to exact dimensions (normally thumbnails are proportional)', $this->plugin_name ),
                        'type'          => 'checkbox',
                        'class'         => null,
                        'description'   => sprintf( '<em>%s</em>', __( 'Does not affect normal thumbnail settings', $this->plugin_name ) ),
                        'checked_value' => true,
                    ),
                ),
                'default' => array(
                    'width'   => 150,
                    'height'  => 150,
                    'crop'    => false,
                ),
            ),
         );
    }
    /**
     * Set network (multisite) plugin options, including default settings
     * @since 1.0.0
     * @see https://codex.wordpress.org/Function_Reference/update_option
     * @access private
     */
    private function set_network_options() {
        $this->network_options = array(
            'api_key'     => array(
               'title'       => __( 'API Key', $this->plugin_name ),
               'label'       => null,
               'type'        => 'text',
               'class'       => 'widefat',
               'section'     => $this->settings_key . '_general',
               'id'          => 'api_key',
               'description' => sprintf( '%s%s %s%s', __( 'Enter your GitHub', $this->plugin_name ), '<a href="https://github.com/blog/1509-personal-api-tokens" target="_blank">', __( 'Personal Access Token', $this->plugin_name ), '</a>' ),
               'placeholder' => null,
               'default'     => null
            ),
        );
    }

    /**
     * Set plugin default settings
     * @since 1.0.0
     * @see https://codex.wordpress.org/Function_Reference/update_option
     * @access private
     * @param (boolean) $write_to_database : whether to write options to the database, optional, default = false
     */
    public function set_defaults( $write_to_database = false ) {
         // Structure default setting array
         foreach( $this->options as $key => $option ) {
             $this->defaults[$key] = $option['default'];
         }
         // Write to database, if applicable ( used for activation )
         if( $write_to_database ) {
             update_option( $this->settings_key, $this->defaults, true );
         }
    }

    public function set_network_defaults( $write_to_database = false ) {
         // Structure default setting array
         foreach( $this->network_options as $key => $option ) {
             $this->network_defaults[$key] = $option['default'];
         }
    }

    /**
     * Initialize settings
     * @since 1.0.0
     */
    public function set_settings() {
         // Get Settings from database and store temporarily. If no settings are present, an empty array is used
         $settings = get_option( $this->settings_key, array() );
         // Assign each setting, or default value if setting isn't present
         foreach( $this->options as $key => $option ) {
            if( is_array( $this->defaults[$key] ) ) {
                foreach( $this->defaults[$key] as $index => $value ) {
                    $this->settings[ $key ][$index] = ( isset( $settings[$key][$index] ) ) ? $settings[$key][$index] : $this->defaults[$key][$index];
                }
            } else {
                $this->settings[ $key ] = ( isset( $settings[$key] ) ) ? $settings[ $key ] : $this->defaults[$key];
            }
         }
    }

    public function set_network_settings() {
        // Get Settings from database and store temporarily. If no settings are present, an empty array is used
        $settings = get_site_option( $this->settings_key, array() );
        // Assign each setting, or default value if setting isn't present
        foreach( $this->network_options as $key => $option ) {
           if( is_array( $this->network_defaults[$key] ) ) {
               foreach( $this->network_defaults[$key] as $index => $value ) {
                   $this->network_settings[ $key ][$index] = ( isset( $settings[$key][$index] ) ) ? $settings[$key][$index] : $this->network_defaults[$key][$index];
               }
           } else {
               $this->network_settings[ $key ] = ( isset( $settings[$key] ) ) ? $settings[ $key ] : $this->network_defaults[$key];
           }
        }
    }

    /**
     * Register the settings page
     * @see https://codex.wordpress.org/Function_Reference/add_submenu_page
     * @since 1.0.0
     */
    public function register_settings_page() {
       add_submenu_page( $this->plugin_name, __( 'Settings', $this->plugin_name ), __( 'Settings', $this->plugin_name ), 'manage_options', sprintf('%s-settings', $this->plugin_name ), array( $this, 'display_settings_page' ) );
    }

    /**
     * Register the network settings page
     * @see https://codex.wordpress.org/Function_Reference/add_submenu_page
     * @since 1.0.0
     */
    public function register_network_settings_page() {
       add_submenu_page( 'settings.php', __( 'Show Manager', $this->plugin_name ), __( 'Show Manager', $this->plugin_name ), 'manage_options', sprintf('%s-settings', $this->plugin_name ), array( $this, 'display_network_settings_page' ) );
    }

    /**
     * Save network settings
     * @see https://codex.wordpress.org/Function_Reference/add_submenu_page
     * @since 1.0.0
     */
    public function mdmsm_update_network_options() {
       global $new_whitelist_options;
       $options = $new_whitelist_options['mdm_show_manager_settings'];
       foreach ( $options as $option ) {
           if ( isset( $_POST[$option] ) && !empty( $_POST[$option] ) ) {
               update_site_option( $option, $_POST[$option] );
           } else {
               delete_site_option( $option );
           }
       }
       wp_redirect( add_query_arg( array( 'page' => 'mdm_show_manager-settings',
           'updated' => 'true'), network_admin_url('settings.php') ) );
       exit;
    }

    /**
     * Display the settings page
     * @since 1.0.0
     */
    public function display_settings_page() {
       include plugin_dir_path( __FILE__ ) . 'partials/display_settings_page.php';
       // Flushing rewrite rules on the settings page, to ensure the archive link works
       flush_rewrite_rules();
    }

    /**
     * Display the network settings page
     * @since 1.0.0
     */
    public function display_network_settings_page() {
       include plugin_dir_path( __FILE__ ) . 'partials/display_network_settings_page.php';
    }

    /**
     * Register individual settings with Wordpress settings API
     * @since 1.0.0
     * @see https://codex.wordpress.org/Function_Reference/register_setting
     * @see https://codex.wordpress.org/Function_Reference/add_settings_section
     * @see https://codex.wordpress.org/Function_Reference/add_settings_field
     */
    public function register_settings() {
        register_setting( $this->settings_key, $this->settings_key );
        // Add Section
        add_settings_section( $this->settings_key . '_general', __( 'Settings', $this->plugin_name ), array( $this, 'general_section_heading' ), $this->settings_key );
        foreach( $this->settings as $key => $value ) {
            // Initialize $setting w/ data from options array
            $setting = $this->options[$key];
            // make sure this isn't a restricted setting
            if( isset( $setting['restrict'] ) && $setting['restrict'] === 'network' && $this->network == true ) {
               continue;
            }
            // Append $key to array so we know what were working with
            $setting['key']   = $key;
            // Append value to array
            $setting['value'] = $value;
            // Add field
            add_settings_field( $key, $setting['title'], array( $this, 'settings_page_field' ), $this->settings_key, $setting['section'], $setting );
        }
    }

    /**
     * Register individual settings with Wordpress settings API for network admin screen
     * @since 1.0.0
     * @see https://codex.wordpress.org/Function_Reference/register_setting
     * @see https://codex.wordpress.org/Function_Reference/add_settings_section
     * @see https://codex.wordpress.org/Function_Reference/add_settings_field
     */
    public function register_network_settings() {
        register_setting( 'mdmsm_network_settings', 'mdmsm_network_settings');
        // Add Section
        add_settings_section( 'mdmsm_network_settings_general', __( 'Settings', $this->plugin_name ), array( $this, 'network_section_heading' ), 'mdmsm_network_settings' );

        foreach( $this->network_settings as $key => $value ) {
            // Initialize $setting w/ data from options array
            $setting = $this->network_options[$key];
            // Append $key to array so we know what were working with
            $setting['key']   = $key;
            // Append value to array
            $setting['value'] = $value;
            // Add field
            add_settings_field( $key, $setting['title'], array( $this, 'settings_page_field' ), 'mdmsm_network_settings', 'mdmsm_network_settings_general', $setting );
        }
    }

    /**
     * Heading to display for general settings
     * @since 1.0.0
     */
    public function general_section_heading() {
        include plugin_dir_path( __FILE__ ) . 'partials/general_section_heading.php';
    }

    /**
     * Heading to display for network settings
     * @since 1.0.0
     */
    public function network_section_heading() {
        include plugin_dir_path( __FILE__ ) . 'partials/network_section_heading.php';
    }

    /**
     * Display individual settings fields
     * @since 1.0.0
     */
    public function settings_page_field( $setting ) {
        $option_key    = sprintf( '%s[%s]', $this->settings_key, $setting['key'] );
        $input_wrapper = 'mdmsm_input_wrapper';
        switch( $setting['type'] ) {
           case 'text' :
               include plugin_dir_path( __FILE__ ) . 'partials/input_text.php';
               break;
           case 'checkbox' :
               include plugin_dir_path( __FILE__ ) . 'partials/input_checkbox.php';
               break;
           case 'mixed' :
               include plugin_dir_path( __FILE__ ) . $setting['path'];
               break;
            default :
               break;
        } // end switch
    }
 } // end class