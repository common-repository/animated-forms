<?php

class PMAF_Animated_Forms_Entries {
	
	public $form_id = null;
			
	private static $_instance = null;
		
	public function __construct() {}
	
	public function get_form_post_data() {
		
		$form_settings = pmaf_forms_data()->get_form_settings( $this->form_id );	
		
		// default form data
		$sform_data = pmaf_forms_data()->get_form_data( $this->form_id );

		// post form data
		$form_data = $_POST;
		
		// remove unwanted fields
		$form_data = $this->filter_data( $form_data );
		
		$form_data_out = [];
		foreach( $form_data as $key => $data ) {
			$field_id = str_replace( 'field_', '', $key );
			$f_label = $sform_data[$field_id]['label']; 
			$f_type = $sform_data[$field_id]['type']; 
			if( $f_type != 'file' ) {
				if( !empty( $data ) && is_array( $data ) ) {
					$form_data_out[$field_id] = [ 'label' => $f_label, 'value' => [] ];
					$values = [];
					foreach( $data as $d ) {
						$values[] = $d;						
					}
					$form_data_out[$field_id] = [ 'label' => $f_label, 'value' => $values ];
				} else {
					$form_data_out[$field_id] = [ 'label' => $f_label, 'value' => $data ];
				}
			}
		}
		
		return $form_data_out;
		
	}
	
	public function upload_files( $all_attachments ) {
		
		$files = [];
		if( !empty( $all_attachments ) ) {
			
			// default form data
			$sform_data = pmaf_forms_data()->get_form_data( $this->form_id );
			
			$i = 1;

			foreach( $all_attachments as $field_id => $attachments ) {
				$path = trailingslashit( wp_upload_dir()['basedir'] ) . 'pmaf';
				$f_files = [];
				foreach( $attachments as $file_name => $file ) { 
								
					$suffix_name = current_time( 'timestamp' );
					$suffix_name = $suffix_name . wp_rand( 10, 99 );
					$extension = pathinfo( $file_name, PATHINFO_EXTENSION );
					$file_without_ext = str_replace( '.'.$extension, "", $file_name );
					$file_name = $file_without_ext .'-'. $suffix_name .'.'. $extension;
					$file_path = $path  .'/'. $file_name;
					
					move_uploaded_file( $file, $file_path );
					$f_files[$file_name] = $file_path;
					
				}
				$files[$field_id] = [ 'label' => $sform_data[$field_id]['label'], 'files' => $f_files ];
			}
			
		}
		return $files;
	
	}
	
	public function make_entry( $data ) {
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'pmaf_entries';
		$table_data = array(
			'form_id' => $this->form_id,
			'form_data' => maybe_serialize($data),
			'source_url' => wp_get_referer(),
			'status' => 'unread',
			'is_favourite' => 0,
			//'browser' => $_SERVER['HTTP_USER_AGENT'],
			'ip' => $_SERVER['REMOTE_ADDR'],
			'created_at' => current_time( 'mysql' ),
			'updated_at' => current_time( 'mysql' )
		);
		
		$wpdb->insert(
			$table_name,
			$table_data
		);
		
	}
	
	public function filter_data( $data ) {
		
		unset( $data['action'] );
		unset( $data['nonce'] );
		unset( $data['form_id'] );
		
		return $data;
		
	}
	
	public function entries_list( $entries ) {
		
		?>
		<ul class="pmaf-entries-list">
			<li>
				<span><?php esc_html_e( 'ID', 'animated-forms' ); ?></span><span><?php esc_html_e( 'Form', 'animated-forms' ); ?></span><span><?php esc_html_e( 'Status', 'animated-forms' ); ?></span><span><?php esc_html_e( 'Favourite', 'animated-forms' ); ?></span><span><?php esc_html_e( 'Created/Updated', 'animated-forms' ); ?></span>
			</li>
			<?php
				$status_arr = [
					'all' => esc_html__( 'All', 'animated-forms' ),
					'unread' => esc_html__( 'Unread', 'animated-forms' ),
					'read' => esc_html__( 'Read', 'animated-forms' )
				];
				foreach( $entries as $entry ) :
				?>
					<li>
						<span><?php echo esc_html( '#'. $entry->id ); ?></span><span>
						<a href="<?php echo esc_url( admin_url( '/admin.php?page=alf_custom_forms&pmaf_entry='. esc_attr( $entry->id ) ) ); ?>"><strong><?php echo esc_html( '#'. $entry->form_id .' '. get_the_title( $entry->form_id ) ); ?></strong></a>
						<ul class="pmaf-other-settings"><li><a href="<?php echo esc_url( admin_url( '/admin.php?page=alf_custom_forms&pmaf_entry='. esc_attr( $entry->id ) ) ); ?>"><?php esc_html_e( 'Edit', 'animated-forms' ); ?></a></li><li><a href="#"><?php esc_html_e( 'Delete', 'animated-forms' ); ?></a></li></ul>
						</span><span><?php echo esc_html( $status_arr[$entry->status] ); ?></span><span><a href="#" class="pmaf-make-fav<?php if( $entry->is_favourite ) echo esc_attr( ' liked' ); ?>" title="<?php if( $entry->is_favourite ) echo esc_attr__( 'Liked', 'animated-forms' ); else echo esc_attr__( 'Make this favourite list', 'animated-forms' ); ?>" data-id="<?php echo esc_attr( $entry->id ); ?>"><i class="af-heart"></i></a></span><span><?php echo esc_html( $entry->updated_at ); ?></span>
					</li>
				<?php
				endforeach;
			?>
		</ul>
		<?php
		
	}
	
	public function no_entries_html() {
	?>
		<div class="pmaf-no-entries"><p><?php esc_html_e( '!Oops.. No entries found.', 'animated-forms' ); ?></p></div>
	<?php
	}
	
	public function entries_pagination( $max, $range ) {
		
		$defaults = array(
			'range'           => $range,
			'custom_query'    => false,
			'first_string' => '<i class="bi bi-arrow-bar-left"></i>',
			'previous_string' => '<i class="bi bi-arrow-left-short"></i>',
			'next_string'     => '<i class="bi bi-arrow-right-short"></i>',
			'last_string'     => '<i class="bi bi-arrow-bar-right"></i>',
			'before_output'   => '<div class="post-pagination-wrap"><ul class="nav pagination post-pagination justify-content-center">',
			'after_output'    => '</ul></div>'
		);

		$args = apply_filters( 'pmaf_entries_pagination_defaults', $defaults );

		$args['range'] = (int) $args['range'] - 1;
		
		$count = $max;
		$page = 1;
		
		$ceil  = ceil( $args['range'] / 2 );

		if ( $count <= 1 )
			return FALSE;

		if ( !$page )
			$page = 1;

		if ( $count > $args['range'] ) {
			if ( $page <= $args['range'] ) {
				$min = 1;
				$max = $args['range'] + 1;
			} elseif ( $page >= ($count - $ceil) ) {
				$min = $count - $args['range'];
				$max = $count;
			} elseif ( $page >= $args['range'] && $page < ($count - $ceil) ) {
				$min = $page - $ceil;
				$max = $page + $ceil;
			}
		} else {
			$min = 1;
			$max = $count;
		}

		$previous = intval($page) - 1;
		$previous = '#';

		// For theme check
		$t_next_post_link = '#';
		$t_prev_post_link = '#';

		echo '<div class="post-pagination-wrap"><ul class="pagination">';

		$firstpage = "#";
		if ( $firstpage && (1 != $page) && isset( $args['first_string'] ) && $args['first_string'] != '' ){
			echo sprintf( 
					'<li class="nav-item previous"><a href="%s" title="%s">%s</a></li>',
					esc_url( $firstpage ),
					esc_attr__( 'First', 'briddge'),
					$args['first_string']
				);
		}
		if ( $previous && (1 != $page) ){
			echo sprintf(
					'<li class="nav-item"><a href="%s" class="prev-page" title="%s">%s</a></li>',
					esc_url( $previous ),
					esc_attr__( 'previous', 'briddge'),
					$args['previous_string']
				);
		}

		if ( !empty($min) && !empty($max) ) {
			for( $i = $min; $i <= $max; $i++ ) {
				if ($page == $i) {
					echo sprintf( 
							'<li class="nav-item active"><span class="active">%s</span></li>',
							esc_attr( $i )
						);
				} else {
					echo sprintf( 
							'<li class="nav-item"><a href="%s" data-page="%s">%s</a></li>', 
							'#', 
							esc_attr( $i ),
							esc_attr( $i )
						);
				}
			}
		}

		$next = intval($page) + 1;
		$next = '#';
		if ($next && ($count != $page) )
			echo sprintf(
					'<li class="nav-item"><a href="%s" class="next-page" title="%s">%s</a></li>',
					esc_url( $next ),
					esc_attr__( 'next', 'briddge'),
					$args['next_string']
				);

		$lastpage = '#';
		if ( $lastpage && isset( $args['last_string'] ) && $args['last_string'] != '' ) {
			echo sprintf(
					'<li class="nav-item next"><a href="%s" title="%s">%s</a></li>',
					'#',
					esc_attr__( 'Last', 'briddge'),
					$args['last_string']
				);
		}
	
	}
		
	public static function get_instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}
		
}

function pmaf_entries() {
	return PMAF_Animated_Forms_Entries::get_instance();
}