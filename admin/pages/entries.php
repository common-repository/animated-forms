<?php
/*
 * Dashboard - Entries
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

?>
	
<div class="wrap">

	<h1 class="hidden"><?php esc_html_e( 'Animated Forms Entries', 'animated-forms' ); ?></h1>
	
	<?php do_action( 'pmaf_page_top' ); ?>
	
	<div class="pmaf-wrap-inner">
	
		<?php require_once PMAF_DIR . "admin/pages/admin-header.php"; ?>
	
		<div class="pmaf-entries-header">
			<div class="pmaf-left-part">
				<div class="pmaf-entries-forms">
					<strong><?php esc_html_e( 'Choose Form: ', 'animated-forms' ); ?></strong>
					<?php
						require_once PMAF_DIR . "admin/class.admin-entries.php";
						$forms = pmaf_animated_admin_entries()->get_form_titles();
						if( !empty( $forms ) ) {
							echo '<select class="pmaf-select pmaf-entries-select2 pmaf-form-titles" placeholder="Choose form">';
							echo '<option value="">'. esc_html__( 'Choose Form', 'animated-forms' ) .'</option>';
							foreach( $forms as $form ) {
								echo '<option value="'. esc_attr( $form->ID ) .'">'. esc_html( $form->post_title ) .'</option>';
							}
							echo '</select>';
						}
					?>
				</div>
				<div class="pmaf-entries-filter-wrap">
					<strong><?php esc_html_e( 'Read Status: ', 'animated-forms' ); ?></strong>
					<select class="pmaf-select pmaf-entries-filter">
						<option value="all"><?php esc_html_e( 'All', 'animated-forms' ); ?></option>
						<option value="unread"><?php esc_html_e( 'Unread Only', 'animated-forms' ); ?></option>
						<option value="read"><?php esc_html_e( 'Read Only', 'animated-forms' ); ?></option>
					</select>
				</div>
				<div class="pmaf-entries-fav-wrap">
					<strong><?php esc_html_e( 'Favourites: ', 'animated-forms' ); ?></strong>
					<select class="pmaf-select pmaf-entries-fav">
						<option value="all"><?php esc_html_e( 'All', 'animated-forms' ); ?></option>
						<option value="fav"><?php esc_html_e( 'Favourites', 'animated-forms' ); ?></option>
						<option value="non-fav"><?php esc_html_e( 'Non-favourites', 'animated-forms' ); ?></option>
					</select>
					<a href="#" class="pmaf-entries-filter-reset pmaf-filled-btn"><?php esc_html_e( 'Reset', 'animated-forms' ); ?></a>
				</div>
			</div>
		</div>
		
		<div class="pmaf-entries-wrap">
		<?php
			$entries = pmaf_animated_admin_entries()->get_entries_table( 50 );
			require_once PMAF_DIR . 'inc/class.entries.php';
			if( !empty( $entries ) ) {
				echo '<div class="pmaf-all-entries">';
					pmaf_entries()->entries_list( $entries );
				echo '</div>';
				echo '<div id="pmaf-entries-pagination" class="pmaf-entries-pagination"></div>';
			}else {			
				pmaf_entries()->no_entries_html();
			}
		?>
			
		</div>
		
	</div> <!-- .pmaf-wrap-inner -->
	
</div> <!-- .wrap -->
<?php 
