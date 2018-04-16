<?php
/**
 * Bit4Bytest  functions.
 *
 * @package bit4bytes
 */

if ( ! function_exists( 'bit4bytes_is_woocommerce_activated' ) ) {
	/**
	 * Query WooCommerce activation
	 */
	function bit4bytes_is_woocommerce_activated() {
		return class_exists( 'WooCommerce' ) ? true : false;
	}
}


if ( ! function_exists( 'bit4bytes_cart_link' ) ) {
	/**
	 * Cart Link
	 * Displayed a link to the cart including the number of items present and the cart total
	 *
	 * @return void
	 * @since  1.0.0
	 */
	function bit4bytes_cart_link() {
		?>
        <a href="#" id="cart" title="Cart"><i class="fa fa-shopping-cart"></i> 
        <span class="cart__contents">
           
            <?php echo WC()->cart->get_cart_contents_count();?>
        </span>
        
        
        </a>
        
			<a class="cart-contents"  style="display:none;" href="<?php echo esc_url( wc_get_cart_url() ); ?>" title="<?php esc_attr_e( 'View your shopping cart', 'storefront' ); ?>">
				<span class="amount">
                   <?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?>
                </span> 
                <span class="count">
                  <?php echo wp_kses_data( sprintf( 
                                           _n( '%d item', '%d items', WC()->cart->get_cart_contents_count()
                                           , 'storefront' 
                                         )
                      , WC()->cart->get_cart_contents_count() ) );?>
                </span>
			</a>
		<?php
	}
}

/** Mini-Cart */

if ( ! function_exists( 'bit4bytes_woocommerce_mini_cart' ) ) {

    /**
     * Output the Mini-cart - used by cart widget.
     *
     * @param array $args Arguments.
     */
    function bit4bytes_woocommerce_mini_cart( $args = array() ) {

        $defaults = array(
            'list_class' => '',
        );

        $args = wp_parse_args( $args, $defaults );

        wc_get_template( 'woocommerce/cart/mini-cart.php', $args );
    }
}

if ( ! function_exists( 'bit4bytes_header_cart' ) ) {
	/**
	 * Display Header Cart
	 *
	 * @since  1.0.0
	 * @uses  storefront_is_woocommerce_activated() check if WooCommerce is activated
	 * @return void
	 */
	function bit4bytes_header_cart() {
		if ( bit4bytes_is_woocommerce_activated() ) {
			if ( is_cart() ) {
				$class = 'current-menu-item';
			} else {
				$class = '';
			}
		?>        

		<ul id="site-header-cart" class="menu__extra">
	
			<li class="<?php echo esc_attr( $class ); ?> cart__menu">
				<?php bit4bytes_cart_link(); ?>
			</li>
			<li>
                <?php bit4bytes_woocommerce_mini_cart(); ?>
			</li>
		</ul>
		<?php
		}
	}
}