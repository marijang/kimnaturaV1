<?php
/**
 * Single Product Up-Sells
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/up-sells.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}




   echo "TESTIRANJE";

   //print_r($product->get_tag_ids());

   /* Get the product tag */
	$terms = get_the_terms( the_ID(), 'product_tag' );

	$aromacheck = array();
	if ( ! empty( $terms ) && ! is_wp_error( $terms ) ){
		foreach ( $terms as $term ) {
			$aromacheck[] = $term->slug;
			//print_r($term);
			echo "naziv:".$term->image;
			//if (function_exists(‘get_wp_term_image’)){
			  $meta_image = get_wp_term_image($term->id);
			  echo "Meta:".$meta_image; // category/term image url 
			//}
		}
	}
	//print_r($aromacheck);

 

	if (function_exists('get_wp_term_image'))
	{
	$meta_image = get_wp_term_image(21);
	//It will give category/term image url
	}
	
	echo $meta_image; // category/term image url




if ( $upsells ) : ?>

	<section class="up-sells upsells products">

		<h2><?php esc_html_e( 'You may also like&hellip;', 'woocommerce' ) ?></h2>

		<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $upsells as $upsell ) : ?>

				<?php
				 	$post_object = get_post( $upsell->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object );

					wc_get_template_part( 'content', 'product' ); ?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>

<?php endif;




wp_reset_postdata();
