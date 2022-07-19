<?php

namespace workout_manager\cpt\gym;

use workout_manager\planningServices\Planning_Services;
class cpt extends \workout_manager\Entities\Entity{

    public $cpt_label;
    public static $cpt_name = "gym";
    public $dashicon = "dashicons-store";

    private static $instance = null;

    public $services;

	public static function get() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}
    

    public function __construct()
    {
        $this->CPT_slug = 'gym';
        $this->cpt_label = __('Gyms', 'workout_manager');

        $this->create_post_type();
        $this->create_taxonomies();
        $this->create_acf_fields();

        $this->fields = array(
			'fitplan_planning' => array("type" => "json", "default" => ""),

			'fitplan_planning_weekdays' 				=> array("type" => "array", "default" => ""),
			'fitplan_planning_morning_start' 			=> array("type" => "time", "default" => "09:00"),
			'fitplan_planning_morning_finish' 			=> array("type" => "time", "default" => "13:00"),
			'fitplan_planning_afternoon_start' 			=> array("type" => "time", "default" => "17:00"),
			'fitplan_planning_afternoon_finish' 		=> array("type" => "time", "default" => "21:00"),
			'fitplan_planning_show_morning'     		=> array("type" => "bool", "default" => "on"),
			'fitplan_planning_show_afternoon'   		=> array("type" => "bool", "default" => "on"),

			'fitplan_planning_workout_display_pic' 		=> array("type" => "time", "default" => true),
			'fitplan_planning_workout_display_color' 	=> array("type" => "time", "default" => true),
			'fitplan_planning_workout_display_title' 	=> array("type" => "time", "default" => false),
			'fitplan_planning_workout_text_color' 	 	=> array("type" => "color", "default" => "#444"),
			'fitplan_planning_workout_default_color' 	=> array("type" => "color", "default" => "#eee"),
			'fitplan_planning_workout_radius' 			=> array("type" => "int", "default" => 4),

			'fitplan_planning_background_color' 		=> array("type" => "text", "default" => ""),
			'fitplan_planning_border_color' 			=> array("type" => "color", "default" => "#eee"),
			'fitplan_planning_days_text_color' 			=> array("type" => "color", "default" => "#000"),
			'fitplan_planning_px_per_hour' 				=> array("type" => "int", "default" => 90),
		);

        // Methods for preparing datas
		$this->services = new Planning_Services();

        //add_action('admin_menu', array( $this, 'add_admin_menu'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
		add_action('add_meta_boxes', array($this, 'register_meta_boxes'));
		add_action('save_post', array($this, 'save_custom_fields'), 10, 3);
		add_filter('manage_'.$this->CPT_slug.'_posts_columns', array($this, 'register_custom_columns'));
		add_action('manage_'.$this->CPT_slug.'_posts_custom_column' , array($this, 'add_custom_column_content'), 10, 2);
		//add_filter('post_row_actions', array($this, 'add_duplicate_link'), 10, 2);
		//add_action('admin_action_duplicate', array($this,'duplicate_post'));
		add_shortcode('fitness-planning', array($this,'execute_planning_shortcode'));
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
                //'publicly_queryable' => false,
                'rewrite' => array('slug' => self::$cpt_name),
                'menu_icon' => $this->dashicon,
            )

        );

    }

    private function create_taxonomies(){
        $taxo_name = self::$cpt_name . "_facilitie";

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
                'rewrite' => array('slug' => 'gym-facilitie'),
                'hierarchical' => true
            )
        );

        register_taxonomy_for_object_type( 'facilitie', self::$cpt_name );
    }

    private function create_acf_fields(){

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
                /* array(
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
                            'return_format' => 'id',
                            'ui' => 1,
                        ),
                    ),
                ), */
                array(
                    'key' => $prefix_field.'coachs',
                    'label' => 'Coach de la salle',
                    'name' => 'coachs',
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
                    'multiple' => 1,
                    'return_format' => 'id',
                    'ui' => 1,
                ),
                array(
                    'key' => $prefix_field.'gym_photos',
                    'label' => 'Gym\'s photos',
                    'name' => $prefix_field.'gym_photos',
                    'type' => 'gallery',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '',
                        'class' => '',
                        'id' => '',
                    ),
                    'return_format' => 'id',
                    'preview_size' => 'large',
                    'insert' => 'append',
                    'library' => 'all',
                    'min' => '',
                    'max' => '',
                    'min_width' => '',
                    'min_height' => '',
                    'min_size' => '',
                    'max_width' => '',
                    'max_height' => '',
                    'max_size' => '',
                    'mime_types' => 'png,	jpeg, jpeg, webp',
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
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => 0,
                    'wrapper' => array(
                        'width' => '50',
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
                    'key' => $prefix_field.'differents_weekend_hours',
                    'label' => 'Heures d\'ouverture différentes le week-end ?',
                    'name' => 'differents_weekend_hours',
                    'type' => 'true_false',
                    'instructions' => '',
                    'required' => 0,
                    'conditional_logic' => '0',
                    'wrapper' => array(
                        'width' => '50',
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
                    'instructions' => '(ex: 06h-12h)',
                    'required' => 0,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => $prefix_field.'differents_hours',
                                'operator' => '==',
                                'value' => '1',
                            )
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
                    'instructions' => '(ex: 14h-22h)',
                    'required' => 0,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => $prefix_field.'differents_hours',
                                'operator' => '==',
                                'value' => '1',
                            )
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
                    'key' => $prefix_field.'weekend_hours',
                    'label' => 'Heures d\'ouverture le week-end',
                    'name' => 'weekend_hours',
                    'type' => 'text',
                    'instructions' => '(ex: 06h-12h)',
                    'required' => 0,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => $prefix_field.'differents_weekend_hours',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
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

    // Send datas about Workouts and Coachs to JS
	public function enqueue_assets() {
		if (!empty($this->datas['collective_workouts'])){
			wp_localize_script(
				"workout-manager-manage-workouts",
				'fitnessPlanningWorkouts',
				$this->datas['collective_workouts']
			);
		}
		if (!empty($this->datas['coachs'])){
			wp_localize_script(
				'workout-manager-manage-workouts',
				'fitnessPlanningCoachs',
				$this->datas['coachs']
			);
		}
	}

	public function register_meta_boxes() {
		global $post;

		// Get custom fields values and prepare datas for template
		// These methods are in AbstractEntity
		$raw_datas = $this->get_custom_fields($post->ID);
		$this->datas = $this->services->prepare_datas($raw_datas);

		add_meta_box('fitness-planning-workout', __('Add a workout', 'fitness-schedule'), array($this, 'render_metabox_workout'), $this->CPT_slug, 'normal', 'high');

		add_meta_box('fitness-planning-preview', __('Planning Preview', 'fitness-schedule'), array($this, 'render_metabox_preview'), $this->CPT_slug, 'normal', 'high');

		add_meta_box('fitness-planning-settings', __('Settings', 'fitness-schedule'), array($this, 'render_metabox_settings'), $this->CPT_slug, 'normal', 'high');

		//add_meta_box('fitness-planning-shortcode', __('Shortcode', 'fitness-schedule'), array($this, 'render_metabox_shortcode'), $this->CPT_slug, 'side', 'low');

		add_meta_box('fitness-planning-workout-styling', __('Customize Workouts', 'fitness-schedule'), array($this, 'render_metabox_workout_styling'), $this->CPT_slug, 'side', 'low');

		add_meta_box('fitness-planning-styling', __('Customize Planning', 'fitness-schedule'), array($this, 'render_metabox_planning_styling'), $this->CPT_slug, 'side', 'low');
	}

	public function render_metabox_workout($post) {
		include WORKOUT_MANAGER_DIR.'/includes/planning/metabox-workout.php';
	}

	public function render_metabox_preview($post) {
		include WORKOUT_MANAGER_DIR.'/includes/planning/metabox-preview.php';
	}

	public function render_metabox_settings($post) {
		include WORKOUT_MANAGER_DIR.'/includes/planning/metabox-settings.php';
	}

	/* public function render_metabox_shortcode($post) {
		$post_id = $post->ID;
		include WORKOUT_MANAGER_DIR.'/includes/planning/metabox-shortcode.php';
	} */

	public function render_metabox_workout_styling($post) {
		include WORKOUT_MANAGER_DIR.'/includes/planning/metabox-workout-styling.php';
	}

	public function render_metabox_planning_styling($post) {
		include WORKOUT_MANAGER_DIR.'/includes/planning/metabox-planning-styling.php';
	}

		public function register_custom_columns($columns) {
		unset($columns['date']);
		$columns['shortcode'] = __('Shortcode', 'fitness-schedule');

		return $columns;
		}

	// Add a column in Admin > Planning > All items showing the shortcode to use
	public function add_custom_column_content($column, $post_id) {
		switch ($column) {
		case 'shortcode':
			include WORKOUT_MANAGER_DIR.'/includes/planning/column-shortcode.php';
			break;
		}
	}

	/* // Add a duplicate link in Admin >Planning > All Items
	public function add_duplicate_link($actions, $post) {
		if ($post->post_type == $this->CPT_slug and current_user_can('edit_posts') and isset($actions['trash'])) {

			// Keep delete link at the end of actions list
			$trash = $actions['trash'];
			unset($actions['trash']);

			$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="'.__('Duplicate', 'fitness-schedule').'" rel="permalink">'.__('Duplicate', 'fitness-schedule').'</a>';

			$actions['trash'] = $trash;
		}
		return $actions;
	}

	// Duplicate a planning
	public function duplicate_post() {

		// Nonce verification
		if (!isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__))) {
			return;
		}

		// Get original post
		$post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
		$post = get_post($post_id);

		if (isset($post) && $post != null) {

			$args = array(
				'post_author'    => $post->post_author,
				'post_name'      => $post->post_name,
				'post_status'    => $post->post_status,
				'post_title'     => $post->post_title.' - '._x('Copy', 'noun', 'fitness-schedule'),
				'post_type'      => $post->post_type,
			);

			// Duplicate post
			$new_post_id = wp_insert_post($args);

			// Get Custom fields
			$post_metas = $this->get_custom_fields($post_id);

			// Add the custom fields values to duplicate post
			foreach($post_metas as $key => $value) {
				update_post_meta($new_post_id, '_'.$key, $value);
			}

			// Go to duplicated post edit page
			wp_redirect(admin_url('post.php?action=edit&post='.$new_post_id));
			exit;
		}

		wp_redirect(admin_url('edit.php?post_type='.$this->CPT_slug));
		exit;
	} */

	public function execute_planning_shortcode($attributes) {

		// Get all required datas
		$raw_datas = $this->get_custom_fields($attributes['id']);
		$this->datas = $this->services->prepare_datas($raw_datas);

		// Store content in buffer (goal is to return var, not echoing it now)
		ob_start();
		include WORKOUT_MANAGER_DIR.'/includes/planning/shortcode-planning.php';
		$output = ob_get_contents();
		ob_end_clean();

		return $output;
	}

}