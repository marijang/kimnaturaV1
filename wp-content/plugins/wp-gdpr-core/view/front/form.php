<?php
/**
 *  FORM to send request for access to data about user
 */
?>
<?php if ( 'GET' == $_SERVER['REQUEST_METHOD'] && ! isset( $_REQUEST['thank_you'] ) ) : ?>
    <form action="" method="post">
		<?php _e( 'Email', 'wp_gdpr' ); ?>:<br>
        <input type="email" name="email" value="" required>
        <br><br>
        <input type="checkbox" name="checkbox_gdpr" required>
        <label for="checkbox_gdpr">
			<?php echo wp_unslash( $privacy_policy_strings[3] ); ?>
        </label>
        <br><br>
        <input type="hidden" name="gdpr_translation" value="<?php echo $pieces; ?>">
        <input type="submit" name="gdpr_req" value="<?php _e( 'Submit', 'wp_gdpr' ); ?>">
    </form>
<?php else: ?>
    <h3><?php _e( 'Thank You! We will send you an email with a link to access your personal data.', 'wp_gdpr' ); ?></h3>
    <p>
        <b><?php _e( 'Warning:', 'wp_gdpr' ); ?></b> <?php _e( 'This link will become deprecated after 48 hours.', 'wp_gdpr' ); ?>
    </p>
<?php endif; ?>
