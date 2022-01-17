<?php
namespace workout_manager;

function get_plugin_version(){

	if ( ! function_exists( 'get_plugins' ) ) require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    $plugin_folder 	= get_plugins( '/' . plugin_basename( WORKOUT_MANAGER_DIR ) );
	$plugin_file 	= basename( WORKOUT_MANAGER_LOADER );
    $version 		= $plugin_folder[$plugin_file]["Version"];

	return $version;

}

function create_cpt(){


    foreach(WORKOUT_MANAGER_CPT as $cpt_name){


        $class = '\\workout_manager\cpt\\'.$cpt_name."\\cpt";
        new $class;
        
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
		$user_fields[$field["name"]] 	= ["label" => __($field["label"] , "workout_manager") , "value" => $value];
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