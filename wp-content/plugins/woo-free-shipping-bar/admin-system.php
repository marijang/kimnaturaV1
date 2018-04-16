<?php
/**
 * Created by PhpStorm.
 * User: huyko
 * Date: 02/21/2017
 * Time: 8:48 PM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class WFSPB_F_Admin_System {
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'menu_page' ),999 );
		/*Update Pro Version*/
		add_action( 'admin_bar_menu', array( $this, 'admin_bar_menu' ), 100 );
	}
	/**
	 * Add Menu upgrade
	 */
	function admin_bar_menu() {
		global $wp_admin_bar;
		/* Add the main siteadmin menu item */
		$counter = sprintf( ' <div class="wp-core-ui wp-ui-notification woo-multi-currency-issue-counter"><span aria-hidden="true">%d</span><span class="screen-reader-text">%s</span></div>', 1, esc_html( 'WC Free Shipping Bar', 'woo-multi-currency' ) );
		$wp_admin_bar->add_menu( array(
			'id'    => 'upgrade-villatheme',
			'title' => esc_html__( 'Upgrade', 'woo-free-shipping-bar' ) . $counter,
			'meta'  => array( 'tabindex' => false ),
			'href'  => 'https://goo.gl/W6bfKC'
		) );
		$wp_admin_bar->add_menu( array(
			'parent' => 'upgrade-villatheme',
			'id'     => 'woo-free-shipping-bar',
			'title'  => esc_html__( 'Upgrade WC Free Shipping Bar', 'woo-free-shipping-bar' ),
			'href'   => 'https://goo.gl/W6bfKC',
			'meta'   => array( 'class' => 'villatheme-notice' )
		) );
	}

	/**
	 * System status page
	 */
	public function page_callback() { ?>
		<div class="wrap">
			<h2><?php esc_html_e( 'System Status', 'woo-free-shipping-bar' ) ?></h2>
			<table cellspacing="0" id="status" class="widefat">
				<tbody>
				<tr>
					<td data-export-label="<?php esc_html_e( 'PHP Time Limit', 'woo-free-shipping-bar' ) ?>"><?php esc_html_e( 'PHP Time Limit', 'woo-free-shipping-bar' ) ?></td>
					<td><?php echo ini_get( 'max_execution_time' ); ?></td>
				</tr>
				<tr>
					<td data-export-label="<?php esc_html_e( 'PHP Max Input Vars', 'woo-free-shipping-bar' ) ?>"><?php esc_html_e( 'PHP Max Input Vars', 'woo-free-shipping-bar' ) ?></td>

					<td><?php echo ini_get( 'max_input_vars' ); ?></td>
				</tr>
				<tr>
					<td data-export-label="<?php esc_html_e( 'Memory Limit', 'woo-free-shipping-bar' ) ?>"><?php esc_html_e( 'Memory Limit', 'woo-free-shipping-bar' ) ?></td>

					<td><?php echo ini_get( 'memory_limit' ); ?></td>
				</tr>
				</tbody>
			</table>
		</div>
	<?php }

	function menu_page() {
		add_submenu_page(
			'woo-free-shipping-bar',
			esc_html__( 'System Status', 'woo-free-shipping-bar' ),
			esc_html__( 'System Status', 'woo-free-shipping-bar' ),
			'manage_options',
			'woo-free-shipping-bar-status',
			array( $this, 'page_callback' )
		);
	}
}

new WFSPB_F_Admin_System();