<?php
if( !function_exists('acf_add_options_page') ) return;

// Option page for the Workout Manager Settings
acf_add_options_page(array(
    'page_title' 	=> 'Configure the Workout Manager settings',
    'menu_title'	=> 'Workout Manager Settings',
    'menu_slug' 	=> 'workout_manager_settings',
    'icon_url'     => 'dashicons-admin-settings',
    'capability'	=> 'edit_posts',
    'redirect'		=> false,
	'position'    	=> 2
));

acf_add_local_field_group(array(
	'key' => 'group_'.WORKOUT_MANAGER_ACF_PREFIX.'_dashboard',
	'title' => 'Workout Manager',
	'fields' => array(
		array(
			'key' => 'field_61976c2fd453c',
			'label' => 'Login Page',
			'name' => 'login_page_url',
			'type' => 'page_link',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'post_type' => array(
				0 => 'page',
			),
			'taxonomy' => '',
			'allow_null' => 0,
			'allow_archives' => 1,
			'multiple' => 0,
		),
		array(
			'key' => 'field_61276a2fd493c',
			'label' => 'Show past workout on athlete dashboard',
			'name' => 'show_archived_workout',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'allow_null' => 0,
		),
		array(
			'key' => 'field_21296a2ed493c',
			'label' => 'Show future workout on athlete dashboard',
			'name' => 'show_future_workout',
			'type' => 'true_false',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'allow_null' => 0,
		),
	),
    'location' => [
        array(
            array(
                'param' => 'options_page',
                'operator' => '==',
                'value' => 'workout_manager_settings',
            ),
        )
    ],
    'menu_order' => 0,
    'position' => 'normal',
    'style' => 'seamless',
    'label_placement' => 'top',
    'instruction_placement' => 'label',
    'hide_on_screen' => '',
    'active' => true,
    'description' => '',
));


// Athlete infos
acf_add_local_field_group(array(
	'key' => 'group_'.WORKOUT_MANAGER_ACF_PREFIX.'_athlete',
	'title' => 'Informations athlete',
	'fields' => array(
		array(
			'key' => WORKOUT_MANAGER_ACF_PREFIX.'_telephone',
			'label' => __('Phone' , 'workout_manager'),
			'name' => 'telephone',
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
			'maxlength' => 10,
		),
		array(
			'key' => WORKOUT_MANAGER_ACF_PREFIX.'_adresse',
			'label' => __('Address' , 'workout_manager'),
			'name' => 'adresse',
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
			'rows' => '',
			'new_lines' => '',
		),
		array(
			'key' => WORKOUT_MANAGER_ACF_PREFIX.'_code_postal',
			'label' => __('Postal code' , 'workout_manager'),
			'name' => 'code_postal',
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
			'maxlength' => 5,
		),
		array(
			'key' => WORKOUT_MANAGER_ACF_PREFIX.'_date_of_birth',
			'label' =>__('Date of birth' , 'workout_manager'),
			'name' => 'date_of_birth',
			'type' => 'date_picker',
			'instructions' => '',
			'required' => 0,
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
			'key' => WORKOUT_MANAGER_ACF_PREFIX.'_place_of_birth',
			'label' => __('Place of birth' , 'workout_manager'),
			'name' => 'place_of_birth',
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
			'rows' => '',
			'new_lines' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'user_role',
				'operator' => '==',
				'value' => WORKOUT_MANAGER_ROLE_NAME,
			),
		),
	),
	'menu_order' => 0,
	'position' => 'acf_after_title',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

// Infos sur le athlete
acf_add_local_field_group(array(
	'key' => 'group_'.WORKOUT_MANAGER_ACF_PREFIX.'_athlete_active',
	'title' => __('Activate account' , 'workout_manager'),
	'fields' => array(
		array(
			'key' => WORKOUT_MANAGER_ACTIVE_FIELD.'_active',
			'label' => __('Active account' , 'workout_manager'),
			'name' => WORKOUT_MANAGER_ACTIVE_FIELD,
			'type' => 'true_false',
			'instructions' => '',
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
			'ui_off_text' => ''
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'user_role',
				'operator' => '==',
				'value' => WORKOUT_MANAGER_ROLE_NAME,
			),
		),
	),
	'menu_order' => 0,
	'position' => 'acf_after_title',
	'style' => 'default',
	'label_placement' => 'top',
	'instruction_placement' => 'label',
	'hide_on_screen' => '',
	'active' => true,
	'description' => '',
));

