<?php
namespace workout_manager;

get_header("athlete");

$context = [
    "wp_lostpassword_url" => wp_lostpassword_url(),
    "has_been_logout" => !empty($_GET["logout"]) && $_GET["logout"] == 1,
    "loader_image" => esc_url( get_admin_url() . 'images/spinner.gif' ),
    "nonce_login" => wp_nonce_field( 'ajax-login-nonce', 'security_login' , true , false ),

    // get BO athlete options
    "accreditations_titre" => get_field("accreditations_titre", "option"),
    "accreditations_texte" => get_field("accreditations_texte", "option"),
    "titre_prochaine_formation" => get_field("titre_prochaine_formation", "option"),
    "formation_a_metre_en_avant" => get_field("formation_a_metre_en_avant", "option"),
    "en_savoir_plus_titre" => get_field("en_savoir_plus_titre", "option"),
    "en_savoir_plus_blocs" => get_field("en_savoir_plus_blocs", "option"),
    "login_video" => get_field("login_video", "option"),
    "login_image_dillustration" => get_field("login_image_dillustration", "option"),
];


// Récupération des informations de la personne
$context["user_fields"]         = get_athlete_profile_datas();
$context["nonce_register"]      = wp_nonce_field( 'ajax-register-nonce', 'security_register' , true , false );

// Récupération des informations à afficher sur la page
$page_fields        = [];
$acf_fields         = acf_get_fields('group_'.WORKOUT_MANAGER_ACF_PREFIX.'_dashboard');
foreach($acf_fields as $field){
    if(empty($field["name"])) continue;
    $page_fields[$field["name"]] = get_field($field["name"] , 'option');
}
$context["page_fields"]  = $page_fields;


// Affichage du rendu
$view_path  = WORKOUT_MANAGER_DIR."/templates/login.twig";

\Timber::render($view_path, $context);

get_footer("athlete");

