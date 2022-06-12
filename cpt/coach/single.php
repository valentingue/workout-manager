<?php
get_header(); 

while ( have_posts() ) : the_post();

    global $post;

    // On récupère les paramètres qui nous intéressent et on récupère tous les champs ACF possible
    $twig_vars = [
        'title'     => get_the_title(),
        'content'   => get_the_content(),
        'img'       => get_the_post_thumbnail_url($post->ID, 'medium'),
        'acf_fields' => [],
        'taxos' => [],
    ];

    // Get the post thumbnail
    $job_img = (is_object($post)) ? wp_get_attachment_url(get_post_thumbnail_id($post->ID)) : "";
    $twig_vars["job_img"] = $job_img;

    // Get post's acf fields
    if (class_exists('ACF')){
        $fields = \get_fields($post->ID);
        if(is_array($fields)){
            foreach($fields as $field_name => $field_datas) $twig_vars["acf_fields"][$field_name] = $field_datas;
        }
    }

    $twig_vars['taxos'] = get_the_terms($post->ID, 'coach-specialite');
    //var_dump($twig_vars['taxos']);
    // render
    Timber::render("single.twig", $twig_vars);
    
endwhile;

get_footer(); 