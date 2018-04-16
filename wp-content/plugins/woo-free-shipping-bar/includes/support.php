<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
if ( ! class_exists( 'VillaTheme_Support' ) ) {


	class VillaTheme_Support {
		public function __construct( $data ) {
			$this->data               = array();
			$this->data['support']    = $data['support'];
			$this->data['docs']       = $data['docs'];
			$this->data['review']     = $data['review'];
			$this->data['css_url']    = $data['css'];
			$this->data['images_url'] = $data['image'];
			$this->data['slug']       = $data['slug'];
			add_action( 'villatheme_support_' . $this->data['slug'], array( $this, 'villatheme_support' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
			add_action( 'admin_notices', array( $this, 'review_notice' ) );
			add_action( 'admin_init', array( $this, 'hide_review_notice' ) );

			/*Admin notices*/
			if ( ! get_transient( 'villatheme_call' ) || get_transient( 'villatheme_call' ) == $this->data['slug'] ) {
				set_transient( 'villatheme_call', $this->data['slug'], 86400 );
				/*Hide notices*/
				add_action( 'admin_init', array( $this, 'hide_notices' ) );

				add_action( 'admin_notices', array( $this, 'form_ads' ) );

				/*Admin dashboard*/
				add_action( 'wp_dashboard_setup', array( $this, 'dashboard' ) );
			}
		}

		/**
		 * Hide notices
		 */
		public function hide_review_notice() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( ! isset( $_GET['_villatheme_nonce'] ) ) {
				return;
			}
			if ( wp_verify_nonce( $_GET['_villatheme_nonce'], $this->data['slug'] . '_hide_notices' ) ) {
				set_transient( $this->data['slug'] . '_hide_notices', 1, 604800 );
			}
		}

		/**
		 * Show review wordpress
		 */
		public function review_notice() {
			if ( get_transient( $this->data['slug'] . '_hide_notices' ) ) {
				return;
			}
			$name = str_replace( '-', ' ', $this->data['slug'] );
			$name = ucwords( $name );
			?>
			<div class="villatheme-dashboard updated" style="border-left: 4px solid #ffba00">
				<div class="villatheme-content">
					<p><?php echo esc_html__( 'Hi there! You\'ve been using ', $this->data['slug'] ) . '<strong>' . $name . '</strong>' . esc_html__( ' on your site for a few days - I hope it\'s been helpful. If you\'re enjoying my plugin, would you mind rating it 5-stars to help spread the word?', $this->data['slug'] ) ?></p>
					<p>
						<a target="_blank" href="<?php echo esc_url( $this->data['review'] ); ?>" class="button button-primary"><?php esc_html_e( 'Rate now', $this->data['slug'] ) ?></a>
						<a href="<?php echo esc_url( wp_nonce_url( add_query_arg(), $this->data['slug'] . '_hide_notices', '_villatheme_nonce' ) ); ?>" class="button"><?php esc_html_e( 'Thanks later', $this->data['slug'] ) ?></a>
					</p>
				</div>

			</div>
		<?php }

		/**
		 * Dashboard widget
		 */
		public function dashboard() {
			$hide = get_transient( 'villatheme_hide_notices' );
			if ( $hide ) {
				return;
			}
			wp_add_dashboard_widget( 'villatheme_dashboard_status', __( 'VillaTheme Offer', $this->data['slug'] ), array( $this, 'widget' ) );
		}

		public function widget() {

			$default = array(
				'heading'     => '',
				'description' => '',
				'link'        => ''
			);
			$data    = get_transient( 'villatheme_notices' );

			if ( ! $data ) {
				@$data = json_decode( file_get_contents( 'https://villatheme.com/notices.php' ), true );
				set_transient( 'villatheme_notices', $data, 86400 );
			}
			if ( ! is_array( $data ) ) {
				return;
			}
			$data = wp_parse_args( $data, $default );
			if ( ! $data['heading'] && ! $data['description'] ) {
				return;
			} ?>
			<div class="villatheme-dashboard">
				<div class="villatheme-content">
					<?php if ( $data['heading'] ) { ?>
						<div class="villatheme-left">
							<?php echo $data['heading'] ?>
						</div>
					<?php } ?>
					<div class="villatheme-right">
						<?php if ( $data['description'] ) { ?>
							<div class="villatheme-description">
								<?php echo $data['description']; ?>
							</div>
						<?php } ?>
						<div class="villatheme-notification-controls">
							<?php if ( $data['link'] ) { ?>
								<a target="_blank" href="<?php echo esc_url( $data['link'] ) ?>" class="villatheme-button villatheme-primary"><?php esc_html_e( 'View', $this->data['slug'] ) ?></a>
							<?php } ?>
							<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'villatheme-hide-notice', '1' ), 'hide_notices', '_villatheme_nonce' ) ); ?>" class="villatheme-button"><?php esc_html_e( 'Skip', $this->data['slug'] ) ?></a>
						</div>
					</div>
				</div>

			</div>

		<?php }

		/**
		 * Hide notices
		 */
		public function hide_notices() {
			if ( ! current_user_can( 'manage_options' ) ) {
				return;
			}
			if ( ! isset( $_GET['villatheme-hide-notice'] ) && ! isset( $_GET['_villatheme_nonce'] ) ) {
				return;
			}
			if ( wp_verify_nonce( $_GET['_villatheme_nonce'], 'hide_notices' ) ) {
				set_transient( 'villatheme_hide_notices', 1, 86400 );
			}
		}

		/**
		 * Show Notices
		 */
		public function form_ads() {
			$hide = get_transient( 'villatheme_hide_notices' );
			if ( $hide ) {
				return;
			}
			$default = array(
				'heading'     => '',
				'description' => '',
				'link'        => ''
			);
			$data    = get_transient( 'villatheme_notices' );

			if ( ! $data ) {
				@$data = json_decode( file_get_contents( 'https://villatheme.com/notices.php' ), true );
				set_transient( 'villatheme_notices', $data, 86400 );
			}
			if ( ! is_array( $data ) ) {
				return;
			}
			$data = wp_parse_args( $data, $default );
			if ( ! $data['heading'] && ! $data['description'] ) {
				return;
			}
			ob_start(); ?>
			<div class="villatheme-notification-wrapper notice">
				<div class="villatheme-content">
					<?php if ( $data['heading'] ) { ?>
						<div class="villatheme-left">
							<?php echo $data['heading'] ?>
						</div>
					<?php } ?>
					<div class="villatheme-right">
						<?php if ( $data['description'] ) { ?>
							<div class="villatheme-description">
								<?php echo $data['description']; ?>
							</div>
						<?php } ?>
						<div class="villatheme-notification-controls">
							<?php if ( $data['link'] ) { ?>
								<a target="_blank" href="<?php echo esc_url( $data['link'] ) ?>" class="villatheme-button villatheme-primary"><?php esc_html_e( 'View', $this->data['slug'] ) ?></a>
							<?php } ?>
							<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( 'villatheme-hide-notice', '1' ), 'hide_notices', '_villatheme_nonce' ) ); ?>" class="villatheme-button"><?php esc_html_e( 'Skip', $this->data['slug'] ) ?></a>
						</div>
					</div>
				</div>

			</div>
			<?php $html = ob_get_clean();
			$html       = apply_filters( 'form_ads_data', $html );
			echo $html;
		}

		/**
		 * Init script
		 */
		public function scripts() {
			wp_enqueue_style( 'villatheme-support', $this->data['css_url'] . 'villatheme-support.css' );
		}

		/**
		 *
		 */
		public function villatheme_support() { ?>

			<div id="villatheme-support" class="vi-ui form segment">

				<div class="fields">
					<div class="four wide field ">
						<h3><?php echo esc_html__( 'HELP CENTER', $this->data['slug'] ) ?></h3>
						<div class="villatheme-support-area">
							<a target="_blank" href="<?php echo esc_url( $this->data['support'] ) ?>">
								<img src="<?php echo $this->data['images_url'] . 'support.jpg' ?>">
							</a>
						</div>
						<div class="villatheme-docs-area">
							<a target="_blank" href="<?php echo esc_url( $this->data['docs'] ) ?>">
								<img src="<?php echo $this->data['images_url'] . 'docs.jpg' ?>">
							</a>
						</div>
						<div class="villatheme-review-area">
							<a target="_blank" href="<?php echo esc_url( $this->data['review'] ) ?>">
								<img src="<?php echo $this->data['images_url'] . 'reviews.jpg' ?>">
							</a>
						</div>
					</div>
					<?php $items = $this->get_data( $this->data['slug'] );
					if ( count( $items ) && is_array( $items ) ) {
						shuffle( $items );
						$items = array_slice( $items, 0, 2 );
						foreach ( $items as $k => $item ) { ?>
							<div class="six wide field">
								<?php if ( $k == 0 ) { ?>
									<h3><?php echo esc_html__( 'MAYBE YOU LIKE', $this->data['slug'] ) ?></h3>
								<?php } else { ?>
									<h3>&nbsp;</h3>
								<?php } ?>
								<div class="villatheme-item">
									<a target="_blank" href="<?php echo esc_url( $item->link ) ?>">
										<img src="<?php echo esc_url( $item->image ) ?>" />
									</a>
								</div>
							</div>
						<?php }
						?>

					<?php } ?>
				</div>

			</div>
		<?php }

		/**
		 * Get data from server
		 * @return array
		 */
		protected function get_data( $slug = false ) {
			$feeds = get_transient( 'villatheme_ads' );
			if ( ! $feeds ) {
				@$ads = file_get_contents( 'https://villatheme.com/popular.php' );
				set_transient( 'villatheme_ads', $ads, 86400 );
			} else {
				$ads = $feeds;
			}
			if ( $ads ) {
				$ads = json_decode( $ads );
				$ads = array_filter( $ads );
			} else {
				return false;
			}
			if ( count( $ads ) ) {
				$theme_select = null;
				foreach ( $ads as $ad ) {
					if ( $slug ) {
						if ( $ad->slug == $slug ) {
							continue;
						}
					}
					$item        = new stdClass();
					$item->title = $ad->title;
					$item->link  = $ad->link;
					$item->thumb = $ad->thumb;
					$item->image = $ad->image;
					$item->desc  = $ad->description;
					$results[]   = $item;
				}
			} else {
				return false;
			}
			if ( count( $results ) ) {
				return $results;
			} else {
				return false;
			}
		}
	}
}
new VillaTheme_Support(
	array(
		'support' => 'https://wordpress.org/support/plugin/woo-free-shipping-bar',
		'docs'    => 'http://docs.villatheme.com/?item=woocommerce-free-shipping-bar',
		'review'  => 'https://wordpress.org/support/plugin/woo-free-shipping-bar/reviews/?rate=5#rate-response',
		'css'     => WFSPB_F_SHIPPING_CSS,
		'image'   => WFSPB_F_SHIPPING_IMAGES,
		'slug'    => 'woo-free-shipping-bar'
	)
);