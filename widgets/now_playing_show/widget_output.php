<div id="mdmsm_now_playing_show_widget">

    <?php if( $instance['hide_title'] !== 'on' ) { echo $title; } ?>

    <div class="mdmsm_onair_wrapper">
        <figure class="mdmsm_show_thumbnail">
            <?php echo $this->onair->thumbnail; ?>
        </figure>
        <div class="mdmsm_show_meta">
            <h3 class="mdmsm_show_title">
                <a href="<?php echo $this->onair->permalink; ?>"><?php echo $this->onair->title; ?></a>
            </h3>
            <?php echo wpautop( wptexturize( $this->onair->description ) ); ?>
        </div>
    </div>

    <?php if( $instance['hide_next'] !== 'on' ) : ?>
        <div class="mdmsm_upcoming_wrapper">
            <h4 class="mdmsm_upcoming_title"><?php echo $instance['next_title']; ?></h4>
            <?php if( isset( $this->ondeck ) ) : ?>
                <p><a href="<?php echo $this->ondeck->permalink; ?>"><?php echo $this->ondeck->title; ?></a></p>
            <?php else : ?>
                <?php echo apply_filters( 'the_content', $instance['default_content'] ); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>