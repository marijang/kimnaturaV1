<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width"/>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
    <link rel="stylesheet" type="text/css" href="<?php echo GDPR_URL . 'assets/css/frontend-styling.css' ?>">
    <title><?php _e( 'View Comments', 'wp_gdpr' ); ?></title>
	<?php wp_head(); ?>
</head>
<body>
<div class="gdpr-container">
	<?php echo $controller->message; ?>
	<?php use wp_gdpr\lib\Gdpr_Options_Helper; ?>
    <div id="gdpr-message-updated-entry" class="js-update-message"></div>
	<?php if ( Gdpr_Options_Helper::is_option_off( 'switch_on_comments' ) ): ?>
        <h2><?php _e( 'All comments added by email address', 'wp_gdpr' ); ?>
             <?php echo $controller->email_request; ?></h2>
		<?php
		/**
		 * create table with comments
		 */
		$controller->create_table_with_comments();
	endif;
	/**
	 * do action for addon
	 */
	do_action( 'gdpr_show_entries', $controller->email_request );
	?>
</div>
</body>
<?php wp_footer(); ?>
</html>





