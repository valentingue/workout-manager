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
            foreach($fields as $field_name => $field_datas) {
                if($field_name == "collective_workout_field_pic"){
                    $field_datas = wp_get_attachment_url($field_datas['ID']);
                };

                $twig_vars["acf_fields"][$field_name] = $field_datas;
                $twig_vars["taxos"] = get_the_terms($post->ID, "collective_workout_category");
            }
            
            if( !empty($twig_vars['taxos']) ){
                foreach( $twig_vars['taxos'] as $i => $taxo ){
                    $twig_vars['taxos'][$i]->permalink = get_term_link($taxo->term_id);
                }
            }
        }
    }

    $args = array(
        'post_type' => 'gym',
        'numberposts' => -1
    );
    $gyms  = get_posts( $args );
    
    foreach($gyms as $k => $gym){
        $gym_post_meta = get_post_meta($gym->ID, "attached_collective_workout");
        if(in_array($post->ID, array_keys($gym_post_meta[0]))) $twig_vars["attached_gyms"][] = $gym;
    }

    // render
    Timber::render("single.twig", $twig_vars);
    
endwhile;

get_footer(); 