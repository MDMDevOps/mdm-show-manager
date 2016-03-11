<?php

/**
 * Provide a admin area view for the plugin
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */
?>

<?php // Feature in development : include plugin_dir_path( __FILE__ ) . 'display_calendar_form.php'; ?>

<div id="mdmsm_calendar">
    <div class="mdmsm_calendar_wrapper">
        <!-- Time Label Column -->
        <ol class="calendar-time">
            <!-- Set left column 'header' -->
            <li class="header"><?php _e( 'Day', $this->plugin_name ); ?></li>
            <!-- Set left column time labels -->
            <?php for( $i = 0; $i < 48; $i++  ) : ?>
                <li class="timelabel" data-time="<?php echo $time->format( 'h:i:s' ); ?>"><span class="label"><?php echo $time->format( 'h:i A' ); ?></span></li>
                <?php $time->add( new DateInterval('PT30M') ); ?>
            <?php endfor; ?>
        </ol>
        <!-- reset time -->
        <?php $time->setTime( 0, 0, 0 ); ?>
        <!-- Set calendar days -->
        <div class="mdmsm_calendar_slots">
        <?php for( $day = 1; $day <= 7; $day++ ) : ?>
            <ol class="dayslot" data-day="<?php echo $day; ?>">
                <!-- Setting Heading -->
                <li class="header"><?php echo $this->get_day_name( $day ) ?></li>
                <!-- Set individuatl rows -->
                <?php for( $slot = 0; $slot < 48; $slot++  ) : ?>
                    <?php echo $this->get_calendar_row( $slot, $day, $time->format( 'H:i:s' ) ); ?>
                    <!--Increment time object -->
                    <?php $time->add( new DateInterval('PT30M') ); ?>
                <?php endfor; ?>
            </ol>
            <!-- reset time -->
            <?php $time->setTime( 0, 0, 0 ); ?>
        <?php endfor; ?>
        </div>
    </div>
</div>