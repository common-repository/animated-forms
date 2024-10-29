<?php
/*
 * Dashboard
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

?>

		<div class="pmaf-admin-header">
			<div class="pmaf-flex-left pmaf-logo-wrap">
				<img src="<?php echo esc_url( PMAF_URL . 'admin/assets/images/logo.png' ); ?>">
				<h1><?php esc_html_e( 'Animated Forms', 'animated-forms' ); ?></h1>
			</div>			
			<div class="pmaf-flex-right pmaf-help-btns">
				<a href="<?php echo esc_url( 'https://plugin.net/help-centre/' ); ?>" target="_blank" class="pmaf-help-centre pmaf-filled-btn"><i class="af-chat"></i><?php esc_html_e( 'Help Centre', 'animated-forms' ); ?></a>
				<a href="<?php echo esc_url( PMAF_PRO_LINK ); ?>" class="pmaf-upgrade-pro pmaf-filled-btn" target="_blank"><i class="af-pro"></i><?php esc_html_e( 'Upgrade to Pro', 'animated-forms' ); ?></a>
			</div>
		</div>

<?php 
