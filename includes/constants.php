<?php
define( 'WORKOUT_MANAGER_TEMPLATE_LOGIN'	, 'login' );
define( 'WORKOUT_MANAGER_URL_DASHBOARD'		, 'athlete-dashboard' );

define(	'WORKOUT_MANAGER_ACF_PREFIX'    , 'workout_manager_settings_acf_field'); 
define(	'WORKOUT_MANAGER_ROLE_NAME'     , 'athlete'); 
define(	'WORKOUT_MANAGER_ACTIVE_FIELD'  , 'athlete_is_active'); 

use function workout_manager\get_plugin_version;

const WORKOUT_MANAGER_CPT = [
	"workout"
];

define(	'WORKOUT_MANAGER_VERSION', get_plugin_version());