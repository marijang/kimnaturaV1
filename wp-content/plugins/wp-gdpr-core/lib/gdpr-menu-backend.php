<?php

namespace wp_gdpr\lib;

/**
 * lib element to add menu page
 */
class Gdpr_Menu_Backend {
	const MENU_PAGE_TITLE = 'WP GDPR';

	const PAGE_SLUG = 'wp_gdpr';

	const MENU_TITLE = 'WP GDPR';

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_menu_callback' ) );
	}

	/**
	 * add menu page
	 */
	public function add_menu_callback() {
		add_menu_page( self::MENU_PAGE_TITLE, self::MENU_TITLE, 'manage_options', self::PAGE_SLUG, '', GDPR_URL . 'assets/images/wp-gdpr.svg' );

		add_submenu_page( static::PAGE_SLUG, __( 'Requests', 'wp_gdpr' ), __( 'Requests', 'wp_gdpr' ), 'manage_options', self::PAGE_SLUG, array(
			$this,
			'set_wp_gdpr_listOfDataRequests'
		) );

//	    Not for v1.5!!!    add_submenu_page(static::PAGE_SLUG, __('Dataregister', 'wp_gdpr'), __('Dataregister', 'wp_gdpr'), 'manage_options', 'datareg', array($this, 'set_wp_dataregister'));

		add_submenu_page( static::PAGE_SLUG, __( 'Add-ons', 'wp_gdpr' ), __( 'Add-ons', 'wp_gdpr' ), 'manage_options', 'addon', array(
			$this,
			'set_wp_addon'
		) );

		add_submenu_page( static::PAGE_SLUG, __( 'Settings', 'wp_gdpr' ), __( 'Settings', 'wp_gdpr' ), 'manage_options', 'settings_wp-gdpr', array(
			$this,
			'set_wp_gdpr_settings'
		) );

		add_submenu_page( static::PAGE_SLUG, __( 'Help Center', 'wp_gdpr' ), __( 'Help Center', 'wp_gdpr' ), 'manage_options', 'help', array(
			$this,
			'set_wp_gdpr_help'
		) );


	}

	/**
	 * generate output for menu page from template
	 *
	 * @since v1.5      some menu's are merged
	 */
	public function set_wp_gdpr_listOfDataRequests() {
		if ( isset( $_GET['page_type'] ) && $_GET['page_type'] == 'datarequest' ) {
			require_once GDPR_DIR . 'view/admin/menu/data-page.php';
		} else {
			require_once GDPR_DIR . 'view/admin/menu/delete-page.php';
		}
	}

	public function set_wp_addon() {
		if ( isset( $_GET['page_type'] ) && $_GET['page_type'] == 'addonlist' ) {
			require_once GDPR_DIR . 'view/admin/menu/plugin-page.php';
		} else {
			require_once GDPR_DIR . 'view/admin/menu/addon_page.php';
		}
	}

	public function set_wp_gdpr_settings() {
		require_once GDPR_DIR . 'view/admin/menu/settings-page.php';
	}

	public function set_wp_gdpr_help() {
		require_once GDPR_DIR . 'view/admin/menu/help_page.php';
	}

//	   Not for v1.5!!!      public function set_wp_dataregister() {
//		require_once GDPR_DIR . 'view/admin/menu/dataregister.php';
//	}
}
