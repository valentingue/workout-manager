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

/* -------------------------------------------------------------------------- */
/*                         Add attached gym to coachs                         */
/* -------------------------------------------------------------------------- */
add_action( 'save_post', 'set_coach_gym', 0, 3 );
function set_coach_gym( $ID, $post, $update ) {
    if( ! $update ) return;
    if( wp_is_post_revision( $ID ) ) return;
    if( defined( 'DOING_AUTOSAVE' ) and DOING_AUTOSAVE ) return;
    if( $post->post_type != 'gym' ) return;

    $post_meta_name = 'attached_gym';
    
    $gyms_per_coach = [];
    $all_coachs = get_posts([
        'numberposts' => -1,
        'post_type' => 'coach',
        'post_status' => 'publish'
    ]);

    foreach( $all_coachs as $coach){
        $attached_gyms = get_post_meta($coach->ID, $post_meta_name, true);
        if( empty($attached_gyms)) $attached_gyms = [];

        $gyms_per_coach[$coach->ID] = $attached_gyms;
    }

    $coachs = get_field('coachs', $ID);
    foreach( $coachs as $coach ){
        $gyms_per_coach[$coach['coach']->ID][] = $ID;
    }

    foreach($gyms_per_coach as $coach_id => $gyms_id){
        update_post_meta($coach_id, $post_meta_name, array_unique($gyms_id));
    }
}