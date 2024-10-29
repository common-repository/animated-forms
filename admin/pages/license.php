<?php
/*
 * Dashboard
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;
?>
	
<div class="wrap">

	<h1 class="hidden"><?php esc_html_e( 'Animated Forms Dashboard', 'animated-forms' ); ?></h1>
	
	<div class="pmaf-wrap-inner">

		<div class="pmaf-media pmaf-logo-wrap">
			<img src="<?php echo esc_url( PMAF_URL . 'admin/assets/images/logo.png' ); ?>">
			<h1><?php esc_html_e( 'Animated Forms License Active/Deactive', 'animated-forms' ); ?></h1>
		</div>
		
		<div class="pmaf-wrap-inner">
		
			<div class="pmaf-container">
			<?php echo do_shortcode( '[plugin_market_license item_id="1048"]' ); ?>
			</div>
			
		</div>
	
	</div>
	
</div>