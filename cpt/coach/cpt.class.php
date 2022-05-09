<?php

namespace workout_manager\cpt\coach;
class cpt{

    public $cpt_label;
    public static $cpt_name = "wm-coach";
    public $dashicon = "dashicons-admin-users";

    private static $instance = null;

	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
    

    public function __construct()
    {

        $this->cpt_label = __('Coachs', 'workout_manager');

        $this->create_post_type();
        //$this->create_taxonomies();
        $this->create_acf_fields();

    }


    private function create_post_type()
    {

        register_post_type(self::$cpt_name,
            array(
                'labels' => array(
                    'name' => __('Coachs', 'workout_manager'),
                    'singular_name' => __('Coach', 'workout_manager'),
                    'all_items' => __('All coachs', 'workout_manager'),
                    'add_new_item' => __('Add coact', 'workout_manager'),
                    'edit_item' => __('Edit coach', 'workout_manager'),
                    'new_item' => __('New coach', 'workout_manager'),
                    'view_item' => __('See coach', 'workout_manager'),
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

        $prefix_field = self::$cpt_name . "_field_";

        acf_add_local_field_group(array(
            'key' => $prefix_field.'group',
            'title' => 'Coach info',
            'fields' => array(
                array(
                    'key' => $prefix_field.'coach_desc',
                    'label' => 'Description of the coach',
                    'name' => $prefix_field.'coach_desc',
                    'type' => 'textarea',
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
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'wm-coach',
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