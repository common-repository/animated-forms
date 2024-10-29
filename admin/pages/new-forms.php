<?php
/*
 * Dashboard
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

?>
	
<div class="wrap">

	<h1 class="hidden"><?php esc_html_e( 'Animated Forms', 'animated-forms' ); ?></h1>
	
	<?php do_action( 'pmaf_page_top' ); ?>
	
	<div class="pmaf-wrap-inner">

		<?php require_once PMAF_DIR . "admin/pages/admin-header.php"; ?>
		
		<!-- Animated Forms Main Page -->
		
		<?php require_once PMAF_DIR . 'admin/pages/form-templates/form-templates.php'; ?>
		
	</div> <!-- .pmaf-wrap-inner -->
	
</div> <!-- .wrap -->
<?php 
