<?php
get_header(); 

while ( have_posts() ) : the_post();

    global $post;

    // On récupère les paramètres qui nous intéressent et on récupère tous les champs ACF possible
    $twig_vars = [
        'title'     => get_the_title(),
        'content'   => get_the_content(),
        'img'       =>  get_the_post_thumbnail_url($post->ID, 'medium'),
        'acf_fields' => [],
        'taxos' => get_the_terms($post->ID, 'gym-facilitie'),
        'post_metas' =>  get_post_meta($post->ID, '_fitplan_planning'),
        'planning' => do_shortcode('[fitness-planning id="'.$post->ID.'"]')
    ];

    // Get post's acf fields
    if (class_exists('ACF')){
        $fields = \get_fields($post->ID);
        if(is_array($fields)){
            foreach($fields as $field_name => $field_datas){
                if( $field_name === 'opening_hours'){
                    $twig_vars["acf_fields"][$field_name] = explode('-', $field_datas);
                }
                elseif( $field_name === 'weekend_hours'){
                    $twig_vars["acf_fields"][$field_name] = explode('-', $field_datas);
                }
                elseif( $field_name === 'coachs'){
                    foreach($field_datas as $k => $coach){
                        $coach['coach']->permalink = get_the_permalink( $coach['coach']->ID);
                        $coach['coach']->taxos = get_the_terms($coach['coach']->ID, 'coach-specialite');
                        $coach['coach']->picture = get_the_post_thumbnail_url($coach['coach']->ID, 'large');
                        $twig_vars["acf_fields"][$field_name][] =$coach['coach'];
                    }
                }
                elseif( $field_name === 'gym_field_gym_photos' ){
                    $images = [];
                    foreach( $field_datas as $k => $image){
                        $images[] = wp_get_attachment_url($image);
                    }
                    $twig_vars["acf_fields"][$field_name] = $images;
                }
                else $twig_vars["acf_fields"][$field_name] = $field_datas;
                
            }
        }
    }

    var_dump($twig_vars);

    // render
    Timber::render("single.twig", $twig_vars);
    
endwhile;

get_footer(); 