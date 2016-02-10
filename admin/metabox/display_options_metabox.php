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
    <textarea class="widefat" name="widget_description" id="widget_description" cols="30" rows="10" placeholder="Short Description"><?php echo $description; ?></textarea>
    </p>

    <label for="uri_redirect">Redirect URI</label>
    <input class="widefat" type="text" name="uri_redirect" id="uri_redirect" value="<?php echo $uri; ?>" placeholder="URI Redirect">
    <p class="description">URI to link to from the widget, will <strong>NOT</strong> redirect traffic to show URI</p>
</div>