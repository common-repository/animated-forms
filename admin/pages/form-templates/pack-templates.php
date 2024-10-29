<?php
/*
 * Animated Pack Templates
 */
?>
	
<div class="pmaf-bg-templates pmaf-animate-pack-templates">
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
					<span><?php esc_html_e( 'Select a Pre-Made Animation', 'animated-forms' ) ?></span>
				</div>
				<p class="pmaf-import-dec"><?php esc_html_e( 'Explore pre-made animations to get started. Import the below templates and use it with any form type. Have a template suggestion? Let us know!', 'animated-forms' ) ?></p>	
				
				<div class="pmaf-row">
					<div class="pmaf-col pmaf-col-3">
						<div class="pmaf-form-templates-wrap">
							<div class="pmaf-form-templates-sidebar">
								<div class="pmaf-form-templates-search-wrap">
									<span class="af-search"></span>
									<input type="text" id="pmaf-import-pack-search" value="" placeholder="Search Animations">
								</div>
								<?php
									require_once PMAF_DIR . "admin/animated-data/class.animated-data.php";
									$ani_packs = pmaf_animated_forms_data()->get_animated_pack();
									$cat_counts = pmaf_animated_forms_data()->get_templates_count();
								?>
								<ul class="pmaf-form-templates-categories pmaf-animate-pack-categories">
									<li class="active" data-category="all">
										<div class="pmaf-form-templates-categories-inner">
											<span class="pmaf-form-templates-categories-name"><?php esc_html_e( 'All Templates', 'animated-forms' ) ?></span>
											<span class="pmaf-form-templates-categories-count"><?php echo esc_html( count( $ani_packs ) ); ?></span>
										</div>
									</li>
								<?php
									foreach( $cat_counts as $cat => $count ) {
										if( !empty( $cat ) ) {
								?>
									<li data-category="<?php echo esc_attr( $cat ); ?>">
										<div class="pmaf-form-templates-categories-inner">
											<span class="pmaf-form-templates-categories-name"><?php printf( '%s %s', esc_html( ucfirst( $cat ) ), esc_html__( 'Templates', 'animated-forms' ) ) ?></span>
											<span class="pmaf-form-templates-categories-count"><?php echo esc_html( $count ); ?></span>
										</div>
									</li>
								<?php
										}
									}
								?>
								</ul>
							</div>
						</div>
					</div>
					<div class="pmaf-col">
						<div class="pmaf-import-templates-list-wrap">
							<div id="pmaf-import-pack-list" class="pmaf-import-templates-list">
							
								<?php
									
									$path = pmaf_animated_forms_data()->get_assets_url();
									
									foreach( $ani_packs as $key => $ani_pack ) {
										$pro_status = isset( $ani_pack['pro'] ) && $ani_pack['pro'] == true ? true : false;
										$bg_data = pmaf_animated_forms_data()->get_animated_templates( $ani_pack['bg'] );
										$o_data = pmaf_animated_forms_data()->get_overlay_templates( $ani_pack['o'] );
										$cat = $ani_pack['c'];
								?>
										<div class="pmaf-import-template pmaf-template-item" data-category="<?php echo esc_attr( $cat ); ?>">
											<?php if( $pro_status ) echo '<span class="pmaf-pro-template"><i class="af-pro"></i> '. esc_html__( 'Pro', 'animated-forms' ) .'</span>'; ?>
											<div class="pmaf-import-thumbnail">
												<img src="<?php echo esc_url( $path .'pack/thumb/'. $key .'.jpg' ); ?>" alt="<?php esc_html( $ani_pack['title'] ); ?>" />
											</div>
											<h3 class="pmaf-import-template-name"><?php echo esc_html( $ani_pack['title'] ); ?></h3><p class="hidden"><?php echo esc_html( $ani_pack['title'] ); ?></p>
											<?php if( !$pro_status ) : ?>
											<div class="pmaf-import-template-buttons">
												<a class="pmaf-btn pmaf-primary-btn pmaf-pack-import" href="#" data-bg="<?php echo esc_attr( $ani_pack['bg'] ); ?>" data-overlay="<?php echo esc_attr( $ani_pack['o'] ); ?>" data-fi="<?php echo esc_attr( $ani_pack['fi'] ); ?>"><?php esc_html_e( 'Import', 'animated-forms' ); ?></a>
												<a class="pmaf-btn pmaf-primary-btn pmaf-pack-templates-demo" target="_blank" href="<?php echo esc_url( $ani_pack['demo'] ); ?>"><?php esc_html_e( 'View Demo', 'animated-forms' ); ?></a>
											</div>
											<svg class="pmaf-checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="pmaf-checkmark__circle" cx="26" cy="26" r="25" fill="none"/><path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/></svg>
											<?php else : ?>
											<div class="pmaf-import-template-buttons">
												<a class="pmaf-btn pmaf-primary-btn pmaf-pro-popup" href="<?php echo esc_url( PMAF_PRO_LINK ); ?>" target="_blank"><?php esc_html_e( 'Import', 'animated-forms' ); ?></a>
												<a class="pmaf-btn pmaf-primary-btn pmaf-pack-templates-demo" target="_blank" href="<?php echo esc_url( $ani_pack['demo'] ); ?>"><?php esc_html_e( 'View Demo', 'animated-forms' ); ?></a>
											</div>
											<?php endif; ?>
										</div>
								<?php 
										
									}
								 ?>							
							</div>
						</div>
					</div> <!-- .col -->
				</div>
			</div>		
		</div>
	</div>
</div>