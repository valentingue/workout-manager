<?php

namespace workout_manager\cpt\workout;
class cpt{

    public $cpt_label;
    public static $cpt_name = "workout";
    public $dashicon = "dashicons-welcome-write-blog";

    private static $instance = null;

	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
    

    public function __construct()
    {

        $this->cpt_label = __('Workouts', 'workout_manager');

        $this->create_post_type();
        //$this->create_taxonomies();
        $this->create_acf_fields();

    }


    private function create_post_type()
    {

        register_post_type(self::$cpt_name,
            array(
                'labels' => array(
                    'name' => __('Workouts', 'workout_manager'),
                    'singular_name' => __('Workout', 'workout_manager'),
                    'all_items' => __('All workouts', 'workout_manager'),
                    'add_new_item' => __('Add workout', 'workout_manager'),
                    'edit_item' => __('Edit workout', 'workout_manager'),
                    'new_item' => __('New workout', 'workout_manager'),
                    'view_item' => __('See workout', 'workout_manager'),
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


    /* private function create_taxonomies()
    {

        $taxo_name = self::$cpt_name . "-category";

        register_taxonomy($taxo_name,
            self::$cpt_name,
            array(
                'capabilities' => array(
                    'manage_terms' => 'edit_posts',
                    'edit_terms' => 'edit_posts',
                    'delete_terms' => 'edit_posts',
                    'assign_terms' => 'edit_posts',
                ),
                'hierarchical' => true,
                'label' => __('Categories', 'workout_manager'),
                'query_var' => true,
                'rewrite' => true,
                'label' => __('Categories', 'workout_manager'),
                'show_ui' => true,
                'labels' => array(
                    'name' => __('Categories', 'workout_manager'),
                    'singular_name' => __('Category', 'workout_manager'),
                    'all_items' => __('All categories', 'workout_manager'),
                    'edit_item' => __('Edit category', 'workout_manager'),
                    'view_item' => __('See category', 'workout_manager'),
                    'update_item' => __('Update category', 'workout_manager'),
                    'add_new_item' => __('Add a category', 'workout_manager'),
                    'new_item_name' => __('New category', 'workout_manager'),
                    'search_items' => __('Search category', 'workout_manager'),
                    'popular_items' => __('Most use category', 'workout_manager')
                )
            )

        );


    } */

    private function create_acf_fields()
    {

        if (!function_exists("acf_add_local_field_group")) return;

        $all_users = get_users(array('role'=>'athlete'));
        $athletes = [];

        foreach($all_users as $index => $athlete){

            $athletes[$athlete->ID] = $athlete->display_name;

        }

        $prefix_field = self::$cpt_name . "_field_";

        acf_add_local_field_group(array(
            'key' => $prefix_field.'group',
            'title' => 'Workouts',
            'fields' => array(
                array(
                    'key' => $prefix_field.'athlete',
                    'label' => 'Athlete',
                    'name' => 'athlete',
                    'type' => 'select',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'choices' => $athletes,
                    'default_value' => false,
                    'allow_null' => 1,
                    'multiple' => 0,
                    'ui' => 0,
                    'return_format' => 'array',
                    'ajax' => 0,
                    'placeholder' => '',
                ),
                array(
                    'key' => $prefix_field.'name',
                    'label' => 'Name of the workout',
                    'name' => $prefix_field.'name',
                    'type' => 'text',
                    'instructions' => '',
                    'required' => 1,
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
                    'key' => 'field_6234dfee57e2d',
                    'label' => 'Training Days',
                    'name' => $prefix_field.'training_days',
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
                        1 => 'Lundi',
                        2 => 'Mardi',
                        3 => 'Mercredi',
                        4 => 'Jeudi',
                        5 => 'Vendredi',
                        6 => 'Samedi',
                        7 => 'Dimanche',
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
                    'key' => $prefix_field.'start_date',
                    'label' => 'Start workout date',
                    'name' => $prefix_field.'start_date',
                    'type' => 'date_picker',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'display_format' => 'd/m/Y',
                    'return_format' => 'Y-m-d',
                    'first_day' => 1,
                ),
                array(
                    'key' => $prefix_field.'end_date',
                    'label' => 'End workout date',
                    'name' => $prefix_field.'end_date',
                    'type' => 'date_picker',
                    'instructions' => '',
                    'required' => 1,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'display_format' => 'd/m/Y',
                    'return_format' => 'Y-m-d',
                    'first_day' => 1,
                ),
                array(
                    'key' => $prefix_field.'exercices',
                    'label' => 'Exercice(s)',
                    'name' => $prefix_field.'exercices',
                    'type' => 'repeater',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'collapsed' => 'exercice_name',
                    'min' => 0,
                    'max' => 0,
                    'layout' => 'row',
                    'button_label' => '',
                    'sub_fields' => array(
                        array(
                            'key' => 'exercice_image',
                            'label' => 'Exercice image',
                            'name' => 'exercice_image',
                            'type' => 'image',
                            'instructions' => '',
                            'required' => 0,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'return_format' => 'id',
                            'preview_size' => 'medium',
                            'library' => 'all',
                            'min_width' => '',
                            'min_height' => '',
                            'min_size' => '',
                            'max_width' => '',
                            'max_height' => '',
                            'max_size' => '',
                            'mime_types' => '',
                        ),
                        array(
                            'key' => 'exercice_name',
                            'label' => 'Exercice name',
                            'name' => 'exercice_name',
                            'type' => 'text',
                            'instructions' => '',
                            'required' => 1,
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
                            'key' => 'exercice_nb_reps',
                            'label' => 'Number of repetitions',
                            'name' => 'exercice_nb_reps',
                            'type' => 'number',
                            'instructions' => '',
                            'required' => 1,
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
                            'min' => 1,
                            'max' => 99,
                            'step' => 1,
                        ),
                        array(
                            'key' => 'exercice_rest_time',
                            'label' => 'Rest time',
                            'name' => 'exercice_rest_time',
                            'type' => 'select',
                            'instructions' => 'Time in minutes & seconds',
                            'required' => 1,
                            'conditional_logic' => 0,
                            'wrapper' => array(
                                'width' => '',
                                'class' => '',
                                'id' => '',
                            ),
                            'choices' => array(
                                '30"' => '30"',
                                '45"' => '45"',
                                '1\'' => '1\'',
                                '1\'30"' => '1\'30"',
                                '2"' => '2"',
                                '3"' => '3"',
                                '4"' => '4"',
                                '5"' => '5"',
                                '6"' => '6"',
                                '7"' => '7"',
                                '8"' => '8"',
                            ),
                            'default_value' => false,
                            'allow_null' => 0,
                            'multiple' => 0,
                            'ui' => 0,
                            'return_format' => 'value',
                            'ajax' => 0,
                            'placeholder' => '',
                        ),
                        array(
                            'key' => 'exercice_weight',
                            'label' => 'Weight',
                            'name' => 'exercice_weight',
                            'type' => 'number',
                            'instructions' => 'Weight in KG',
                            'required' => 1,
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
                            'min' => '0.5',
                            'max' => 1000,
                            'step' => '0.5',
                        ),
                        array(
                            'key' => 'exercice_note',
                            'label' => 'Note',
                            'name' => 'exercice_note',
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
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'workout',
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