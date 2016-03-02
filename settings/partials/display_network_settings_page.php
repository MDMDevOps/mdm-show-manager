<?php
/**
 * The markup to display the settings page
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */
?>
<?php if (isset($_GET['updated'])): ?>
    <div id="message" class="updated notice is-dismissible"><p><?php _e('Options saved.') ?></p></div>
<?php endif; ?>

<div id="mdmsm_network_settings" class="wrap">
    <form method="post" action="edit.php?action=mdmsm_update_network_options">
        <?php wp_nonce_field( 'update-options' ); ?>
        <?php settings_fields( 'mdmsm_network_settings' ); ?>
        <?php do_settings_sections( 'mdmsm_network_settings' ); ?>
        <?php submit_button(); ?>
    </form>
</div>