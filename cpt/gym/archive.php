<?php

get_header(); 

// On définit l'objet contenant les paramètres à passer au rendu Twig
$twig_vars = [
    "archives"      => [],
    "pagination"    => workout_manager\clean_pagination(get_the_posts_pagination())
];

while ( have_posts() ) : the_post();

    global $post;

    // On récupère toutes les jobnes créees
    $archive = [
        'title'     => get_the_title(),
        'content'   => get_the_content(),
        'permalink'   => get_the_permalink(),
        'img'       => get_the_post_thumbnail_url($post, 'large'),
        'acf_fields' => [],
        'taxos' =>  get_the_terms($post->ID, "collective_workout_category")
    ];

    if (class_exists('ACF')){
        $fields = \get_fields($post->ID);
        if(is_array($fields)){
            foreach($fields as $field_name => $field_datas) $archive["acf_fields"][$field_name] = $field_datas;
        }
    }

    if( !empty($archive['taxos']) ){
        foreach( $archive['taxos'] as $i => $taxo ){
            $archive['taxos'][$i]->permalink = get_term_link($taxo->term_id);
        }
    }
    
    $twig_vars["archives"][] = $archive;
    
endwhile;

// render
Timber::render("archive.twig", $twig_vars);

get_footer();