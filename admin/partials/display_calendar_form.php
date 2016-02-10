<div id="mdmsm_calendar_form" class="mdmsm_postbox">
    <div class="post_box_heading">
        <h2><?php _e( 'New Schedule Entry', $this->plugin_name ); ?></h2>
    </div>
    <div class="post_box_body">
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
    </div>
</div>