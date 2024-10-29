<?php

class PMAF_Animated_Forms_Data {

	public static $instance = null;
		
	public function __construct() {}
	
	public function get_templates_count() {
		
		$all = $this->get_animated_pack();
		$total = count( $all );
		$cat_arr = []; $count_arr = [];
		foreach( $all as $t ) {
			$c = $t['c'];
			$c_arr = explode( ",", $c );
			if( empty( $count_arr ) ) {
				foreach( $c_arr as $single_cat ) {
					$count_arr[$single_cat] = 1;
				}
			} else {
				foreach( $c_arr as $single_cat ) {
					if( isset( $count_arr[$single_cat] ) ) {
						$count_arr[$single_cat] = $count_arr[$single_cat] + 1;
					} else {
						$count_arr[$single_cat] = 1;
					}
				}
			}
		}
		
		return $count_arr;
		
	}
	
	public function get_animated_pack() {
		
		$data = [			
			'p13' => [ 'c' => 'trending,nature', 'title' => esc_html__( 'Orange Tree with Falling Leaves', 'animated-forms' ), 'bg' => 'a22', 'o' => 'o1', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/orange-tree-with-falling-leaves/' ],			
			'p7' => [ 'c' => 'trending,space', 'title' => esc_html__( 'Cosmic Particles', 'animated-forms' ), 'bg' => '', 'o' => 'o36', 'fi' => 'fi3', 'demo' => 'https://animatedforms.com/demos/cosmic-particles/', 'pro' => true ],
			'p8' => [ 'c' => 'trending,nature', 'title' => esc_html__( 'Snowy Tree', 'animated-forms' ), 'bg' => 'a3', 'o' => 'o21', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/snowy-tree/' ],
			'p6' => [ 'c' => 'trending', 'title' => esc_html__( 'Exploding Balls', 'animated-forms' ), 'bg' => '', 'o' => 'o13', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/exploding-balls-animation/', 'pro' => true ],		
			'p11' => [ 'c' => 'trending,nature', 'new', 'title' => esc_html__( 'Raindrop Mirror', 'animated-forms' ), 'bg' => 'a2', 'o' => 'o20', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/raindrop-mirror/' ],
			'p9' => [ 'c' => 'trending', 'title' => esc_html__( 'Bubble Burst', 'animated-forms' ), 'bg' => 'a4', 'o' => 'o27', 'fi' => 'fi8', 'demo' => 'https://animatedforms.com/demos/bubble-burst/' ],	
			'p1' => [ 'c' => 'water', 'new', 'title' => esc_html__( 'Water Droplet Bliss', 'animated-forms' ), 'bg' => 'a11', 'o' => 'o2', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/water-droplet-bliss/' ],
			'p2' => [ 'c' => 'trending,neon', 'title' => esc_html__( 'Neon Blue Ball Motion', 'animated-forms' ), 'bg' => 'a12', 'o' => 'o28', 'fi' => 'fi3', 'demo' => 'https://animatedforms.com/demos/neon-blue-ball-motion/', 'pro' => true ],
			'p3' => [ 'c' => '', 'new', 'title' => esc_html__( 'Dancing Elements Phone', 'animated-forms' ), 'bg' => 'a13', 'o' => 'o3', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/dancing-elements-phone/' ],
			'p14' => [ 'c' => 'nature', 'title' => esc_html__( 'Orange Tree New with Falling Leaves', 'animated-forms' ), 'bg' => 'a23', 'o' => 'o40', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/tree-background-falling-leaf/' ],
			'p15' => [ 'c' => 'fitness', 'title' => esc_html__( 'Blazing Fitness', 'animated-forms' ), 'bg' => 'a24', 'o' => 'o27', 'fi' => 'fi8', 'demo' => 'https://animatedforms.com/demos/blazing-fitness/' ],			
			'p16' => [ 'c' => 'technology', 'new', 'title' => esc_html__( 'Technology with Rain', 'animated-forms' ), 'bg' => 'a25', 'o' => 'o20', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/technology/' ],
			'p12' => [ 'c' => 'trending,space', 'title' => esc_html__( 'Blue Space with Particles', 'animated-forms' ), 'bg' => 'a7', 'o' => 'o25', 'fi' => 'fi7', 'demo' => 'https://animatedforms.com/demos/blue-space-with-particles/', 'pro' => true ],
			'p4' => [ 'c' => '', 'title' => esc_html__( 'Dancing Elements Phone', 'animated-forms' ), 'bg' => 'a14', 'o' => 'o34', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/cityscape-blasting-balls/' ],
			'p5' => [ 'c' => '', 'title' => esc_html__( 'Cityscape Blasting Balls', 'animated-forms' ), 'bg' => 'a15', 'o' => 'o3', 'fi' => 'fi3', 'demo' => 'https://animatedforms.com/demos/contact-us-dancing-elements/' ],			
			'p10' => [ 'c' => 'trending', 'title' => esc_html__( 'Twinkling Stars', 'animated-forms' ), 'bg' => '', 'o' => 'o24', 'fi' => 'fi5', 'demo' => 'https://animatedforms.com/demos/twinkling-stars-animation/', 'pro' => true ],			
			'p17' => [ 'c' => 'trending,snowfall', 'title' => esc_html__( 'Snowy Family Joy', 'animated-forms' ), 'bg' => 'a27', 'o' => 'o21', 'fi' => 'fi8', 'demo' => 'https://animatedforms.com/demos/snowy-family-joy/' ],
			'p18' => [ 'c' => 'trending,snowfall', 'title' => esc_html__( 'Snowfall Serenity', 'animated-forms' ), 'bg' => 'a28', 'o' => 'o21', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/snowfall-serenity/' ],			
			'p21' => [ 'c' => '', 'title' => esc_html__( 'Laser Light Moving Grids', 'animated-forms' ), 'bg' => 'a31', 'o' => 'o3', 'fi' => 'fi8', 'demo' => 'https://animatedforms.com/demos/laser-light-moving-grids/' ],	
			'p20' => [ 'c' => '', 'title' => esc_html__( 'Luminous Blast', 'animated-forms' ), 'bg' => 'a30', 'o' => 'o27', 'fi' => 'fi8', 'demo' => 'https://animatedforms.com/demos/luminous-blast/' ],
			'p22' => [ 'c' => 'trending,space', 'title' => esc_html__( 'Starlit Tree Magic', 'animated-forms' ), 'bg' => 'a32', 'o' => 'o17', 'fi' => 'fi1', 'demo' => 'https://animatedforms.com/demos/starlit-tree-magic/', 'pro' => true ],
			'p19' => [ 'c' => 'snowfall', 'title' => esc_html__( 'Frosted Forest Falls', 'animated-forms' ), 'bg' => 'a29', 'o' => 'o21', 'fi' => 'fi5', 'demo' => 'https://animatedforms.com/demos/frosted-forest-falls/' ],
			'p23' => [ 'c' => 'space', 'title' => esc_html__( 'Starry Nightfall', 'animated-forms' ), 'bg' => 'a33', 'o' => 'o37', 'fi' => 'fi7', 'demo' => 'https://animatedforms.com/demos/starry-nightfall/', 'pro' => true ],					
			// 23 done
		];
		
		return $data;
		
	}
	
	public function get_animated_templates( $key = '', $count = false ) {
		
		$data = [			
			'a11' => [ 'file' => 'a11.jpg', 'name' => esc_html__( 'Water Wave', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/water-droplet-bliss/' ],
			'a12' => [ 'file' => 'a12.jpg', 'name' => esc_html__( 'Contact Form Background', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/neon-blue-ball-motion/', 'pro' => true ],
			'a13' => [ 'file' => 'a13.jpg', 'name' => esc_html__( 'Dancing Elements Phone', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/dancing-elements-phone/',],
			'a14' => [ 'file' => 'a14.jpg', 'name' => esc_html__( 'Cityscape Blasting Balls', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/cityscape-blasting-balls/',],
			'a15' => [ 'file' => 'a15.jpg', 'name' => esc_html__( 'Contact Us Dancing Elements', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/contact-us-dancing-elements/',],
			'a3' => [ 'file' => 'a3.jpg', 'name' => esc_html__( 'Snowy Tree', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/snowy-tree/',],
			'a4' => [ 'file' => 'a4.jpg', 'name' => esc_html__( 'Colour Bubbles', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/bubble-burst/',],
			'a2' => [ 'file' => 'a2.jpg', 'name' => esc_html__( 'Pine Tree', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/raindrop-mirror/',],
			'a7' => [ 'file' => 'a7.jpg', 'name' => esc_html__( 'Space', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/blue-space-with-particles-2/',],	
			'a22' => [ 'file' => 'a22.jpg', 'name' => esc_html__( 'Orange Trees', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/orange-tree-with-falling-leaves/',],
			'a23' => [ 'file' => 'a23.jpg', 'name' => esc_html__( 'Orange Trees New', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/orange-tree-with-falling-leaves/',],
			'a24' => [ 'file' => 'a24.jpg', 'name' => esc_html__( 'Fitness Women', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/blazing-fitness/',],
			'a25' => [ 'file' => 'a25.jpg', 'name' => esc_html__( 'Technology', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/technology/',],
			
			'a27' => [ 'file' => 'a27.jpg', 'name' => esc_html__( 'Snow Tourism', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/snowy-family-joy/',],
			'a28' => [ 'file' => 'a28.jpg', 'name' => esc_html__( 'Snow Mountain', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/snowfall-serenity/',],
			'a29' => [ 'file' => 'a29.jpg', 'name' => esc_html__( 'Snow Trees', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/frosted-forest-falls/',],
			'a30' => [ 'file' => 'a30.jpg', 'name' => esc_html__( 'Laser Light', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/luminous-blast/',],
			'a31' => [ 'file' => 'a31.jpg', 'name' => esc_html__( 'Laser Light Diamond', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/laser-light-moving-grids/',],
			'a32' => [ 'file' => 'a32.jpg', 'name' => esc_html__( 'Night Sky', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/starlit-tree-magic/',],
			'a33' => [ 'file' => 'a33.jpg', 'name' => esc_html__( 'Night Sky 1', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/starry-nightfall/',],
			
			'a34' => [ 'file' => 'a34.jpg', 'name' => esc_html__( 'Purple Spotlight', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a35' => [ 'file' => 'a35.jpg', 'name' => esc_html__( 'Afternoon Sun', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a36' => [ 'file' => 'a36.jpg', 'name' => esc_html__( 'Autumn Forest Morning', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a37' => [ 'file' => 'a37.jpg', 'name' => esc_html__( 'Calm River', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a38' => [ 'file' => 'a38.jpg', 'name' => esc_html__( 'Colorful Abstract', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a39' => [ 'file' => 'a39.jpg', 'name' => esc_html__( 'Crescent Moon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a40' => [ 'file' => 'a40.jpg', 'name' => esc_html__( 'Floral Background', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a41' => [ 'file' => 'a41.jpg', 'name' => esc_html__( 'Forest Canopy', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a42' => [ 'file' => 'a42.jpg', 'name' => esc_html__( 'Galaxy Stars', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a43' => [ 'file' => 'a43.jpg', 'name' => esc_html__( 'Galaxy', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a44' => [ 'file' => 'a44.jpg', 'name' => esc_html__( 'Grass Heart', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a45' => [ 'file' => 'a45.jpg', 'name' => esc_html__( 'Lantern Fireflies', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a46' => [ 'file' => 'a46.jpg', 'name' => esc_html__( 'Leaning Tree', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a47' => [ 'file' => 'a47.jpg', 'name' => esc_html__( 'Lighthouse Ocean', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a48' => [ 'file' => 'a48.jpg', 'name' => esc_html__( 'Milky Way Galaxy', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a49' => [ 'file' => 'a49.jpg', 'name' => esc_html__( 'Million Stars', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a50' => [ 'file' => 'a50.jpg', 'name' => esc_html__( 'Nebula Sky', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a51' => [ 'file' => 'a51.jpg', 'name' => esc_html__( 'Night Moon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a52' => [ 'file' => 'a52.jpg', 'name' => esc_html__( 'Pop Art Background', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a53' => [ 'file' => 'a53.jpg', 'name' => esc_html__( 'Riverbank Mountains', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a54' => [ 'file' => 'a54.jpg', 'name' => esc_html__( 'Stars At Galaxy', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a55' => [ 'file' => 'a55.jpg', 'name' => esc_html__( 'Stormy Island', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a56' => [ 'file' => 'a56.jpg', 'name' => esc_html__( 'Thunder Lights', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a57' => [ 'file' => 'a57.jpg', 'name' => esc_html__( 'Top View Beach', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a58' => [ 'file' => 'a58.jpg', 'name' => esc_html__( 'Universe Constillations', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a59' => [ 'file' => 'a59.jpg', 'name' => esc_html__( 'Waves', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a60' => [ 'file' => 'a60.jpg', 'name' => esc_html__( 'Wide Beach View', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a61' => [ 'file' => 'a61.jpg', 'name' => esc_html__( '3D Code View', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a62' => [ 'file' => 'a62.jpg', 'name' => esc_html__( 'Beach', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a63' => [ 'file' => 'a63.jpg', 'name' => esc_html__( 'Black Starry Sky', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a64' => [ 'file' => 'a64.jpg', 'name' => esc_html__( 'Blurred Grass', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a65' => [ 'file' => 'a65.jpg', 'name' => esc_html__( 'Canopy Sunset', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a66' => [ 'file' => 'a66.jpg', 'name' => esc_html__( 'Cherry Blossom Moon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a67' => [ 'file' => 'a67.jpg', 'name' => esc_html__( 'Christmas Bokeh', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a68' => [ 'file' => 'a68.jpg', 'name' => esc_html__( 'Earth From Space', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a69' => [ 'file' => 'a69.jpg', 'name' => esc_html__( 'Eiffer Tower', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a70' => [ 'file' => 'a70.jpg', 'name' => esc_html__( 'Fairy Sky', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a71' => [ 'file' => 'a71.jpg', 'name' => esc_html__( 'Foggy Sunrise', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a72' => [ 'file' => 'a72.jpg', 'name' => esc_html__( 'Full Moon Art', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a73' => [ 'file' => 'a73.jpg', 'name' => esc_html__( 'Galaxy Stars View', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a74' => [ 'file' => 'a74.jpg', 'name' => esc_html__( 'Galaxy View', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a75' => [ 'file' => 'a75.jpg', 'name' => esc_html__( 'Girl Grabbing Mobile', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a76' => [ 'file' => 'a76.jpg', 'name' => esc_html__( 'Lake Sunrise', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a77' => [ 'file' => 'a77.jpg', 'name' => esc_html__( 'Mobile Apps Icon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a78' => [ 'file' => 'a78.jpg', 'name' => esc_html__( 'Morning Beach', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a79' => [ 'file' => 'a79.jpg', 'name' => esc_html__( 'Nature Background', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a80' => [ 'file' => 'a80.jpg', 'name' => esc_html__( 'Ocean Bay', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a81' => [ 'file' => 'a81.jpg', 'name' => esc_html__( 'Office Employee Working', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a82' => [ 'file' => 'a82.jpg', 'name' => esc_html__( 'Office Employees', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a83' => [ 'file' => 'a83.jpg', 'name' => esc_html__( 'Orange Telephone Icon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a84' => [ 'file' => 'a84.jpg', 'name' => esc_html__( 'Planets Near Mountain', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a85' => [ 'file' => 'a85.jpg', 'name' => esc_html__( 'River Boats', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a86' => [ 'file' => 'a86.jpg', 'name' => esc_html__( 'Rocky Ocean Bay', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a87' => [ 'file' => 'a87.jpg', 'name' => esc_html__( 'Romantic Couple', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a88' => [ 'file' => 'a88.jpg', 'name' => esc_html__( 'Space Icon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a89' => [ 'file' => 'a89.jpg', 'name' => esc_html__( 'Space Stars', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a90' => [ 'file' => 'a90.jpg', 'name' => esc_html__( 'Starry Galaxy', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a91' => [ 'file' => 'a91.jpg', 'name' => esc_html__( 'Sunrays Road', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a92' => [ 'file' => 'a92.jpg', 'name' => esc_html__( 'Sunset Field', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a93' => [ 'file' => 'a93.jpg', 'name' => esc_html__( 'White Telephone Drawing', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a94' => [ 'file' => 'a94.jpg', 'name' => esc_html__( 'Accumulated Waves', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a95' => [ 'file' => 'a95.jpg', 'name' => esc_html__( 'Autumn Leaves Fall', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a96' => [ 'file' => 'a96.jpg', 'name' => esc_html__( 'Blurred Citylights', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a97' => [ 'file' => 'a97.jpg', 'name' => esc_html__( 'Butterflies - Mushroom Patch', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a98' => [ 'file' => 'a98.jpg', 'name' => esc_html__( 'Cloud Data Icon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a99' => [ 'file' => 'a99.jpg', 'name' => esc_html__( 'Colorful Geometric', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a100' => [ 'file' => 'a100.jpg', 'name' => esc_html__( 'Couple Workout', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a101' => [ 'file' => 'a101.jpg', 'name' => esc_html__( 'Dark Clouds', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a102' => [ 'file' => 'a102.jpg', 'name' => esc_html__( 'Flocking Tree', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a103' => [ 'file' => 'a103.jpg', 'name' => esc_html__( 'Foggy Road', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a104' => [ 'file' => 'a104.jpg', 'name' => esc_html__( 'Forest Canopy Sunrise', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a105' => [ 'file' => 'a105.jpg', 'name' => esc_html__( 'Forest Sunrise', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a106' => [ 'file' => 'a106.jpg', 'name' => esc_html__( 'Future AI World', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a107' => [ 'file' => 'a107.jpg', 'name' => esc_html__( 'Girls Workout', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a108' => [ 'file' => 'a108.jpg', 'name' => esc_html__( 'Gold Waves', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a109' => [ 'file' => 'a109.jpg', 'name' => esc_html__( 'Laptop Lock Icon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a110' => [ 'file' => 'a110.jpg', 'name' => esc_html__( 'Lightning Storm', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a111' => [ 'file' => 'a111.jpg', 'name' => esc_html__( 'Loarre Castle', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a112' => [ 'file' => 'a112.jpg', 'name' => esc_html__( 'Lock Icon', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a113' => [ 'file' => 'a113.jpg', 'name' => esc_html__( 'Man Abs', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a114' => [ 'file' => 'a114.jpg', 'name' => esc_html__( 'Morning Team Workout', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a115' => [ 'file' => 'a115.jpg', 'name' => esc_html__( 'Night Sky', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a116' => [ 'file' => 'a116.jpg', 'name' => esc_html__( 'Server Block', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a117' => [ 'file' => 'a117.jpg', 'name' => esc_html__( 'Shooting Star', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a118' => [ 'file' => 'a118.jpg', 'name' => esc_html__( 'Snowy Park', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a119' => [ 'file' => 'a119.jpg', 'name' => esc_html__( 'Star Trails Sunsut', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a120' => [ 'file' => 'a120.jpg', 'name' => esc_html__( 'Star Trails', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a121' => [ 'file' => 'a121.jpg', 'name' => esc_html__( 'Sunset Sky', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],
			'a122' => [ 'file' => 'a122.jpg', 'name' => esc_html__( 'Tech City', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/',],

		];
		
		if( $count ) return count( $data );
		
		if( $key ) return isset( $data[$key] ) ? $data[$key] : '';
		
		return $data;
		
	}
	
	public function get_overlay_templates( $key = '', $count = false ) {
		
		$data = [
			'o21' => [ 'file' => 'o21.jpg', 'name' => esc_html__( 'Snow Fall', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o3' => [ 'file' => 'o3.jpg', 'name' => esc_html__( 'Dancing Elements', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o9' => [ 'file' => 'o9.jpg', 'name' => esc_html__( 'Bubbles', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o6' => [ 'file' => 'o6.jpg', 'name' => esc_html__( 'Moving Grids', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o17' => [ 'file' => 'o17.jpg', 'name' => esc_html__( 'Falling Stars', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o7' => [ 'file' => 'o7.jpg', 'name' => esc_html__( 'Zoomout Squares', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o12' => [ 'file' => 'o12.jpg', 'name' => esc_html__( 'Blue Particles', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o10' => [ 'file' => 'o10.jpg', 'name' => esc_html__( 'Moving Colour Balls', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o19' => [ 'file' => 'o19.jpg', 'name' => esc_html__( 'Colourful Hearts', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o20' => [ 'file' => 'o20.jpg', 'name' => esc_html__( 'Rain Drops', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o24' => [ 'file' => 'o24.jpg', 'name' => esc_html__( 'Night Stars', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o25' => [ 'file' => 'o25.jpg', 'name' => esc_html__( 'Dust Particles', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o26' => [ 'file' => 'o26.jpg', 'name' => esc_html__( 'Fire Fly', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o2' => [ 'file' => 'o2.jpg', 'name' => esc_html__( 'Water Droplet', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o1' => [ 'file' => 'o1.jpg', 'name' => esc_html__( 'Falling Leaf', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o13' => [ 'file' => 'o13.jpg', 'name' => esc_html__( 'Blasting Balls (Non-Transparent)', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/exploding-balls-animation/', 'pro' => true ],
			'o18' => [ 'file' => 'o18.jpg', 'name' => esc_html__( 'Colorful Waves (Non-Transparent)', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],			
			'o27' => [ 'file' => 'o27.jpg', 'name' => esc_html__( 'Colourful Balls Moving', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o28' => [ 'file' => 'o28.jpg', 'name' => esc_html__( 'Blue Lighting Balls Moving', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o29' => [ 'file' => 'o29.jpg', 'name' => esc_html__( 'Snowfall Flake', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o30' => [ 'file' => 'o30.jpg', 'name' => esc_html__( 'Snowfall with Light', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o31' => [ 'file' => 'o31.jpg', 'name' => esc_html__( 'Moving Stars', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o32' => [ 'file' => 'o32.jpg', 'name' => esc_html__( 'Rain Thunder Lightning', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o33' => [ 'file' => 'o33.jpg', 'name' => esc_html__( 'Black Sky Crackers', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o34' => [ 'file' => 'o34.jpg', 'name' => esc_html__( 'Blasting Colour Balls', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o35' => [ 'file' => 'o35.jpg', 'name' => esc_html__( 'Colour Bubbles', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o36' => [ 'file' => 'o36.jpg', 'name' => esc_html__( 'Space Particles', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o37' => [ 'file' => 'o37.jpg', 'name' => esc_html__( 'Falling Stars', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'o38' => [ 'file' => 'o38.jpg', 'name' => esc_html__( 'Fire', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o39' => [ 'file' => 'o39.jpg', 'name' => esc_html__( 'Simple Rain Fall', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'o40' => [ 'file' => 'o40.jpg', 'name' => esc_html__( 'Falling Leaf New', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
		];
		
		if( $count ) return count( $data );
		
		if( $key ) return isset( $data[$key] ) ? $data[$key] : '';
		
		return $data;
		
	}
	
	public function get_form_inner_templates( $key = '', $count = false ) {
		
		$data = [
			'fi1' => [ 'file' => 'fi1.jpg', 'name' => esc_html__( 'Blurred Mirror', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'fi2' => [ 'file' => 'fi2.jpg', 'name' => esc_html__( 'Blue Bubbles', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/', 'pro' => true ],
			'fi3' => [ 'file' => 'fi3.jpg', 'name' => esc_html__( 'Transparent Fields', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'fi4' => [ 'file' => 'fi4.jpg', 'name' => esc_html__( 'Dark Slide Style', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'fi5' => [ 'file' => 'fi5.jpg', 'name' => esc_html__( 'Classic White', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'fi6' => [ 'file' => 'fi6.jpg', 'name' => esc_html__( 'Elephant Dark', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'fi7' => [ 'file' => 'fi7.jpg', 'name' => esc_html__( 'Dark Transparent', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
			'fi8' => [ 'file' => 'fi8.jpg', 'name' => esc_html__( 'Light Transparent', 'animated-forms' ), 'demo' => 'https://animatedforms.com/demos/' ],
		];
		
		if( $count ) return count( $data );
		
		if( $key ) return isset( $data[$key] ) ? $data[$key] : '';
		
		return $data;
		
	}
	
	public function get_new_forms_count() {
		
		$all = $this->get_animated_forms();
		$total = count( $all );
		$cat_arr = []; $count_arr = []; $n = 1;
		foreach( $all as $key => $t ) {
			$c = $t['category'];
			if( empty( $count_arr ) ) {
				$count_arr[$c] = 1;
			} else {
				if( isset( $count_arr[$c] ) ) {
					$count_arr[$c] = $count_arr[$c] + 1;
				} else {
					$count_arr[$c] = 1;
				}
				$n++;
			}
		}
		
		$count_arr['all'] = $n;
		return $count_arr;
		
	}
	
	public function get_animated_forms() {
		
		$data = [
			'f1' => [ 'file' => 'f1.jpg', 'category' => 'simple', 'name' => esc_html__( 'Simple Contact Form', 'animated-forms' ), 'desc' => esc_html__( 'Collect the names, emails, and messages from site visitors that need to talk to you.', 'animated-forms' ) ],
			'f2' => [ 'file' => 'f2.jpg', 'category' => 'appointment', 'name' => esc_html__( 'Appointment Form', 'animated-forms' ), 'desc' => esc_html__( 'Schedule appointments by collecting details such as name, email, phone number, and address.', 'animated-forms' ) ],
			'f3' => [ 'file' => 'f3.jpg', 'category' => 'appointment', 'name' => esc_html__( 'Tutor Appointment Form', 'animated-forms' ), 'desc' => esc_html__( 'Collects student\'s name, parent\'s details, and preferred tutoring type to better serve educational needs.', 'animated-forms' ) ],
			'f4' => [ 'file' => 'f4.jpg', 'category' => 'registration', 'name' => esc_html__( 'New Customer Registration Form', 'animated-forms' ), 'desc' => esc_html__( 'Facilitates onboarding by collecting full name, email, contact details, and address with mandatory fields.', 'animated-forms' ) ],
			'f5' => [ 'file' => 'f5.jpg', 'category' => 'registration', 'name' => esc_html__( 'Business Registration Form', 'animated-forms' ), 'desc' => esc_html__( 'Officially register a business, gathering owner\'s details, business name, contact, and address.', 'animated-forms' ) ],
			'f6' => [ 'file' => 'f6.jpg', 'category' => 'contact form', 'name' => esc_html__( 'Quick Emergency Contact Form', 'animated-forms' ), 'desc' => esc_html__( 'Medical professionals quickly gather essential details from the form, including name, email, phone, relationship, and message.', 'animated-forms' ) ],
			'f7' => [ 'file' => 'f7.jpg', 'category' => 'registration', 'name' => esc_html__( 'Photo Contest Entry Form', 'animated-forms' ), 'desc' => esc_html__( 'Participants can provide name, email, comments, and photos for evaluation and recognition in the contest.', 'animated-forms' ) ],
			'f8' => [ 'file' => 'f8.jpg', 'category' => 'newsletter', 'name' => esc_html__( 'Petition Letter Form', 'animated-forms' ), 'desc' => esc_html__( 'Raise a request to address your concern to authorities by providing details such as name, title, organization, subject, and body of the petition.', 'animated-forms' ) ],
			'f9' => [ 'file' => 'f9.jpg', 'category' => 'newsletter', 'name' => esc_html__( 'Casting Fill-Out Form', 'animated-forms' ), 'desc' => esc_html__( 'The form collects essential information such as name, email, address, casting roles, and a cover letter from models.', 'animated-forms' ) ],
			'f10' => [ 'file' => 'f10.jpg', 'category' => 'contact form', 'name' => esc_html__( 'Real Estate Contact Form', 'animated-forms' ), 'desc' => esc_html__( 'Gather information from buyers, including their names, contact details, preferred areas, and referrals to facilitate property sales.', 'animated-forms' ) ],
			'f11' => [ 'file' => 'f11.jpg', 'category' => 'registration', 'name' => esc_html__( 'Automobile Information Form', 'animated-forms' ), 'desc' => esc_html__( 'This form ensures that all necessary information is accurately captured for various purposes such as insurance, registration, sale, or maintenance.', 'animated-forms' ) ],
			'f12' => [ 'file' => 'f12.jpg', 'category' => 'invitation', 'name' => esc_html__( 'Party Invitation Form', 'animated-forms' ), 'desc' => esc_html__( 'To ensure we have all the details needed to make this event a success, please take a few moments to fill out this form.', 'animated-forms' ) ],
			'f13' => [ 'file' => 'f13.jpg', 'category' => 'registration', 'name' => esc_html__( 'Meeting Room Registration Form', 'animated-forms' ), 'desc' => esc_html__( 'This form streamlines the reservation process, ensuring that all necessary information is collected to prevent scheduling conflicts and to make sure that the room is set up to meet the needs of the meeting participants.', 'animated-forms' ) ],
			'f14' => [ 'file' => 'f14.jpg', 'category' => 'notes', 'name' => esc_html__( 'Lecture Notes Form', 'animated-forms' ), 'desc' => esc_html__( 'This form provides a structured format to capture essential details, making it easier to review and study the material later.', 'animated-forms' ) ],
			'f15' => [ 'file' => 'f15.jpg', 'category' => 'business', 'name' => esc_html__( 'Constant Contact GDPR Form', 'animated-forms' ), 'desc' => esc_html__( 'Our GDPR form is designed to ensure that your contact information and preferences are collected, stored, and used in accordance with the strict data privacy standards set by the European Union.', 'animated-forms' ) ],
			'f16' => [ 'file' => 'f16.jpg', 'category' => 'business', 'name' => esc_html__( 'Select a Recipient Contact Form', 'animated-forms' ), 'desc' => esc_html__( 'This form allows you to choose a specific recipient for your message or inquiry. It ensures your communication reaches the intended person or department efficiently.', 'animated-forms' ) ],
			'f17' => [ 'file' => 'f17.jpg', 'category' => 'business', 'name' => esc_html__( 'GetResponse Signup Contact Form', 'animated-forms' ), 'desc' => esc_html__( 'Welcome to our GetResponse Signup Contact Form! Please fill out the form below to join our community and gain access to exclusive updates, newsletters, and special offers.', 'animated-forms' ) ],
			'f18' => [ 'file' => 'f18.jpg', 'category' => 'business', 'name' => esc_html__( 'ConvertKit Signup Contact Form', 'animated-forms' ), 'desc' => esc_html__( 'Creating a compelling description for a ConvertKit Signup Contact Form involves emphasizing the benefits of subscribing, providing clear instructions.', 'animated-forms' ) ],
			'f19' => [ 'file' => 'f19.jpg', 'category' => 'order', 'name' => esc_html__( 'T-Shirt Order Form', 'animated-forms' ), 'desc' => esc_html__( 'Please fill out the form below to place your order. Ensure all information is accurate to avoid any delays in processing your request.', 'animated-forms' ) ],
			'f20' => [ 'file' => 'f20.jpg', 'category' => 'order', 'name' => esc_html__( 'Work Order Request Form', 'animated-forms' ), 'desc' => esc_html__( 'Please fill out the required fields to ensure that your request is processed promptly and efficiently.', 'animated-forms' ) ],
			'f21' => [ 'file' => 'f21.jpg', 'category' => 'order', 'name' => esc_html__( 'Advertisement Order Form', 'animated-forms' ), 'desc' => esc_html__( 'An Advertisement Order Form is a document used by businesses to collect all necessary details from clients looking to place advertisements.', 'animated-forms' ) ],
			'f22' => [ 'file' => 'f22.jpg', 'category' => 'order', 'name' => esc_html__( 'Gift Card Order Form', 'animated-forms' ), 'desc' => esc_html__( 'Whether you\'re looking for the perfect gift for a loved one, a friend, or a colleague, our gift cards are the ideal choice. Simply fill out the form below to order your gift cards.', 'animated-forms' ) ],
			'f23' => [ 'file' => 'f23.jpg', 'category' => 'order', 'name' => esc_html__( 'Return Order Form', 'animated-forms' ), 'desc' => esc_html__( 'It ensures that all necessary information is collected, allowing for efficient handling and processing of returns.', 'animated-forms' ) ],
			'f24' => [ 'file' => 'f24.jpg', 'category' => 'enquiry', 'name' => esc_html__( 'Customer Enquiry Form', 'animated-forms' ), 'desc' => esc_html__( 'The form typically includes fields for contact details, a description of the enquiry, and any relevant details that can help the customer service team address the issue effectively.', 'animated-forms' ) ],
			'f25' => [ 'file' => 'f25.jpg', 'category' => 'enquiry', 'name' => esc_html__( 'User Review Form', 'animated-forms' ), 'desc' => esc_html__( ' Please take a moment to share your thoughts and experiences with our product/service. Your input helps us to continuously improve and provide you with the best possible experience.', 'animated-forms' ) ],
			'f26' => [ 'file' => 'f26.jpg', 'category' => 'enquiry', 'name' => esc_html__( 'Business Inquiry Form', 'animated-forms' ), 'desc' => esc_html__( ' Please fill out the form below to provide us with the necessary details regarding your inquiry. Our team will review your submission and get back to you as soon as possible.', 'animated-forms' ) ],
			'f27' => [ 'file' => 'f27.jpg', 'category' => 'education', 'name' => esc_html__( 'Grade Book Form', 'animated-forms' ), 'desc' => esc_html__( ' This form typically includes sections for listing student names, subjects or courses, and various assessment criteria, such as assignments, quizzes, exams, participation, and projects.', 'animated-forms' ) ],
			'f28' => [ 'file' => 'f28.jpg', 'category' => 'registration', 'name' => esc_html__( 'Conference Registration Form', 'animated-forms' ), 'desc' => esc_html__( ' Please fill out the form below to secure your spot at the conference. The registration process is quick and easy, designed to gather all the necessary information to ensure your participation is seamless.', 'animated-forms' ) ],
			'f29' => [ 'file' => 'f29.jpg', 'category' => 'registration', 'name' => esc_html__( 'Oscars Party RSVP Form', 'animated-forms' ), 'desc' => esc_html__( ' Please fill out the form below to confirm your attendance and help us ensure a spectacular event for everyone.', 'animated-forms' ) ],
			'f30' => [ 'file' => 'f30.jpg', 'category' => 'registration', 'name' => esc_html__( 'Sponsor Form', 'animated-forms' ), 'desc' => esc_html__( ' The form typically includes sections for sponsor details, sponsorship levels, benefits, and terms of agreement.', 'animated-forms' ) ],
			'f31' => [ 'file' => 'f31.jpg', 'category' => 'registration', 'name' => esc_html__( 'School Sponsor Form', 'animated-forms' ), 'desc' => esc_html__( ' It helps sponsors understand the needs of the school and the benefits they will receive by supporting educational programs, events, or facilities.', 'animated-forms' ) ],
			'f32' => [ 'file' => 'f32.jpg', 'category' => 'registration', 'name' => esc_html__( 'Exhibition Booking Form', 'animated-forms' ), 'desc' => esc_html__( ' This form serves as a comprehensive tool to gather all necessary information from exhibitors, ensuring a seamless and efficient booking process.', 'animated-forms' ) ],
		];
		
		return $data;
		
	}
	
	public function get_assets_url() {
		
		return 'https://animatedforms.com/animated-forms/';
		
	}
	
	public function get_api_url() {
		
		return 'https://animatedforms.com/wp-json/transformer/v1/pmaf';
		
	}
	
	public function api_call( $body_args ) { //pmaf_animated_forms_data()->api_call()
		
		global $wp_version;
		
		$args = array(
			'user-agent' => 'WordPress/' . $wp_version . '; ' . esc_url( home_url() ),
			'method'      => 'POST',
			'timeout'     => 120,
			'sslverify' => false,
			'httpversion' => '1.0',
			'body'       => $body_args
		);
		
		$api_url = $this->get_api_url(); 
		$response = wp_remote_post( esc_url_raw( $api_url ), $args ); 

		// Check the response code.
		$response_code    = wp_remote_retrieve_response_code( $response );
		$response_message = wp_remote_retrieve_response_message( $response );

		if ( empty( $response_code ) && is_wp_error( $response ) ) {
			return $response;
		}

		if ( 200 !== $response_code && ! empty( $response_message ) ) {
			return new WP_Error( $response_code, $response_message );
		}
		if ( 200 !== $response_code ) {
			return new WP_Error( $response_code, esc_html__( 'An unknown API error occurred.', 'ai-addons' ) );
		}
		$data = json_decode( wp_remote_retrieve_body( $response ), true );
		
		if ( null === $data ) {
			return new WP_Error( 'api_error', esc_html__( 'An unknown API error occurred.', 'ai-addons' ) );
		}
				
		if( isset( $data['error'] ) ) {
			return new WP_Error( 'api_error', esc_html__( 'An unknown API error occurred.', 'ai-addons' ) );
		}
		
		if( isset( $data['status'] ) && isset( $data['status'] ) == 'success' ) {
			return $data['response'];
		}

		return false;
		
	}
	
	public static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
	
}

function pmaf_animated_forms_data() {
	return PMAF_Animated_Forms_Data::get_instance();
}