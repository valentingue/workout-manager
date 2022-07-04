<?php
get_header(); 

while ( have_posts() ) : the_post();

    global $post;

    // On récupère les paramètres qui nous intéressent et on récupère tous les champs ACF possible
    $twig_vars = [
        'title'     => get_the_title(),
        'content'   => get_the_content(),
        'img'       => (is_object($post)) ? wp_get_attachment_url(get_the_post_thumbnail_url($post->ID, 'medium')) : "",
        'acf_fields' => [],
        'taxos' => [],
        'post_metas' => []
    ];

    // Get post's acf fields
    if (class_exists('ACF')){
        $fields = \get_fields($post->ID);
        if(is_array($fields)){
            foreach($fields as $field_name => $field_datas) {
                $twig_vars["acf_fields"][$field_name] = $field_datas;
                
                /* if( $field_name === 'gym_planning' ){
                    $twig_vars["acf_fields"]['gym_planning_shortcode'] = do_shortcode('[fitness-planning id="'.$field_datas.'"]');
                } */
            }
        }
    }

    $twig_vars['taxos'] = get_the_terms($post->ID, 'gym-facilities');
    $twig_vars['post_metas'] = get_post_meta($post->ID, '_fitplan_planning');
    var_dump($twig_vars['post_metas']);
    die;
    
    // render
    Timber::render("single.twig", $twig_vars);
    
endwhile;

get_footer(); 