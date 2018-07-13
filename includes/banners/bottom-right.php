<?php
/**
 * Sets up banner on the bottom right of site for Sitewide sale
 *
 * @package pmpro_sitewide_sale/includes/banners
 */

function pmpro_sws_show_bottom_right_banner() {
	$options              = pmprosws_get_options();
	$active_sitewide_sale = $options['active_sitewide_sale_id'];
	?>
	<style>
		#pmpro_sws_banner_bottom_right {
			background: rgba(255, 255, 255, .95);
			bottom: 0;
			box-shadow: 0 2px 20px 0 rgba(0,0,0,.2);
			padding: 20px;
			position: fixed;
			right: 0;
			max-width: 300px;
			z-index: 400;
		}
		#pmpro_sws_banner_bottom_right .pmpro_btn {
			display: block;
		}
		.pmpro_sws_banner h3 {
			margin: 0 0 10px 0;
			padding: 0;
		}
		.pmpro_sws_banner p {
			margin: 0 0 10px 0;
			padding: 0;
		}

		#pmpro_sws_banner_bottom_right a.dismiss, a.dismiss:active, a.dismiss:link, a.dismiss:visited {
			color: #FFF;
			float: right;
			text-decoration: none;
		}
		#pmpro_sws_banner_bottom_right a.dismiss:hover {
			text-decoration: underline;
		}
	</style>
	<div id="pmpro_sws_banner_bottom_right" class="pmpro_sws_banner">
		<a href="javascript:void(0);" onclick="document.getElementById('pmpro_sws_banner_bottom_right').style.display = 'none';" class="dismiss">x</a>
		<h3><?php _e( get_post_meta( $active_sitewide_sale, 'banner_title', true ) ); ?></h3>
		<?php echo wpautop( get_post_meta( $active_sitewide_sale, 'banner_description', true ) ); ?>
		<a class="pmpro_btn" href="<?php echo get_permalink( get_post_meta( $active_sitewide_sale, 'landing_page_post_id', true ) ); ?>"><?php _e( get_post_meta( $active_sitewide_sale, 'link_text', true ) ); ?></a>
	</div> <!-- end pmpro_sws_banner -->
	<?php
}
add_action( 'wp_footer', 'pmpro_sws_show_bottom_right_banner' );
