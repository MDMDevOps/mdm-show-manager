<p>
    <label for="<?php echo $this->get_field_name( 'title' ); ?>">Title</label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $instance['title'] ); ?>" />
</p>
<p>
    <label for="<?php echo $this->get_field_name( 'default_show' ); ?>">Default Show</label>
    <select class="widefat" name="<?php echo $this->get_field_name( 'default_show' ); ?>" id="<?php echo $this->get_field_id( 'default_show' ); ?>">
        <option value="">None</option>
        <?php foreach( $shows as $show ) : ?>
            <?php $selected = ( $instance['default_show'] == $show['id'] ) ? 'selected' : null; ?>
            <option value="<?php echo $show['id']; ?>" <?php echo $selected; ?>><?php echo $show['title'] ?></option>
        <?php endforeach; ?>
    </select>
</p>
<p class="description">Default show to display if no show is scheduled</p>
<p>
    <label for="<?php echo $this->get_field_name( 'default_content' ); ?>">Default Content</label>
    <textarea class="widefat" id="<?php echo $this->get_field_id( 'default_content' ); ?>" name="<?php echo $this->get_field_name( 'default_content' ); ?>" cols="30" rows="10"><?php echo esc_attr( $instance['default_content'] ); ?></textarea>
</p>
<p class="description">Default content to display if no show is scheduled. <strong>Note: </strong> This content will display <em>only</em> if no default show is specified</p>
<p>
    <label for="<?php echo $this->get_field_name( 'next_title' ); ?>">Title for Upcoming Show Section</label>
    <input class="widefat" id="<?php echo $this->get_field_id( 'next_title' ); ?>" name="<?php echo $this->get_field_name( 'next_title' ); ?>" type="text" value="<?php echo esc_attr( $instance['next_title'] ); ?>" />
</p>
<p class="description">If left blank, the default phrase "Up Next" text will be displayed</p>
<p>
    <label for="<?php echo $this->get_field_name( 'hide_title' ); ?>"><input type="checkbox" id="<?php echo $this->get_field_id( 'hide_title' ); ?>" name="<?php echo $this->get_field_name( 'hide_title' ); ?>" value='on' <?php checked( $instance['hide_title'], 'on' ); ?>><strong>DO NOT</strong> display title</label>
</p>
<p>
    <label for="<?php echo $this->get_field_name( 'hide_next' ); ?>"><input type="checkbox" id="<?php echo $this->get_field_id( 'hide_next' ); ?>" name="<?php echo $this->get_field_name( 'hide_next' ); ?>" value='on' <?php checked( $instance['hide_next'], 'on' ); ?>><strong>DO NOT</strong> display next upcoming show</label>
</p>