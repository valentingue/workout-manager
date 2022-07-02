<?php 
/* -------------------------------------------------------------------------- */
/*                       // Admin column for workout cpt                      */
/* -------------------------------------------------------------------------- */
add_filter( 'manage_workout_posts_columns', 'workout_filter_posts_columns' );
function workout_filter_posts_columns( $columns ) {
    unset( $columns['date'] );    
    $columns['workout_athlete'] = __( 'Athlete' );
    $columns['start_workout_date'] = __( 'Start workout date', 'workout_manager' );
    $columns['end_workout_date'] =  __( 'End workout date', 'workout_manager' );
    return $columns;
}


add_action( 'manage_workout_posts_custom_column', 'custom_workout_column', 10, 2);
function custom_workout_column( $column, $post_id ) {

    $acf_fields         = get_fields($post_id);
    $user_meta = get_user_meta($acf_fields['athlete']['value']);

    if ( 'workout_athlete' === $column ) {
        echo $user_meta['first_name'][0].' '.$user_meta['first_name'][0];
    }
    if ( 'start_workout_date' === $column ) {
        echo date('d/m/Y', strtotime($acf_fields['workout_field_start_date'])); 
    }
    if ( 'end_workout_date' === $column ) {
        echo date('d/m/Y', strtotime($acf_fields['workout_field_end_date']));
    }
}

/* -------------------------------------------------------------------------- */
/*                   Admin column for collective workout cpt                  */
/* -------------------------------------------------------------------------- */
add_filter( 'manage_collective_workout_posts_columns', 'collective_workout_filter_posts_columns' );
function collective_workout_filter_posts_columns( $columns ) {
    unset( $columns['date'] );    
    $columns['collective_workout_field_course_level'] = __( 'Level' );
    return $columns;
}


add_action( 'manage_collective_workout_posts_custom_column', 'custom_collective_workout_column', 10, 2);
function custom_collective_workout_column( $column, $post_id ) {

    $acf_fields         = get_fields($post_id);
    
    if ( 'collective_workout_field_course_level' === $column ) {
        echo $acf_fields['collective_workout_field_course_level']; 
    }
}