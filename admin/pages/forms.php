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
		
		<?php if( isset( $_GET['page'] ) && $_GET['page'] == 'alf_custom_forms' && isset( $_GET['pmaf_entry'] ) && !empty( $_GET['pmaf_entry'] ) ) : 
			
			require_once PMAF_DIR . "admin/pages/entry.php";
			
		elseif( isset( $_GET['page'] ) && $_GET['page'] == 'alf_custom_forms' && !isset( $_GET['pmaf_new'] ) && !isset( $_GET['pmaf_login'] ) && !isset( $_GET['pmaf'] ) && !isset( $_GET['pmaf_import'] ) ) : ?>
		<div class="pmaf-header-filter">
			<ul class="pmaf-filter-list">
				<li>
					<a href="<?php echo admin_url('/admin.php?page=pmaf_new' ) ?>" class="pmaf-new-form-link pmaf-filled-btn"><i class="af-add"></i><?php esc_html_e( 'Create Form', 'animated-forms' ); ?></a>
					<span class="pmaf-separator"><?php esc_html_e( 'or', 'animated-forms' ); ?></span>
					<a href="<?php echo admin_url('/admin.php?page=pmaf_import') ?>" class="pmaf-import pmaf-filled-btn"><i class="af-download"></i><?php esc_html_e( 'Import', 'animated-forms' ); ?></a>
				</li>
				<li>							
					<?php 
						$b_operations = [ 'none' => esc_html__( 'None', 'animated-forms' ), 'bulk' => esc_html__( 'Bulk Select', 'animated-forms' ), 'select-all' => esc_html__( 'Select All', 'animated-forms' ), 'unselect-all' => esc_html__( 'Unselect All', 'animated-forms' ) ];
						$b_process = [ 'none' => esc_html__( 'None', 'animated-forms' ), 'export' => esc_html__( 'Export', 'animated-forms' ), 'delete' => esc_html__( 'Delete', 'animated-forms' ) ];
					?>
					<label><?php esc_html_e( 'Bulk Operation', 'animated-forms' ); ?></label>
					<select class="pmaf-filter-bulk-operations pmaf-editor-select">
					<?php foreach( $b_operations as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
					</select>
					<span class="pmaf-continue-arrow"><i class="af-arrow-right"></i></span>
					<select class="pmaf-filter-bulk-process pmaf-editor-select">
					<?php foreach( $b_process as $key => $label ) : ?>
						<option value="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?></option>
					<?php endforeach; ?>
					</select>
				</li>
			</ul>
		</div>
		
		<!-- Animated Forms Posts -->		
		<div class="pmaf-posts-wrap">
		<?php
			
			$pagination = isset( $_GET['p'] ) ? absint( $_GET['p'] ) : 1;
			$ppp = 10;
			$forms_counts = wp_count_posts('pmaf');
			$form_total = isset( $forms_counts->publish ) ? $forms_counts->publish : 0;
			
			$args = [
				'post_type' => 'pmaf',
				'post_status' => 'publish',
				'posts_per_page' => $ppp,
				'paged' => $pagination
			];
		
			$the_query = new WP_Query( $args );

			// The Loop.
			if ( $the_query->have_posts() ) {	
				echo '<ul class="pmaf-posts-list">';
				echo '<li><span>'. esc_html__( 'ID', 'animated-forms' ) .'</span><span>'. esc_html__( 'Form Name', 'animated-forms' ) .'</span><span>'. esc_html__( 'Shortcode', 'animated-forms' ) .'</span><span>'. esc_html__( 'Active/Inactive', 'animated-forms' ) .'</span><span>'. esc_html__( 'Author', 'animated-forms' ) .'</span><span>'. esc_html__( 'Modified Date', 'animated-forms' ) .'</span></li>';
				while( $the_query->have_posts() ) {
					$the_query->the_post();
					$post_id = get_the_ID();
					$post_title = get_the_title();
					$form_settings = pmaf_forms_data()->get_form_settings( $post_id );
					$login_form_stat = pmaf_forms_data()->is_login_form( $post_id );
					//print_r( $form_settings );
					//if( !empty( $form_settings ) ) {
						
						if( isset( $form_settings['basic'] ) && !empty( $form_settings['basic']['form_name'] ) ) {
							$form_name = $form_settings['basic']['form_name'];
						} elseif( $post_title ) {
							$form_name = $post_title;
						} else {
							$form_name = 'Form #'. $post_id;
						}
						
						$form_edit_url = 'admin.php?page=alf_custom_forms&pmaf='. esc_attr( $post_id );
						if( $login_form_stat ) $form_edit_url = 'admin.php?page=alf_custom_forms&pmaf='. esc_attr( $post_id ) .'&pmaf_login=1';
						$preview_url = add_query_arg( array( 'pmaf_form_preview' => $post_id ), home_url() ); 
						$post_status = get_post_meta( $post_id, 'pmaf_form_status', true );
						$post_status = !$post_status ? 'e' : $post_status; 
						
						echo '<li class="pmaf-single-form-row-'. esc_attr( $post_id ) .'" data-status="'. $post_status .'"><span class="pmaf-bulk-select"><input type="checkbox" name="pmaf" value="'. esc_attr( $post_id ) .'" /></span><span class="pmaf-form-id">'. esc_html( $post_id ) . '</span><span><strong><a href="' . admin_url( $form_edit_url ) . '" class="row-title" target="_blank">'. esc_html( $form_name ) .'</a></strong><ul class="pmaf-other-settings"><li><a href="' . admin_url( $form_edit_url ) . '">Edit</a></li><li><a href="' . esc_url( $preview_url ) . '" target="_blank">Preview</a></li><li><a href="#" class="pmaf-single-item-export" data-id="'. esc_attr( $post_id ) .'">Export</a></li><li><a href="#" class="pmaf-single-item-delete" data-id="'. esc_attr( $post_id ) .'">Delete</a></li></ul></span><span class="pmaf-shortcode-copy-parent"><a href="#" class="pmaf-shortcode-copy">[animated_form id="'. esc_attr( $post_id ) .'"]</a><i class="af-copy"></i></span><span><div class="pmaf-single-status-change pmaf-switch"><input type="checkbox" data-id="'. esc_attr( $post_id ) .'" class="onoffswitch-checkbox" '. ( checked( $post_status, 'e', false ) ) .'><span class="slider round"></span></div></span><span>'. get_the_author() .'</span><span><time datetime="'. get_the_date('c') .'">'. get_the_date( 'Y/m/d h:i A' ).'</time></span></li>';
					//}
					
				}
				echo '</ul>';
				echo '<div id="pmaf-forms-pagination" class="pmaf-forms-pagination" data-current="'. esc_attr( $pagination ) .'" data-total="'. esc_attr( $form_total ) .'" data-limit="'. esc_attr( $ppp ) .'"></div>';
			} else {
				echo '<div class="pmaf-empty-posts">';
				echo esc_html__( 'Sorry, no posts matched your criteria.', 'animated-forms' );
				echo ' <a href="'. admin_url('/admin.php?page=pmaf_new' ) .'" class="pmaf-new-form-link pmaf-filled-btn"><i class="af-add"></i>'. esc_html__( 'Create Form', 'animated-forms' ) .'</a>';
				echo '</div>';
			}
			// Restore original Post Data.
			wp_reset_postdata();
		?>			
		<div>
		<?php endif; ?>
		
		<?php if( isset( $_GET['page'] ) && $_GET['page'] == 'alf_custom_forms' && isset( $_GET['pmaf_import'] ) && $_GET['pmaf_import'] == 1 ) : ?>
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
			<p class="submit"><input type="submit" name="submit" id="submit" class="btn-primary button button-primary" value="<?php esc_html_e( 'Upload file and import', 'animated-forms' ); ?>" disabled=""></p>				
		</form>
		<?php endif; ?>
		
		<?php if( isset( $_GET['page'] ) && $_GET['page'] == 'alf_custom_forms' && isset( $_GET['pmaf_new'] ) && $_GET['pmaf_new'] == 1 ) : 
			require_once PMAF_DIR . 'admin/pages/form-templates/form-templates.php';
		endif; ?>
	
		<?php if( isset( $_GET['page'] ) && $_GET['page'] == 'alf_custom_forms' && isset( $_GET['pmaf'] ) && $_GET['pmaf'] != '' ) : 
			$form_id = $_GET['pmaf'];
			$login_form_stat = pmaf_forms_data()->is_login_form( $form_id );
		
			// form editor
			if( ( isset( $_GET['pmaf_login'] ) && $_GET['pmaf_login'] != '' ) || $login_form_stat ) {
				require_once PMAF_DIR . 'admin/pages/login-form-editor.php';
			} else {
				require_once PMAF_DIR . 'admin/pages/form-editor.php';
			}
			
			// import animate packs
			require_once PMAF_DIR . 'admin/pages/form-templates/pack-templates.php';
			
			// import animated templates
			require_once PMAF_DIR . 'admin/pages/form-templates/animated-templates.php';
			
			// import overlay templates
			require_once PMAF_DIR . 'admin/pages/form-templates/overlay-templates.php';
			
			// import form inner templates
			require_once PMAF_DIR . 'admin/pages/form-templates/form-inner-templates.php';
			
		endif; ?>
		
	</div> <!-- .pmaf-wrap-inner -->
	
</div> <!-- .wrap -->
<?php 
