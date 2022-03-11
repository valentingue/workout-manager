<?php
namespace workout_manager;

if(!is_user_logged_in()) wp_redirect(get_field('login_page_url', 'option'));

get_header("athlete");

$context                    = []; 
$context["logout_url"]      = wp_logout_url(get_login_page_url()."?logout=1");

// Récupération des informations de l'athlete
$context["user_fields"]         = get_athlete_profile_datas();
$current_user = wp_get_current_user();
$context["nonce_user_fields"]   = wp_nonce_field( 'ajax-editprofile-nonce', 'security_edit_profile' , true , false );

// Récupération des informations à afficher sur la page
$page_fields        = [];
$acf_fields         = acf_get_fields('group_'.WORKOUT_MANAGER_ACF_PREFIX.'_dashboard');
foreach($acf_fields as $field){
    if(empty($field["name"])) continue;
    $page_fields[$field["name"]] = get_field($field["name"] , 'option');
}

$context["page_fields"]  = $page_fields;
$context["archived_workout"] = [];

$get_archived_workout = get_field('show_archived_workout', 'option');
$get_future_workout = get_field('show_future_workout', 'option');

// get workouts "workout"
$args = [
    'post_type' => cpt\workout\cpt::$cpt_name,
    'numberposts' => -1,
    'orderby' => 'date', 
    'order' => 'DESC',
    'meta_key' => 'athlete',
    'meta_value' => $current_user->ID
];
$posts = get_posts( $args );

foreach($posts as $k => $post){
    
    $acf_fields         = get_fields($post->ID);
    $post->acf_fields = [];
    if (is_array($acf_fields)) {
        foreach ($acf_fields as $field_name => $field_datas)  $post->acf_fields[$field_name] = $field_datas;
        $posts[$k] = $post;
    }

    // if workout start date > today && end date < today => add workout to current workouts array
    if ( date('Y-m-d', strtotime($post->acf_fields['wm-workout_field_start_date'])) < date('Y-m-d') && date('Y-m-d', strtotime($post->acf_fields['wm-workout_field_end_date'])) > date('Y-m-d')){
        $context["workouts"][] = $post;
    }

    // if workout end date < today's date => add it to archived workout array
    if ($get_archived_workout){
        $context["get_archived_workout"] = $get_archived_workout;
        if (strtotime($post->acf_fields['wm-workout_field_end_date']) < strtotime(date('Y-m-d'))) $context["archived_workout"][] = $post; 
    }
    
    // if workout start date > today's date => add it to archived future array
    if ($get_future_workout){
        $context["get_future_workout"] = $get_future_workout;
        if ( strtotime($post->acf_fields['wm-workout_field_start_date']) < strtotime(date('Y-m-d')) && strtotime($post->acf_fields['wm-workout_field_end_date']) < strtotime(date('Y-m-d'))){
             $context["next_workouts"][] = $post; 
        }
    }

}
//$context["workouts"] = $posts;
//printr( $context["workouts"] );

$view_path  = WORKOUT_MANAGER_DIR."/templates/dashboard.twig";
\Timber::render($view_path, $context);
get_footer("athlete");