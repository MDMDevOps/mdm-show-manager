<p class="<?php echo $input_wrapper; ?>">
    <label for="<?php echo $option_key ?>"><input type="checkbox" class="<?php echo sprintf( '%s%s %s', $this->plugin_name, '_checkbox', esc_attr( $setting['class'] ) ); ?>" name="<?php echo $option_key; ?>" value="<?php echo $setting['checked_value']; ?>" <?php checked( $setting['value'], $setting['checked_value'] ); ?>>&nbsp;<?php echo $setting['label']; ?></label>
    <?php if( $setting['description'] ) : ?>
        <p class="description"><?php echo $setting['description']; ?></p>
    <?php endif; ?>
</p>