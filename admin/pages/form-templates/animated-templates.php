<?php
/*
 * Animated BG Templates
 */
?>
	
<div class="pmaf-bg-templates pmaf-animation-templates">
	<div class="pmaf-templates-import-inner">
		<div class="pmaf-toolbar">
			<div class="pmaf-toolbar-left">
				<img class="pmaf-toolbar-logo" src="<?php echo esc_url(  PMAF_URL . 'admin/assets/images/logo.png'); ?>" />
			</div>
			<div class="pmaf-toolbar-right">
				<span class="af-close pmaf-templates-close"></span>
			</div>
		</div>
		<div class="pmaf-import-content-wrap">
			<div class="pmaf-import-content-inner">
				<div class="pmaf-import-title-wrap">
					<span><?php esc_html_e( 'Select a Background Template', 'animated-forms' ) ?></span>
				</div>
				<p class="pmaf-import-dec"><?php esc_html_e( 'Explore hundreds of pre-made animations to get started. Import the below templates with single-click. Have a template suggestion? Let us know!', 'animated-forms' ) ?></p>				
				<div class="pmaf-import-templates-search-wrap">
					<span class="af-search"></span>
					<input type="text" id="pmaf-import-templates-search" value="" placeholder="Search Background Templates">
				</div>	
				<div class="pmaf-import-templates-list-wrap">
					<div id="pmaf-import-templates-list" class="pmaf-import-templates-list">
					
						<?php
							require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
							
							$bgs = pmaf_animated_forms_data()->get_animated_templates();
							foreach( $bgs as $key => $bg ) {
								$pro_status = isset( $bg['pro'] ) && $bg['pro'] == true ? true : false;
							?>
								<div class="pmaf-import-template pmaf-template-item">
									<?php if( $pro_status ) echo '<span class="pmaf-pro-template"><i class="af-pro"></i> '. esc_html__( 'Pro', 'animated-forms' ) .'</span>'; ?>
									<div class="pmaf-import-thumbnail">
										<img src="<?php echo esc_url( pmaf_animated_forms_data()->get_assets_url() .'bg/thumb/'. $bg['file'] ); ?>" alt="<?php esc_html( $bg['name'] ); ?>" />
									</div>
									<h3 class="pmaf-import-template-name"><?php echo esc_html( $bg['name'] ); ?></h3><p class="hidden"><?php echo esc_html( $bg['name'] ); ?></p>
									<?php if( !$pro_status ) : ?>
									<div class="pmaf-import-template-buttons">
										<a class="pmaf-btn pmaf-primary-btn pmaf-template-import" href="#" data-id="<?php echo esc_attr( $key ); ?>"><?php esc_html_e( 'Import', 'animated-forms' ); ?></a>
										<a class="pmaf-btn pmaf-primary-btn pmaf-bg-templates-demo" target="_blank" href="<?php echo esc_url( $bg['demo'] ); ?>"><?php esc_html_e( 'View Demo', 'animated-forms' ); ?></a>
									</div>
									<svg class="pmaf-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="pmaf-checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
									<?php else : ?>
									<div class="pmaf-import-template-buttons">
										<a class="pmaf-btn pmaf-primary-btn pmaf-pro-popup" href="<?php echo esc_url( PMAF_PRO_LINK ); ?>" target="_blank"><?php esc_html_e( 'Import', 'animated-forms' ); ?></a>
										<a class="pmaf-btn pmaf-primary-btn pmaf-bg-templates-demo" target="_blank" href="<?php echo esc_url( $bg['demo'] ); ?>"><?php esc_html_e( 'View Demo', 'animated-forms' ); ?></a>
									</div>
									<?php endif; ?>
								</div>
							<?php
							}
							
							
						 ?>					
						
					</div>
				</div>	
			</div>		
		</div>
	</div>
</div>