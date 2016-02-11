<?php

class Mdm_Show_Manager_Now_Playing_Widget extends WP_Widget {

    public $widget_id_base;
    public $widget_name;
    public $widget_options;
    public $control_options;
    private $utilities;
    private $nowplaying;
    private $onair;
    private $ondeck;
    private $upcoming;
    private $shows = array();

    /**
     * Constructor, initialize the widget
     * @param $id_base, $name, $widget_options, $control_options ( ALL optional )
     * @since 1.0.0
     */
    public function __construct() {
        $this->utilities      = new Mdm_Show_Manager_Utilities();
        $this->widget_id_base = 'mdm_now_playing';
        $this->widget_name    = 'Now Playing Widget';
        $this->widget_options = array(
            'classname'   => 'mdm_now_playing_widget',
            'description' => 'Widget to display currently playing show'
        );
        $this->utilities->set_onair_master();
        // Initialize a holder array to store our current show
        $this->nowplaying = array(
            'showid'    => null,
            'nonce'     => null,
            'show'      => array(
                'sday'     => null,
                'stime'    => null,
                'etime'    => null,
                'duration' => null,
            ),
            'slot'  => array(
                'sday'     => null,
                'stime'    => null,
                'etime'    => null,
                'duration' => null,
            ),
        );
        // Initialize a holder array to store our upcoming show
        $this->upcoming = array(
            'showid'    => null,
            'nonce'     => null,
            'show'      => array(
                'sday'     => null,
                'stime'    => null,
                'etime'    => null,
                'duration' => null,
            ),
            'slot'  => array(
                'sday'     => null,
                'stime'    => null,
                'etime'    => null,
                'duration' => null,
            ),
        );
        parent::__construct( $this->widget_id_base, $this->widget_name, $this->widget_options );
    } // end __construct

    /**
     * Create back end form for specifying image and content
     * @param $instance
     * @see https://codex.wordpress.org/Function_Reference/wp_parse_args
     * @since 1.0.0
     */
    public function form( $instance ) {
        // define our default values
        $defaults = array(
            'title'           => null,
            'default_show'    => null,
            'default_content' => null,
            'hide_title'      => false,
            'hide_next'       => false,
            'next_title'      => null,

        );
        // merge instance with default values
        $instance = wp_parse_args( (array)$instance, $defaults );
        // include our form markup
        $this->get_all_shows();
        include plugin_dir_path( __FILE__ ) . 'widget_form.php';
    } // end form()

    /**
     * Update form values
     * @param $new_instance, $old_instance
     * @since 1.0.0
     */
    public function update( $new_instance, $old_instance ) {
        // initially set instance = old_instance, and replace individual values as we validate them
        $instance = $old_instance;
        // escape and set new values
        $instance['title']           = esc_attr( $new_instance['title'] );
        $instance['default_show']    = esc_attr( $new_instance['default_show'] );
        $instance['default_content'] = $new_instance['default_content'];
        $instance['hide_title']      = esc_attr( $new_instance['hide_title'] );
        $instance['hide_next']       = esc_attr( $new_instance['hide_next'] );
        $instance['next_title']      = esc_attr( $new_instance['next_title'] );
        return $instance;
    } // end update()

    private function get_all_shows() {
        $args = array( 'post_type' => 'show', 'post_per_page' => -1, );
        $the_query = new WP_Query( $args );
        if( $the_query->have_posts() ) {
            $index = 0;
            while( $the_query->have_posts() ) {
                $the_query->the_post();
                $this->shows[$index]['title'] = get_the_title();
                $this->shows[$index]['id']    = get_the_id();
                ++$index;
            } // endwhile
        } // endif
    }

    private function set_onair( $next = true ) {
        $options     = ( is_array( get_post_meta( $this->nowplaying['showid'], 'show_options', true ) ) ) ? get_post_meta( $this->nowplaying['showid'], 'show_options', true ) : array();
        $permalink   = ( isset( $options['uri_redirect'] ) ) ? esc_url( $options['uri_redirect'], 'display' ) : get_permalink( $this->nowplaying['showid'] );
        $description = ( isset( $options['widget_description'] ) ) ? esc_attr( $options['widget_description'] ) : null;
        $this->onair = array(
            'showid'      => $this->nowplaying['showid'],
            'title'       => get_the_title( $this->nowplaying['showid'] ),
            'permalink'   => $permalink,
            'description' => $description,
            'thumbnail'   => get_the_post_thumbnail( $this->nowplaying['showid'], 'mdmsm_thumbnail' ),
            'stime'       => isset( $this->nowplaying['show']['stime'] ) ? $this->nowplaying['show']['stime']  : null,
            'etime'       => isset( $this->nowplaying['show']['etime'] ) ? $this->nowplaying['show']['stime']  : null,
        );
        if( $next != 'on' && isset( $this->upcoming['showid'] ) ) {
            $options      = ( is_array( get_post_meta( $this->upcoming['showid'], 'show_options', true ) ) ) ? get_post_meta( $this->upcoming['showid'], 'show_options', true ) : array();
            $permalink    = ( isset( $options['uri_redirect'] ) ) ? esc_url( $options['uri_redirect'], 'display' ) : get_permalink( $this->upcoming['showid'] );
            $description  = ( isset( $options['widget_description'] ) ) ? esc_attr( $options['widget_description'] ) : null;
            $this->ondeck = array(
                'showid'      => $this->upcoming['showid'],
                'title'       => get_the_title( $this->upcoming['showid'] ),
                'permalink'   => $permalink,
                'description' => $description,
                'thumbnail'   => get_the_post_thumbnail( $this->upcoming['showid'], 'mdmsm_thumbnail' ),
                'stime'       => isset( $this->upcoming['show']['stime'] ) ? $this->upcoming['show']['stime']  : null,
                'etime'       => isset( $this->upcoming['show']['etime'] ) ? $this->upcoming['show']['stime']  : null,
            );
        }
    }

    /**
     * Output widget on the front end
     * @param $args, $instance
     * @since 1.0.0
     */
    public function widget( $args, $instance ) {
        // Extract the widget arguments ( before_widget, after_widget, description, etc )
        extract( $args );
        // Instantiate $title to avoid errors
        $title = '';
        // Append before / after title elements if title is not blank
        if( !empty( $instance['title'] ) ) {
            $title = $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        // Create a datetime object to hold NOW time, using timezone defined in wordpress
        $now = new DateTime();
        // Set timezone
        $timezone = get_option( 'timezone_string', 'UTC' );
        if( !isset( $timezone ) || !trim( $timezone ) || $timezone == '' ) {
            $timezone = 'UTC';
        }
        $now->setTimeZone( new DateTimeZone( $timezone ) );
        // Loop through the schedule to see if we have a match
        $keys  = array_keys( $this->utilities->onair_master[ $now->format( 'N' ) ] );
        $index = 0;
        foreach( $this->utilities->onair_master[ $now->format( 'N' ) ] as $onair ) {
            $nextday = ( $now->format( 'N' ) == 7 ) ? 1 : $now->format( 'N' ) + 1;
            // Check to make sure now is between the start and end times
            if( $now->format( 'H:i:s' ) >= $onair['show']['stime'] && $now->format( 'H:i:s' ) <= $onair['show']['etime'] ) {
                $this->nowplaying = $onair;
                if( isset( $keys[ $index + 1 ] ) ) {
                    // Get the upcoming show
                    $this->upcoming = $this->utilities->onair_master[ $now->format( 'N' ) ][ $keys[ $index + 1 ] ];
                } else {
                    // If no next show exist, and the next day has a show that isn't this show, use that
                    $tomKeys = array_keys( $this->utilities->onair_master[ $nextday ] );
                    if( !empty( $tomKeys ) && ( $this->utilities->onair_master[ $nextday ][ $tomKeys[0] ]['showid'] !== $onair['showid'] ) ) {
                        $this->upcoming = $this->utilities->onair_master[ $nextday ][ $tomKeys[0] ];
                    }
                }
                // Let's get outta here, we got what we want
                break;
            }
            ++$index;
        }
        // If we didn't find a show, let's show a default
        if( $this->nowplaying['showid'] == null && isset( $instance['default_show'] ) ) {
            $this->nowplaying['showid'] = $instance['default_show'];
        }
        // Get all the data and set our onair values
        $this->set_onair( $instance['hide_next'] );
        // Display the markup before the widget (as defined in functions.php)
        echo $before_widget;
        // Include our output markup
        include plugin_dir_path( __FILE__ ) . 'widget_output.php';

        echo $after_widget;
    } // end widget()
} // end class