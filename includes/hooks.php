<?php 

add_filter( 'manage_wm-workout_posts_columns', 'wm_workout_filter_posts_columns' );
function wm_workout_filter_posts_columns( $columns ) {
    unset( $columns['date'] );    
    $columns['collective_workout_athlete'] = __( 'Athlete' );
    $columns['start_workout_date'] = __( 'Start workout date', 'workout_manager' );
    $columns['end_workout_date'] =  __( 'End workout date', 'workout_manager' );
    return $columns;
}


add_action( 'manage_wm-workout_posts_custom_column', 'custom_wm_workout_column', 10, 2);
function custom_wm_workout_column( $column, $post_id ) {

    $acf_fields         = get_fields($post_id);
    $user_meta = get_user_meta($acf_fields['athlete']['value']);

    if ( 'workout_athlete' === $column ) {
        echo $user_meta['first_name'][0].' '.$user_meta['first_name'][0];
    }
    if ( 'start_workout_date' === $column ) {
        echo date('d/m/Y', strtotime($acf_fields['collective_workout_field_start_date'])); 
    }
    if ( 'end_workout_date' === $column ) {
        echo date('d/m/Y', strtotime($acf_fields['collective_workout_field_end_date']));
    }
}