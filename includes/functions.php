<?php
namespace workout_manager;

function get_plugin_version(){

    $plugin_folder 	= get_plugins( '/' . plugin_basename( WORKOUT_MANAGER_DIR ) );
	$plugin_file 	= basename( WORKOUT_MANAGER_LOADER );
    $version 		= $plugin_folder[$plugin_file]["Version"];

	return $version;

}

function create_cpt(){


    foreach(WORKOUT_MANAGER_CPT as $cpt_name){
		$class = '\\workout_manager\cpt\\'.$cpt_name."\\cpt";

		if($cpt_name === 'planning'){
			$class_planning = new $class;
			$class_planning->register_hooks();
		}
        else  new $class; 
		
    }

}

function get_login_page_url(){

	$pages = get_pages(array(
		'meta_key' => '_wp_page_template',
		'meta_value' => WORKOUT_MANAGER_TEMPLATE_LOGIN
	));
	foreach($pages as $page) return  get_the_permalink($page->ID);

}

function get_athlete_profile_datas(){

	$is_user_logged_in = (is_user_logged_in());

	// Récupération des informations de la personne
	if($is_user_logged_in) $current_user = wp_get_current_user();
	
	$user_fields        = [
		"user_email"        => ["label" => __("Email" , "workout_manager")  , "value" => ($is_user_logged_in) ? $current_user->user_email : ""],
		"user_first_name"	=> ["label" => __("First name" , "workout_manager") , "value" => ($is_user_logged_in) ? $current_user->user_firstname : ""],
		"user_last_name"	=> ["label" => __("Last name" , "workout_manager")    , "value" => ($is_user_logged_in) ? $current_user->user_lastname : ""]
	];
	

	$current_user_id    = get_current_user_id();
	$acf_fields         = acf_get_fields('group_'.WORKOUT_MANAGER_ACF_PREFIX.'_athlete');

	foreach($acf_fields as $field){
		$value 							= ($is_user_logged_in) ? get_field($field["name"] , "user_".$current_user_id) : "";
		$user_fields[$field["name"]] 	= [
			"label" => __($field["label"] , "workout_manager"), 
			"value" => $value,
			"maxlength" => (array_key_exists('maxlength', $field) ? $field["maxlength"] : ''),
			"type" => $field["type"],
		];

	}
	return $user_fields;

}

function clean($str){
	if(is_array($str)) return $str;
	if($str == null) return "";
	$str 	= str_replace("\t", " ", $str);
	$str 	= str_replace("\n", " ", $str);
	$str 	= str_replace("\r\n", " ", $str);
	$str 	= str_replace("\r", " ", $str);
	$str 	= strip_tags($str);
	while(strstr($str , "  ")) $str 	= str_replace("  ", " ", $str);
	while(substr($str , 0 , 1) == " ") $str = substr($str , 1 , strlen($str)-1);
	while(substr($str , strlen($str)-1 , 1) == " ") $str = substr($str , 0 , strlen($str)-1);
	return $str;
}

function strings_to_js(){
	
	return [
		'mediaUploaderTitle' => __('Select a image to upload', PLUGIN_TEXT_DOMAIN),
		'mediaUploaderButton' => __('Use this image', PLUGIN_TEXT_DOMAIN),
		'editWorkoutTitle' => __('Edit this Workout', PLUGIN_TEXT_DOMAIN),
		'editWorkoutButton' => __('Apply changes', PLUGIN_TEXT_DOMAIN),
		'editWorkoutAction' => __('Edit', PLUGIN_TEXT_DOMAIN),
		'addWorkoutTimeError' => __('Start time must be before end Time', PLUGIN_TEXT_DOMAIN),
		'addWorkoutConflictError' => __("You can't add a workout here because there is already another one at this time. We suggest you make another planning (eg: special bike planning) ", PLUGIN_TEXT_DOMAIN),
		'addWorkoutOutsideBoundariesError' => __("This workout is outside the current planning hours boundaries", PLUGIN_TEXT_DOMAIN),
	];
}

function update_extra_profile_fields($user_id) {
    
    if ( current_user_can('edit_user',$user_id) ){

        // Si la personne n'a pas la coche "active"
        $is_active = intval(get_user_meta($user_id, WORKOUT_MANAGER_ACTIVE_FIELD , true));
        if($is_active === 0) return;

        // Si la personne à déja été alertée
        $has_been_alerted = intval(get_user_meta($user_id, "alerted_for_account_activated" , true));
        if($has_been_alerted === 1) return;

        // Sujet de l'email, si vide on envoie pas l'email
        $email_subject = get_field("subject_received_by_a_doctor_when_his_account_is_activated" , "option");
        if($email_subject == "") return;

        $user = get_userdata($user_id); 


        $email_template = get_field("content_received_by_a_doctor_when_his_account_is_activated" , "option");
        $email_template = str_replace("#first_name#" , $user->user_firstname , $email_template);
        $email_template = str_replace("#last_name#" , $user->user_lastname , $email_template);

    
        //wp_mail($user->user_email , $email_subject , $email_template);
        
        // On note la personne comme ayant recu l'alerte
        update_user_meta($user_id, "alerted_for_account_activated" , 1);

    }
        
}
add_action('profile_update', '\workout_manager\update_extra_profile_fields');


// Make functions available in Twig
add_filter('timber/twig', function ($twig) {
	// Affiche une image avec les parametres src-set , lazy , etc
	$twig->addFunction(new \Timber\Twig_Function('workout_manager_display_image', function ($image_id , $params = []) {

		if( empty($image_id) ) return;

		$width 				= (!empty($params["w"])) ? $params["w"] : "";
		$height 			= (!empty($params["h"])) ? $params["h"] : "";
		$max_width_srcset 	= (!empty($params["srcset"])) ? $params["srcset"] : "";
		$class 				= (!empty($params["class"])) ? $params["class"] : "";
		$alt 				= (!empty($params["alt"])) ? $params["alt"] : "";
		$loading 			= (isset($params["lazy"]) && !$params["lazy"]) ? "eager" : "lazy";
		$crop 				= (isset($params["crop"])) ? $params["crop"] : 0;

		if(!empty($width) && !empty($height)) $src = create_image_size($image_id, $width, $height, $crop);
		else{
			$src 				= wp_get_attachment_image_src($image_id , 'full')[0];
			$meta 				= wp_get_attachment_metadata($image_id);
			$width 				= $meta["width"];
			$height 			= $meta["height"];
			$max_width_srcset 	= $width+1;
		}

		$src_sets	= wp_get_attachment_image_srcset( $image_id, 'full' );

		$max_width_srcset = intval($max_width_srcset);
		if($max_width_srcset > 0) $src_sets	= get_src_sets_limited_per_width($src_sets , $max_width_srcset);

		$attachment_image_alt 	= get_post_meta($image_id, '_wp_attachment_image_alt', TRUE);
		$image_alt 				= (!empty($attachment_image_alt)) ? $attachment_image_alt : $alt;



		$return = "";
		$return = '<picture>';

			// Set distinct <source> size
			$sources = [];
			foreach(explode("," , $src_sets) as $srcset){
				$elements 		= explode(" " , trim($srcset));
				$source_width	= str_replace("w" , "" , $elements[1]);
				$source_url		= $elements[0];
				if($source_width > 100) $sources[(int)$source_width] = $source_url;
			}
			krsort($sources , SORT_NUMERIC);

			foreach($sources as $source_width => $source_url){
				$return .='<source media="(min-width: '.$source_width.'px)" srcset="'.$source_url.'">';
			}

			// Set <img> tag
			$return .= '<img src="'.$src.'" width="'.$width.'" height="'.$height.'" ';
			$return .= 'loading="'.$loading.'" ';
			if($image_alt != "") $return .= 'alt="'.$image_alt.'" ';
			if($class != "") $return .= 'class="'.$class.'" ';
			$return .= "/>";


		$return .= '</picture>';


		return $return;
	}));
	return $twig;
});

function create_image_size($image_id, $width, $height, $crop = 0) {

	if(is_null($image_id)) return "";

    // Temporarily create an image size
    $size_id = 'lazy_' . $width . 'x' .$height . '_' . ((string) $crop);
    add_image_size($size_id, $width, $height, $crop);

    // Get the attachment data
    $meta = wp_get_attachment_metadata($image_id);
	if(!is_array($meta['sizes']))$meta['sizes'] = [];


    // If the size does not exist
    if(!isset($meta['sizes'][$size_id])) {
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $file       = get_attached_file($image_id);
		if(!file_exists($file)) return wp_get_attachment_image_src($image_id);

        $new_meta   = wp_generate_attachment_metadata($image_id, $file);

        // Merge the sizes so we don't lose already generated sizes
        $new_meta['sizes'] = array_merge($meta['sizes'], $new_meta['sizes']);

        // Update the meta data
        wp_update_attachment_metadata($image_id, $new_meta);
    }

    // Fetch the sized image
    $sized = wp_get_attachment_image_src($image_id, $size_id);

    // Remove the image size so new images won't be created in this size automatically
    remove_image_size($size_id);
    return $sized[0];

}

/**
 * Remove srcset images versions if the image has width > $max_width , can be usefull for webperfs
 */
function get_src_sets_limited_per_width($src_sets , $max_width){

    $src_sets       = explode("," , $src_sets);

    foreach($src_sets as $it => $src_set){
        $src_set    = trim($src_set);
        $size       = explode(" " , $src_set);
		if(!isset($size[1])) return '';
		$size = $size[1];
        $size       = str_replace("w" , "" , $size);

        if($size > $max_width) unset($src_sets[$it]);
    }
    
    return trim(implode("," , $src_sets));

}
