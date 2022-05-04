<?php
/*
Plugin Name: Workout Manager
Plugin URI: 
Description: Build your workout plan for your athletes in a minute.
Version: 0.0.1
Author: Valentin Guerchet
Copyright 2021 Valentin Guerchet
Text Domain: workout_manager
Domain Path: /languages
*/

define( 'WORKOUT_MANAGER_LOADER'	, __FILE__ );
define( 'WORKOUT_MANAGER_DIR'		, dirname(__FILE__) );
define( 'WORKOUT_MANAGER_URL'		, plugins_url('/', __FILE__) );

// ===================== Autoloader des classes du plugin avec namespace  =====================
function workout_manager_autoloader( $class_name ) {

	/* if ( false !== strpos( $class_name, 'workout_manager\\' ) ) {

		$class_name = str_replace("workout_manager\\", "", $class_name);
		$class_name = str_replace("\\", "/", $class_name);

		$classFile 	= WORKOUT_MANAGER_DIR . '/' .$class_name . '.class.php';
		
        if (file_exists($classFile)) require_once $classFile;
        else die("Missing file class : ".$classFile);
	   
	} */
	
	require_once( WORKOUT_MANAGER_DIR . '/cpt/workout/cpt.class.php');
	require_once( WORKOUT_MANAGER_DIR . '/cpt/contract/cpt.class.php');

}
spl_autoload_register( 'workout_manager_autoloader' );


// ===================== Initialisation du plugin =====================
function workout_manager_plugins_loaded(){

	require_once( WORKOUT_MANAGER_DIR . '/includes/contract_form.php');
	require_once( WORKOUT_MANAGER_DIR . "/includes/functions.php"  );
	require_once( WORKOUT_MANAGER_DIR . "/includes/hooks.php"  );
	require_once( WORKOUT_MANAGER_DIR . "/includes/constants.php"  );
	require_once( WORKOUT_MANAGER_DIR . '/src/ajax.php' );
	require_once( WORKOUT_MANAGER_DIR . "/src/acf.php");
	
	//if(is_admin())	new \workout_manager\back\main();

	\workout_manager\get_plugin_version();

	require_once( WORKOUT_MANAGER_DIR . "/cpt/functions.inc.php"  );
		
}
add_action( 'plugins_loaded', 'workout_manager_plugins_loaded' );

// Composer loader
require_once('includes/vendor/autoload.php');

require_once('classes/generate.php');

// Add Timber
if(class_exists("Timber\Timber")) new Timber\Timber();

function workout_manager_init(){

	\workout_manager\create_cpt();
}
add_action( 'init', 'workout_manager_init' );

// ===================== Protect athlete's files =====================
function workout_manager_protect_files( $array, $int, $int2 ) {

	global $post;
	if(empty($post)) return;
	if(!isset($post->post_type) || ($post->post_type != "workout" && $post->post_type != "telechargement")) return;

	$str_to_add = "<IfModule mod_rewrite.c>\nRewriteEngine On\n";


	// Get files to protect - Formulaires
	$posts = get_posts([
		'numberposts'      => -1,
		'post_type'        => 'workout',
		'suppress_filters' => true,
	]);
	foreach($posts as $post){
		$file 		= get_field("fichier" , $post->ID);
		if(empty($file) || !isset($file["url"])) continue;
		$permalink 	= str_replace(get_site_url()."/" , "" , $file["url"]);
		
		$str_to_add .= "RewriteRule ^".$permalink." /wp-content/plugins/workout-manager/view-file.php\n";
	}

	$str_to_add .= "</IfModule>";


	$comment_begin 		= "# ------- Workout Manager Begin -------";
	$comment_end 		= "# ------- Workout Manager end -------";


	$htaccess_file 		= ABSPATH."/.htaccess";
	$htaccess_content 	= file_get_contents($htaccess_file);

	// first add
	if(!stristr($htaccess_content , $comment_begin)){
		$htaccess_content .= "\n\n".$comment_begin."\n".$str_to_add."\n".$comment_end;
	}
	else{ // update our rules
		$explode 			= explode($comment_begin , $htaccess_content);
		$explode 			= $explode[1];
		$explode 			= explode($comment_end , $explode);
		$explode 			= $explode[0];

		$htaccess_content 	= str_replace($explode , "\n".$str_to_add."\n" , $htaccess_content);

	}

	$fp = fopen($htaccess_file , "w+");
	fputs($fp , $htaccess_content);
	fclose($fp);

	
}
add_action('updated_post_meta', 'workout_manager_protect_files' , 10 , 3);

add_action( 'admin_enqueue_scripts', 'workout_manager_enqueue_custom_admin_style' );
function workout_manager_enqueue_custom_admin_style() {
	wp_enqueue_script('back-js', WORKOUT_MANAGER_URL.'assets/js/back.js', ['jquery']);
}


function workout_manager_enqueue_scripts() {
	$current_plugin_version = \workout_manager\get_plugin_version();

	wp_enqueue_style('front-style', WORKOUT_MANAGER_URL.'assets/scss/front.css', [], time());
	wp_enqueue_script('script-js', WORKOUT_MANAGER_URL.'assets/js/script.js', ['jquery']);

	wp_enqueue_script( 'workout_manager_athlete_js', WORKOUT_MANAGER_URL . 'assets/js/athlete.js', array( 'jquery' ) , time(), false );
	wp_enqueue_script( 'workout_manager_dashboard_js', WORKOUT_MANAGER_URL . 'assets/js/dashboard.js', array( 'jquery' ) , time(), false );

	wp_enqueue_script( 'jsPDF', WORKOUT_MANAGER_URL.'assets/js/lib/jspdf.js', array( 'jquery' ),'2.3.1',false );
	wp_enqueue_script( 'jsPDF-debug', WORKOUT_MANAGER_URL.'assets/js/lib/jspdf-debug.js', array( 'jquery' ),'1.3.2',false );
	wp_enqueue_script( 'jsPDF-autotable', WORKOUT_MANAGER_URL.'assets/js/lib/jspdf-autotable.js', array( 'jquery' ),'3.5.23',false );

	wp_enqueue_script('plyr-wm', WORKOUT_MANAGER_URL.'node_modules/plyr/dist/plyr.min.js', ["jquery"], '1.0', true);

	// import calendar library
    wp_enqueue_script('fullcalendar-js', WORKOUT_MANAGER_URL.'node_modules/fullcalendar/main.min.js');
    wp_enqueue_style('fullcalendar-css', WORKOUT_MANAGER_URL.'node_modules/fullcalendar/main.min.css');

	wp_localize_script( 'workout_manager_athlete_js', 'workout_manager_athlete_ajax_object', array( 
		'ajaxurl' => admin_url( 'admin-ajax.php'),
        'redirecturl' => home_url(),
        'loadingmessage' => __('Sending user info, please wait...' , 'workout_manager'),
		'delete_account_confirmation_sentence' => __('Are you sure you want to delete your account?' , 'workout_manager'),
    ));

	wp_enqueue_script('popper-wm', WORKOUT_MANAGER_URL.'node_modules/@popperjs/core/dist/umd/popper.min.js', ['jquery']);
    wp_enqueue_script('bootstrap-wm', WORKOUT_MANAGER_URL.'node_modules/bootstrap/dist/js/bootstrap.min.js', ['jquery', 'popper-wm']);

	wp_enqueue_script('toastr-wm', WORKOUT_MANAGER_URL.'node_modules/toastr/build/toastr.min.js', ['jquery']);
    wp_enqueue_style('toastr-css', WORKOUT_MANAGER_URL.'node_modules/toastr/build/toastr.min.css');
}
add_action( 'wp_enqueue_scripts', 'workout_manager_enqueue_scripts' );

// ===================== Athlete role creation =====================
function workout_manager_update_custom_roles() {
    /* if ( get_option( 'workout_manager_update_custom_roles' ) < 1 ) {
        add_role( WORKOUT_MANAGER_ROLE_NAME, 'Athlete', array( 'read' => true, 'level_0' => true ) );
        update_option( 'workout_manager_update_custom_roles', 1 );
    } */
	add_role( WORKOUT_MANAGER_ROLE_NAME, 'Athlete', array( 'read' => true, 'level_0' => true ) );
	update_option( 'workout_manager_update_custom_roles', 1 );
}
add_action( 'init', 'workout_manager_update_custom_roles' );

function workout_manager_remove_admin_bar() {
	if (current_user_can(WORKOUT_MANAGER_ROLE_NAME)) show_admin_bar(false);
}
add_action('after_setup_theme', 'workout_manager_remove_admin_bar');

// ===================== Empeche les athletes non activés de se connecter =====================
//add_filter( 'authenticate', 'chk_active_user',100,2);
function chk_active_user ($user,$username) {
	$user_data = $user->data;
	$user_id = $user_data->ID;
	$user_sts = get_user_meta($user_id,"user_active_status",true);
	if ($user_sts==="no") return new WP_Error( 'disabled_account','this account is disabled');
	else return $user;
	
}

// ===================== Athlete dashboard page name =====================
add_filter( 'wpseo_opengraph_title'		, 'workout_manager_change_single_title_tag_with_site_name' , 100 ,1	);
add_filter( 'wpseo_twitter_title'		, 'workout_manager_change_single_title_tag_with_site_name' , 100 ,1	);
add_filter( 'pre_get_document_title'	, 'workout_manager_change_single_title_tag_with_site_name' , 20);
function workout_manager_change_single_title_tag_with_site_name ($title) {
	if(in_array($_SERVER["REQUEST_URI"], ["/".WORKOUT_MANAGER_URL_DASHBOARD."/", "/en/".WORKOUT_MANAGER_URL_DASHBOARD."/"])) {
        return __('Athlete dashboard', 'workout_manager');
    }
}

// ===================== Custom template for login page =====================
add_filter( 'theme_page_templates', 'workout_manager_include_pages_templates', 10, 4 );
function workout_manager_include_pages_templates( $post_templates, $wp_theme, $post, $post_type ) {
	$post_templates[WORKOUT_MANAGER_TEMPLATE_LOGIN] = 'Athlete - Login';
	return $post_templates;
}

function workout_manager_page_template( $template ) {
	$post          = get_post();
	$page_template = get_post_meta( $post->ID, '_wp_page_template', true );
	if ( basename( $page_template ) === WORKOUT_MANAGER_TEMPLATE_LOGIN ) {

		if(current_user_can(WORKOUT_MANAGER_ROLE_NAME)){
			wp_redirect("/".WORKOUT_MANAGER_URL_DASHBOARD."/");
			exit;
		}

		$template = WORKOUT_MANAGER_DIR . '/src/controllers/'.WORKOUT_MANAGER_TEMPLATE_LOGIN.'.php';
	}
	return $template;
}
add_filter( 'page_template', 'workout_manager_page_template' );

// ===================== When athlete is logged => redirect him ti his dashboard =====================
function workout_manager_redirect(){

    if( !defined('DOING_AJAX') && is_admin() && current_user_can(WORKOUT_MANAGER_ROLE_NAME)){
        wp_redirect("/".WORKOUT_MANAGER_URL_DASHBOARD."/");
        exit;
    }

}
add_action('init','workout_manager_redirect');

function workout_manager_include_template_dashboard() {

	global $wp;

	if ( ! isset( $wp->request ) || $wp->request != WORKOUT_MANAGER_URL_DASHBOARD )  return;

	// If you're not a athlete => can't access to the dashboard
	if(!is_user_logged_in() || !current_user_can(WORKOUT_MANAGER_ROLE_NAME)){
        wp_redirect(home_url());
        exit;
    }
	
	remove_filter( 'template_redirect', 'redirect_canonical' );
	add_action( 'redirect_canonical', '__return_false' );

	header( 'HTTP/1.1 200 OK' );

	include WORKOUT_MANAGER_DIR . '/src/controllers/' . WORKOUT_MANAGER_URL_DASHBOARD . '.php';
	die();

}
add_action( 'template_redirect', 'workout_manager_include_template_dashboard' );

// ===================== Check la version du plugin, et execute des actions si on à subit une MAJ =====================
function workout_manager_update_check(){
	
	$current_plugin_version = \workout_manager\get_plugin_version();

	// Si le plugin à été mis à jour
    if (get_site_option( 'workout_manager_current_version' ) == $current_plugin_version ) return;

	// On enregistre la nouvelle version du plugin
	update_site_option("workout_manager_current_version" , $current_plugin_version , 'no');

	// Actions à effectuer à chaque MAj de plugin
	flush_rewrite_rules();

}
add_action( 'init', 'workout_manager_update_check' );

// ===================== Gestion des trads =====================
add_action( 'after_setup_theme', 'workout_manager_load_plugin_textdomain' );
function workout_manager_load_plugin_textdomain(){

	if ( ! function_exists( 'get_plugins' ) ) require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	$plugin_folder 	= get_plugins( '/' . plugin_basename( dirname( __FILE__ ) ) );
	$plugin_file   	= basename( ( __FILE__ ) );
	$domain       	= $plugin_folder[$plugin_file]["TextDomain"];

	$mo_file 		= WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . get_locale() . '.mo';

	load_textdomain( $domain, $mo_file ); 
	load_plugin_textdomain( $domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
}

add_filter('template_include', 'workout_manager_obj_templates');
function workout_manager_obj_templates( $template ) {

	foreach(WORKOUT_MANAGER_CPT as $cpt_name){
		$cpt_slug = "wm-".$cpt_name;
		if(is_singular($cpt_slug)){
            $theme_file 		= 'single-'.$cpt_name.'.php';
			$exists_in_theme 	= locate_template($theme_file, false);
			if ( $exists_in_theme != '' ) $template =  $exists_in_theme;
			else $template =  WORKOUT_MANAGER_DIR."/cpt/".$cpt_name."/single.php";
		}
		elseif(is_post_type_archive($cpt_slug)){
            $theme_file 		= 'archive-'.$cpt_name.'.php';
			$exists_in_theme 	= locate_template($theme_file, false);
			if ( $exists_in_theme != '' ) $template =  $exists_in_theme;
			else $template =  WORKOUT_MANAGER_DIR."/cpt/".$cpt_name."/archive.php";
		}

	}
	
  	return $template;

}