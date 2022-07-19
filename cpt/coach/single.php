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
        'gyms' => []
    ];

    // Get post's acf fields
    if (class_exists('ACF')){
        $fields = \get_fields($post->ID);
        if(is_array($fields)){
            foreach($fields as $field_name => $field_datas) $twig_vars["acf_fields"][$field_name] = $field_datas;
        }
    }

    $args = array(
        'post_type' => 'gym',
        'meta_query' => array(
            'relation' => 'AND',
            array(
                'key' => 'coachs',
                'value' => $post->ID,
                'compare' => 'LIKE'
            )
        )
    );

    $twig_vars['gyms']  = get_posts( $args );

    $twig_vars['taxos'] = get_the_terms($post->ID, 'coach-specialite');
    
    // render
    Timber::render("single.twig", $twig_vars);
    
endwhile;

get_footer(); 