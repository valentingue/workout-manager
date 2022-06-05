<?php

namespace workout_manager\cpt\contract;
class cpt{

    public $cpt_label;
    public static $cpt_name = "contract";
    public $dashicon = "dashicons-media-document";

    private static $instance = null;

	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
    

    public function __construct()
    {

        $this->cpt_label = __('Contracts', 'workout_manager');

        $this->create_post_type();
        //$this->create_taxonomies();
        $this->create_acf_fields();

    }


    private function create_post_type()
    {

        register_post_type(self::$cpt_name,
            array(
                'labels' => array(
                    'name' => __('Contracts', 'workout_manager'),
                    'singular_name' => __('Contract', 'workout_manager'),
                    'all_items' => __('All contracts', 'workout_manager'),
                    'add_new_item' => __('Add contract', 'workout_manager'),
                    'edit_item' => __('Edit contract', 'workout_manager'),
                    'new_item' => __('New contract', 'workout_manager'),
                    'view_item' => __('See contract', 'workout_manager'),
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
            'title' => 'Contracts',
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
                    'key' => $prefix_field.'contract',
                    'label' => 'Name of the contract',
                    'name' => $prefix_field.'contract',
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
                    'key' => $prefix_field.'contract',
                    'label' => 'Contract file',
                    'name' => $prefix_field.'contract',
                    'type' => 'file',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'return_format' => 'array',
                    'library' => 'all',
                    'min_size' => '',
                    'max_size' => '',
                    'mime_types' => '',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'contract',
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