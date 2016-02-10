<p class="<?php echo $input_wrapper; ?>">

    <?php if( $setting['label'] ) : ?>
        <label for="<?php echo $option_key; ?>"><?php echo $setting['label']; ?></label>
    <?php endif; ?>

    <input type="text" id="<?php echo esc_attr( $setting['id'] ); ?>" class="<?php echo sprintf( '%s%s %s', $this->plugin_name, '_text', esc_attr( $setting['class'] ) ); ?>" name="<?php echo $option_key; ?>" value="<?php echo esc_attr( $setting['value'] ); ?>" placeholder="<?php echo $setting['placeholder']; ?>">

    <?php if( $setting['description'] ) : ?>
        <p class="description"><?php echo $setting['description'] ?></p>
    <?php endif; ?>

</p>