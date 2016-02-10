<?php
/**
 * The markup to display the settings page
 * @author  Mid-West Family Marketing <author@email.com>
 * @link    http://midwestfamilymarketing.com
 * @since   1.0.0
 * @package mdm_show_manager
 */
?>
<div class="wrap <?php echo sprintf( '%s%s', $this->plugin_name, '_settings' ); ?>">
    <form method="post" action="options.php">
        <?php wp_nonce_field( 'update-options' ); ?>
        <?php settings_fields( $this->settings_key ); ?>
        <?php do_settings_sections( $this->settings_key ); ?>
        <?php submit_button(); ?>
    </form>
</div>