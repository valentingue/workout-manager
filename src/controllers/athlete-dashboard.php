<?php
namespace workout_manager;

get_header("athlete");

$context                    = []; 
$context["logout_url"]      = wp_logout_url(get_login_page_url()."?logout=1");

// Récupération des informations de la personne
$context["user_fields"]         = get_athlete_profile_datas();
$context["nonce_user_fields"]   = wp_nonce_field( 'ajax-editprofile-nonce', 'security_edit_profile' , true , false );

// Récupération des informations à afficher sur la page
$page_fields        = [];
$acf_fields         = acf_get_fields('group_'.WORKOUT_MANAGER_ACF_PREFIX.'_dashboard');
foreach($acf_fields as $field){
    if(empty($field["name"])) continue;
    $page_fields[$field["name"]] = get_field($field["name"] , 'option');
}
$context["page_fields"]  = $page_fields;


// Récupération des "formulaires interne"
$posts = get_posts( 'post_type="'.cpt\workout::$cpt_name.'"&numberposts=-1&orderby=date&order=DESC' );
foreach($posts as $k => $post){
    $acf_fields         = get_fields($post->ID);
    $post->acf_fields = [];
    if (is_array($acf_fields)) {
        foreach ($acf_fields as $field_name => $field_datas)  $post->acf_fields[$field_name] = $field_datas;
        $posts[$k] = $post;
    }
}
$context["formulaires"] = $posts;

// Récupération des "téléchargement"
$posts = get_posts([
    'numberposts'      => -1,
    'post_type'        => 'workout',
    'suppress_filters' => false,
]);
$workouts = [];
if(is_array($posts)){
    foreach($posts as $k => $post){

        $display_on_athlete_dashboard = get_field("display_on_athlete_dashboard" , $post);
        if($display_on_athlete_dashboard === null || !$display_on_athlete_dashboard) continue;

        $acf_fields         = get_fields($post->ID);
        $post->acf_fields = [];
        if (is_array($acf_fields)) {
            foreach ($acf_fields as $field_name => $field_datas)  $post->acf_fields[$field_name] = $field_datas;
        }

        $workouts[] = $post;

    }
}

$context["telechargements"] = $workouts;


$view_path  = WORKOUT_MANAGER_DIR."/templates/dashboard.twig";
\Timber::render($view_path, $context);
get_footer("athlete");