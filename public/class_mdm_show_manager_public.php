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
     * The settings retreived from the database
     * @since  1.0.0
     * @access private
     * @var    (array) $version : array of settings retreived from the database
     */
    private $settings;

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
     * Set the settings field from the database
     * @since 1.0.4
     */
    public function set_settings() {
        $this->settings = get_option( $this->settings_key, array() );
    }

    /**
     * Register the stylesheets for the public-facing side of the site.
     * @since 1.0.0
     */
    public function enqueue_styles() {
        // If css is disabled, just return
        if( isset( $this->settings['css_disable'] ) && $this->settings['css_disable'] == true ) {
            return;
        }
        // else lets enqueue it
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( $this->plugin_base ) . 'assets/styles/public.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the public-facing side of the site.
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        // If js is disabled, just return
        if( isset( $this->settings['js_disable'] ) && $this->settings['js_disable'] == true ) {
            return;
        }
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( $this->plugin_base ) . 'assets/scripts/public.js', array( 'jquery' ), $this->version, true );
    }
    public function output_social_links( $post_id = null, $args = array() ) {
        // if no post id is set, let's just bail
        if( $post_id === null ) {
            return;
        }
        $defaults = array(
            'id'      => null,
            'class'   => null,
            'show_names'   => false,
            'exclude' => array(),
        );
        $args = ( !empty( $args ) ) ? wp_parse_args( $args, $defaults ) : $defaults;
        echo $this->social_links_list( $post_id, $args );
    }

    private function social_links_list( $post_id, $args ) {
        $fields = Mdm_Show_Manager::get_social_fields();
        // Get social settings from database
        $socuri = ( is_array( get_post_meta( $post_id, 'social_uri', true ) ) ) ? get_post_meta( $post_id, 'social_uri', true ) : array();
        $socico = ( is_array( get_post_meta( $post_id, 'social_ico', true ) ) ) ? get_post_meta( $post_id, 'social_ico', true ) : array();
        // initialize output to hold our data
        $output = sprintf( '<ul %s class="mdmsm-social-links%s">', isset( $args['id'] ) ? sprintf( 'id="%s"', esc_attr( $args['id'] ) ) : null, isset( $args['class'] ) ? sprintf( ' %s', esc_attr( $args['class'] ) ) : null );
        foreach( $socuri as $name => $uri ) {
            // If this is set but empty, just move to the next iteration
            if( empty( $uri ) || in_array( $name, $args['exclude'] ) ) {
                continue;
            }
            // Else lets build some output
            $output .= '<li class="social-link">';
            $output .= sprintf( '<a %s><span class="%2$s"></span><span class="%3$s">%4$s</span></a>',
                $fields[$name]['uri']['type'] === 'email' ? sprintf( 'href="mailto:%s"', $uri ) : sprintf( 'href="%s" target="_blank"', esc_url( $uri, null, 'display' ) ),
                isset( $socico[$name] ) && !empty( $socico[$name] ) ? esc_attr( $socico[$name] ) : Mdm_Show_Manager::get_mdmsm_icon( $name ),
                $args['show_names'] === true ? 'mdmsm-social-name' : 'mdmsm-screen-reader-text',
                $name
            );
            $output .= '</li>';
        }
        // Close output
        $output .= '</ul>';
        // And finally, echo it
        return $output;

    }

    public function show_content_shortcode( $atts = array() ) {
        $atts = shortcode_atts( array( 'id' => null, ), $atts, 'mdmsm_show_content' );
        // If no ID is set, there's nothing to see here
        if( $atts['id'] === null ) {
            return;
        }
        // If show is not published or doesn't exist, there's nothing to see here
        if( get_post_status( $atts['id'] ) !== 'publish' ) {
            return;
        }
        // Construct the query
        $the_query = new WP_Query( array( 'post_type' => 'show', 'p' =>  $atts['id'], 'post_count' => 1 ) );

        if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post();
            $output = apply_filters( 'the_content', get_the_content() );
        endwhile; endif;
        // Reset post data
        wp_reset_postdata();
        // Return $output
        return $output;
    }
    public function show_social_links_shortcode( $atts = array() ) {
        $default = array(
            'post_id'   => null,
            'id'        => null,
            'class'     => null,
            'show_names'=> false,
            'exclude'   => array(),
        );
        $atts = shortcode_atts( $default, $atts, 'mdmsm_show_content' );
        // If no ID is set, there's nothing to see here
        if( $atts['post_id'] === null ) {
            return;
        }
        // If show is not published or doesn't exist, there's nothing to see here
        if( get_post_status( $atts['post_id'] ) !== 'publish' ) {
            return;
        }
        // output list
        return $this->social_links_list( $atts['post_id'], $atts );
    }
    // Rewrites permalink
    public function rewrite_permalink( $permalink, $show, $leavename ) {
        // Get redirect option from database
        $options = get_post_meta( $show->ID, 'show_options', true );
        // Get Permalink
        $permalink = ( !empty( $options['uri_redirect'] ) ) ? esc_url_raw( $options['uri_redirect'] ) : $permalink;
        // Return permalink
        return $permalink;
    }
    // Actually redirects old permalink
    public function redirect_permalink() {
        global $post;
        // Get redirect option from database
        $options = get_post_meta( $post->ID, 'show_options', true );
        // Get Permalink
        $redirect = isset( $options['uri_redirect'] ) && !empty( $options['uri_redirect'] ) ? esc_url_raw( $options['uri_redirect'] ) : null;
        // If has a redirect URI, redirect it
        if( !empty( $redirect ) ) {
            wp_redirect( $redirect );
        }
    }

    public function register_shortcodes() {
        add_shortcode( 'mdmsm_show_content', array( $this, 'show_content_shortcode' ) );
        add_shortcode( 'mdmsm_show_social', array( $this, 'show_social_links_shortcode' ) );
    }
}
