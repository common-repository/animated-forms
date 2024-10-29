<?php
/*
 * Dashboard
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$form_id = $_GET['pmaf'];
$form_ani_settings = pmaf_forms_data()->get_form_animation_settings( $form_id );
$form_settings = pmaf_forms_data()->get_form_settings( $form_id );
$post_title = get_the_title( $form_id );
//$form_name = isset( $form_settings['basic']['form_name'] ) ? $form_settings['basic']['form_name'] : '';
$form_name = '';
if( isset( $form_settings['basic'] ) && !empty( $form_settings['basic']['form_name'] ) ) {
	$form_name = $form_settings['basic']['form_name'];
} elseif( $post_title ) {
	$form_name = $post_title;
} else {
	$form_name = 'Form #'. $form_id;
}
?>
<div class="pmaf-field-editor-wrap">			
	<div class="pmaf-editor-row">
		<div class="pmaf-toolbar">
			<div class="pmaf-toolbar-left">
				<a href="<?php echo admin_url( 'admin.php?page=alf_custom_forms' ); ?>" title="<?php esc_html_e( 'Back to Dashboard.', 'animate-forms' ); ?>" class="pmaf-back-to-db"><i class="dashicons dashicons-wordpress-alt"></i></a>				
			</div>
			<div class="pmaf-toolbar-center pmaf-login-toolbar">
				<img class="pmaf-toolbar-logo" src="<?php echo esc_url(  PMAF_URL . 'admin/assets/images/mini-logo.png'); ?>" />
				<?php if( !empty( $form_name ) ) : ?><h3 class="pmaf-form-title"><?php echo esc_html( $form_name ); ?></h3><?php endif; ?>
				
			</div>
			<div class="pmaf-tools-wrap">
				<div class="pmaf-shortcode-display pmaf-shortcode-copy-parent">
					<a href="#" class="pmaf-shortcode-copy">[animated_form id="<?php echo esc_attr( $form_id ); ?>"]</a><i class="af-copy"></i>
				</div>
				<a href="#" class="pmaf-preview-trigger pmaf-btn"><span><i class="af-eye"></i><?php esc_html_e( 'Preview Mode', 'animated-forms' ); ?></span></a>
				
			</div>
			<div class="pmaf-toolbar-right">
				<div class="pamf-save-btn-wrap">
					<a href="#" class="pmaf-save-form" id="pmaf-save-form"><img src="<?php echo esc_url( PMAF_URL . 'admin/assets/images/loader.gif' ); ?>" /><span><?php esc_html_e( 'Save', 'animated-forms' ); ?></span></a>
					<span class="pmaf-smart-p"><?php esc_html_e( 'Save for a updated preview.', 'animate-forms' ); ?></span>
				</div>
				<a href="<?php echo admin_url( 'admin.php?page=alf_custom_forms' ); ?>"><span class="af-close"></span></a>
			</div>
		</div>
		<div class="pmaf-field-editor-content-wrap">
			<div class="pmaf-field-editor-content-inner">
				<div class="pmaf-field-editor-preview-wrap">
				
					<div class="pmaf-common-sidebar">
						<ul class="pmaf-editor-common-controls">
							<li><a href="#pmaf-add-new" class="pmaf-global-tab-item pmaf-add-new"><i class="af-add"></i><span><?php esc_html_e( 'New', 'animated-forms' ); ?></span></a></li>
							<li><a href="#pmaf-form-animation" class="pmaf-global-tab-item pmaf-import-animate-pack"><i class="dashicons dashicons-art"></i><span><?php esc_html_e( 'Animation', 'animated-forms' ); ?></span></a></li>
							<li><a href="#pmaf-form-customizer" class="pmaf-global-tab-item pmaf-animate-customizer active"><i class="dashicons dashicons-admin-customizer"></i><span><?php esc_html_e( 'Customizer', 'animated-forms' ); ?></span></a></li>
							<li><a href="#pmaf-form-settings" class="pmaf-global-tab-item"><i class="dashicons dashicons-admin-settings"></i><span><?php esc_html_e( 'Settings', 'animated-forms' ); ?></span></a></li>
							<li><a href="<?php echo esc_url( PMAF_PRO_LINK ); ?>" class="pmaf-global-tab-item pmaf-get-pro"><i class="af-pro"></i><span><?php esc_html_e( 'Get Pro', 'animated-forms' ); ?></span></a></li>
						</ul>
					</div>
					
					<div class="pmaf-field-editor-sidebar">
						
						<div class="pmaf-editor-field-settings">
							
							<div id="pmaf-form-customizer" class="pmaf-main-tab-content active">								
								<h3><?php esc_html_e( 'Background Settings', 'animated-forms' ); ?></h3>
								<div class="pmaf-sub-tab-content">
									<a href="#" class="pmaf-editor-btn pmaf-choose-bg-animation"><i class="af-download"></i><?php esc_html_e( 'Import Background', 'animated-forms' ); ?></a>
									<div class="pmaf-form-ani-outer-settings"></div>
								</div>
								<h3><?php esc_html_e( 'Animation Settings', 'animated-forms' ); ?></h3>
								<div class="pmaf-sub-tab-content">
									<a href="#" class="pmaf-editor-btn pmaf-choose-overlay"><i class="af-download"></i><?php esc_html_e( 'Import Animation', 'animated-forms' ); ?></a>								
									<?php if( !empty( $form_ani_settings ) && isset( $form_ani_settings['overlay']['name'] ) && !empty( $form_ani_settings['overlay']['name'] ) ) { ?>
									<div class="pmaf-selected-overlay"><strong><?php esc_html_e( 'Selceted Overlay', 'animated-forms' ); ?></strong><span><?php echo esc_html( $form_ani_settings['overlay']['name'] ); ?></span><a href="#" class="pmaf-overlay-remove"><i class="af-close"></i></a></div>
									<?php } ?>
								</div>
								<h3><?php esc_html_e( 'Form Settings', 'animated-forms' ); ?></h3>
								<div class="pmaf-sub-tab-content">
									<a href="#" class="pmaf-editor-btn pmaf-choose-form-styles"><i class="af-download"></i><?php esc_html_e( 'Import Form Styles', 'animated-forms' ); ?></a>
									<?php if( !empty( $form_ani_settings ) && isset( $form_ani_settings['inner'] ) && !empty( $form_ani_settings['inner'] ) ) { 
										require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
										$data = pmaf_animated_forms_data()->get_form_inner_templates( $form_ani_settings['inner']['model'] );
									?>
									<div class="pmaf-selected-fi"><strong><?php esc_html_e( 'Selceted Form Inner Style', 'animated-forms' ); ?></strong><span><?php echo esc_html(  $data['name'] ); ?></span><a href="#" class="pmaf-fi-remove"><i class="af-close"></i></a></div>
									<?php } ?>
									<div class="pmaf-form-outer-settings"></div>								
									<div class="pmaf-form-ani-overlay-settings"></div>
								</div>								
								<h3><?php esc_html_e( 'Form Labels Settings', 'animated-forms' ); ?></h3>
								<div class="pmaf-sub-tab-content">
									<div class="pmaf-form-ani-labels-settings"></div>	
								</div>
								<h3><?php esc_html_e( 'Input Box Settings', 'animated-forms' ); ?></h3>
								<div class="pmaf-sub-tab-content">
									<div class="pmaf-form-ani-box-settings"></div>
								</div>
								<h3><?php esc_html_e( 'Button Settings', 'animated-forms' ); ?></h3>
								<div class="pmaf-sub-tab-content">
									<div class="pmaf-form-btn-settings"></div>
								</div>
								<h3><?php esc_html_e( 'Content Settings', 'animated-forms' ); ?></h3>
								<div class="pmaf-sub-tab-content">
									<div class="pmaf-form-title-settings"></div>
								</div>
							</div>
							
							<div id="pmaf-form-settings" class="pmaf-main-tab-content">	
								<div class="pmaf-admin-msg align-center">
									<p>This is login form. Custom forms only have those form settings. For Login form go to Global settings</p>
									<a class="pmaf-btn pmaf-filled-btn" href="<?php echo admin_url( 'admin.php?page=alf_settings' ); ?>" title="<?php esc_attr_e( 'Global Settings', 'animate-forms' ); ?>"><?php esc_html_e( 'Global Settings', 'animate-forms' ); ?></a>
								</div>
							</div>

							<div class="pmaf-editor-options"></div>
						</div>	
					</div>
					<div class="pmaf-editor-preview-wrap pmaf-scroll-1 pmaf-login-preview-wrap">
						<a href="#" class="pmaf-field-toggle"><span class="af-angle-down"></span></a>
						<div class="pmaf-all-form-preview loading active">
							<div class="pmaf-preview-loader"><span class="pmaf-page-loader"></span></div>
							<?php
								$preview_url = add_query_arg( array( 'pmaf_form_preview' => $form_id ), home_url() ); 
								echo '<iframe id="pmaf-form-preview-frame" src="'. esc_url( $preview_url ) .'"></iframe>';
							?>
						</div>
					</div>
				</div>	
			</div>
		</div>	
	</div>
</div>