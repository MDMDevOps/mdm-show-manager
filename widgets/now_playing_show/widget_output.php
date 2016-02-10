<div id="mdmsm_now_playing_show_widget">

    <?php if( isset( $this->onair['showid'] ) && !empty( $this->onair['showid'] ) ) : ?>
        <?php if( $instance['hide_title'] != 'on' ) { echo $title; } ?>
        <div class="mdmsm_onair_wrapper">
            <figure class="mdmsm_show_thumbnail">
                <?php echo $this->onair['thumbnail']; ?>
            </figure>
            <div class="mdmsm_show_meta">
                <h3 class="mdmsm_show_title">
                    <a href="<?php echo $this->onair['permalink'] ?>"><?php echo $this->onair['title'] ?></a>
                </h3>
                <?php echo apply_filters( 'the_content', $this->onair['description'] ); ?>
            </div>
        </div>
        <?php if( $instance['hide_next'] != 'on' && isset( $this->ondeck ) ) : ?>
        <div class="mdmsm_upcoming_wrapper">
            <h4 class="mdmsm_upcoming_title">Up Next</h4>
            <p> <a href="<?php echo $this->ondeck['permalink'] ?>"><?php echo $this->ondeck['title'] ?></a></p>
        </div>
        <?php endif; ?>
    <?php elseif( isset( $instance['default_content'] ) && trim( $instance['default_content'] ) != '' ) : ?>
        <?php if( $instance['hide_title'] != 'on' ) { echo $title; } ?>
        <?php echo apply_filters( 'the_content', $instance['default_content'] ); ?>
    <?php endif; ?>
</div>