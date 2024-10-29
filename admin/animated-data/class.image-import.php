<?php

class PMAF_Animated_Forms_Image_Import {

	public static $instance = null;
	
	public function __construct() {}
	
	/**
	 * Retrieves file extension by mime type.
	 *
	 * @since 0.7.0
	 *
	 * @param string $mime_type Mime type to search extension for.
	 * @return string|null File extension if available, or null if not found.
	 */
	protected static function get_file_extension_by_mime_type( $mime_type ) {
		static $map = null;

		if ( is_array( $map ) ) {
			return isset( $map[ $mime_type ] ) ? $map[ $mime_type ] : null;
		}

		$mime_types = wp_get_mime_types();
		$map        = array_flip( $mime_types );

		// Some types have multiple extensions, use only the first one.
		foreach ( $map as $type => $extensions ) {
			$map[ $type ] = strtok( $extensions, '|' );
		}

		return isset( $map[ $mime_type ] ) ? $map[ $mime_type ] : null;
	}
	
	/**
	 * Decide what the maximum file size for downloaded attachments is.
	 * Default is 0 (unlimited), can be filtered via import_attachment_size_limit
	 *
	 * @return int Maximum attachment file size to import
	 */
	function max_attachment_size() {
		return apply_filters( 'import_attachment_size_limit', 0 );
	}
	
	/**
	 * Parses filename from a Content-Disposition header value.
	 *
	 * As per RFC6266:
	 *
	 *     content-disposition = "Content-Disposition" ":"
	 *                            disposition-type *( ";" disposition-parm )
	 *
	 *     disposition-type    = "inline" | "attachment" | disp-ext-type
	 *                         ; case-insensitive
	 *     disp-ext-type       = token
	 *
	 *     disposition-parm    = filename-parm | disp-ext-parm
	 *
	 *     filename-parm       = "filename" "=" value
	 *                         | "filename*" "=" ext-value
	 *
	 *     disp-ext-parm       = token "=" value
	 *                         | ext-token "=" ext-value
	 *     ext-token           = <the characters in token, followed by "*">
	 *
	 * @since 0.7.0
	 *
	 * @see WP_REST_Attachments_Controller::get_filename_from_disposition()
	 *
	 * @link http://tools.ietf.org/html/rfc2388
	 * @link http://tools.ietf.org/html/rfc6266
	 *
	 * @param string[] $disposition_header List of Content-Disposition header values.
	 * @return string|null Filename if available, or null if not found.
	 */
	protected static function get_filename_from_disposition( $disposition_header ) {
		// Get the filename.
		$filename = null;

		foreach ( $disposition_header as $value ) {
			$value = trim( $value );

			if ( strpos( $value, ';' ) === false ) {
				continue;
			}

			list( $type, $attr_parts ) = explode( ';', $value, 2 );

			$attr_parts = explode( ';', $attr_parts );
			$attributes = array();

			foreach ( $attr_parts as $part ) {
				if ( strpos( $part, '=' ) === false ) {
					continue;
				}

				list( $key, $value ) = explode( '=', $part, 2 );

				$attributes[ trim( $key ) ] = trim( $value );
			}

			if ( empty( $attributes['filename'] ) ) {
				continue;
			}

			$filename = trim( $attributes['filename'] );

			// Unquote quoted filename, but after trimming.
			if ( substr( $filename, 0, 1 ) === '"' && substr( $filename, -1, 1 ) === '"' ) {
				$filename = substr( $filename, 1, -1 );
			}
		}

		return $filename;
	}
	
	/**
	 * Attempt to download a remote file attachment
	 *
	 * @param string $url URL of item to fetch
	 * @param array $post Attachment details
	 * @return array|WP_Error Local file location details on success, WP_Error otherwise
	 */
	function fetch_remote_file( $url, $post_date = '' ) {
			
		// Extract the file name from the URL.
		$path      = parse_url( $url, PHP_URL_PATH );
		$file_name = '';
		if ( is_string( $path ) ) {
			$file_name = basename( $path );
		}

		if ( ! $file_name ) {
			$file_name = md5( $url );
		}
		
		// remove image dimensions from file name
		$dim_re_pattern = '/-[0-9]{1,4}x[0-9]{1,4}/m';
		$file_name = preg_replace( $dim_re_pattern, '', $file_name );
		
		// allow duplicate images comapre with image name
		$duplicate_opt = false;
		if( $duplicate_opt ) {
			$raw_file_name = pathinfo( $file_name, PATHINFO_FILENAME );
			$file_ext = pathinfo( $file_name, PATHINFO_EXTENSION );
			$file_name = $raw_file_name .'-'. time() .'.'. $file_ext;
		}
		
		require_once( ABSPATH . 'wp-admin/includes/file.php' );
		$tmp_file_name = wp_tempnam( $file_name );
		
		if ( ! $tmp_file_name ) {
			return new WP_Error( 'import_no_file', __( 'Could not create temporary file.', 'wordpress-importer' ) );
		}	
		
		// Fetch the remote URL and write it to the placeholder file.
		$remote_response = wp_safe_remote_get(
			$url,
			array(
				'timeout'  => 300,
				'stream'   => true,
				'filename' => $tmp_file_name,
				'headers'  => array(
					'Accept-Encoding' => 'identity',
				),
			)
		);
						
		if ( is_wp_error( $remote_response ) ) {
			@unlink( $tmp_file_name );
			return new WP_Error(
				'import_file_error',
				sprintf(
					/* translators: 1: The WordPress error message. 2: The WordPress error code. */
					__( 'Request failed due to an error: %1$s (%2$s)', 'wordpress-importer' ),
					esc_html( $remote_response->get_error_message() ),
					esc_html( $remote_response->get_error_code() )
				)
			);
		}
		
		
		// code start
		$headerResult = wp_remote_retrieve_headers( $remote_response );
		$content_length = $headerResult['content-length'];
		
		$curl_img = false;
		
		// check image lenght
		if( $content_length < 256 ) { // get through curl 
		
			$ch = curl_init( $url );
			curl_setopt( $ch, CURLOPT_HEADER, 0 );
			curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
			curl_setopt( $ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.11 (KHTML, like Gecko) Chrome/23.0.1271.1 Safari/537.11' );
			curl_setopt( $ch, CURLOPT_TIMEOUT, 120 );
			curl_setopt( $ch, CURLOPT_BINARYTRANSFER, 1 );
			$content = curl_exec( $ch );			
			
			$content_info = curl_getinfo($ch); //wp_send_json( $content_info );
			$headers = [];
			if( $content_info ) {
				$headers = [
					'content-length' => $content_info['size_download'], 
					'content-disposition' => '', 
					'content-type' => $content_info['content_type']
				];
				$remote_response_code = $content_info['http_code'];
			}
			
			curl_close( $ch );
			
			$uploads = wp_upload_dir();
			$tmp_file_name = $uploads['basedir'] . '/pmaf/'. $file_name;
			if( file_exists( $tmp_file_name ) ) unlink( $tmp_file_name );
			file_put_contents ( $tmp_file_name, $content );
			
			$curl_img = true;
						
		} else { // wp remote way
			
			$content = $remote_response['body']; // use the content
			
			// other code		
			$remote_response_code = (int) wp_remote_retrieve_response_code( $remote_response );					

		}
		
		// code end
		
		// Make sure the fetch was successful.
		if ( 200 !== $remote_response_code ) {
			@unlink( $tmp_file_name );
			return new WP_Error(
				'import_file_error',
				sprintf(
					/* translators: 1: The HTTP error message. 2: The HTTP error code. */
					__( 'Remote server returned the following unexpected result: %1$s (%2$s)', 'wordpress-importer' ),
					get_status_header_desc( $remote_response_code ),
					esc_html( $remote_response_code )
				)
			);
		}
		
		if( !$curl_img ) {
			$headers = wp_remote_retrieve_headers( $remote_response );
		}
				
		// Request failed.
		if ( ! $headers ) {
			@unlink( $tmp_file_name );
			return new WP_Error( 'import_file_error', __( 'Remote server did not respond', 'wordpress-importer' ) );
		}

		$filesize = (int) filesize( $tmp_file_name );

		if ( 0 === $filesize ) {
			@unlink( $tmp_file_name );
			return new WP_Error( 'import_file_error', __( 'Zero size file downloaded', 'wordpress-importer' ) );
		}

		if ( ! isset( $headers['content-encoding'] ) && isset( $headers['content-length'] ) && $filesize !== (int) $headers['content-length'] ) {
			@unlink( $tmp_file_name );
			return new WP_Error( 'import_file_error', __( 'Downloaded file has incorrect size', 'wordpress-importer' ) );
		}
		
		$max_size = (int) $this->max_attachment_size();
		if ( ! empty( $max_size ) && $filesize > $max_size ) {
			@unlink( $tmp_file_name );
			return new WP_Error( 'import_file_error', sprintf( __( 'Remote file is too large, limit is %s', 'wordpress-importer' ), size_format( $max_size ) ) );
		}
		
		// Override file name with Content-Disposition header value.
		if ( ! empty( $headers['content-disposition'] ) ) {
			$file_name_from_disposition = self::get_filename_from_disposition( (array) $headers['content-disposition'] );
			if ( $file_name_from_disposition ) {
				$file_name = $file_name_from_disposition;
			}
		}
		
		// Set file extension if missing.
		$file_ext = pathinfo( $file_name, PATHINFO_EXTENSION );
		if ( ! $file_ext && isset( $headers['content-type'] ) && !empty( $headers['content-type'] ) ) {
			$extension = self::get_file_extension_by_mime_type( $headers['content-type'] );
			if ( $extension ) {
				$file_name = "{$file_name}.{$extension}";
			}
		}
		
		// .attach file extension fix
		$file_name = str_replace( ".attach", ".jpg", $file_name );
		
		// Handle the upload like _wp_handle_upload() does.
		$wp_filetype     = wp_check_filetype_and_ext( $tmp_file_name, $file_name );
		$ext             = empty( $wp_filetype['ext'] ) ? '' : $wp_filetype['ext'];
		$type            = empty( $wp_filetype['type'] ) ? '' : $wp_filetype['type'];
		$proper_filename = empty( $wp_filetype['proper_filename'] ) ? '' : $wp_filetype['proper_filename'];

		// Check to see if wp_check_filetype_and_ext() determined the filename was incorrect.
		if ( $proper_filename ) {
			$file_name = $proper_filename;
		}

		/*if ( ( ! $type || ! $ext ) && ! current_user_can( 'unfiltered_upload' ) ) {
			return new WP_Error( 'import_file_error', __( 'Sorry, this file type is not permitted for security reasons.', 'wordpress-importer' ) );
		}*/
		
		$uploads = wp_upload_dir( $post_date );
		if ( ! ( $uploads && false === $uploads['error'] ) ) {
			return new WP_Error( 'upload_dir_error', $uploads['error'] );
		}
		
		// check file already exists on same name
		/*$file_exists_status = $this->check_file_exists( $file_name );
		if( $file_exists_status ) {
			return new WP_Error( 'upload_dir_error', [ 'status' => 'img exist', 'attachment_id' => $file_exists_status ] );
		}*/
		
		// Move the file to the uploads dir.
		$file_name     = wp_unique_filename( $uploads['path'], $file_name ); 
		$new_file      = $uploads['path'] . "/$file_name";
		$move_new_file = copy( $tmp_file_name, $new_file );

		if ( ! $move_new_file ) {
			@unlink( $tmp_file_name );
			return new WP_Error( 'import_file_error', __( 'The uploaded file could not be moved', 'wordpress-importer' ) );
		}
		
		if( $curl_img ) { 
			@unlink( $tmp_file_name );
		}

		// Set correct file permissions.
		$stat  = stat( dirname( $new_file ) );
		$perms = $stat['mode'] & 0000666;
		chmod( $new_file, $perms );
		
		$upload = array(
			'file'  => $new_file,
			'url'   => $uploads['url'] . "/$file_name",
			'type'  => $wp_filetype['type'],
			'error' => false,
		);
						
		//return $upload;
		
		
		$img_file = $upload['file'];
		$guid = $upload['url'];

		$attachment = array (
				'guid' => $guid,
				'post_mime_type' => $upload['type'],
				'post_title' => '',
				'post_content' => '',
				'post_status' => 'inherit' 
		);	
		$attach_id = wp_insert_attachment( $attachment, $img_file );
		
		update_post_meta( $attach_id, '_wp_attachment_image_alt', '' );
		
		require_once (ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata ( $attach_id, $img_file );
		wp_update_attachment_metadata ( $attach_id, $attach_data );
		
		return [ 'id' => $attach_id, 'url' => $guid ];		
		
	}
	
	public function check_file_exists( $file_name ) {
		
		global $wpdb;
		$querystr = "SELECT $wpdb->posts.ID FROM $wpdb->posts WHERE $wpdb->posts.post_type='attachment' AND $wpdb->posts.guid LIKE '%$file_name%'";
		$attachments = $wpdb->get_results( $querystr, OBJECT );
		if( count( $attachments ) && isset( $attachments[0] ) ) return $attachments[0]->ID;
		
		return false;
		
	}
			
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
}

function pmaf_image_import_call() {
	return PMAF_Animated_Forms_Image_Import::instance();
}