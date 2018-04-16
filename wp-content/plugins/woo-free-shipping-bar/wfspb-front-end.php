<?php
/**
 * Created by PhpStorm.
 * User: huyko
 * Date: 02/08/2017
 * Time: 8:26 AM
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class WFSPB_F_FrontEnd {
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script_fontend' ), 899999 );
		add_action( 'wp_footer', array( $this, 'show_bar_conditional' ), 90 );

	}

	public function enqueue_script_fontend() {
		$params               = new WFSPB_F_Shipping();
		$enable               = $params->get_field( 'enable' );
		if ( $enable && $params->get_woo_shipping_zone() ) {

			wp_enqueue_style( 'woo-free-shipping-bar', plugins_url( 'woo-free-shipping-bar/assets/css/woo-free-shipping-bar-frontend-style.css', 'woo-free-shipping-bar' ) );
			wp_enqueue_script( 'woo-free-shipping-bar', plugins_url( 'woo-free-shipping-bar/assets/js/woo-free-shipping-bar-frontend.js' ), array( 'jquery' ), VI_WFREESHIPPINGBAR_VERSION, true );

			$params          = new WFSPB_F_Shipping();
			$bg_color        = $params->get_field( 'bg-color' );
			$text_color      = $params->get_field( 'text-color' );
			$link_color      = $params->get_field( 'link-color' );
			$text_align      = $params->get_field( 'text-align' );
			$font            = $params->get_field( 'font' );
			$font_size       = $params->get_field( 'font-size' );
			$enable_progress = $params->get_field( 'enable-progress' );
			$style           = $params->get_field( 'style' );

			if ( ! empty( $font ) ) {
				$font = str_replace( '+', ' ', $font );
				wp_enqueue_style( 'google-font-' . strtolower( $font ), '//fonts.googleapis.com/css?family=' . $font . ':400,700' );
			}

			switch ( $style ) {
				case 2:
					wp_enqueue_style( 'woo-free-shipping-bar-style2', plugins_url( 'woo-free-shipping-bar/assets/css/style-progress/style2.css', 'woo-free-shipping-bar' ) );
					$css_style2 = "
						#wfspb-top-bar #wfspb-progress::before, #wfspb-top-bar #wfspb-progress::after{
							border-bottom-color: {$bg_color} !important;
						}
					";
					wp_add_inline_style( 'woo-free-shipping-bar-style2', $css_style2 );
					break;
				case 3:
					wp_enqueue_style( 'woo-free-shipping-bar-style3', plugins_url( 'woo-free-shipping-bar/assets/css/style-progress/style3.css', 'woo-free-shipping-bar' ) );
					break;
				default :
					wp_enqueue_style( 'woo-free-shipping-bar-style', plugins_url( 'woo-free-shipping-bar/assets/css/style-progress/style.css', 'woo-free-shipping-bar' ) );
					break;
			}

			$custom_css = "
				#wfspb-top-bar{
					background-color: {$bg_color} !important;
					color: {$text_color} !important;
					font-family: {$font} !important;
				} 
				#wfspb-top-bar{
					text-align: {$text_align} !important;
				}
				#wfspb-top-bar > #wfspb-main-content{
					padding: 0 " . ( $font_size * 2 ) . "px;
					font-size: {$font_size}px !important;
					text-align: {$text_align} !important;
					color: {$text_color} !important;
				}
				#wfspb-top-bar > #wfspb-main-content > a{
					color: {$link_color} !important;
				}
				div#wfspb-close{
				font-size: {$font_size}px !important;
				line-height: {$font_size}px !important;
				}
				";

			if ( $enable_progress ) {
				$detect_ip           = $params->get_field( 'detect-ip' );
				$default_zone        = $params->get_field( 'default-zone' );

				if ( $detect_ip && $params->detect_ip() ) {
					$min_amount = $params->toInt( $params->detect_ip() );
				} else {
					$min_amount = $params->toInt( $params->get_min_amount( $default_zone ) );
				}

				if ( ! WC()->cart->prices_include_tax ) {
					$current_amount = WC()->cart->cart_contents_total;
				} else {
					$current_amount = WC()->cart->cart_contents_total + WC()->cart->tax_total;
				}
				if ( $current_amount >= $min_amount ) {
					$custom_css .= "
							#wfspb-current-progress{ width: 100%; }
						";
				} else {
					if ( $min_amount == 0 ) {
						$amount_total_pr = $current_amount * 100;
					} else {
						$amount_total_pr = round( ( $current_amount * 100 ) / $min_amount, 2 );
					}
					$custom_css .= "
						#wfspb-current-progress{
							width: {$amount_total_pr}%;
						}";
				}
			}
			$css = $params->get_field( 'default-zone' );

			wp_add_inline_style( 'woo-free-shipping-bar', $custom_css . $css );
			// Localize the script with new data
			$translation_array = array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			);
			wp_localize_script( 'woo-free-shipping-bar', '_wfsb_params', $translation_array );


		}

	}

	public function show_bar_conditional() {
		$params = new WFSPB_F_Shipping();
		if ( ! is_admin() ) {
			$enable = $params->get_field( 'enable' );
			if ( $enable && $params->get_woo_shipping_zone() ) {
				echo $this->show_bar();
			}

		}
	}

	public function show_bar() {
		$params            = new WFSPB_F_Shipping();
		$message_purchased = $params->get_field( 'message-purchased' );
		$message_success   = $params->get_field( 'message-success' );
		$message_error     = $params->get_field( 'message-error' );
		$default_zone      = $params->get_field( 'default-zone' );
		$detect_ip         = $params->get_field( 'detect-ip' );
		$announce_system   = $params->get_field( 'announce-system' );
		$position          = $params->get_field( 'position' );
		$enable_progress   = $params->get_field( 'enable-progress' );


		/*Time display bar*/

		$show_giftbox       = $params->get_field( 'show-giftbox' );

		if ( $position == 0 ) {
			$class_pos = 'top_bar';
		} else {
			$class_pos = 'bottom_bar';
		}

		$announce_min_amount = '{min_amount}';

		$key          = array(
			'{total_amounts}',
			'{cart_amount}',
			'{min_amount}',
			'{missing_amount}'
		);
		$key_msgerror = array(
			'{missing_amount}',
			'{shopping}'
		);

		$shopping = '<a class="button" href="' . get_permalink( get_option( 'woocommerce_shop_page_id' ) ) . '">' . __( 'Shopping', 'woo-free-shipping-bar' ) . '</a>';
		$checkout = '<a href="' . wc_get_checkout_url() . '" title="' . esc_html__( 'Checkout', 'woo-free-shipping-bar' ) . '">' . esc_html__( 'Checkout', 'woo-free-shipping-bar' ) . '</a>';

		$message_ss = str_replace( '{checkout_page}', $checkout, '<div id="wfspb-main-content">' . strip_tags( $message_success ) . '</div>' );

		// get value current total cart
		if ( ! WC()->cart->prices_include_tax ) {
			$amount_current = WC()->cart->cart_contents_total;
		} else {
			$amount_current = WC()->cart->cart_contents_total + WC()->cart->tax_total;
		}

		$cart_amount = WC()->cart->cart_contents_count;

		$params->check_wooversion();
		// get minimum order amount of free shipping method
		$chosen_methods         = WC()->session->get( 'chosen_shipping_methods' );
		$option_value           = str_replace( ':', '_', $chosen_methods[0] );
		$option_value           = 'woocommerce_' . $option_value . '_settings';
		$free_shipping_settings = get_option( $option_value );

		if ( $detect_ip && $params->detect_ip() ) {
			$order_min_amount = $params->detect_ip();
		} elseif ( $free_shipping_settings['title'] == 'Free Shipping' ) {
			$order_min_amount = $free_shipping_settings['min_amount'];
		} else {
			$order_min_amount = $params->get_min_amount( $default_zone );
		}

		if ( is_checkout() ) {
			if ( $amount_current < $order_min_amount ) {
				$missing_amount    = $order_min_amount - $amount_current;
				$msgerror_replaced = array( wc_price( $missing_amount ), $shopping );
				$message           = str_replace( $key_msgerror, $msgerror_replaced, '<div id="wfspb-main-content">' . strip_tags( $message_error ) . '</div>' );
			} else {
				$message = $message_ss;
			}
		} else {
			if ( $amount_current < $order_min_amount ) {
				$total_amount    = '<b id="current_amout">' . wc_price( $amount_current ) . '</b>';
				$cart_amount1    = '<b id="current_amout">' . $cart_amount . '</b>';
				$order_mins      = '<b id="wfspb_min_order_amount">' . wc_price( $order_min_amount ) . '</b>';
				$missing_amount  = $order_min_amount - $amount_current;
				$missing_amount1 = '<b id="wfspb_missing_amount">' . wc_price( $missing_amount ) . '</b>';
				$replaced        = array(
					$total_amount,
					$cart_amount1,
					$order_mins,
					$missing_amount1
				);
				$message         = str_replace( $key, $replaced, '<div id="wfspb-main-content">' . strip_tags( $message_purchased ) . '</div>' );
			} else {
				$message = $message_ss;
			}
		}
		if ( $amount_current ) {
			$class_pos .= ' has_items';
		}
		ob_start(); ?>

		<div id="wfspb-top-bar"  class="displaying customized <?php echo esc_attr( $class_pos ) ?>" style="<?php if ( ! is_checkout() ) {
			echo 'display: none;';
		} ?>">
			<?php
			if ( $amount_current == 0 ) {
				echo $message = str_replace( $announce_min_amount, wc_price( $order_min_amount ), '<div id="wfspb-main-content">' . strip_tags( $announce_system ) . '</div>' );
			} else {
				echo $message;
			}

			if ( $enable_progress ) {
				if ( $order_min_amount == 0 ) {
					$current_percent = $amount_current * 100;
				} else {
					$current_percent = ( $amount_current * 100 ) / $order_min_amount;
				}
				$class = array();
				if ( ! $amount_current || $current_percent >= 100 ) {
					$class[] = 'hidden';
				}


				?>
				<div id="wfspb-progress" class="<?php echo esc_attr( implode( ' ', $class ) ) ?>">
					<div id="wfspb-current-progress">
						<div id="wfspb-label"><?php echo round( $current_percent, 0 ); ?>%</div>
					</div>
				</div>
				<?php
			}

				echo '<div class="" id="wfspb-close"></div>';
			?>
		</div>
		<?php
		$class = '';

		if ( $show_giftbox == 0 ) {
			return ob_get_clean();
		} ?>
		<div class="wfspb-gift-box <?php echo esc_attr( $class ); ?>" data-display="<?php echo esc_attr( $show_giftbox ); ?>">
			<img src="<?php echo esc_url( WFSPB_F_SHIPPING_IMAGES . 'free-delivery.png' ) ?>" />
		</div>

		<?php return ob_get_clean();
	}
}

new WFSPB_F_FrontEnd();