<?php

class Mdm_Show_Manager_Now_Playing_Widget extends WP_Widget {

    public $widget_id_base;
    public $widget_name;
    public $widget_options;
    public $control_options;

    private $settings_key;
    private $settings;
    private $plugin_name;
    private $datetime;
    private $schedule;
    private $onair;
    private $ondeck;


    /**
     * Constructor, initialize the widget
     * @param $id_base, $name, $widget_options, $control_options ( ALL optional )
     * @since 1.0.0
     */
    public function __construct() {
        $this->widget_id_base = 'mdm_now_playing';
        $this->widget_name    = 'Now Playing Widget';
        $this->widget_options = array(
            'classname'   => 'mdm_now_playing_widget',
            'description' => 'Widget to display currently playing show'
        );
        $this->set_datetime();
        $this->set_settings();
        $this->set_schedule();
        parent::__construct( $this->widget_id_base, $this->widget_name, $this->widget_options );
    } // end __construct

    private function set_settings() {
        $plugin_args = mdmsm_plugin_config();
        $this->plugin_name  = $plugin_args['plugin_name'];
        $this->settings_key = $plugin_args['settings_key'];
        $this->settings = get_option( $this->settings_key, array() );
    }
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
            'next_title'      => __( 'Up Next', $this->plugin_name ),

        );
        // merge instance with default values
        $instance = wp_parse_args( (array)$instance, $defaults );
        // Get list of all shows
        $shows = $this->get_shows();
        // Include markup for the form
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
        // Return new instance of widget
        return $instance;
    } // end update()

    private function get_shows() {
        // Initialize holding array
        $shows = array();
        // Construct query
        $query_args = array( 'post_type' => array( 'show' ), 'post_status' => array( 'publish' ), 'post_per_page' => -1, 'nopaging' => true );
        $the_query  = new WP_Query( $query_args );
        if( $the_query->have_posts() ) : while( $the_query->have_posts() ) : $the_query->the_post();
            array_push( $shows, array( 'title' => get_the_title(), 'id' => get_the_id() ) );
        endwhile; endif;
        // Set shows field
        $this->shows = $shows;
        return $shows;
    }

    /**
     * Output widget on the front end
     * @param $args, $instance
     * @since 1.0.0
     */
    public function widget( $args, $instance ) {
        // Get currently playing show
        $this->get_current_show( $instance );
        // If we didn't find a show, let's show a default (if that option is selected)
        if( $this->onair === null && isset( $instance['default_show'] ) ) {
            $default_show =  array( 'showid' => $instance['default_show'], 'show' => array( 'stime' => null, 'etime' => null ) );
            $this->onair = new Mdmsm_Widget_Showslot( $default_show );
        }
        // Extract the widget arguments ( before_widget, after_widget, description, etc )
        extract( $args );
        // Instantiate $title to avoid errors
        $title = '';
        // Append before / after title elements if title is not blank
        if( !empty( $instance['title'] ) ) {
            $title = $args['before_title'] . $instance['title'] . $args['after_title'];
        }
        // Display the markup before the widget (as defined in functions.php)
        echo $before_widget;
        // Include our output markup
        include plugin_dir_path( __FILE__ ) . 'widget_output.php';
        // Display after widget markup (as defined in functions.php)
        echo $after_widget;
    } // end widget()

    public function get_current_show( $instance ) {
        // Include showslot class
        include_once plugin_dir_path( __FILE__ ) . 'class_mdmsm_widget_showslot.php';
        // Get just today and tomorrow
        $current_day   = $this->schedule[ $this->datetime->format( 'N' ) ];
        $following_day = ( $this->datetime->format( 'N' ) == 7 ) ? 1 : $this->datetime->format( 'N' ) + 1;
        // Get array index keys
        $array_keys = array_keys( $current_day );
        // Loop through the schedule to see if we have a match
        $counter = 0;
        foreach( $current_day as $scheduled_show )  {
            // If current time is more than or equal to start && less than or equal to end, it's currently playing
            if( $this->datetime->format( 'H:i:s' ) >= $scheduled_show['show']['stime'] && $this->datetime->format( 'H:i:s' ) <= $scheduled_show['show']['etime'] ) {
                // Set currently playing show
                $this->onair = new Mdmsm_Widget_Showslot( $scheduled_show );
                // If we're hiding the upcoming show, we can bail now
                if( $instance['hide_next'] === 'on' ) {
                    break;
                }
                // Else, let's go ahead and get the next (upcoming show)
                if( isset( $array_keys[$counter + 1] ) ) {
                    $this->ondeck = new Mdmsm_Widget_Showslot( $current_day[ $array_keys[$counter + 1]] );
                    break;
                }
                // If no next show exist, and the next day has a show that isn't this show, use that
                $next_array_keys = array_keys( $following_day );
                if( !empty( $next_array_keys ) && ( $this->schedule[$following_day][$next_array_keys[0] ]['showid'] !== $this->onair->showid ) ) {
                    $this->ondeck = new Mdmsm_Widget_Showslot( $this->schedule[$following_day][$next_array_keys[0]] );
                }
                // Let's get outta here, we got what we came for
                break;
            }
            ++$counter;
        }
    }

    private function set_datetime() {
        // Create datetime object
        $this->datetime = new DateTime();
        // Get timezone option from database
        $timezone = get_option( 'timezone_string', 'UTC' );
        // Sanatize timezone
        if( !isset( $timezone ) || !trim( $timezone ) || $timezone === '' ) {
            $timezone = 'UTC';
        }
        // update datetime object
        $this->datetime->setTimeZone( new DateTimeZone( $timezone ) );
    }

    private function set_schedule() {
        // Get full master shedule
        $this->schedule = Mdm_Show_Manager_Utilities::get_onairmaster();
    }
} // end class