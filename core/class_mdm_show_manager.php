<?php
/**
 * The core plugin class.
 *
 * This is used to define internationalization, and register all of the hooks
 *
 * @since   1.0.0
 * @package mdm_show_manager
 * @author  Mid-West Family Marketing <author@email.com>
 */
class Mdm_Show_Manager {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power the plugin.
     * @since  1.0.0
     * @access protected
     * @var    ( Mdm_Show_Manager_Loader ) $loader : Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     * @since  1.0.0
     * @access protected
     * @var    (string) $plugin_name : The string used to uniquely identify this plugin.
     */
    protected $plugin_name;
    /**
     * The current version of the plugin.
     * @since  1.0.0
     * @access protected
     * @var    (string) $version : The current version of the plugin.
     */
    protected $version;

    /**
     * The current version of the plugin.
     * @since  1.0.0
     * @access protected
     * @var    (string) $settings_key : The string used to identify the settings in the database
     */
    protected $settings_key;

    /**
     * The plugin slug
     * @since  1.0.0
     * @access protected
     * @var    (string) $version : The slug used by wordpress to identify this plugin
     */
    protected $plugin_slug;

    /**
     * Define the core functionality of the plugin.
     * Set the plugin parameters that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the plugin
     * @since 1.0.0
     */
    public function __construct( $plugin_args = array() ) {
        $default_args = array (
            'plugin_name'     => null,
            'plugin_slug'     => null,
            'plugin_version'  => null,
            'plugin_base'     => null,
            'settings_key'    => null,
        );
        $plugin_args = ( !empty( $plugin_args ) ) ? array_merge( $default_args, $plugin_args ) : $default_args;

        $this->plugin_name  = $plugin_args['plugin_name'];
        $this->plugin_slug  = $plugin_args['plugin_slug'];
        $this->version      = $plugin_args['plugin_version'];
        $this->plugin_base  = $plugin_args['plugin_base'];
        $this->settings_key = $plugin_args['settings_key'];


        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
        $this->define_settings_hooks();
        $this->define_content_hooks();
        $this->define_widget_hooks();
        $this->define_update_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     * - Mdm_Show_Manager_Loader   : Orchestrates the hooks of the plugin.
     * - Mdm_Show_Manager_i18n     : Defines internationalization functionality.
     * - Mdm_Show_Manager_Admin    : Defines all hooks for the admin area.
     * - Mdm_Show_Manager_Public   : Defines all hooks for the public side of the site.
     * - Mdm_Show_Manager_Content  : Defines all content specific hooks
     * - Mdm_Show_Manager_Settings : Defines all plugin specific settings
     * Create an instance of the loader which will be used to register the hooks with WordPress.
     * @since  1.0.0
     * @access private
     */
    private function load_dependencies() {

        /**
         * The class responsible for orchestrating the actions and filters of the core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'core/class_mdm_show_manager_loader.php';

        /**
         * The class responsible for defining internationalization functionality of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'core/class_mdm_show_manager_i18n.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class_mdm_show_manager_admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class_mdm_show_manager_public.php';

        /**
         * The class responsible for defining all the content types required by the plugin
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'content/class_mdm_show_manager_content.php';

        /**
         * The class responsible for defining all actions that occur to handle the core settings of the plugin
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'settings/class_mdm_show_manager_settings.php';

        /**
         * The class responsible for defining all widget actions
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'widgets/class_mdm_show_manager_widgets.php';

        /**
         * The class responsible for updating from github
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'updater/class_mdm_show_manager_updater.php';

        /**
         * Small helper class
         */
        require_once plugin_dir_path( __FILE__  ) . 'class_mdm_show_manager_utilities.php';

        $this->loader = new Mdm_Show_Manager_Loader();
    }

    /**
     * Define the locale for this plugin for internationalization.
     * @since  1.0.0
     * @access private
     */
    private function set_locale() {
        $plugin_i18n = new Mdm_Show_Manager_i18n( $this->get_plugin_name() );
        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
    }

    /**
     * Register all of the hooks related to the admin area functionality of the plugin.
     * @since  1.0.0
     * @access private
     */
    private function define_admin_hooks() {
        // Instantiate Admin Object
        $plugin_admin = new Mdm_Show_Manager_Admin( $this->get_plugin_base(), $this->get_plugin_name(), $this->get_version(), $this->get_settings_key() );
        // Define Hooks
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_menu' );
        $this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'register_meta_boxes' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'onair_metabox_save' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'showoptions_metabox_save' );
        // Ajax Hooks
        $this->loader->add_action( 'wp_ajax_nopriv_add_metabox_row', $plugin_admin, 'add_metabox_row' );
        $this->loader->add_action( 'wp_ajax_add_metabox_row', $plugin_admin, 'add_metabox_row' );
        $this->loader->add_action( 'wp_ajax_nopriv_remove_metabox_row', $plugin_admin, 'remove_metabox_row' );
        $this->loader->add_action( 'wp_ajax_remove_metabox_row', $plugin_admin, 'remove_metabox_row' );
    }

    /**
     * Register all of the hooks related to the public-facing functionality of the plugin.
     * @since  1.0.0
     * @access private
     */
    private function define_public_hooks() {
        // Instantiate Public Object
        $plugin_public = new Mdm_Show_Manager_Public( $this->get_plugin_base(), $this->get_plugin_name(), $this->get_version(), $this->get_settings_key() );
        // Define Hooks
        $this->loader->add_action( 'init', $plugin_public, 'set_settings' );
        $this->loader->add_action( 'init', $plugin_public, 'register_shortcodes' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
        $this->loader->add_action( 'mdmsm_show_social', $plugin_public, 'output_social_links' );
        $this->loader->add_action( 'template_redirect', $plugin_public, 'redirect_permalink', 1, 1 );
        $this->loader->add_filter( 'post_type_link', $plugin_public, 'rewrite_permalink', 10, 4  );

    }

    /**
     * Register all of the hooks related to the content types
     * @since  1.0.0
     * @access private
     */
    private function define_content_hooks() {
        // Instantiate Content Object
        $plugin_content = new Mdm_Show_Manager_Content( $this->get_plugin_name(), $this->get_settings_key() );
        // Define Hooks
        $this->loader->add_action( 'init', $plugin_content, 'register_post_types' );
        $this->loader->add_action( 'init', $plugin_content, 'register_taxonomies' );
        $this->loader->add_action( 'after_setup_theme', $plugin_content, 'update_image_size' );
    }

    /**
     * Register all of the hooks related to the plugin core settings
     * @since  1.0.0
     * @access private
     */
    private function define_settings_hooks() {
        // Instantiate Settings Object
        $plugin_settings = new Mdm_Show_Manager_Settings( $this->get_plugin_name(), $this->get_settings_key() );
        // Define Hooks
        $this->loader->add_action( 'init', $plugin_settings, 'set_settings' );
        $this->loader->add_action( 'admin_menu', $plugin_settings, 'register_settings_page' );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'register_settings' );
        $this->loader->add_action( 'init', $plugin_settings, 'set_network_settings' );
        $this->loader->add_action( 'network_admin_menu', $plugin_settings, 'register_network_settings_page' );
        $this->loader->add_action( 'admin_init', $plugin_settings, 'register_network_settings' );
        $this->loader->add_action( 'network_admin_edit_mdmsm_update_network_options', $plugin_settings, 'mdmsm_update_network_options' );
    }

    private function define_widget_hooks() {
        $plugin_widgets = new Mdm_Show_Manager_Widgets( $this->get_plugin_base(), $this->get_plugin_name(), $this->get_version(), $this->get_settings_key() );
        $this->loader->add_action( 'widgets_init', $plugin_widgets, 'register_widgets' );
    }

    /**
     * Register all of the hooks etc needed for the updater
     * @since  1.0.0
     * @access private
     */
    private function define_update_hooks() {

        $plugin_updater = new Mdm_Show_Manager_Updater( $this->get_plugin_name(), $this->get_version(), $this->get_settings_key(), $this->get_plugin_base(), $this->get_plugin_slug() );
        $this->loader->add_action( 'admin_init', $plugin_updater, 'set_plugin_properties' );
        $this->loader->add_filter( 'pre_set_site_transient_update_plugins', $plugin_updater, 'modify_transient', 10, 1 );
        $this->loader->add_filter( 'upgrader_post_install', $plugin_updater, 'after_install', 10, 3  );
        $this->loader->add_filter( 'plugins_api', $plugin_updater, 'plugin_popup', 10, 3 );
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     * @since 1.0.0
     */
    public function run() {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of WordPress and to define internationalization functionality.
     * @since  1.0.0
     * @return (string) The name of the plugin.
     */
    public function get_plugin_name() {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     * @since  1.0.0
     * @return (Mdm_Show_Manager_Loader) Orchestrates the hooks of the plugin.
     */
    public function get_loader() {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     * @since  1.0.0
     * @return (string) The version number of the plugin.
     */
    public function get_version() {
        return $this->version;
    }

    /**
     * Retrieve the settings key of the plugin
     * @since  1.0.0
     * @return (string) The settings key
     */
    public function get_settings_key() {
        return $this->settings_key;
    }

    /**
     * Retrieve the base file of the plugin
     * @since  1.0.0
     * @return (string) The base  file
     */
    public function get_plugin_base() {
        return $this->plugin_base;
    }

    /**
     * Retrieve the wordpress slug of the plugin
     * @since  1.0.0
     * @return (string) The slug used by wordpress
     */
    public function get_plugin_slug() {
        return $this->plugin_slug;
    }
} // end class
