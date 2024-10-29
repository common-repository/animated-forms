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
		<form enctype="multipart/form-data" class="pmaf-campaign-import-form" id="import-upload-form" method="post" class="wp-upload-form" name="camps_import">
			<h2 class="upload-header mb-20"><?php esc_html_e( 'Upload Campaign Exported XML File', 'animated-forms' ); ?></h2>
			<p class="description"><?php esc_html_e( 'Choose .json file to upload, then click "Upload file and import".', 'animated-forms' ); ?></p>
			<fieldset>
				<p>
					<label for="upload"><?php esc_html_e( 'Choose files from your computer:', 'animated-forms' ); ?></label>
					<?php esc_html_e( '(Maximum size: 40 MB)', 'animated-forms' ); ?>
					<input type="file" id="pmaf-import-file" name="pmaf_import_files[]" size="25" accept="application/json,.json" multiple="multiple">
					<input type="hidden" name="action" value="pmaf_import_form">
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'pmaf-import-&*^%#$#' ); ?>">
					<input type="hidden" name="max_file_size" value="41943040">
				</p>
			</fieldset>
			<p class="submit"><input type="submit" name="submit" id="submit" class="btn-primary button button-primary" value="<?php esc_html_e( 'Upload file and import', 'animated-forms' ); ?>" disabled=""><span class="dashicons dashicons-update"></span></p>				
		</form>
		
	</div> <!-- .pmaf-wrap-inner -->
	
</div> <!-- .wrap -->
<?php 
