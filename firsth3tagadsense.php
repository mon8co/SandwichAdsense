<?php
/*
Plugin Name: Sandwich Adsense
Plugin URI: https://github.com/j801/firsth3tagadsense
Description: This is a WordPress plugin to insert Google AdSense code in your blog entry. 
Author: Minoru Wada
Version: 2.0.1
Author URI: http://mon8co.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/
define('SPN', "<p>SPONSORED LINK<p/>");

define('H2_REG', "/<h2>[^<]*<\/h2>/i"); //h2Tag
define('H3_REG', "/<h3>[^<]*<\/h3>/i"); //h3Tag
define('H4_REG', "/<h4>[^<]*<\/h4>/i"); //h4Tag

$Fhas = new FirstH3TagAdsense();

add_action('admin_menu',array($Fhas,'my_admin_menu'));

class FirstH3TagAdsense {

	function __construct() {
		add_action( 'plugins_loaded', array( &$this, 'initialize' ) );
	}

	function initialize() {

		add_action('admin_init', array($this,'my_admin_init'));
		add_filter('admin_init', array($this,'add_custom_whitelist_options_fields' ));
		add_filter('the_content', array($this,'add_ads_before_1st_h3'));
	
		if ( function_exists('register_uninstall_hook') )  {
    		register_uninstall_hook(__FILE__, 'FirstH3TagAdsense::my_uninstall_hook');
		}
    }

	function my_admin_menu() {
	add_options_page (
			'Sandwich Adsense',
			'Sandwich Adsense',
			'manage_options',
			'Sandwich Adsense',
			array($this,'my_submenu')
			);
	}

	function my_submenu() { 
		    $options_tag = get_option("tagtype");
		    $options_which = get_option("whichtag");
		    $options_locate = get_option("taglocate");
		?>
		    <div class="wrap">
		    <h1>Sandwich Adsense(Version2.0)</h1>
		    <h2>Adsense Code Setting</h2>
		    <form id="my-submenu-form" method="post" action="">
		    	<?php wp_nonce_field('my-nonce-key','FirstH3TagAdsense'); ?>
		    	<ul>
		    	<li>Target Tag
		    	<select name="tagtype">
		    		<option value="h2tag" <?php selected( $options_tag, "h2tag" ); ?>>H2</option>
					<option value="h3tag" <?php selected( $options_tag, "h3tag" ); ?>>H3</option>
					<option value="h4tag" <?php selected( $options_tag, "h4tag" ); ?>>H4</option>
				</select></li>
				<li>Which Tag
				<select name="whichtag">
		    		<option value="1" <?php selected( $options_which, "1" ); ?>>1st</option>
					<option value="2" <?php selected( $options_which, "2" ); ?>>2nd</option>
					<option value="3" <?php selected( $options_which, "3" ); ?>>3rd</option>
				</select></li>
				<li>Target Place
				<select name="taglocate">
		    		<option value="above" <?php selected( $options_tag, "above" ); ?>>above</option>
					<option value="under" <?php selected( $options_tag, "under" ); ?>>under</option>
				</select></li>
				<li>Setting Code(PC)</li>
				<textarea name="adsense_code" id="adsense_code" cols="100" rows="10"><?php echo esc_textarea(stripslashes(get_option('adsense_code'))); ?></textarea><p/></ul>
				<li>Setting Code(mobile)</li>
				<textarea name="adsense_code_mobile" id="adsense_code" cols="100" rows="10"><?php echo esc_textarea(stripslashes(get_option('adsense_code_mobile'))); ?></textarea><p/></ul>
				<p/>
		  		<input type="submit" value="Save"><div>This Plugin made by Minoru Wada@mon8co(<a href="http://mon8co.com">http://mon8co.com</a>)</div>
		  	</form> 
		  	</div>
		 <?php
	}

	function my_admin_init() {

		if (isset($_POST['adsense_code']) && $_POST['adsense_code']) {
					update_option('adsense_code',stripslashes($_POST['adsense_code']));
		}

		if (isset($_POST['tagtype']) && $_POST['tagtype']) {
	   			update_option('tagtype',$_POST['tagtype']);
	   	}

		if (isset($_POST['whichtag']) && $_POST['whichtag']) {
	   			update_option('whichtag',$_POST['whichtag']);
	   	}   	

	   	if (isset($_POST['taglocate']) && $_POST['taglocate']) {
	   			update_option('taglocate',$_POST['taglocate']);
	   }

		wp_safe_redirect(menu_page_url('my-subemenu',false));
	}

	function add_custom_whitelist_options_fields() {
	    register_setting( 'general', 'adsense_code' );
	}

	function add_ads_before_1st_h3($the_content) {
	  if ( is_single() ) {
	    
	    $ad_template = get_option('adsense_code');
	    
	    preg_match_all( H3_REG, $the_content, $h3results);
	    $h3result = $h3results[0];

	    if ( $h3result ) {
	    	if (get_option('taglocate') == "above") {
	    		if (get_option('whichtag') == "1") {
	    	  			$the_content = str_replace($h3result[0], $ad_template.$h3result[0], $the_content);
	    			} else if (get_option('whichtag') == "2") {
	    				$the_content = str_replace($h3result[1], $ad_template.$h3result[1], $the_content);
	    			} else if (get_option('whichtag') == "3") {
	    				$the_content = str_replace($h3result[2], $ad_template.$h3result[2], $the_content);
	    		}
	    	} else {
	    		if (get_option('whichtag') == "1") {
	    			$the_content = str_replace($h3result[0], $h3result[0].$ad_template, $the_content);
	    		} else if (get_option('whichtag') == "2") {
	    			$the_content = str_replace($h3result[1], $h3result[1].$ad_template, $the_content);
	    		} else if (get_option('whichtag') == "3") {
	    			$the_content = str_replace($h3result[2], $h3result[2].$ad_template, $the_content);	
	    			}
	  			}
			}
		}

	  	return $the_content;
	}

	function my_uninstall_hook() {
	    	delete_option('adsense_seting');
	}
}
?>
