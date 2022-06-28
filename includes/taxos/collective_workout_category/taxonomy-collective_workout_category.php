<?php
$current_term = get_queried_object();

get_header(); 
    
$twig_vars = [];

$args = array(
    'post_type' => 'collective_workout',
    'tax_query' => array(
        array(
            'taxonomy' => 'collective_workout_category',
            'field'    => 'slug',
            'terms'    => $current_term->slug
        )
    )
);
$postslist = get_posts( $args );

if( !empty($postslist)){
    foreach( $postslist as $i => $singlepost){
        $postslist[$i]->permalink = get_the_permalink( $singlepost->ID); 
    }
}

$twig_vars['postslist'] = $postslist;

//var_dump($twig_vars['postslist']);

// render
Timber::render("view.twig", $twig_vars);

get_footer(); 