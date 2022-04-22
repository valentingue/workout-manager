<?php

use mikehaertl\pdftk\Pdf;

add_action( 'admin_menu', 'workout_manager_contract_admin_menu' );
function workout_manager_contract_admin_menu(){
    add_menu_page( 'Send contract', 'Send contract', 'administrator', 'workout_manager_contract', 'workout_manager_contract_do_page' );
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
			<label for="<?php echo PLUGIN_NAME; ?>-formule"> <?php _e('Select formula', PLUGIN_NAME); ?> </label><br>
			<?php foreach( $formules as $value => $formule_label){ ?>
				<div>
					<input type="radio" id="<?php echo ($value) ?>" name="<?php echo "workout_manager"; ?>[formule]" value="<?php echo ($value) ?>">
					<label for="<?php echo "workout_manager"; ?>[formule]"> <?php echo ($formule_label) ?> </label>
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
			<label for="<?php echo PLUGIN_NAME; ?>-payment_method"> <?php _e('Payment method', PLUGIN_NAME); ?> </label><br>
			<?php foreach( $payment_methods as $value => $payment_method){ ?>
				<div>
					<input type="radio" id="<?php echo ($value) ?>" name="<?php echo "workout_manager"; ?>[payment_method]" value="<?php echo ($value) ?>">
					<label for="<?php echo "workout_manager"; ?>[payment_method]"> <?php echo ($payment_method) ?> </label>
				</div>
			<?php } ?>
		</div>   
		<div>
			<label for="<?php echo PLUGIN_NAME; ?>-validity_date"> <?php _e('Date de validité', PLUGIN_NAME); ?> </label><br>
			<input required id="<?php echo PLUGIN_NAME; ?>-validity_date" type="text" name="<?php echo "workout_manager"; ?>[validity_date]" value="" placeholder="<?php _e('Date de validité', PLUGIN_NAME);?>"/><br>
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

	/* if( isset( $_POST['workout_manager_add_user_meta_nonce'] ) && wp_verify_nonce( $_POST['workout_manager_add_user_meta_nonce'], 'workout_manager_add_user_meta_form_nonce') ) { */
				
		/* print_r($pdf);
		die; 
		// get pdf file content
		$file = WP_PLUGIN_DIR.'/workout-manager/tmp/contrat_vierge_copie.pdf';
		$contract = file_get_contents($file, true);

		// get pdf info for filling the pdf
		$formule = $_POST['workout_manager']['formule'] ;
		$payment_method =  $_POST['workout_manager']['payment_method'];
		$duration =  $_POST['workout_manager']['duration'];
		$mensuality =  $_POST['workout_manager']['mensuality'];
		$validity_date =  $_POST['workout_manager']['validity_date']; 
		$workout_manager_user =  get_user_by( 'login',  $_POST['workout_manager']['user_select'] );
		$workout_manager_user_id = absint( $workout_manager_user->ID ) ; */
		
		// server processing logic
	

		if( isset( $_POST['workout_manager_add_user_meta_nonce'] ) ) {

			$pdf = new Pdf(WORKOUT_MANAGER_DIR.'/tmp/contrat_vierge_copie.pdf');

			$params = [
				'page1_lastname_firstname' => 'test@test.com',
				'page2_coach_info' => 'El Bergando',
				'page4_offer_solo' => true,
				'page4_offer_solo_duration' => $_POST['workout_manager']['duration'],
				'page4_offer_solo_mensuality' => $_POST['workout_manager']['mensuality'],
				'page4_offer_team_validity_date' => $_POST['workout_manager']['validity_date']
			];

			$pdf->fillForm($params)
				->flatten()
				->saveAs(WORKOUT_MANAGER_DIR.'/tmp/rendered/filled.pdf');	
				
			if ($pdf === false) {
				$error = $pdf->getError();
			}
			
			/* $pdf = new GeneratePDF();
			$response = $pdf->generate($params); */

			// server response
			/* echo '<pre>';					
				print_r( $_POST );
			echo '</pre>';				
			wp_die(); */
		}

		// server response
		$admin_notice = "success";
		custom_redirect( $admin_notice, $_POST );
		exit;
	/* }			
	else { 
		wp_die( __( 'Invalid nonce specified', PLUGIN_NAME ), __( 'Error', PLUGIN_NAME ), array(
					'response' 	=> 403,
					'back_link' => 'admin.php?page=' . PLUGIN_NAME,

			) );
	}*/
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
	
	if ( isset( $_REQUEST['workout_manager_admin_add_notice'] ) ) {
	if( $_REQUEST['workout_manager_admin_add_notice'] === "success") {
		$html =	'<div class="notice notice-success is-dismissible"> 
					<p><strong>The request was successful. </strong></p><br>';
		$html .= '<pre>' . htmlspecialchars( print_r( $_REQUEST['workout_manager_response'], true) ) . '</pre></div>';
		echo $html;
	}
	
	// handle other types of form notices

	}
	else {
		return;
	}

}
