<?php
/*
 * Dashboard - Entries
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

$entry_id = isset( $_GET['pmaf_entry'] ) && !empty( $_GET['pmaf_entry'] ) ? $_GET['pmaf_entry'] : '';

if( !$entry_id ) return '';

$prev_id = ''; $next_id = '';

require_once PMAF_DIR . "admin/class.admin-entries.php";
$next_entry = pmaf_animated_admin_entries()->get_next_entry( $entry_id );
if( !empty( $next_entry ) && isset( $next_entry[0] ) ) {
	$next_id = $next_entry[0]->id;
}

$prev_entry = pmaf_animated_admin_entries()->get_prev_entry( $entry_id );
if( !empty( $prev_entry ) && isset( $prev_entry[0] ) ) {
	$prev_id = $prev_entry[0]->id;
}


?>
	
<div class="wrap">

	<h1 class="hidden"><?php esc_html_e( 'Animated Forms Entry', 'animated-forms' ); ?></h1>
	
	<?php do_action( 'pmaf_page_top' ); ?>
	
	<div class="pmaf-wrap-inner">
	
		<div class="pmaf-entries-wrap">
		
			<ul class="pmaf-entry-nav">
				<?php if( $prev_id ) : ?>
				<li><a href="<?php echo esc_url( admin_url( '/admin.php?page=alf_custom_forms&pmaf_entry='. esc_attr( $prev_id ) ) ); ?>" class="pmaf-entry-prev pmaf-filled-btn"><i class="af-arrow-right"></i><?php esc_html_e( 'Previous', 'animated-forms' ); ?></a></li>
				<?php endif; ?>
				
				<?php if( $next_id ) : ?>
				<li><a href="<?php echo esc_url( admin_url( '/admin.php?page=alf_custom_forms&pmaf_entry='. esc_attr( $next_id ) ) ); ?>" class="pmaf-entry-next pmaf-filled-btn"><?php esc_html_e( 'Next', 'animated-forms' ); ?><i class="af-arrow-right"></i></a></li>
				<?php endif; ?>
				
				<li><a href="<?php echo esc_url( admin_url( 'admin.php?page=alf_entries' ) ); ?>" class="pmaf-entry-all pmaf-filled-btn"><?php esc_html_e( 'All Entries', 'animated-forms' ); ?></a></li>
			</ul>
		
			<div class="pmaf-single-entry">
			
				<div class="pmaf-left-content">
					<div class="pmaf-single-header">
						<?php printf( '%s %s', '#'. esc_attr( $entry_id ), esc_html__( ' Form Entry Data', 'animated-forms' ) ); ?>
					</div>
					
					<?php
						
						$entry = pmaf_animated_admin_entries()->get_entry( $entry_id );
						if( !empty( $entry ) ) { 
							$form_data = maybe_unserialize( $entry[0]->form_data );
							foreach( $form_data as $key => $single_entry ) {
							?>
								<div class="pmaf-each-entry">
									<label><?php echo esc_html( $single_entry['label'] ); ?></label>
									<div class="pmaf-each-content">
									<?php 
										if( isset( $single_entry['files'] )  ) {
											echo '<ul class="pmaf-single-choices">';
											foreach( $single_entry['files'] as $file_name => $file_url ) {
												echo '<li><a href="'. esc_url( $file_url ) .'">'. esc_html( $file_name ) .'</a></li>';
											}
											echo '</ul>';
										} elseif( is_array( $single_entry['value'] ) ) {
											echo '<ul class="pmaf-single-choices">';
											foreach( $single_entry['value'] as $choice ) {
												echo '<li>'. esc_html( $choice ) .'</li>';
											}
											echo '</ul>';
										} else {
											echo esc_html( $single_entry['value'] );
										}
									?>
									</div>
								</div>
							<?php
							}
						}
						pmaf_animated_admin_entries()->update_entry_data( $entry_id, 'status', 'read' );						
					?>
					
				</div>
				<div class="pmaf-right-content">
					<div class="pmaf-single-header">
						<?php esc_html_e( 'Submission Informations', 'animated-forms' ); ?>
					</div>
					
					<?php if( !empty( $entry ) ) : 
					
						$source_url = $entry[0]->source_url;
						$read_status = $entry[0]->status;
						$fav = $entry[0]->is_favourite;
						$user_ip = $entry[0]->ip;
						$updated = $entry[0]->updated_at;
					?>
					<ul class="pmaf-entry-info">
						<li><label><?php esc_html_e( 'Entry ID', 'animated-forms' ); ?></label><span><?php echo esc_html( $entry_id ); ?></span></li>
						<li><label><?php esc_html_e( 'Source URL', 'animated-forms' ); ?></label><span><?php echo esc_html( $source_url ); ?></span></li>
						<li><label><?php esc_html_e( 'User IP', 'animated-forms' ); ?></label><span><?php echo esc_html( $user_ip ); ?></span></li>
						<li><label><?php esc_html_e( 'Read Status', 'animated-forms' ); ?></label><span><?php if( $read_status == 'read' ) esc_html_e( 'Read', 'animated-forms' ); else esc_html_e( 'Unread', 'animated-forms' ); ?></span></li>
						<li><label><?php esc_html_e( 'Favourite', 'animated-forms' ); ?></label><span><a href="#" class="pmaf-make-fav<?php if( $fav ) echo esc_attr( ' liked' ); ?>" title="<?php if( $fav ) echo esc_attr__( 'Liked', 'animated-forms' ); else echo esc_attr__( 'Make this favourite list', 'animated-forms' ); ?>" data-id="<?php echo esc_attr( $entry[0]->id ); ?>"><i class="af-heart"></i></a></span></li>
						<li><label><?php esc_html_e( 'Submitted On', 'animated-forms' ); ?></label><span><?php echo esc_html( $updated ); ?></span></li>
					</ul>
					<?php endif; ?>
					
				</div>
			</div>			
		</div>
		
	</div> <!-- .pmaf-wrap-inner -->
	
</div> <!-- .wrap -->
<?php 
