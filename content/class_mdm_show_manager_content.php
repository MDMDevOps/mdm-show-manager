<?php
/**
 * The content-specific functionality of the plugin.
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */

class Mdm_Show_Manager_Content {

    /**
     * The ID of this plugin.
     * @since  1.0.0
     * @access private
     * @var    (string) $plugin_name : The ID of this plugin.
     */
    private $plugin_name;

    /**
     * An array that defines all custom post types
     * @since  1.0.0
     * @access private
     * @var    (array) $post_types : The post type definitions
     */
    private $post_types;

    /**
     * An array that defines all custom taxonomies
     * @since  1.0.0
     * @access private
     * @var    (array) $post_types : The taxonomy definitions
     */
    private $taxonomies;

    private $settings_key;

    private $settings;

    /**
     * Initialize the class and set its properties.
     * @since 1.0.0
     * @param (string) $plugin_name : The name of this plugin.
     */
    public function __construct( $plugin_name, $settings_key ) {
        $this->plugin_name = $plugin_name;
        $this->settings_key = $settings_key;
        $this->settings = get_option( $this->settings_key, array() );
        $this->init_post_types();
        $this->init_taxonomies();
    }

    /**
     * Init Custom Post Types
     * @see https://codex.wordpress.org/Function_Reference/register_post_type
     * @since 1.0.0
     */
    public function init_post_types() {
        $this->post_types = array(
            'show' => array(
                'label'                 => __( 'Show', $this->plugin_name ),
                'description'           => __( 'Radio Show', $this->plugin_name ),
                'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', ),
                'taxonomies'            => array( 'post_tag' ),
                'hierarchical'          => true,
                'public'                => true,
                'show_ui'               => true,
                'show_in_menu'          => false,
                'menu_position'         => 5,
                'menu_icon'             => 'dashicons-microphone',
                'show_in_admin_bar'     => true,
                'show_in_nav_menus'     => true,
                'can_export'            => true,
                'has_archive'           => ( isset( $this->settings['archive_disable'] ) && $this->settings['archive_disable'] == true ) ? false : true,
                'exclude_from_search'   => false,
                'publicly_queryable'    => true,
                'capability_type'       => 'page',
                'register_meta_box_cb' => null,
                'show_in_rest'         => true,
                'rest_base'            => 'show',
                'rewrite'              => array(
                    'slug'                  => 'shows',
                    'with_front'            => true,
                    'pages'                 => true,
                    'feeds'                 => true,
                ),
                'labels'               => array(
                    'name'                  => _x( 'Shows', 'Post Type General Name', $this->plugin_name ),
                    'singular_name'         => _x( 'Show', 'Post Type Singular Name', $this->plugin_name ),
                    'menu_name'             => __( 'Shows', $this->plugin_name ),
                    'name_admin_bar'        => __( 'Shows', $this->plugin_name ),
                    'archives'              => __( 'Show Archives', $this->plugin_name ),
                    'parent_item_colon'     => __( 'Parent Show:', $this->plugin_name ),
                    'all_items'             => __( 'All Shows', $this->plugin_name ),
                    'add_new_item'          => __( 'Add New Show', $this->plugin_name ),
                    'add_new'               => __( 'Add New', $this->plugin_name ),
                    'new_item'              => __( 'New Show', $this->plugin_name ),
                    'edit_item'             => __( 'Edit Show', $this->plugin_name ),
                    'update_item'           => __( 'Update Show', $this->plugin_name ),
                    'view_item'             => __( 'View Show', $this->plugin_name ),
                    'search_items'          => __( 'Search Shows', $this->plugin_name ),
                    'not_found'             => __( 'Not found', $this->plugin_name ),
                    'not_found_in_trash'    => __( 'Not found in Trash', $this->plugin_name ),
                    'featured_image'        => __( 'Featured image', $this->plugin_name ),
                    'set_featured_image'    => __( 'Set featured image', $this->plugin_name ),
                    'remove_featured_image' => __( 'Remove featured image', $this->plugin_name ),
                    'use_featured_image'    => __( 'Use as featured image', $this->plugin_name ),
                    'insert_into_item'      => __( 'Insert into show', $this->plugin_name ),
                    'uploaded_to_this_item' => __( 'Uploaded to this show', $this->plugin_name ),
                    'items_list'            => __( 'Show list', $this->plugin_name ),
                    'items_list_navigation' => __( 'Show list navigation', $this->plugin_name ),
                    'filter_items_list'     => __( 'Filter show list', $this->plugin_name ),
                )
            ),
            // start second post type if applicable
        );
    }

    /**
     * Register Custom Post Types
     * @see https://codex.wordpress.org/Function_Reference/register_post_type
     * @since 1.0.0
     */
    public function register_post_types() {
        // Register each individual post type
        foreach( $this->post_types as $type_type => $args ) {
            register_post_type( $type_type, $args );
        }
    }

    /**
     * Init Custom Taxonomies
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     * @since 1.0.0
     */
    public function init_taxonomies() {
        // Placeholder for future development
        $this->taxonomies = array();
    }

    /**
     * Register Custom Taxonomies
     * @see https://codex.wordpress.org/Function_Reference/register_taxonomy
     * @since 1.0.0
     */
    public function register_taxonomies() {
        foreach( $this->taxonomies as $taxonomy => $args ) {
            register_taxonomy( $taxonomy, $args['attach'], $args );
            // Just to be safe, we explicitly attach taxonomies to custom post types
            foreach( $args['attach'] as $post_type ) {
                register_taxonomy_for_object_type( $taxonomy, $post_type );
            }
        }
    }

    /**
     * Insert default taxonomy terms into database
     * @see https://codex.wordpress.org/Function_Reference/register_post_type
     * @since 1.0.0
     */
    public function set_default_terms() {
        foreach( $this->taxonomies as $taxonomy => $args ) {
            if( isset( $args['default_terms'] ) ) {
                foreach( $args['default_terms'] as $term => $args ) {
                    wp_insert_term( $term, $taxonomy, $args );
                }
            } // endif
        } // end loop
    }

    public function update_image_size() {
        $this->settings = get_option( $this->settings_key, 'aklsdjflkajsdlkfjlkajsdf' );
        if( !isset( $this->settings['thumbnail']['width'] ) || !isset( $this->settings['thumbnail']['height'] ) ) {
            return;
        }
        $crop = ( isset( $this->settings['thumbnail']['crop'] ) ) ? true : false;
        add_image_size ( 'mdmsm_thumbnail', $this->settings['thumbnail']['width'], $this->settings['thumbnail']['height'], $crop );
    }

} // end class
