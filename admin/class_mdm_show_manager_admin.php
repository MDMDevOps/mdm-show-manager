<?php

/**
 * The admin-specific functionality of the plugin.
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */

class Mdm_Show_Manager_Admin {

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
     * Datetime object for utility purposes
     * @since  1.1.0
     * @access private
     * @var    (obj) $time : The datetime object to use in various places
     */
    private $time;

    /**
     * Master On Air Schedule
     * @since  1.1.0
     * @access private
     * @var    (array) $onair_master : The master schedule of all shows
     */
    private $onair_master;

    /**
     * Single On Air Schedule
     * @since  1.1.0
     * @access private
     * @var    (array) $onair_single : The single show schedule from post_meta
     */
    public $onair_single;

    /**
     * Initialize the class and set its properties.
     * @since 1.0.0
     * @param (string) $plugin_name  : The name of this plugin.
     * @param (string) $version      : The current version of the plugin.
     * @param (string) $settings_key : The unique identifier used to get settings from the database.
     */
    public function __construct( $plugin_base, $plugin_name, $version, $settings_key  ) {
        // Set fields from passed in arguments
        $this->plugin_base  = $plugin_base;
        $this->plugin_name  = $plugin_name;
        $this->version      = $version;
        $this->settings_key = $settings_key;
        // Create Datetime object
        $this->time = new DateTime();
        // Set timezone from wordpress timezone setting
        // Set timezone
        $timezone = get_option( 'timezone_string', 'UTC' );
        if( !isset( $timezone ) || !trim( $timezone ) || $timezone == '' ) {
            $timezone = 'UTC';
        }
        $this->time->setTimeZone( new DateTimeZone( $timezone ) );
    }

    /**
     * Register the stylesheets for the admin area.
     * @since 1.0.0
     */
    public function enqueue_styles() {
        wp_enqueue_style( $this->plugin_name, plugin_dir_url( $this->plugin_base ) . 'assets/styles/admin.css', array(), $this->version, 'all' );
    }

    /**
     * Register the JavaScript for the admin area.
     * @since 1.0.0
     */
    public function enqueue_scripts() {
        wp_enqueue_script( $this->plugin_name, plugin_dir_url( $this->plugin_base ) . 'assets/scripts/admin.js', array( 'jquery' ), $this->version, false );
        wp_localize_script( $this->plugin_name, 'mdmsmajax', array( 'wpajaxurl' => admin_url( 'admin-ajax.php' ) ) );
    }

    /**
     * Set onair_master, sorted, from options table
     * @since 1.0.0
     * @access private
     */
    private function set_onair_master() {
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

    /**
     * Set onair_single, sorted, from individual post meta
     * @since 1.0.0
     * @access private
     * @param (int) $post_id : ID of show to get data for
     */
    private function set_onair_single( $post_id = null ) {
        if( !$post_id ) {
            return false;
        }
        $this->onair_single = ( is_array( get_post_meta( $post_id, 'onair', true ) ) ) ? get_post_meta( $post_id, 'onair', true ) : array();
        // Now we'll go ahead and sort
        usort( $this->onair_single, array( $this,'sort_onair_single' ) );
    }

    /**
     * Sorting callback function used for usort during the set operation
     * @since 1.0.0
     * @access private
     * @param (array) $a : Single timeslot to sort by day
     * @param (array) $b : Single timeslot to sort by day
     */
    private function sort_onair_single( $a, $b ) {
        if( $a['show']['sday'] > $b['show']['sday'] ) {
            return 1;
        }
        if( $a['show']['sday'] < $b['show']['sday'] ) {
            return -1;
        }
        if( $a['show']['sday'] == $b['show']['sday'] ) {
            return 0;
        }
    }

    /**
     * Reset our dateTime worker back to midnight
     * @since 1.0.0
     * @access private
     */
    private function reset_time() {
        $this->time->setTime( 0, 0, 0 );
    }

    /**
     * Create a nonce to link different timeslots together
     * @since 1.0.0
     * @access private
     * @param (int) $showid : ID of show to get data for
     */
    private function get_record_nonce( $showid ) {
        $id = get_option( 'showschedid', 100 );
        update_option( 'showschedid', $id + 1, true );
        $datetime = new DateTime();
        return sha1( sprintf( '%s%s%s', $showid, $datetime->format( 'Y-m-d H:i:s' ), $id ) );
    }

    /**
     * Get the name of a day from integer value
     * @since 1.0.0
     * @access private
     * @param (int) $day : numeric id of day to retrieve name of
     */
    private function get_day_name( $day = null ) {
        $days = array(
            1 => 'Monday',
            2 => 'Tuesday',
            3 => 'Wednesday',
            4 => 'Thursday',
            5 => 'Friday',
            6 => 'Saturday',
            7 => 'Sunday',
        );
        if( $day === null ) {
            return $days;
        }
        else if( isset( $days[ $day ] ) ) {
            return $days[ $day ];
        }
        else {
            return false;
        }
    }

    /**
     * Register admin menu page
     * @see https://codex.wordpress.org/Administration_Menus
     * @since 1.0.0
     */
    public function register_admin_menu() {
        // Add top level menu page
        add_menu_page( __( 'MDM Show Manager', $this->plugin_name ), __( 'Show Manager', $this->plugin_name ), 'manage_options', $this->plugin_name, array( $this, 'display_menu_page' ), 'dashicons-microphone', 5 );
        add_submenu_page( $this->plugin_name, __( 'On Air Schedule', $this->plugin_name ), __( 'On Air Schedule', $this->plugin_name ), 'manage_options', $this->plugin_name, false );
        add_submenu_page( $this->plugin_name, __( 'All Shows', $this->plugin_name ), __( 'All Shows', $this->plugin_name ), 'manage_options', 'edit.php?post_type=show', false );
        add_submenu_page( $this->plugin_name, __( 'Add New Show', $this->plugin_name ), __( 'Add New Show', $this->plugin_name ), 'manage_options', 'post-new.php?post_type=show', false );
    }

    /**
     * Display the base menu page, which is the calendar
     * @see https://codex.wordpress.org/Administration_Menus
     * @since 1.0.0
     */
    public function display_menu_page() {
        // Get schedule from database
        $this->set_onair_master();
        // create a datetime object
        $time = new DateTime();
        // Set the time to midnight
        $time->setTime( 0, 0, 0 );
        // Include calendar markup
        include plugin_dir_path( __FILE__ ) . 'partials/admin_display_list.php';
    }

    /**
     * Get single row, of single day of the calendar
     * @since 1.0.0
     * @access private
     * @param (int)    $iter : Which iteration of the loop is being used
     * @param (int)    $day  : numeric id of day
     * @param (string) $time : H:i:s representation of the time
     */
    private function get_calendar_row( $iter = null, $day = null, $time = null ) {
        // if nothing is set, lets just bail...these are not the droids I'm looking for
        if( !isset( $iter ) || !isset( $day ) || !isset( $time ) ) {
            // Temp
            echo 'malformed';
            return false;
        }
        // If nothing is set, we need to return an empty list item and bail
        if( !isset( $this->onair_master[ $day ][ $time ] ) ) {
            return sprintf( '<li class="timeslot" data-time="%1$s" style="z-index: %2$s"></li>', $this->time->format( 'h:i:s' ), 100 - $iter );
        }
        // If we've made it here, we can insert a value
        $airtime   = $this->onair_master[ $day ][ $time ];
        $stime     = new DateTime( '@' . strtotime( $airtime['show']['stime'] ) );
        $etime     = new DateTime( '@' . strtotime( $airtime['show']['etime'] ) );
        $class     = sprintf( 'span%smin', $airtime['slot']['duration'] * 60 );
        $meta      = sprintf( '<p class="showmeta">%s - %s</p>', $stime->format( 'g:i A' ), $etime->modify( '+1 second' )->format( 'g:i A' ) );
        $showtitle = sprintf( '<h4 class="showtitle">%1$s</h4>', get_the_title( $airtime['showid'] ) );
        $content   = sprintf( '<a class="mdmsm-calendar-item button button-primary %1$s" href="%2$s" style="height:%3$spx;">%4$s%5$s</a>', $class, get_edit_post_link( $airtime['showid'] ), $airtime['slot']['duration'] * 60, $showtitle, $meta );
        return sprintf( '<li class="timeslot" data-time="%1$s" style="z-index: %2$s">%3$s</li>', $this->time->format( 'h:i:s' ), 100 - $iter, $content );
    }
    /**
     * Register Meta Box for onair schedule
     * @since 1.0.0
     */
    public function register_meta_boxes() {
        add_meta_box( 'showoptions_metabox', __( 'Show Options', $this->plugin_name ), array( $this, 'showoptions_metabox_callback' ), 'show', 'normal', 'high' );
        add_meta_box( 'onair_metabox', __( 'On Air Schedule', $this->plugin_name ), array( $this, 'onair_metabox_callback' ), 'show', 'normal', 'high' );
    }

    /**
     * Callback function to display onair meta box
     * @since 1.0.0
     * @param (object) $post : Post object
     */
    public function onair_metabox_callback( $post ) {
        // Set our single schedule
        $this->set_onair_single( $post->ID );
        // WP Nonce Field for saving post
        wp_nonce_field( 'onair_metabox_nonce', 'onair_nonce' );
        // Set initial class to indicate if schedule is empty or not
        $display_status = ( empty( $this->onair_single ) ) ? 'onair-empty' : 'onair';
        // Include metabox markup
        include plugin_dir_path( __FILE__ ) . 'metabox/display_onair_metabox.php';
    }

    /**
     * Callback function to display show options meta box
     * @since 1.0.0
     * @param (object) $post : Post object
     */
    public function showoptions_metabox_callback( $post ) {
        $social_fields = $this->get_social_fields( $post );
        $options       = ( is_array( get_post_meta( $post->ID, 'show_options', true ) ) ) ? get_post_meta( $post->ID, 'show_options', true ) : array();
        $uri_redirect  = ( isset( $options['uri_redirect'] ) ) ? esc_url( $options['uri_redirect'], 'display' ) : null;
        $description   = ( isset( $options['widget_description'] ) ) ? esc_attr( $options['widget_description'] ) : null;
        // WP Nonce Field for saving post
        wp_nonce_field( 'options_metabox_nonce', 'options_nonce' );
        // Include metabox markup
        include plugin_dir_path( __FILE__ ) . 'metabox/display_options_metabox.php';
    }

    private function get_social_fields( $post ) {
        $fields = array(
            'facebook' => array(
                'uri' => array(
                    'value'       => null,
                    'label'       => __( 'Facebook URI', $this->plugin_name ),
                    'placeholder' => null,
                ),
                'ico' => array(
                    'value'       => null,
                    'label'       => __( 'Facebook Icon', $this->plugin_name ),
                    'placeholder' => __( 'mdmsm-icon-facebook', $this->plugin_name ),
                ),
            ),
            'twitter' => array(
                'uri' => array(
                    'value'       => null,
                    'label'       => __( 'Twitter URI', $this->plugin_name ),
                    'placeholder' => null,
                ),
                'ico' => array(
                    'value'       => null,
                    'label'       => __( 'Twitter Icon', $this->plugin_name ),
                    'placeholder' => __( 'mdmsm-icon-twitter', $this->plugin_name ),
                ),
            ),
            'instagram' => array(
                'uri' => array(
                    'value'       => null,
                    'label'       => __( 'Instagram URI', $this->plugin_name ),
                    'placeholder' => null,
                ),
                'ico' => array(
                    'value'       => null,
                    'label'       => __( 'Instagram Icon', $this->plugin_name ),
                    'placeholder' => __( 'mdmsm-icon-instagram', $this->plugin_name ),
                ),
            ),
            'pinterest' => array(
                'uri' => array(
                    'value'       => null,
                    'label'       => __( 'Pinterest URI', $this->plugin_name ),
                    'placeholder' => null,
                ),
                'ico' => array(
                    'value'       => null,
                    'label'       => __( 'Pinterest Icon', $this->plugin_name ),
                    'placeholder' => __( 'mdmsm-icon-pinterest', $this->plugin_name ),
                ),
            ),
            'youtube' => array(
                'uri' => array(
                    'value'       => null,
                    'label'       => __( 'Youtube URI', $this->plugin_name ),
                    'placeholder' => null,
                ),
                'ico' => array(
                    'value'       => null,
                    'label'       => __( 'Youtube Icon', $this->plugin_name ),
                    'placeholder' => __( 'mdmsm-icon-youtube', $this->plugin_name ),
                ),
            ),
            'vimeo' => array(
                'uri' => array(
                    'value'       => null,
                    'label'       => __( 'Vimeo URI', $this->plugin_name ),
                    'placeholder' => null,
                ),
                'ico' => array(
                    'value'       => null,
                    'label'       => __( 'Vimeo Icon', $this->plugin_name ),
                    'placeholder' => __( 'mdmsm-icon-vimeo', $this->plugin_name ),
                ),
            ),
            'gplus' => array(
                'uri' => array(
                    'value'       => null,
                    'label'       => __( 'Google+ URI', $this->plugin_name ),
                    'placeholder' => null,
                ),
                'ico' => array(
                    'value'       => null,
                    'label'       => __( 'Google+ Icon', $this->plugin_name ),
                    'placeholder' => __( 'mdmsm-icon-gplus', $this->plugin_name ),
                ),
            ),
        );

        // Get options from database
        $socuri = ( is_array( get_post_meta( $post->ID, 'social_uri', true ) ) ) ? get_post_meta( $post->ID, 'social_uri', true ) : array();
        $socico = ( is_array( get_post_meta( $post->ID, 'social_ico', true ) ) ) ? get_post_meta( $post->ID, 'social_ico', true ) : array();
        // Merge with defaults
        foreach( $fields as $name => $setting ) {
            $fields[$name]['uri']['value'] = ( isset( $socuri[ $name ] ) && !empty( $socuri[$name] ) ) ? $socuri[$name] : null;
            $fields[$name]['ico']['value'] = ( isset( $socico[ $name ] ) && !empty( $socico[$name] ) ) ? $socico[$name] : null;
        }

        return $fields;
    }

    /**
     * Callback function that happens when we save a show - for onair metabox
     * @since 1.0.0
     * @param (object) $post : Post object
     */
    public function onair_metabox_save( $post_id ) {
        // Bail if we're doing an auto save
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        // if our nonce isn't there, or we can't verify it, bail
        if( !isset( $_POST['onair_nonce'] ) || !wp_verify_nonce( $_POST['onair_nonce'], 'onair_metabox_nonce' ) ) return;
        // if our current user can't edit this post, bail
        if( !current_user_can( 'edit_post', $post_id ) ) return;
        /***************************************************************************
         * Now that we've verified our user, we can (safely) save our data
         **************************************************************************/
        // Now, we can use this as an opportunity to rebase our master schedule, but that's about it
        // We (should have) already saved the data with the ajax functions, but we want to make sure it stays in sync
        $this->rebase_onair_master();
    }

    /**
     * Callback function that happens when we save a show - for options metabox
     * @since 1.0.0
     * @param (object) $post : Post object
     */
    public function showoptions_metabox_save( $post_id ) {
        // Bail if we're doing an auto save
        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        // if our nonce isn't there, or we can't verify it, bail
        if( !isset( $_POST['onair_nonce'] ) || !wp_verify_nonce( $_POST['onair_nonce'], 'onair_metabox_nonce' ) ) return;
        // if our current user can't edit this post, bail
        if( !current_user_can( 'edit_post', $post_id ) ) return;
        /***************************************************************************
         * Now that we've verified our user, we can (safely) save our data
         **************************************************************************/
        if( !isset( $_POST ) ) {
            return;
        }
        $options = array(
            'uri_redirect' => isset( $_POST['uri_redirect'] ) ? esc_url_raw( $_POST['uri_redirect'] ) : null,
            'widget_description' => isset( $_POST['widget_description'] ) ? stripslashes( wp_filter_post_kses( $_POST['widget_description'] ) ) : null,
        );
        // Get values from $_POST
        $socuri = ( isset( $_POST['social_uri'] ) && !empty( $_POST['social_uri'] ) ) ? $_POST['social_uri'] : array();
        $socico = ( isset( $_POST['social_ico'] ) && !empty( $_POST['social_ico'] ) ) ? $_POST['social_ico'] : array();
        // Sanitize
        foreach( $socuri as $name => $uri ) {
            $socuri[$name] = esc_url_raw( $uri );
        }
        foreach( $socico as $name => $ico ) {
            $socico[$name] = sanitize_text_field( stripslashes( $ico ) );
        }
        // Update
        update_post_meta( $post_id, 'show_options', $options );
        update_post_meta( $post_id, 'social_uri', $socuri );
        update_post_meta( $post_id, 'social_ico', $socico );
    }

    /**
     * Rebase the master schedule, so it stays in sync with actual post data...just in case
     * @since 1.0.0
     * @access private
     */
    private function rebase_onair_master() {
        // Array to hold onair schedules
        $onair  = array();
        // Array to hold our new master schedule
        $master = array();
        // Get the post meta from each show, and push to $onair
        $args = array( 'post_type' => array( 'show' ), 'post_status' => array( 'publish' ), 'post_per_page' => -1, 'nopaging' => true );
        // Run the query
        $the_query = new WP_Query( $args );
        if( $the_query->have_posts() ) {
            while( $the_query->have_posts() ) {
                $the_query->the_post();
                $post_meta = get_post_meta( $the_query->post->ID, 'onair', true );
                ++$count;
                if( is_array( $post_meta) && !empty( $post_meta ) ) {

                    foreach( $post_meta as $meta ) {
                        array_push( $onair, $meta );
                    }
                }
            }
        }
        wp_reset_postdata();
        // Now we can push each to our master array
        foreach( $onair as $record ) {
            $master[ $record['slot']['sday'] ][ $record['slot']['stime'] ] = $record;
        }
        // Now fill in the empty spaces in the master array
        for( $i = 0; $i < 8; $i++ ) {
            // If this day slot is not an array for some reason, set it to an empty array
            if( !isset( $master[$i] ) || !is_array( $master[$i] ) ) {
                $master[$i] = array();
            }
            // Sort day slot by time
            ksort( $master[$i] );
        }
        ksort(  $master );
        // Udpate the database
        update_option( 'onairmaster', $master, true );
    }

    /**
     * Ajax function to add a row to a single metabox
     * @since 1.0.0
     */
    public function add_metabox_row() {
        // If params are missing, lets send an error and bail
        if( !isset( $_POST['sday'] ) || !isset( $_POST['stime'] ) || !isset( $_POST['etime'] ) || !isset( $_POST['showid'] ) ) {
            $this->metabox_error( 'Unable to add slot: Ajax Parameters Unfound' );
            exit();
        }
        // If time params are malformed, send an error and bail
        if( strtotime( $_POST['stime'] ) == false || strtotime( $_POST['etime'] ) == false ) {
            $this->metabox_error( 'Unable to add slot: Malformed Ajax Parameters' );
            exit();
        }
        // 1. Set our schedules
        $this->set_onair_single( $_POST['showid'] );
        $this->set_onair_master();
        // 2. Set our index value
        $index  = count( $this->onair_single );
        // 3. Set our initial time values
        $airtime = array(
            'showid'    => esc_attr( $_POST['showid'] ),
            'nonce'     => $this->get_record_nonce( $_POST['showid'] ),
            'type'      => 'show',
            'show'      => array(
                'sday'     => esc_attr( $_POST['sday'] ),
                'stime'    => new DateTime( '@' . strtotime( esc_attr( $_POST['stime'] ) ) ),
                'etime'    => new DateTime( '@' . strtotime( esc_attr( $_POST['etime'] ) ) ),
                'duration' => null,
            ),
            'slot'  => array(
                'sday'     => esc_attr( $_POST['sday'] ),
                'stime'    => new DateTime( '@' . strtotime( esc_attr( $_POST['stime'] ) ) ),
                'etime'    => new DateTime( '@' . strtotime( esc_attr( $_POST['etime'] ) ) ),
                'duration' => null,
            ),
        );
        // 4. Set our initial durations
        $airtime['show']['duration'] = $this->calculate_duration( $airtime['show']['stime'], $airtime['show']['etime'] );
        $airtime['slot']['duration'] = $airtime['show']['duration'];
        // 5. Special handling for multiday shows
        if( $airtime['show']['etime']->format( 'H:i:s' ) <= $airtime['show']['stime']->format( 'H:i:s' ) ) {
            $rollover = array(
                'showid'    => $airtime['showid'],
                'nonce'     => $airtime['nonce'],
                'type'      => 'rollover',
                'show'      => array(
                    'sday'     => esc_attr( $_POST['sday'] ),
                    'stime'    => $airtime['show']['stime'],
                    'etime'    => $airtime['show']['etime'],
                    'duration' => $airtime['show']['duration'],
                ),
                'slot'  => array(
                    'sday'     => ( $airtime['show']['sday'] ) == 7 ? 1 :  $airtime['show']['sday'] + 1,
                    'stime'    => new DateTime( '@' . strtotime( '00:00:00' ) ),
                    'etime'    => new DateTime( '@' . strtotime( $airtime['show']['etime']->format( 'H:i:s' ) ) ),
                    'duration' => null,
                ),
            );
            // Get the duration for our rollover timeslot
            $rollover['slot']['duration'] = $this->calculate_duration( $rollover['slot']['stime'], $rollover['slot']['etime'] );
            // Adjust airtime appropriatly
            $airtime['slot']['etime']->setTime( 23, 59, 59 );
            $airtime['slot']['duration'] = $airtime['show']['duration'] - $rollover['slot']['duration'];
        }
        // 6. Check for conflicts with other shows
        $error = $this->check_schedule_conflict( $airtime );
        if( $error ) {
            $this->metabox_error( $error );
            exit();
        }
        if( isset( $rollover ) ) {
            $error = $this->check_schedule_conflict( $rollover );
            if( $error ) {
                $this->metabox_error( $error );
                exit();
            }
        }
        // 7. If we don't have any conflicts, push result onto stack
        $this->push_airtime_single( $airtime );
        $this->push_airtime_master( $airtime );
        if( isset( $rollover ) ) {
            $this->push_airtime_single( $rollover );
            $this->push_airtime_master( $rollover );
        }
        // 8. Update our database
        if( update_post_meta( $airtime['showid'], 'onair', $this->onair_single ) && update_option( 'onairmaster', $this->onair_master, true ) ) {
            // Finally, if we've made it here, we can display single metabox row
            ob_start();
                include plugin_dir_path( __FILE__ ) . 'metabox/display_single.php';
                $content = ob_get_contents();
            ob_end_clean();
            $output = array(
                'success' => true,
                'error'   => null,
                'data'    => $content,
            );
            header('Content-Type: application/json');
            echo json_encode( $output );
            exit();
        }
        $this->metabox_error( 'Unable to delete time slot at this time, Error Code: 103, Unable to reach database' );
        exit();
    }

    /**
     * Ajax function to remove a row from metabox
     * @since 1.0.0
     */
    public function remove_metabox_row() {
        // If params are missing, lets send an error and bail
        if( !isset( $_POST['nonce'] ) || !isset( $_POST['showid'] ) ) {
            $this->metabox_error( 'Unable to delete time slot at this time, Error Code: 001, Undefined Index' );
            exit();
        }
        // 1. Set our schedules
        $this->set_onair_single( $_POST['showid'] );
        $this->set_onair_master();

        // Remove from single
        foreach( $this->onair_single as $index => $onair ) {
            if( $onair['nonce'] == $_POST['nonce'] ) {
                unset( $this->onair_single[ $index ] );
            }
        }
        foreach( $this->onair_master as $day => $slots ) {
            foreach( $slots as $key => $slot ) {
                if( $slot['nonce'] == $_POST['nonce'] ) {
                    unset( $this->onair_master[ $day ][ $key ] );
                }
            }
        }
        // Update our database
        if( update_post_meta( $_POST['showid'], 'onair', $this->onair_single ) && update_option( 'onairmaster', $this->onair_master, true ) ) {
            $output = array(
                'success' => true,
                'error'   => null,
                'data'    => null,
            );
            header('Content-Type: application/json');
            echo json_encode( $output );
            exit();
        }
        $this->metabox_error( 'Unable to delete time slot at this time, Error Code: 003, Unable to reach database' );
        exit();
    }

    /**
     * Push a single scheduled time onto onair_single stack
     * @since 1.0.0
     * @param (array) $airtime : The single timeslot to schedule
     */
    private function push_airtime_single( $airtime ) {
        $update = array(
            'showid'    => $airtime['showid'],
            'nonce'     => $airtime['nonce'],
            'show'      => array(
                'sday'     => $airtime['show']['sday'],
                'stime'    => $airtime['show']['stime']->format( 'H:i:s' ),
                'etime'    => $airtime['show']['etime']->format( 'H:i:s' ),
                'duration' => $airtime['show']['duration'],
            ),
            'slot'  => array(
                'sday'     => $airtime['slot']['sday'],
                'stime'    => $airtime['slot']['stime']->format( 'H:i:s' ),
                'etime'    => $airtime['slot']['etime']->format( 'H:i:s' ),
                'duration' => $airtime['slot']['duration'],
            ),
        );
        // Format datetime objects before save
        $airtime['show']['stime'] = $airtime['show']['stime']->format( 'H:i:s' );
        $airtime['show']['etime'] = $airtime['show']['etime']->format( 'H:i:s' );
        $airtime['slot']['stime'] = $airtime['slot']['stime']->format( 'H:i:s' );
        $airtime['slot']['etime'] = $airtime['slot']['etime']->format( 'H:i:s' );
        array_push( $this->onair_single, $airtime );
    }

    /**
     * Push a single scheduled time onto onair_master stack
     * @since 1.0.0
     * @param (array) $airtime : The single timeslot to schedule
     */
    private function push_airtime_master( $airtime ) {
        $update = array(
            'showid'    => $airtime['showid'],
            'nonce'     => $airtime['nonce'],
            'show'      => array(
                'sday'     => $airtime['show']['sday'],
                'stime'    => $airtime['show']['stime']->format( 'H:i:s' ),
                'etime'    => $airtime['show']['etime']->format( 'H:i:s' ),
                'duration' => $airtime['show']['duration'],
            ),
            'slot'  => array(
                'sday'     => $airtime['slot']['sday'],
                'stime'    => $airtime['slot']['stime']->format( 'H:i:s' ),
                'etime'    => $airtime['slot']['etime']->format( 'H:i:s' ),
                'duration' => $airtime['slot']['duration'],
            ),
        );
        // Format datetime objects before save
        $airtime['show']['stime'] = $airtime['show']['stime']->format( 'H:i:s' );
        $airtime['show']['etime'] = $airtime['show']['etime']->format( 'H:i:s' );
        $airtime['slot']['stime'] = $airtime['slot']['stime']->format( 'H:i:s' );
        $airtime['slot']['etime'] = $airtime['slot']['etime']->format( 'H:i:s' );
        $this->onair_master[ $airtime['slot']['sday'] ][ $airtime['slot']['stime'] ] = $airtime;
    }

    /**
     * Format and return an error to ajax calls from the metabox
     * @since 1.0.0
     * @param (string) $content : The error message to show
     */
    private function metabox_error( $content = 'Unable to add slot: Undefined error' ) {
        $output  = array(
            'success' => false,
            'error'   => $content,
            'data'    => null,
        );
        header('Content-Type: application/json');
        echo json_encode( $output );
    }

    /**
     * Check for conflicts with other scheduled items
     * @since 1.0.0
     * @param (array) $airtime : The timeslot to check for errors, against master schedule
     */
    private function check_schedule_conflict( $airtime ) {
        // Initialize a conflict variable
        $conflict = false;
        // if schedule is empty, all good...lets go!
        if( empty( $this->onair_master[ $airtime['slot']['sday'] ] ) ) {
            return false;
        }
        // Now we can check for conflicts against each show
        foreach( $this->onair_master[ $airtime['slot']['sday'] ] as $timeslot ) {
            // Check that start time doesn't fall between any existing start & end times
            if( $airtime['slot']['stime']->format( 'H:i:s' ) >= $timeslot['slot']['stime'] && $airtime['slot']['stime']->format( 'H:i:s' ) <= $timeslot['slot']['etime'] ) {
                $stime = new DateTime( '@' . strtotime( $timeslot['slot']['stime'] ) );
                $etime = new DateTime( '@' . strtotime( $timeslot['slot']['etime'] ) );
                $conflict = sprintf( '<a href="%s">%s</a> is already scheduled from %s to %s on %s', get_edit_post_link( $timeslot['showid'] ), get_the_title( $timeslot['showid'] ), $stime->format( 'g:i A' ), $etime->modify( '+1 second' )->format( 'g:i A' ), $this->get_day_name( $timeslot['slot']['sday'] ) );
            }
            // Check that show ends before next show starts
            if( $airtime['slot']['stime']->format( 'H:i:s' ) <= $timeslot['slot']['stime'] && $airtime['slot']['etime']->format( 'H:i:s' ) >= $timeslot['slot']['stime'] ) {
                $stime = new DateTime( '@' . strtotime( $timeslot['slot']['stime'] ) );
                $etime = new DateTime( '@' . strtotime( $timeslot['slot']['etime'] ) );
                $conflict = sprintf( '<a href="%s">%s</a> is already scheduled from %s to %s on %s', get_edit_post_link( $timeslot['showid'] ), get_the_title( $timeslot['showid'] ), $stime->format( 'g:i A' ), $etime->modify( '+1 second' )->format( 'g:i A' ), $this->get_day_name( $timeslot['show']['sday'] ) );
            }
        }
        return $conflict;
    }

    /**
     * Calculate the difference between 2 datetime objects
     * @since 1.0.0
     * @param (obj) $stime : The start time datetime object
     * @param (obj) $etime : The end time datetime object
     */
    private function calculate_duration( $stime, $etime ) {
        $diff = $etime->diff( $stime );
        $duration = ( $etime->format( 'H:i:s' ) <= $stime->format( 'H:i:s' ) ) ? 24 - ceil( ( $diff->h + ( $diff->i / 60 ) ) * 2 ) / 2 : ceil( ( $diff->h + ( $diff->i / 60 ) ) * 2 ) / 2;
        return $duration;
    }

} // end class
