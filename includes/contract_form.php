<?php

use mikehaertl\pdftk\Pdf;
use Classes\GeneratePDF;

add_action( 'admin_menu', 'workout_manager_contract_admin_menu' );
function workout_manager_contract_admin_menu(){
    add_menu_page( 'Send contract', 'Send contract', 'administrator', 'workout_manager_contract', 'workout_manager_contract_do_page', 'dashicons-printer', 1);
}

add_action( 'admin_post_workout_manager_form_response', 'the_form_response');
add_action( 'wp_ajax_workout_manager_form_response', 'the_form_response');
add_action( 'admin_notices', 'print_plugin_admin_notices');

function workout_manager_contract_do_page(){

	$formules = [
		'page4_offer_solo' => 'Offre solo',
		'page4_offer_duo' => 'Offre duo',
		'page4_offer_team' => 'Offre team'
	];
	
	$payment_methods = [
		'page4_cheque' => 'Chèque',
		'page4_virement' => 'Virement bancaire',
		'page4_prelevement' => 'Prélévement bancaire',
		'page4_fiat' => 'Éspèces',
		'page4_cb' => 'Carte bancaire',
	];
	
	$reglement_dates = [
		'page5_28_previous_month' => '28 du mois précédent',
		'page5_first_of_month' => '1er du mois',
		'page5_fifth_of_month' => '5 du mois'
	];

	// Populate the dropdown list with exising users.
	$dropdown_html = '<select required id="workout_manager_user_select" name="workout_manager[user_select]">
						<option value="">'.__( 'Select a User', PLUGIN_TEXT_DOMAIN ).'</option>';
	$wp_users = get_users( array( 'fields' => array( 'user_login', 'display_name', 'ID' ) ) );		

	foreach ( $wp_users as $user ) {
		$user_login = esc_html( $user->user_login );
		$user_display_name = esc_html( $user->display_name );
		$user_id = esc_html( $user->ID );
		
		$dropdown_html .= '<option value="' . $user_id . '">' . $user_login . ' (' . $user_display_name  . ') ' . '</option>' . "\n";
	}
	$dropdown_html .= '</select>';
	
	// Generate a custom nonce value.
	$workout_manager_add_meta_nonce = wp_create_nonce( 'workout_manager_add_user_meta_form_nonce' ); 
	
	// Build the Form
	?>		
	<h2><?php _e( 'Send a contract to your athlete', PLUGIN_NAME ); ?></h2>		
	<div class="workout_manager_add_user_meta_form">
				
	<form action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="post" id="workout_manager_add_user_meta_form" >			

		<?php echo $dropdown_html; ?>
		<input type="hidden" name="action" value="workout_manager_form_response">
		<input type="hidden" name="workout_manager_add_user_meta_nonce" value="<?php echo $workout_manager_add_meta_nonce ?>" />			
		<div class='formule-group'>
			<label> <?php _e('Select formula', PLUGIN_NAME); ?> </label><br>
			<?php foreach( $formules as $value => $formule_label){ ?>
				<div>
					<input type="radio" id="<?php echo ($value) ?>" name="<?php echo "workout_manager"; ?>[<?php echo ($value) ?>]" value="Yes">
					<label for="<?php echo ($value) ?>"> <?php echo ($formule_label) ?> </label>
				</div>
			<?php } ?>
		</div>      
		<div>
			<label for="<?php echo PLUGIN_NAME; ?>-duration"> <?php _e('Contract duration', PLUGIN_NAME); ?> </label><br>
			<input required id="<?php echo PLUGIN_NAME; ?>-duration" type="text" name="<?php echo "workout_manager"; ?>[duration]" value="" placeholder="<?php _e('Contract duration', PLUGIN_NAME);?>"/><br>
		</div>    
		<div>
			<label for="<?php echo PLUGIN_NAME; ?>-mensuality"> <?php _e('Mensuality', PLUGIN_NAME); ?> </label><br>
			<input required id="<?php echo PLUGIN_NAME; ?>-mensuality" type="text" name="<?php echo "workout_manager"; ?>[mensuality]" value="" placeholder="<?php _e('Mensuality', PLUGIN_NAME);?>"/><br>
		</div> 
		<div class='payment-method-group'>
			<label> <?php _e('Payment method', PLUGIN_NAME); ?> </label><br>
			<?php foreach( $payment_methods as $value => $payment_method){ ?>
				<div>
					<input type="radio" id="<?php echo ($value) ?>" name="<?php echo "workout_manager"; ?>[<?php echo ($value) ?>]" value="Yes">
					<label for="<?php echo ($value) ?>"> <?php echo ($payment_method) ?> </label>
				</div>
			<?php } ?>
		</div>   
		<div>
			<label for="<?php echo PLUGIN_NAME; ?>-validity_date"> <?php _e('Date de validité', PLUGIN_NAME); ?> </label><br>
			<input required id="<?php echo PLUGIN_NAME; ?>-validity_date" type="text" name="<?php echo "workout_manager"; ?>[validity_date]" value="" placeholder="<?php _e('Date de validité', PLUGIN_NAME);?>"/><br>
		</div> 
		<div class='reglement-date-group'>
			<label> <?php _e('Date de règlement', PLUGIN_NAME); ?> </label><br>
			<?php foreach( $reglement_dates as $value => $date){ ?>
				<div>
					<input type="radio" id="<?php echo ($value) ?>" name="<?php echo "workout_manager"; ?>[<?php echo ($value) ?>]" value="Yes">
					<label for="<?php echo ($value) ?>"> <?php echo ($date) ?> </label>
				</div>
			<?php } ?>
		</div>                          
		<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Submit Form"></p>
	</form>
	<br/><br/>
	<div id="workout_manager_form_feedback"></div>
	<br/><br/>			
	</div>
<?php
} 


function the_form_response() {

	if( isset( $_POST['collective_workout_manager_add_user_meta_nonce'] ) /* && wp_verify_nonce( $_POST['collective_workout_manager_add_user_meta_nonce'], 'workout_manager_add_user_meta_form_nonce') */ ) { 
		
		$selected_user = get_user_by('id', $_POST['collective_workout_manager']['user_select'] );
		$selected_user->user_meta = get_user_meta($selected_user->ID);
		$selected_user->acf_fields = get_fields('user_'.$selected_user->ID);

		$athelete_info = [
			'page1_lastname_firstname' 			=> $selected_user->user_meta['first_name'][0].' '.$selected_user->user_meta['last_name'][0],
			'page2_athlete_lastname_firstname' 	=> $selected_user->user_meta['first_name'][0].' '.$selected_user->user_meta['last_name'][0],
			'page2_athlete_dob_pob' 			=> date('d/m/Y', strtotime($selected_user->acf_fields['date_of_birth'])),
			'page2_athlete_nationality' 		=> '',
			'page2_athlete_address' 			=> $selected_user->acf_fields['adresse'].' - '.$selected_user->acf_fields['code_postal'],
			'page2_athlete_phone_email' 		=> $selected_user->data->user_email,
		];

		$pdf = new GeneratePDF;
		
		$data = [];
		foreach( $_POST['collective_workout_manager'] as $field_name => $field_value){
			$data[$field_name] = $field_value;

			if(stristr($field_name, 'page4_offer')){
				$data[$field_name.'_duration'] = $_POST['collective_workout_manager']['duration'];
				$data[$field_name.'_mensuality'] = $_POST['collective_workout_manager']['mensuality'];
				
			}
			
			$data['page4_offer_team_validity_date'] = $_POST['collective_workout_manager']['validity_date'];
		}

		unset($data['duration']);
		unset($data['mensuality']);
		unset($data['user_select']);

		$data = array_merge($data, $athelete_info);

		$response = $pdf->generate($data); 
		var_dump($response);

		// server response
		$admin_notice = "success";
		custom_redirect( $admin_notice, $_POST );
		exit;	
		
	}				
	else { 
		wp_die( __( 'Invalid nonce specified', PLUGIN_NAME ), __( 'Error', PLUGIN_NAME ), array(
					'response' 	=> 403,
					'back_link' => 'admin.php?page=' . PLUGIN_NAME,

			) );
	}
}

function custom_redirect( $admin_notice, $response ) {
		wp_redirect( esc_url_raw( add_query_arg( array(
			'workout_manager_admin_add_notice' => $admin_notice,
			'workout_manager_response' => $response,
		),
			admin_url('admin.php?page=workout_manager_contract')
	) ) );

}

function print_plugin_admin_notices() {   
	
	if ( isset( $_REQUEST['collective_workout_manager_admin_add_notice'] ) ) {
		if( $_REQUEST['collective_workout_manager_admin_add_notice'] === "success") {
			$html =	'<div class="notice notice-success is-dismissible"> 
						<p><strong>The request was successful. </strong></p><br>';
			$html .= '<pre>' . htmlspecialchars( print_r( $_REQUEST['collective_workout_manager_response'], true) ) . '</pre></div>';
			echo $html;
		}
	}
	else {
		return;
	}

}
