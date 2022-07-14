<?php
get_header(); 

while ( have_posts() ) : the_post();

    global $post;

    // On récupère les paramètres qui nous intéressent et on récupère tous les champs ACF possible
    $twig_vars = [
        'title'     => get_the_title(),
        'content'   => get_the_content(),
        'img'       => (is_object($post)) ? get_the_post_thumbnail_url($post->ID, 'medium') : "",
        'acf_fields' => [],
        'taxos' => [],
    ];

    // Get post's acf fields
    if (class_exists('ACF')){
        $fields = \get_fields($post->ID);
        if(is_array($fields)){
            foreach($fields as $field_name => $field_datas) $twig_vars["acf_fields"][$field_name] = $field_datas;
        }
    }
    $attached_gym = get_post_meta($post->ID, 'attached_gym', true); 
    foreach($attached_gym as $gym){
        $gym_post = get_post($gym);
        $twig_vars['post_meta']['attached_gyms'][] = [
            'gym_name'      => $gym_post->post_title,
            'gym_permalink' => get_the_permalink($gym_post->ID),
            'gym_thumbnail' => get_post_thumbnail_id($gym_post->ID),
        ];
    }

    $twig_vars['taxos'] = get_the_terms($post->ID, 'coach-specialite');
    
    // render
    Timber::render("single.twig", $twig_vars);
    
endwhile;

get_footer(); 