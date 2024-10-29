<?php
/*
 * New or Import Form
 */
?>
	
<div class="wrap">

	<h1 class="hidden"><?php esc_html_e( 'New Animated Form', 'animated-forms' ); ?></h1>
		
	<div class="pmaf-wrap-inner">
		<div class="pmaf-loader">
			<svg version="1.1" id="L9" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 100 100" enable-background="new 0 0 0 0" xml:space="preserve">
    <rect x="20" y="50" width="4" height="10" fill="#1967d2">
      <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0" dur="0.6s" repeatCount="indefinite"></animateTransform>
    </rect>
    <rect x="30" y="50" width="4" height="10" fill="#1967d2">
      <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.2s" dur="0.6s" repeatCount="indefinite"></animateTransform>
    </rect>
    <rect x="40" y="50" width="4" height="10" fill="#1967d2">
      <animateTransform attributeType="xml" attributeName="transform" type="translate" values="0 0; 0 20; 0 0" begin="0.4s" dur="0.6s" repeatCount="indefinite"></animateTransform>
    </rect>
</svg>
		</div>
		<div class="pmaf-edtior-wrap">
			<div class="pmaf-toolbar">
				<div class="pmaf-toolbar-left">
					<img class="pmaf-toolbar-logo" src="<?php echo esc_url(  PMAF_URL . 'admin/assets/images/logo.png'); ?>" />
				</div>
				<div class="pmaf-toolbar-right">
					<a href="<?php echo admin_url( 'admin.php?page=alf_custom_forms' ); ?>"><span class="af-close"></span></a>
				</div>
			</div>
			<div class="pmaf-editor-content-wrap">
				<div class="pmaf-editor-content-inner">
					<div class="pmaf-form-name-wrap">
						<label for="pmaf-form-name"><?php esc_html_e( 'Form Name:', 'animated-forms' ); ?></label>
						<input type="text" placeholder="<?php esc_attr_e( 'Enter your form name', 'animated-forms' ); ?>">
					</div>
					<div class="pmaf-form-templates-wrap">
						<div class="pmaf-form-templates-sidebar">
							<div class="pmaf-form-templates-search-wrap">
								<span class="af-search"></span>
								<input type="text" id="pmaf-form-templates-search" value="" placeholder="Search Templates">
							</div>
							<?php
								require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
								$all_forms = pmaf_animated_forms_data()->get_new_forms_count();
							?>
							<ul class="pmaf-form-templates-categories">
								<li class="active" data-category="all">
									<div class="pmaf-form-templates-categories-inner">
										<span class="pmaf-form-templates-categories-name"><?php esc_html_e( 'All Forms', 'animated-forms' ); ?></span>
										<span class="pmaf-form-templates-categories-count"><?php echo esc_html( $all_forms['all'] ); ?></span>
									</div>
								</li>
							<?php
								unset( $all_forms['all'] );
								foreach( $all_forms as $cat => $count ) {
							?>
								<li class="" data-category="<?php echo esc_attr( $cat ); ?>">
									<div class="pmaf-form-templates-categories-inner">
										<span class="pmaf-form-templates-categories-name"><?php printf( '%s %s', esc_html( ucfirst( $cat ) ), esc_html__( 'Form Templates', 'animated-forms' ) ) ?></span>
										<span class="pmaf-form-templates-categories-count"><?php echo esc_html( $count ); ?></span>
									</div>
								</li>
							<?php
								}
							?>
								
							</ul>
						</div>
						<div class="pmaf-form-templates-list-wrap">
							<div id="pmaf-form-templates-list" class="pmaf-form-templates-list">
								<div class="pmaf-form-template" data-category="blank">
									<div class="pmaf-form-thumbnail">
										<div class="pmaf-form-thumbnail-placeholder">
											<span class="af-page"></span>
										</div>
									</div>
									<h3 class="pmaf-form-template-name"><?php esc_html_e( 'Blank Form', 'animated-forms' ); ?></h3>
									<p class="pmaf-form-template-dec"><?php esc_html_e( 'The blank form allows you to create any type of form using our drag & drop builder.', 'animated-forms' ); ?></p>
									<div class="pmaf-form-template-buttons">
										<a class="pmaf-btn pmaf-primary-btn pmaf-from-templates-import" href="#" data-id="blank"><?php esc_html_e( 'Create Blank Form', 'animated-forms' ); ?></a>
									</div>
								</div>
								<div class="pmaf-form-template" data-category="login">
									<div class="pmaf-form-thumbnail">
										<img src="<?php echo esc_url( 'https://animatedforms.com/animated-forms/form-templates/login/login.jpg' ); ?>" alt="<?php echo esc_attr_e( 'Login Form', 'animated-forms' ); ?>" />
									</div>
									<h3 class="pmaf-form-template-name"><?php esc_html_e( 'Login and Register Form', 'animated-forms' ); ?></h3>
									<p class="pmaf-form-template-dec"><?php esc_html_e( 'Login form with registration and forget password form will be optional. For more reference check Login Settings in Animated Forms menu.', 'animated-forms' ); ?></p>
									<div class="pmaf-form-template-buttons">
										<a class="pmaf-btn pmaf-primary-btn pmaf-from-templates-import" href="#" data-id="login"><?php esc_html_e( 'Import', 'animated-forms' ); ?></a>
									</div>
								</div>
							<?php
								require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
								$aforms = pmaf_animated_forms_data()->get_animated_forms();
								foreach( $aforms as $key => $af ) :
								?>
									<div class="pmaf-form-template" data-category="<?php echo esc_attr( $af['category'] ); ?>">
										<div class="pmaf-form-thumbnail">
											<img src="<?php echo esc_url( 'https://animatedforms.com/animated-forms/form-templates/'. $key .'/'. $af['file'] ); ?>" alt="<?php echo esc_attr( $af['name'] ); ?>" />
										</div>
										<h3 class="pmaf-form-template-name"><?php echo esc_html( $af['name'] ); ?></h3>
										<p class="pmaf-form-template-dec"><?php echo esc_html( $af['desc'] ); ?></p>
										<div class="pmaf-form-template-buttons">
											<a class="pmaf-btn pmaf-primary-btn pmaf-from-templates-import" href="#" data-id="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Import', 'animated-forms' ); ?></a>
										</div>
									</div>
								<?php
								endforeach;
							?>								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- .pmaf-wrap-inner -->
	
</div> <!-- .wrap -->
<?php 
