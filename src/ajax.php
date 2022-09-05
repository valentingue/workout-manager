<?php
declare(strict_types = 1);

/**
 * Allow to call any php function or method.
 */
function workout_manager_ajax_router() {
	// check if function is authorised
    if(!isset($_POST['function']) || !in_array($_POST['function'], [
        "login",
        "register",
        "edit_profile",
        "delete_account",
        "get_gyms_attached_collective_workout"
    ])) {
        die("Cheater :)");
    }

	// call the function (works for methods to)
    if (isset($_POST['data'])) {
        $_POST['function']($_POST['data']);
    } else {
        wp_send_json("No data send.");
    }
}
add_action('wp_ajax_workout_manager', 'workout_manager_ajax_router');
add_action('wp_ajax_nopriv_workout_manager', 'workout_manager_ajax_router');




function login(array $args): string {
    if (isset($_REQUEST['lang'])) {
        do_action('wpml_switch_language', $_REQUEST['lang']);
    }

    // First check the nonce, if it fails the function will break
    //if(!wp_verify_nonce( $args['security_login'], 'ajax-login-nonce' )) wp_send_json(['success' => -1 , "message" => __('Bad request.', 'workout_manager')]);

    // Check if all fields exists
    $error_messages = [];
    if(empty($args["username"]) || \workout_manager\clean($args["username"]) == "") $error_messages[] = __("Email is missing." , "workout_manager");
    if(empty($args["password"]) || \workout_manager\clean($args["password"]) == "") $error_messages[] = __("Password is missing." , "workout_manager");

    if(!empty($error_messages)){
        wp_send_json(['success' => -1 , "message" => implode("\n" , $error_messages)]);
    }

    $info                   = [];
    $info['user_login']     = $args['username'];
    $info['user_password']  = $args['password'];
    $info['remember']       = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
        $return = ['success'=> -1 , 'message'=> $user_signon->get_error_message()];
    }
    else {

        $user_id    = $user_signon->data->ID;
        $user_meta  = get_userdata($user_id);
        $user_roles = $user_meta->roles; //array of roles the user is part of.

        // Check if user is athlete
        if(!in_array(WORKOUT_MANAGER_ROLE_NAME , $user_roles)){
            wp_logout();
            $return = ['success'=> 0 , 'message'=> __("You are not listed as a athlete on the site." , 'workout_manager')];
        }
        else{

            // Check if user is active
            if(get_user_meta($user_id, WORKOUT_MANAGER_ACTIVE_FIELD, true) != 1){
                wp_logout();
                $return = ['success'=> 0 , 'message'=> __("Your account has not yet been activated on the site." , 'workout_manager')];
            }
            else{
                $home = apply_filters( 'wpml_home_url', get_option( 'home' ) );
                $return = ['success'=> 1 , 'message'=> __('Authentication successful, redirection in progress.' , 'workout_manager') , 'redirecturl' => $home."/".WORKOUT_MANAGER_URL_DASHBOARD."/" ];
            }

        }

    }

    wp_send_json($return);

}

function register(array $args): string {
    // First check the nonce, if it fails the function will break
    //if(!wp_verify_nonce( $args['security_register'], 'ajax-register-nonce' )) wp_send_json(['success' => -1 , "message" => __('Bad request.', 'workout_manager')]);

    // Check if all fields exists
    $error_messages = [];
    if(empty($args["user_email"])) $error_messages[] = __("Email is missing." , "workout_manager");
    if(empty($args["password_1"])) $error_messages[] = __("Password is missing." , "workout_manager");

    $fields         = \workout_manager\get_athlete_profile_datas();
    foreach($fields as $field_name => $field_datas){
        if(empty($args[$field_name])) $error_messages[] = sprintf(__("%s is missing." , "workout_manager") , $field_datas["label"]);
    }

    if(!empty($error_messages)){
        wp_send_json(['success' => -1 , "message" => implode("\n" , $error_messages)]);
    }


    // Check if password is long enought
    $password   = \workout_manager\clean($args["password_1"]);
    if(strlen($password) < 6) wp_send_json(['success' => -1 , "message" =>  __("Your password must have at least 6 characters" , "workout_manager")]);

    // Check if email is valid
    $user_email = \workout_manager\clean($args["user_email"]);
    if(!\is_email($user_email)) wp_send_json(['success' => -1 , "message" => __("Your email is not a valid email." , "workout_manager")]);
    

    // Check if email is available
    if(\email_exists($user_email)) wp_send_json(['success' => -1 , "message" => __("An account already exists with this email address." , "workout_manager")]);
    

    // Check if password match
    $password_2 = (isset($args["password_2"])) ? \workout_manager\clean($args["password_2"]) : "";
    if($password !== $password_2) wp_send_json(['success' => -1 , "message" => __("Passwords does not match." , "workout_manager")]);
    

    // Create user
    $user_id = wp_create_user($user_email, $password, $user_email);
    // First + last name
    $args['user_first_name']    = \workout_manager\clean($args['user_first_name']);
    $args['user_last_name']     = \workout_manager\clean($args['user_last_name']);
    update_user_meta( $user_id, "first_name",  $args['user_first_name'] ) ;
    update_user_meta( $user_id, "last_name",  $args['user_last_name'] ) ;

    // Add acf fields
    $allowed_fields = array_keys($fields);
    foreach($args as $field_name => $field_value){
        
        // Champ vide
        $field_value = \workout_manager\clean($field_value);
        if($field_value == "") continue;

        // Champs de profil non autorisé
        if(!in_array($field_name , $allowed_fields)) continue;

        if(substr($field_name , 0 , 5) == "user_"){
            $user_data = wp_update_user([ 'ID' => $user_id, $field_name => $field_value ] );
        }
        else{
            update_field($field_name , $field_value , "user_".$user_id);
        }

    }

    // Send an email to administrator
    $admin_email    = get_field("email_alert" , "option");
    $sujet          = __("New athlete account creation" , "workout_manager");
    $message        = __("Hi,\nA new account has been created on the site by \"%s %s\",\n\nYou can activate it by clicking on the following link:\n%s \n<small>Note: if you are not authenticated, you will have to do it first.</small>\n\nHave a great day." , "workout_manager");
    $message        = sprintf($message , $args['user_first_name'] , $args['user_last_name'] , get_site_url()."/wp-admin/user-edit.php?user_id=".$user_id);
    //wp_mail($admin_email , $sujet , $message);
    
    // Add role "athlete"
    $user = get_user_by('id', $user_id);
    $user->remove_role('subscriber');
    $user->add_role(WORKOUT_MANAGER_ROLE_NAME);

    $return = ['success' => 1 , 'message'=> __("Your account has been successfully created.\nYou will be alerted by email as soon as it is activated by our services.", "workout_manager")];
    //$return = ['success' => 1 , 'message'=> $args];
    wp_send_json($return);

}

function edit_profile(array $args): string {

    // First check the nonce, if it fails the function will break
    //if(!wp_verify_nonce( $args['security_edit_profile'], 'ajax-editprofile-nonce' )) wp_send_json(['success' => -1 , "message" => __('Bad request.', 'workout_manager')]);


    $user_id = get_current_user_id();

    // Si changement de mot de passe
    if(!empty($args["password_1"])){

        $password   = \workout_manager\clean($args["password_1"]);
        if(strlen($password) < 6) wp_send_json(['success' => -1 , "message" => __("Your password must have at least 6 characters." , "workout_manager")]);

        $password_2   = \workout_manager\clean($args["password_2"]);
        if($password_2 != $password) wp_send_json(['success' => -1 , "message" => __("Passwords does not match." , "workout_manager")]);

        // Get current logged-in user.
        $user = wp_get_current_user();

        // Change password.
        wp_set_password($password, $user->ID);

        // Log-in again.
        wp_set_auth_cookie($user->ID);
        wp_set_current_user($user->ID);
        do_action('wp_login', $user->user_login, $user);

    }



    $fields         = \workout_manager\get_athlete_profile_datas();
    $allowed_fields = array_keys($fields);
    
    foreach($args as $field_name => $field_value){
        
        // Champ vide
        $field_value = \workout_manager\clean($field_value);
        if($field_value == "") continue;

        // Champs de profil non autorisé
        if(!in_array($field_name , $allowed_fields)) continue;

        if(substr($field_name , 0 , 5) == "user_"){
            if($field_name == "user_email") wp_update_user([ 'ID' => $user_id, $field_name => $field_value ]);
            else wp_update_user([ 'ID' => $user_id, str_replace("user_" , "", $field_name) => $field_value ]);
        }
        else{
            update_field($field_name , $field_value , "user_".$user_id);
        }

    }

    // First + last name
    if(!empty($args['user_first_name']))    update_user_meta( $user_id, "first_name",  \workout_manager\clean($args['user_first_name']) ) ;
    if(!empty($args['user_last_name']))     update_user_meta( $user_id, "last_name",  \workout_manager\clean($args['user_last_name']) ) ;

    

    $return = ['success' => 1 , 'message'=> __('Your changes have been taken into account' , 'workout_manager')];
    wp_send_json($return);

}

function delete_account(array $args): string {
    if (isset($_REQUEST['lang'])) {
        do_action('wpml_switch_language', $_REQUEST['lang']);
    }

    // First check the nonce, if it fails the function will break
    //if(!wp_verify_nonce( $args['security_edit_profile'], 'ajax-editprofile-nonce' )) wp_send_json(['success' => -1 , "message" => __('Bad request.', 'workout_manager')]);

    $user_id = intval(get_current_user_id());
    if($user_id == 0){
        wp_send_json(['success' => -1 , "message" => "Bad user"]);
    }

    \wp_delete_user($user_id);

    $return = ['success' => 1 , 'message'=> __('Your account was deleted. You will be redirected.' , 'workout_manager') , 'redirecturl' => get_site_url()];
    wp_send_json($return);

}

function get_gyms_attached_collective_workout(array $args){

    if(!empty($args['gym_id'])){
        $gym_post_meta = get_post_meta($args['gym_id'], "attached_collective_workout");

        $posts = [];
        foreach($gym_post_meta[0] as $k => $collective_workout){
            $posts[] = get_post($k);
        }
    }
    else $posts = get_posts([
        'post_type' => 'collective_workout'
    ]);

    foreach($posts as $i => $post){
        $posts[$i]->permalink = get_the_permalink($post->ID);
        $posts[$i]->taxos = get_the_terms($post->ID, "collective_workout_category");
        if (class_exists('ACF')){
            $fields = \get_fields($post->ID);
            if(is_array($fields)){
                foreach($fields as $field_name => $field_datas) $acf_fields[$field_name] = $field_datas;
                $posts[$i]->acf_fields = $acf_fields;
            }
        }
    }

    ob_start();

    Timber::render(WORKOUT_MANAGER_DIR."/views/archive/archive-collective-workout.twig", [
        'posts' => $posts,
    ]);

    $retour['collective_workout'] = ob_get_contents();
    ob_end_clean();


    // Send results in json
    wp_send_json($retour);
}