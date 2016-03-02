<?php

class Mdmsm_Widget_Showslot {
    public $showid;
    public $title;
    public $permalink;
    public $description;
    public $thumbnail;
    public $stime;
    public $etime;
    private $options;

    public function __construct( $show ) {
        $this->showid    = $show['showid'];
        $this->set_options();
        $this->title     = get_the_title( $this->showid );
        $this->thumbnail = get_the_post_thumbnail( $this->showid, 'mdmsm_thumbnail' );
        $this->stime     = $show['show']['stime'];
        $this->etime     = $show['show']['etime'];
        $this->permalink = get_permalink( $this->showid );
        $this->description = $this->options['description'];
    }

    private function set_options() {
        // Get options from database
        $options = get_post_meta( $this->showid, 'show_options', true );
        // Ensure we have a properly formed array, and merge with defaults
        $this->options = array(
            'permalink'   => ( isset( $options['uri_redirect'] ) && !empty( $options['uri_redirect'] ) ) ? $options['uri_redirect'] : get_permalink( $this->showid ),
            'description' => ( isset( $options['widget_description'] ) && !empty( $options['widget_description'] ) ) ? $options['widget_description'] : null,
        );
    }
}