<?php

get_header(); 

while ( have_posts() ) : the_post();

    global $post;

    // On récupère les paramètres qui nous intéressent et on récupère tous les champs ACF possible
    $twig_vars = [
        'title'     => get_the_title(),
        'content'   => get_the_content(),
        'img'       => get_the_post_thumbnail(),
        'acf_fields' => []
    ];

    // Récupération des champs ACF de la personne
    if (class_exists('ACF')){
        $fields = \get_fields($post->ID);
        if(is_array($fields)){
            foreach($fields as $field_name => $field_datas) $twig_vars["acf_fields"][$field_name] = $field_datas;
        }
    }
    printr($twig_vars);

    // render
    Timber::render("single.twig", $twig_vars);
    
endwhile;

get_footer();