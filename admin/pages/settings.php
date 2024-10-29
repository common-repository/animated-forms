<?php 

/*
 * Plugin Options
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
// Framework file connection
require_once PMAF_DIR ."/admin/plugin-options/framework.php";
PMAF_Animated_Forms_Options::$pmaf_alf_options = PMAF_Animated_Forms_Init::$pmaf_alf_options;

?>

<div class="wrap">

	<h1 class="animated-forms-hidden hidden"><?php esc_html_e( 'Animated Forms Settings', 'animated-forms' ); ?></h1>
	
	<div class="pmaf-wrap-inner">
	
		<div class="pmaf-media pmaf-logo-wrap">
			<img src="<?php echo esc_url( PMAF_URL . 'admin/assets/images/logo.png' ); ?>">
			<h1><?php esc_html_e( 'Animated Forms Settings', 'animated-forms' ); ?></h1>
		</div>
		
		<div class="animated-forms-settings-wrap">
			<form id="animated-forms-settings-form" method="POST">
				
				<input type="hidden" name="action" value="animated_login_forms_save_options" />
				
				<?php wp_nonce_field( 'animated-forms-save-options&^%$$', 'pmaf_alf_options_nonce' ); ?>
				
				<?php 
					require_once PMAF_DIR ."admin/plugin-options/config.php";
				?>
				
				<div class="animated-forms-admin-content-wrap">	
					<div class="pmaf-tab-wrap">
						<ul class="pmaf-tab-list">
						<?php  echo PMAF_Animated_Forms_Options::$tab_list; ?>
						</ul>
						<div class="pmaf-tab">
							<?php PMAF_Animated_Forms_Options::pmaf_put_field(); ?>
						</div><!-- .pmaf-tab-contents -->
					</div>
				</div>
				
				<button type="submit" class="button wp-button pmaf-btn"><span class="dashicons dashicons-admin-generic"></span><?php esc_html_e( 'Save Settings', 'animated-forms' ); ?></button>
				
			</form>
		</div> <!-- .animated-forms-settings-wrap -->
		
	</div>
		
</div> <!-- .wrap -->
<?php 