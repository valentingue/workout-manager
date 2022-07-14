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
    
    $gyms_per_coach = [];

    /* -------------------------------------------------------------------------- */
    /*                            Get all coachs posts                            */
    /* -------------------------------------------------------------------------- */
    $all_coachs = get_posts([
        'numberposts' => -1,
        'post_type' => 'coach',
        'post_status' => 'publish'
    ]);
    foreach( $all_coachs as $coach){
        //  for each coach we get their current attached gyms
        //      => if the coach has no current gyms attached we assign it an empty array
        $attached_gyms = get_post_meta($coach->ID, ATTACHED_GYM_POSTMETA, true);
        if( empty($attached_gyms)) $attached_gyms = [];
        //      => else we assign it to $gym_per_coach with his attached gyms
        $gyms_per_coach[$coach->ID] = $attached_gyms;
    }

    /* -------------------------------------------------------------------------- */
    /*                         Current selected gym coachs                        */
    /* -------------------------------------------------------------------------- */
    $coachs = get_field('coachs', $ID);

    // create array of selected gym coachs for better readability
    $gym_coachs = [];
    foreach( $coachs as $coach ){
        $gym_coachs[] = $coach['coach']->ID;
    }
    // for each selected gym coachs
    //  => add him the current gym post id
    foreach( $gym_coachs as $coach ){
        $gyms_per_coach[$coach][] = $ID;
    }

    /* -------------------------------------------------------------------------- */
    /*                           Update coachs postmeta                           */
    /* -------------------------------------------------------------------------- */
    foreach($gyms_per_coach as $coach_id => $gyms_id){
        // for each coachs
        // => if current loop index coach is not in selected gym coachs
        //   => delete the current gym id from his attached gyms
        if( !in_array($coach_id, $gym_coachs) ){
            foreach($gyms_id as $key => $current_gym_id){
                if( $current_gym_id === $ID) unset($gyms_id[$key]);
            }
        }
        // else update his postmeta
        update_post_meta($coach_id, ATTACHED_GYM_POSTMETA, array_unique($gyms_id));
    }
}