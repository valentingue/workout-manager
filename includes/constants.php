<?php
define( 'WORKOUT_MANAGER_TEMPLATE_LOGIN'	, 'login' );
define( 'WORKOUT_MANAGER_URL_DASHBOARD'		, 'athlete-dashboard' );

define(	'WORKOUT_MANAGER_ACF_PREFIX'    , 'workout_manager_settings_acf_field'); 
define(	'WORKOUT_MANAGER_ROLE_NAME'     , 'athlete'); 
define(	'WORKOUT_MANAGER_ACTIVE_FIELD'  , 'athlete_is_active'); 

define('ATTACHED_GYM_POSTMETA', 'attached_gym');

$plugin_data = get_plugin_data(WP_PLUGIN_DIR.'/workout-manager/loader.php');
define('PLUGIN_NAME'					, $plugin_data['Name']);
define('WM_PLUGIN_VERSION'					, $plugin_data['Version']);
define('PLUGIN_TEXT_DOMAIN'				, $plugin_data['TextDomain'] );

use function workout_manager\get_plugin_version;

const WORKOUT_MANAGER_CPT = [
	"workout",
	"contract",
	"gym",
	"coach",
	"collective_workout"
];

const WORKOUT_MANAGER_TAXOS = [
	'collective_workout_category',
];

define(	'WORKOUT_MANAGER_VERSION', get_plugin_version());