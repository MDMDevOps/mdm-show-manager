<?php
/**
 * The Metabox display
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */
?>
<div id="mdmsm_onair_metabox" class="mdmsm_metabox mdmsm_container" data-showid="<?php echo $post->ID; ?>">
    <div class="mdmsm_grid" id="mdmsm_onair_form">
        <div class="field_wrapper mdmsm_col sm-3">
            <label for="sday"><?php _e( 'Start Day', $this->plugin_name ); ?></label>
            <select name="sday" id="sday" data-placeholder="Select Day">
                <option value=''></option>
                <option value="1"><?php _e('Monday', $this->plugin_name); ?></option>
                <option value="2"><?php _e('Tuesday', $this->plugin_name); ?></option>
                <option value="3"><?php _e('Wednesday', $this->plugin_name); ?></option>
                <option value="4"><?php _e('Thursday', $this->plugin_name); ?></option>
                <option value="5"><?php _e('Friday', $this->plugin_name); ?></option>
                <option value="6"><?php _e('Saturday', $this->plugin_name); ?></option>
                <option value="7"><?php _e('Sunday', $this->plugin_name); ?></option>
            </select>
        </div>

        <div class="field_wrapper mdmsm_col sm-3">
            <label for="stime"><?php _e( 'Start Time', $this->plugin_name ); ?></label>
            <select name="sime" id="stime" data-placeholder="Start Time">
                <option value=''></option>

                <?php $this->reset_time(); ?>

                <?php for( $i = 0; $i < 49; $i++ ) : ?>
                    <option value="<?php echo $this->time->format( 'H:i:s' ); ?>"><?php echo $this->time->format( 'h:i A' ); ?></option>
                    <?php $this->time->add( new DateInterval('PT30M') ); ?>
                <?php endfor; ?>

            </select>
        </div>

        <div class="field_wrapper mdmsm_col sm-3">
            <label for="etime"><?php _e( 'End Time', $this->plugin_name ); ?></label>
            <select name="etime" id="etime" data-placeholder="End Time">
                <option value=''></option>

                <?php $this->reset_time(); ?>

                <?php for( $i = 0; $i < 48; $i++ ) : ?>
                    <option value="<?php echo $this->time->modify( '-1 second' )->format( 'H:i:s' ); ?>"><?php echo $this->time->modify( '+1 second' )->format( 'h:i A' ); ?></option>
                    <?php $this->time->add( new DateInterval('PT30M') ); ?>
                <?php endfor; ?>

            </select>
        </div>

        <div class="field_wrapper mdmsm_col sm-3">
            <label for="">Submit</label>
            <button class="button button-primary button-large buttton-block" name="mdmsm_submit" id="mdmsm_submit" disabled><?php _e( 'Schedule', $this->plugin_name ); ?></button>
        </div>
    </div>

    <div id="mdmsm_view">
        <div id="mdmsm_notice"></div>
        <table class="wp-list-table widefat fixed striped <?php echo $display_status; ?>" id="mdmsm_view_table">
            <thead>
                <tr>
                    <th><?php _e( 'Day', $this->plugin_name ); ?></th>
                    <th><?php _e( 'Start Time', $this->plugin_name ); ?></th>
                    <th><?php _e( 'End Time', $this->plugin_name ); ?></th>
                    <th><?php _e( 'Duration', $this->plugin_name ); ?></th>
                    <th><?php _e( 'Action', $this->plugin_name ); ?></th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach( $this->onair_single as $index => $airtime ) {
                        if( $airtime['type'] == 'show' ) {
                            // Create a couple datetime objects
                            $airtime['show']['stime']    = ( isset( $airtime['show']['stime'] ) && strtotime( $airtime['show']['stime'] ) ) ? new DateTime( '@' . strtotime( esc_attr( $airtime['show']['stime'] ) ) ) : new DateTime();
                            $airtime['show']['etime']    = ( isset( $airtime['show']['etime'] ) && strtotime( $airtime['show']['etime'] ) ) ? new DateTime( '@' . strtotime( esc_attr( $airtime['show']['etime'] ) ) ) : new DateTime();
                            include plugin_dir_path( __FILE__ ) . 'display_single.php';
                        }
                    } ?>
            </tbody>
        </table>
    </div>
</div>