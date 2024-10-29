<?php
/*
 * Dashboard
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;
?>
	
<div class="wrap">

	<h1 class="hidden"><?php esc_html_e( 'Animated Forms Dashboard', 'animated-forms' ); ?></h1>
	
	<div class="pmaf-wrap-inner">
	
		<div class="pmaf-media pmaf-logo-wrap">
			<img src="<?php echo esc_url( PMAF_URL . 'admin/assets/images/logo.png' ); ?>">
			<h1><?php esc_html_e( 'Animated Forms', 'animated-forms' ); ?></h1>
		</div>
	
		<div class="pmaf-welcome-wrap">
					
			<div class="pmaf-admin-box">
				<h2><?php esc_html_e( 'Why Choose Our Animated Login Forms Plugin?', 'animated-forms' ); ?></h2>
				<p><?php esc_html_e( 'Our solution stands out with a prebuilt aesthetic design and an advanced AJAX login form, seamlessly combining functionality with a user-friendly interface. Our algorithm ensures hassle-free registration form creation, offering options for easy setup of Registration, Login, and Forget Password forms to save you time.', 'animated-forms' ) ?></p>
			</div>
			
			<div class="pmaf-admin-box">
				<div class="pmaf-row">
					<div class="pmaf-col-6">
						<h2><?php esc_html_e( 'Key Features:', 'animated-forms' ); ?></h2>
						<ul class="pmaf-key-features">
							<li><?php printf( '<strong>%s</strong> %s', esc_html__( 'User-Friendly Interface:', 'animated-forms' ), esc_html__( 'Experience a visually appealing and intuitive form layout that effortlessly guides users through the registration process.', 'animated-forms' ) ); ?></li>
							<li><?php printf( '<strong>%s</strong> %s', esc_html__( 'Customization:', 'animated-forms' ), esc_html__( 'Tailor your forms to perfection with the plugin\'s easy customization options, allowing you to personalize field labels, layout preferences, and more to align seamlessly with your specific needs and branding preferences.', 'animated-forms' ) ); ?></li>
							<li><?php printf( '<strong>%s</strong> %s', esc_html__( 'Security:', 'animated-forms' ), esc_html__( 'The plugin\'s robust security features ensure the utmost protection for your data and maintain a secure environment.', 'animated-forms' ) ); ?></li>
							<li><?php printf( '<strong>%s</strong> %s', esc_html__( 'Easy Registration:', 'animated-forms' ), esc_html__( 'Set up a Registration form seamlessly in minutes. Crafted to be exceptionally user-friendly, it demands no coding expertise, ensuring a smooth and efficient experience for all users.', 'animated-forms' ) ); ?></li>
						</ul>
					</div>
					<div class="pmaf-col-6">
						<h2><?php esc_html_e( 'Shortcodes:', 'animated-forms' ); ?></h2>
						<ul class="pmaf-key-features">
							<li><?php printf( '<strong>%s</strong> %s', '[pmaf_login_full_form]', esc_html__( 'It has Login, Register and Forget forms.', 'animated-forms' ) ); ?></li>
							<li><?php esc_html_e( 'Embed this short code in a Post, Page, or any content to showcase a comprehensive login form on your WordPress site. This feature facilitates New User Registrations and allows existing customers to Log In seamlessly using their username and password. Also, it includes \'Lost Password\' functionality, assisting users who have forgotten their login credentials.', 'animated-forms' ); ?></li>
							<li><?php esc_html_e( 'Ensure that \'Anyone can register\' is enabled in the General Settings for proper functionality', 'animated-forms' ); ?></li>
						</ul>
					</div>
				</div>
			</div>
			
		</div> <!-- .pmaf-welcome-wrap -->
		
		<!-- 
		<div class="pmaf-footer">
			<div class="pmaf-flex flex-wrap">
				<a href="https://plugin.net/downloads/animated-forms/" target="_blank">
					<div class="pmaf-list">								
						<div class="pmaf-list-left"><span class="dashicons dashicons-media-document"></span></div>
						<div class="pmaf-list-right">
							<h3><?php esc_html_e( 'View Documentation', 'animated-forms' ); ?></h3>
						</div>
					</div>
				</a>
				<a href="https://plugin.net/downloads/animated-forms/" target="_blank">
					<div class="pmaf-list">								
						<div class="pmaf-list-left"><span class="dashicons dashicons-email"></span></div>
						<div class="pmaf-list-right">
							<h3><?php esc_html_e( 'Need Help?', 'animated-forms' ); ?></h3>
						</div>
					</div>
				</a>
				<a href="https://plugin.net/downloads/animated-forms/?pm-review=1" target="_blank">
					<div class="pmaf-list">								
						<div class="pmaf-list-left"><span class="dashicons dashicons-star-filled"></span></div>
						<div class="pmaf-list-right">
							<h3><?php esc_html_e( 'Give Us Rating', 'animated-forms' ); ?></h3>
						</div>
					</div>
				</a>
			</div>
		</div>
		-->
	
	</div> <!-- .pmaf-wrap-inner -->
	
</div> <!-- .wrap -->
<?php 