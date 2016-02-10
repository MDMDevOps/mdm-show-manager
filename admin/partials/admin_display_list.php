<?php

/**
 * Provide a admin area view for the plugin
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */
?>
<?php

?>
<?php add_thickbox(); ?>

<?php // Feature in development : include plugin_dir_path( __FILE__ ) . 'display_calendar_form.php'; ?>

<div id="mdmsm_calendar">
    <div class="mdmsm_calendar_wrapper">
        <!-- reset time -->
        <?php $this->reset_time(); ?>
        <!-- Time Label Column -->
        <ol class="calendar-time">
            <!-- Set left column 'header' -->
            <li class="header"><?php _e( 'Day', $this->plugin_name ); ?></li>
            <!-- Set left column time labels -->
            <?php for( $i = 0; $i < 48; $i++  ) : ?>
                <li class="timelabel" data-time="<?php echo $this->time->format( 'h:i:s' ); ?>"><span class="label"><?php echo $this->time->format( 'h:i A' ); ?></span></li>
                <?php $this->time->add( new DateInterval('PT30M') ); ?>
            <?php endfor; ?>
        </ol>
        <!-- reset time -->
        <?php $this->reset_time(); ?>
        <!-- Set calendar days -->
        <div class="mdmsm_calendar_slots">
        <?php for( $day = 1; $day <= 7; $day++ ) : ?>
            <ol class="dayslot" data-day="<?php echo $day; ?>">
                <!-- Setting Heading -->
                <li class="header"><?php echo $this->get_day_name( $day ) ?></li>
                <!-- Set individuatl rows -->
                <?php for( $slot = 0; $slot < 48; $slot++  ) : ?>
                    <?php echo $this->get_calendar_row( $slot, $day, $this->time->format( 'H:i:s' ) ); ?>
                    <!--Increment time object -->
                    <?php $this->time->add( new DateInterval('PT30M') ); ?>
                <?php endfor; ?>
            </ol>
            <!-- reset time -->
            <?php $this->reset_time(); ?>
        <?php endfor; ?>
        </div>
    </div>
</div>