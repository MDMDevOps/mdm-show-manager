<div class="field_wrapper add-margin-bottom mdmsm_grid">
    <div class="mdmsm_column sm-8">
        <label for="<?php echo sprintf( 'social_uri[%s]', $name ); ?>"><?php echo  $field['uri']['label']; ?></label>
        <input class="widefat" type="text" name="<?php echo sprintf( 'social_uri[%s]', $name ); ?>" id="<?php echo sprintf( '%s_uri', $name ); ?>" value="<?php echo $field['uri']['value']; ?>" placeholder="<?php echo $field['uri']['placeholder']; ?>">
    </div>
    <div class="mdmsm_column sm-4">
        <label for="<?php echo sprintf( 'social_ico[%s]', $name ); ?>"><?php echo  $field['ico']['label']; ?></label>
        <input class="widefat" type="text" name="<?php echo sprintf( 'social_ico[%s]', $name ); ?>" id="<?php echo sprintf( '%s_uri', $name ); ?>" value="<?php echo $field['ico']['value']; ?>" placeholder="<?php echo $field['ico']['placeholder']; ?>">
    </div>
</div>