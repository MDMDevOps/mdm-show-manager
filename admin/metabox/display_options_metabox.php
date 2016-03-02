<?php
/**
 * The Metabox display
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */
?>
<div id="mdmsm_options_metabox" class="mdmsm_metabox mdmsm_container" data-showid="<?php echo $post->ID; ?>">
    <p>
        <label for="widget_description">Description to show in widget</label>
        <textarea class="widefat" name="widget_description" id="widget_description" cols="30" rows="10" placeholder="Short Description"><?php echo esc_html( $description ); ?></textarea>
    </p>
    <div class="field_wrapper add-margin-bottom">
        <label for="uri_redirect">Redirect URI</label>
        <input class="widefat" type="text" name="uri_redirect" id="uri_redirect" value="<?php echo $uri_redirect; ?>" placeholder="URI Redirect">
        <p class="description">You can output content from this show on another page using shortcode <strong><?php echo sprintf( '[mdmsm_show_content id="%d"]', $post->ID ); ?></strong></p>
    </div>

    <?php
    // Include social fields
    foreach( $social_fields as $name => $field ) {
        include plugin_dir_path( __FILE__ ) . 'display_social_options.php';
    } ?>
    <div class="explanation">
        <p>There are 2 ways to display the social profile links for a show</p>
        <ul>
            <li><strong>1: </strong>Via the shortcode <strong><?php echo sprintf( '[mdmsm_show_social post_id="%d"]', $post->ID ); ?></strong></li>
            <li><strong>2: </strong>Via the template tag <code>do_action( 'mdmsm_show_social', $post_id, $args );</code></li>
        </ul>
    </div>
</div>