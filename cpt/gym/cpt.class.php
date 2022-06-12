<?php

namespace workout_manager\cpt\gym;
class cpt{

    public $cpt_label;
    public static $cpt_name = "gym";
    public $dashicon = "dashicons-store";

    private static $instance = null;

	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
    

    public function __construct()
    {

        $this->cpt_label = __('Gyms', 'workout_manager');

        $this->create_post_type();
        $this->create_taxonomies();
        $this->create_acf_fields();
    }


    private function create_post_type()
    {

        register_post_type(self::$cpt_name,
            array(
                'labels' => array(
                    'name' => __('Gyms', 'workout_manager'),
                    'singular_name' => __('Gym', 'workout_manager'),
                    'all_items' => __('All gyms', 'workout_manager'),
                    'add_new_item' => __('Add gym', 'workout_manager'),
                    'edit_item' => __('Edit gym', 'workout_manager'),
                    'new_item' => __('New gym', 'workout_manager'),
                    'view_item' => __('See gym', 'workout_manager'),
                    'search_items' => __('Search', 'workout_manager'),
                    'not_found' => __('No data found', 'workout_manager'),
                    'not_found_in_trash' => __('No data found', 'workout_manager'),
                ),
                'show_in_menu' => true,
                'public' => true,
                'exclude_from_search' => true,
                'publicly_queryable' => true,
                'capabilities' => array(
                    'publish_posts' => 'edit_posts',
                    'edit_others_posts' => 'edit_posts',
                    'delete_posts' => 'edit_posts',
                    'delete_others_posts' => 'edit_posts',
                    'read_private_posts' => 'edit_posts',
                    'edit_post' => 'edit_posts',
                    'delete_post' => 'edit_posts',
                    'read_post' => 'edit_posts',
                ),
                'supports' => array('title', 'editor', 'thumbnail'),
                'has_archive' => true,
                'rewrite' => array('slug' => self::$cpt_name),
                'menu_icon' => $this->dashicon
            )

        );

    }

    private function create_taxonomies(){
        $taxo_name = self::$cpt_name . "-facilitie";

        register_taxonomy(
            $taxo_name,
            self::$cpt_name,
            array(
                'label' => 'Équipements',
                'labels' => array(
                    'name' => 'Équipements',
                    'singular_name' => 'Équipement',
                    'all_items' => 'Tous les équipements',
                    'edit_item' => 'Éditer l\'équipement',
                    'view_item' => 'Voir l\'équipement',
                    'update_item' => 'Mettre à jour l\'équipement',
                    'add_new_item' => 'Ajouter un équipement',
                    'new_item_name' => 'Nouvel équipement',
                    'search_items' => 'Rechercher parmi les équipements',
                    'popular_items' => 'Équipements les plus utilisées'
                ),
                'hierarchical' => true
            )
        );

        register_taxonomy_for_object_type( 'facilitie', self::$cpt_name );
    }

    private function create_acf_fields()
    {

        if (!function_exists("acf_add_local_field_group")) return;

        $args = [
            'numberposts' => -1,
            'post_type' => 'coach',
            'post_status' => 'publish'
        ];

        $all_coachs = get_posts($args);
        $coachs = [];

        foreach($all_coachs as $index => $coach){

            $coachs[$coach->ID] = $coach->post_title;

        }

        $args = [
            'numberposts' => -1,
            'post_type' => 'planning',
            'post_status' => 'publish'
        ];
        $collective_courses = get_posts($args);
        $coachs = [];

        foreach($collective_courses as $index => $course){

            $courses[$course->ID] = $course->post_title;

        }

        $prefix_field = self::$cpt_name . "_field_";

        acf_add_local_field_group(array(
            'key' => $prefix_field.'group',
            'title' => 'Gym info',
            'fields' => array(
                array(
                    'key' => $prefix_field.'coachs',
                    'label' => 'Coach de la salle',
                    'name' => 'coachs',
                    'type' => 'repeater',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'collapsed' => '',
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'table',
                    'button_label' => '',
                    'sub_fields' => array(
                        array(
                            'key' => $prefix_field.'coach',
                            'label' => 'Coach',
                            'name' => 'coach',
                            'type' => 'post_object',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'post_type' => array(
                                0 => 'coach',
                            ),
                            'taxonomy' => '',
                            'allow_null' => 0,
                            'multiple' => 0,
                            'return_format' => 'object',
                            'ui' => 1,
                        ),
                    ),
                ),
                array(
                    'key' => $prefix_field.'opening_days',
                    'label' => 'Jours d\'ouverture',
                    'name' => 'opening_days',
                    'type' => 'checkbox',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => array(
                        '0' => 'Lundi',
                        '1' => 'Mardi',
                        '2' => 'Mercredi',
                        '3' => 'Jeudi',
                        '4' => 'Vendredi',
                        '5' => 'Samedi',
                        '6' => 'Dimanche',
                    ),
                    'allow_custom' => 0,
                    'default_value' => array(
                    ),
                    'layout' => 'horizontal',
                    'toggle' => 0,
                    'return_format' => 'value',
                    'save_custom' => 0,
                ),
                array(
                    'key' => $prefix_field.'opening_hours',
                    'label' => 'Heures d\'ouverture',
                    'name' => 'opening_hours',
                    'type' => 'text',
                    'instructions' => '(ex: 6h-22h)',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                array(
                    'key' => $prefix_field.'differents_hours',
                    'label' => 'Heures d\'ouverture différentes (matinée - après midi)',
                    'name' => 'differents_hours',
                    'type' => 'true_false',
                    'instructions' => '(ex: 6h-13h)',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'message' => '',
                    'default_value' => 0,
                    'ui' => 0,
                    'ui_on_text' => '',
                    'ui_off_text' => '',
                ),
                array(
                    'key' => $prefix_field.'morning_hours',
                    'label' => 'Heures d\'ouverture en matinée',
                    'name' => 'morning_hours',
                    'type' => 'text',
                    'instructions' => '(ex: 14h-22h)',
                    'required' => 0,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => $prefix_field.'differents_hours',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                array(
                    'key' => $prefix_field.'afternoon_hours',
                    'label' => 'Heures d\'ouverture en après-midi',
                    'name' => 'afternoon_hours',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => $prefix_field.'differents_hours',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                    'wrapper' => array(
                        'width' => '50',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                array(
                    'key' => $prefix_field.'gym_mail',
                    'label' => 'Gym email',
                    'name' => 'gym_mail',
                    'type' => 'email',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                ),
                array(
                    'key' => $prefix_field.'gym_phone',
                    'label' => 'Gym phone',
                    'name' => 'gym_phone',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'default_value' => '',
                    'placeholder' => '',
                    'prepend' => '',
                    'append' => '',
                    'maxlength' => '',
                ),
                array(
                    'key' => $prefix_field.'gym_address',
                    'label' => 'Gym address',
                    'name' => 'gym_address',
                    'type' => 'google_map',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'center_lat' => '',
                    'center_lng' => '',
                    'zoom' => '',
                    'height' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'gym',
                    ),
                    array(
                        'param' => 'current_user_role',
                        'operator' => '==',
                        'value' => 'administrator',
                    ),
                ),
            ),
            'menu_order' => 0,
            'position' => 'acf_after_title',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
            'hide_on_screen' => array(
                0 => 'the_content',
                1 => 'excerpt',
                2 => 'discussion',
                3 => 'comments',
                4 => 'revisions',
            ),
            'active' => true,
            'description' => '',
        ));
    }

}