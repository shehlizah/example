<?php
/*
 *  WP Edit module: White Label Websites Programs
 */
function white_label_websites_programs_module_form( $key, $visible_on = 'all', $module_title = '', $custom_settings = array() ) {
	global $data;
  global $wpdb;
  $post_id = get_the_ID();
  $table_name = $wpdb->prefix . 'white_label_websites';
  $holidays_json = $wpdb->get_var( $wpdb->prepare( "SELECT custom_holidays FROM $table_name WHERE wp_post_id = %d", $post_id ) );
  $custom_holidays = json_decode( $holidays_json, true ) ?: [];

	if ( empty( $data['wlw_programs_module'] ) ) {
		$data = init_array_on_var_and_array_key($data, 'wlw_programs_module');
		$data['wlw_programs_module'][ $key ] = array(
			'programs' => '',
			'programs_protected' => 'individually'
		);
	}

	$autocomplete_args_programs = array(
		'post_type' => 'program',
		'posts_per_page' => -1,
		'post_status' => 'publish'
	);

	//Left right image choices
	$programs_protected_choices = array(
		'individually'  => __( 'Programs Individually Protected', 'balance' ),
		'all' => __( 'All Programs Protected', 'balance' ),
	);

	if ( empty( $data['wlw_programs_module'][ $key ]['programs_protected'] ) ) {
		$data['wlw_programs_module'][ $key ]['programs_protected'] = 'individually';
	}



/*START OF SETTINGS*/

  if ( empty($data['test_times'][ $key ]) ) {
    $data = init_array_on_var_and_array_key($data, 'test_times');
    $data['test_times'] = array(
      'type' => 'life_stage'
    );
  }


  $array_of_types = array(
    1 => array(
      'id' => '1',
      'title' => '1',
    ),
    2 => array(
      'id' => '2',
      'title' => '2'
    ),
    3 => array(
      'id' => '3',
      'title' => '3'
    ),
    4 => array(
      'id' => '4',
      'title' => '4'
    ),
    5 => array(
      'id' => '5',
      'title' => '5'
    ),
    6 => array(
      'id' => '6',
      'title' => '6'
    ),
    7 => array(
      'id' => '7',
      'title' => '7'
    ),
    8 => array(
      'id' => '8',
      'title' => '8'
    ),
    9 => array(
      'id' => '9',
      'title' => '9'
    ),
    10 => array(
      'id' => '10',
      'title' => '10'
    ),
    11 => array(
      'id' => '11',
      'title' => '11'
    ),
    12 => array(
      'id' => '12',
      'title' => '12'
    ),
    13 => array(
      'id' => '13',
      'title' => '13'
    ),
    14 => array(
      'id' => '14',
      'title' => '14'
    ),
    15 => array(
      'id' => '15',
      'title' => '15'
    ),
    16 => array(
      'id' => '16',
      'title' => '16'
    ),
    17 => array(
      'id' => '17',
      'title' => '17'
    ),
    18 => array(
      'id' => '18',
      'title' => '18'
    ),
    19 => array(
      'id' => '19',
      'title' => '19'
    ),
    20 => array(
      'id' => '20',
      'title' => '20'
    ),
    21 => array(
      'id' => '21',
      'title' => '21'
    ),
    22 => array(
      'id' => '22',
      'title' => '22'
    ),
    23 => array(
      'id' => '23',
      'title' => '23'
    ),
    24 => array(
      'id' => '24',
      'title' => '24'
    ),
    25 => array(
      'id' => '25',
      'title' => '25'
    ),
    26 => array(
      'id' => '26',
      'title' => '26'
    ),
    27 => array(
      'id' => '27',
      'title' => '27'
    ),
    28 => array(
      'id' => '28',
      'title' => '28'
    ),
    29 => array(
      'id' => '29',
      'title' => '29'
    ),
    30 => array(
      'id' => '30',
      'title' => '30'
    )
  );

  $passing_percent = array(
    0 => array(
      'id' => '',
      'title' => 'Select',
    ),
    1 => array(
      'id' => '50',
      'title' => '50',
    ),
    2 => array(
      'id' => '55',
      'title' => '55',
    ),
    3 => array(
      'id' => '60',
      'title' => '60'
    ),
    4 => array(
      'id' => '65',
      'title' => '65'
    ),
    5 => array(
      'id' => '70',
      'title' => '70'
    ),
    6 => array(
      'id' => '75',
      'title' => '75'
    ),
    7 => array(
      'id' => '80',
      'title' => '80'
    ),
    8 => array(
      'id' => '85',
      'title' => '85'
    ),
    9 => array(
      'id' => '90',
      'title' => '90'
    ),
    10 => array(
      'id' => '95',
      'title' => '95'
    ),
    11 => array(
      'id' => '100',
      'title' => '100'
    )
  );


  if ( empty( $data['test_duration'] ) ) {
    $data = init_array_on_var_and_array_key($data, 'test_duration');
    $data['test_duration'] = array(
      'yes_no' => ''
    );
  }

  if ( empty( $data['test_duration'] ) ) {
    $data = init_array_on_var_and_array_key($data, 'test_duration');
    $data['test_duration'] = array(
      'duration_test' => ''
    );
  }

/*END OF SETTINGS*/
	$output = '';
	$output .= '<a name="wlw-programs-module-wrapper-'. $key .'"></a>';
	$output .= '<div class="module-wrapper wlw-programs-module-wrapper-'. $key .' hidden" '. ( $visible_on != "all" ? "data-visible-on='" . $visible_on ."'" : "" ) .'>';
	$output .= '  <div class="postbox postbox-custom wlw-programs-module-list-wrapper-'. $key .'">';
	$output .= '    <h3>'. $module_title . ( intval( $key ) > 0 ? ' #'.( intval( $key )+1 ) : '' ) .'<a class="description fright section-expander is-expanded" data-toggle-title="'. __( 'Expand', 'balance' ) .'" href="javascript:;">'. __( 'Collapse', 'balance' ) .'</a></h3>';
	$output .= '    <div class="inside">';

	$output .= '     <p>';
	$output .= '        <label><b>'. __( 'Programs Protection', 'balance' ) .':</b></label><br>';
	$output .=          radiobuttonlist_field( $data['wlw_programs_module'][ $key ]['programs_protected'], 'wlw_programs_module['.$key.'][programs_protected]', $programs_protected_choices, 'individually', true );
	$output .= '      </p>';

	$output .= '        <p>';
	$output .= '          <label><b>'. __( 'Programs linked to Partner website', 'balance' ) .':</b></label>';
	$output .=              multi_autocomplete_field( $data['wlw_programs_module'][ $key ]['programs'], 'wlw_programs_module['.$key.'][programs]', $autocomplete_args_programs );
	$output .= '        </p>';

	$output .= '    </div>';
	$output .= '  </div>';
	$output .= '</div>';


/*START OF SETTINGS*/

  $output .= '<style>
	.box-div-settings{border: solid 3px #cccccc;padding: 10px 20px 20px 20px;margin-bottom: 10px;}
	</style>';
 
  $output .= '<div class="module-wrapper wlw-programs-module-wrapper-0 hidden" data-visible-on="tab-7">';

  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  $output .= '<h3>Settings<a class="description fright section-expander is-expanded" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  $output .= '  <div class="inside">';
  
  $output .= '  <b>Email settings</b>';
 
  $output .= '     <div class="box-div-settings"> ';
  $output .= '          <br><label><b>'. __( 'Send Email to Siteadmin', 'balance' ) .':</b></label>&nbsp;&nbsp;&nbsp;';
  //$output .=              radiobuttonlist_field( $data['siteadmin_superadmin_mail'], 'siteadmin_superadmin_mail', array( 'Yes' => __( 'Yes', 'balance' ), 'No' => __( 'No', 'balance' ) ), 'y', true);
  
  $siteadmin_superadmin_mail = $data['siteadmin_superadmin_mail'];
  if(isset($siteadmin_superadmin_mail) && $siteadmin_superadmin_mail=='on')$monstatus='checked';else $monstatus=''; 
  $output .= '<input type="checkbox" class="toggleCheck" name="siteadmin_superadmin_mail" '.$monstatus.' style="margin-top:10px !important">'; 
 
  
  $output .= '      </div>';

  $output .= '     <div class="box-div-settings"> ';
  $output .= '          <br><label><b>'. __( 'Send Email to Superadmin', 'balance' ) .':</b></label>&nbsp;&nbsp;&nbsp;';
  //$output .=              radiobuttonlist_field( $data['siteadmin_superadmin_mail'], 'siteadmin_superadmin_mail', array( 'Yes' => __( 'Yes', 'balance' ), 'No' => __( 'No'$

  $superadmin_mail = $data['superadmin_mail'];
  if(isset($superadmin_mail) && $superadmin_mail=='on')$mailstatus='checked';else $mailstatus='';
  $output .= '<input type="checkbox" class="toggleCheck" name="superadmin_mail" '.$mailstatus.' style="margin-top:10px !important">';


  $output .= '      </div>';

  
  
  $output .= '  <b>Quiz settings</b>';
  $output .= '     <div class="box-div-settings"> ';
  $output .= '          <label><b>'. __( 'Quiz Duration', 'balance' ) .':</b></label><br>';
  //$output .=              radiobuttonlist_field( $data['test_duration'], 'test_duration', array( '24' => __( '24 hours', 'balance' ), '48' => __( '48 hours', 'balance' ) ), 'y', true);
  if($data['test_duration'] == "24" || $data['test_duration'] == "48"){
 $output .=              radiobuttonlist_field( $data['test_duration'], 'test_duration', array( '24' => __( '24 hours', 'balance' ), '48' => __( '48 hours', 'balance' ) ), 'y', true);
} else {
 $output .=              radiobuttonlist_field( 24, 'test_duration', array( '24' => __( '24 hours', 'balance' ), '48' => __( '48 hours', 'balance' ) ), 'y', true);
}

  $output .= '    </p>';

  $output .= '  
  <script>
  function disableTestTimes(el){
    var contact_page_status = document.getElementsByName(el.name);
    //alert(el.checked);
    if(el.checked == false){ 
    jQuery("#test_times").attr("disabled","true");
} else {
    jQuery("#test_times").removeAttr("disabled");
}
  }
    </script>';


  $output .= '    <p>';
  $output .= '      <label>';
  $output .= '        <span class="text">'. __( 'Limit ') .'</span>';
  $output .= '      </label>';
  $no_limit = $data['no_limit'];
  if(isset($no_limit) && $no_limit=='on')$no_limitstatus='checked';else $no_limitstatus=''; 
  $output .= '<input type="checkbox" class="toggleCheck" name="no_limit" '.$no_limitstatus.' onclick="disableTestTimes(this)" style="padding-top:10px !important">';
  $output .= '      <label>';
  $output .= '      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  $output .= '        <span class="text">'. __( 'Number of attempts: ') .'</span>';
  $output .= '      </label>';
  if(isset($data['test_duration']) && !isset($data['no_limit']) && $data['no_limit']!='on' || $data['no_limit']=='off') {
  $output .= '       <select id="test_times" class="single-drp " name="test_times" disabled><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option></select>';
} else {

  $output .=        dropdown_field( $data['test_times'], 'test_times', '', $array_of_types, '', false );
}
 // $output .= '    </div>';
 $output .= '    <br><br><hr><br>';

   //$output .= '     <div class="box-div-settings"> ';
  $output .= '          <label><b>'. __( 'Passing Percentage Criteria', 'balance' ) .':</b></label><br>';
  $output .= '      <label>';
 // $output .= '      &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
  $output .= '        <span class="text">'. __( 'Passing Percentage: ') .'</span>';
  $output .= '      </label>';
  $output .=        dropdown_field( $data['passing_percent'], 'passing_percent', '', $passing_percent, '', false );

 $output .= '    <br><br><hr><br>';

  $output .= '     <p>';
  $output .= '          <label><b>'. __( 'Do you want to show Question and Answer after Quiz attempt', 'balance' ) .':</b></label>&nbsp;&nbsp;&nbsp;';
  //$output .=              radiobuttonlist_field( $data['quiz_q_n_a'], 'quiz_q_n_a', array( 'Yes' => __( 'Yes', 'balance' ), 'No' => __( 'No', 'balance', 'checked' ) ), 'y',  true);
  $quiz_q_n_a = $data['quiz_q_n_a'];
  if(isset($quiz_q_n_a) && $quiz_q_n_a=='on')$monstatus='checked';else $monstatus=''; 
  $output .= '<input type="checkbox" class="toggleCheck" name="quiz_q_n_a" '.$monstatus.' style="margin-top:10px !important">'; 
  
  
  $output .= '      </p>';
  $output .= '    </div>';
 /*
  $output .= '  <b>Contact settings</b>';
  $output .= '     <div class="box-div-settings"> ';
  $output .= '          <label><b>'. __( 'Do you want to show contact page in your website', 'balance' ) .':</b></label><br>';
  $output .=              radiobuttonlist_field( $data['contact_page'], 'contact_page', array( 'Yes' => __( 'Yes', 'balance' ), 'No' => __( 'No', 'balance' ) ), 'y', true);

$output .= '      <br>';
   $output .= '      </div>';
*/  
   

  $output .= '  </div>';
  $output .= '</div>';
  $output .= '</div>';

  /*END OF SETTINGS*/

/*START OF MESSAGES*/
  $output .= '<div class="module-wrapper wlw-programs-module-wrapper-0 hidden" data-visible-on="tab-9">';
$output .= '     <style>
                  input.toggleCheck {
                  -webkit-appearance: none !important;
                  appearance: none !important;
                  padding: 16px 30px !important;
                  border-radius: 16px !important;
                  background: radial-gradient(circle 12px, white 100%, transparent calc(100% + 1px)) #ccc -14px !important;
                  transition: 0.3s ease-in-out !important;
                }

                input.toggleCheck:checked {
                  background-color: dodgerBlue !important;
                  background-position: 14px !important;
				  
				 
                }
				 #messagetext_ifr,#thankyoutext_ifr{
					  height:400px !important;
				  }
				  #certificatetext_ifr,#certificateadmintext_ifr{
					  height:200px !important;
				  }
				   
                </style>
';

//START OF Registration MESSAGE
  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  
  $output .= '<h3>Registration Mail Message<a class="description fright section-expander is-expanded" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  $output .= '  <div class="inside">';  
  $output .= '     <p>';
  $output .= '          <label><b>Subject</b></label><br>';
  if(isset($data['register_subject']) && trim($data['register_subject'])!='') $register_subject = $data['register_subject'];
  else $register_subject = 'BALANCE Account Email Verification!';
  $output .= '             <input type="text" class="" name="register_subject" value="'.$register_subject.'">';
  $output .= '            <span><b>[Note: %username%,%site_title% are variables used for user name, your website title]</b>';
  $output .= '      </p>';

 
				
 
  $output .= '     <p>';
  $output .= '          <label><b>Enable Dynamic Message</b></label>&nbsp;&nbsp;&nbsp;';
  if(isset($data['register_message_status']) && $data['register_message_status']=='on'){
  $output .= '             <input type="checkbox" class="toggleCheck" name="register_message_status" checked>';
  } 
  else {
  $output .= '             <input type="checkbox" class="toggleCheck" name="register_message_status">';
  }
  $output .= '      </p>';
    //Textarea field
	
	$default_register_message_text = '<div><p>Thank you %username% for creating your new profile with BALANCE, in partnership with %site_title%. Please click the link below to verify your ownership of %user_email%.</p>

		<p>CLICK THIS LINK TO VERIFY: <BR><a href="%link%">%link%</a> <BR>
		<BR><BR>
		Best,<BR>

		BALANCE in partnership with %site_title%</p></div>';
	
    $output .= '            <p>';
    $output .= '              <label><b>'. __( 'Message Text', 'balance' ) .':</b></label><br/>';
    $output .=                textarea_field( $data['register_message_text'], 'register_message_text', false, 10, $default_register_message_text, '', array('media_buttons' => false, 'quicktags' => true ) );
    $output .= '            <span><b>[Note: %username%,%user_email%,%site_title%,%site_url%,%link% are variables used for user name, user email, your website title, website url and verification link]</b>';
    $output .= '            </p>';    
  $output .= '  </div>';
  $output .= '</div>';
  //END OF Registration Mail Message

//START OF THANK YOU MESSAGE
  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  $output .= '<h3>Thank You Pop up Message [Not email message]<a class="description fright section-expander" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  $output .= '  <div class="inside">';  

  $output .= '     <p>';
  $output .= '          <label><b>Enable Dynamic Message</b></label>&nbsp;&nbsp;&nbsp;';
  if(isset($data['thankYou_status']) && $data['thankYou_status']=='on'){
  $output .= '             <input type="checkbox" class="toggleCheck" name="thankYou_status" checked>';
  }
  else {
  $output .= '             <input type="checkbox" class="toggleCheck" name="thankYou_status">';
  }
  $output .= '      </p>';
    //Textarea field
	
	$default_thankYou_text='<h1 class="text-info text-center">Thank you for verifying your email address</h1>
				<p>Thank you for completing your registration with BALANCE, in partnership with <span class="text-info"><b>%site_title%</b></span>. You’ve taken a step to financial fitness by being an active participant in your own financial wellness.</p>
				<p>Your registration gives you access to more BALANCE online education programs available on this website. Use your email address and BALANCE password to log in to a program when prompted.</p>
				<p>We hope you enjoy your experience with BALANCE, in partnership with <span class="text-info"><b>%site_title%</b></span>. We are here to help you achieve your financial success.</p>
				<p>Best,</p>
				<p>BALANCE in partnership with <span class="text-info"><b>%site_title%</b></span></p>';
    $output .= '            <p>';
    $output .= '              <label><b>'. __( 'Message Text', 'balance' ) .':</b></label><br/>';
    $output .=                textarea_field( $data['thankYou_text'], 'thankYou_text', false, 15, $default_thankYou_text, '', array('media_buttons' => false, 'quicktags' => true ) );
	$output .= '            <span><b>[Note: %site_title%, %site_url% are variables used for your website title and website url .]</b>';
    $output .= '            </p>';
  $output .= '  </div>';
  $output .= '</div>';
  //END OF THANK YOU MESSAGE



//START OF WELCOME MESSAGE
  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  $output .= '<h3>Welcome Message<a class="description fright section-expander" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  $output .= '  <div class="inside">';  
  $output .= '     <p>';
  $output .= '          <label><b>Subject</b></label><br>';
  if(isset($data['message_subject']) && trim($data['message_subject'])!='') $message_subject = $data['message_subject'];
  else $message_subject = 'Welcome to BALANCE, in Partnership with %site_title%';
  $output .= '             <input type="text" class="" name="message_subject" value="'.$message_subject.'">';
  $output .= '            <span><b>[Note: %site_title%,%username% are variables used for your website title and User name]</b></span>';
  $output .= '      </p>';

  $output .= '     <p>';
  $output .= '          <label><b>Enable Dynamic Message</b></label>&nbsp;&nbsp;&nbsp;';
  if(isset($data['message_status']) && $data['message_status']=='on'){
  $output .= '             <input type="checkbox" class="toggleCheck" name="message_status" checked>';
  } 
  else {
  $output .= '             <input type="checkbox" class="toggleCheck" name="message_status">';
  }
  $output .= '      </p>';
    //Textarea field
	
	$default_message_text = '<div><h1>Welcome to BALANCE, in partnership with %site_title%</h1>
					<p>Welcome and thank you for completing your registration with BALANCE, in partnership with %site_title%.</p>
					<p>
					You have taken a step to financial fitness by being an active participant in your own financial wellness.
					</p>
					<p>
					Your registration gives you access to more online education programs available on <a href="%site_url%">%site_url%</a> .
					</p>
					<p>
					Use your email address and BALANCE password to log in to a program when prompted.
					</p>
		<p>
					We hope you enjoy your experience with BALANCE, in partnership with %site_title% We are here to help you achieve your financial success.
				</p>
					<p>Start by visiting us at <a href="%site_url%">%site_url% </a> .
					</p>
				<p>	Best,</p>
				<p>	
					BALANCE, in partnership with %site_title%
					</p>
					</div>';
	
    $output .= '            <p>';
    $output .= '              <label><b>'. __( 'Message Text', 'balance' ) .':</b></label><br/>';
    $output .=                textarea_field( $data['message_text'], 'message_text', false, 20, $default_message_text, '', array('media_buttons' => false, 'quicktags' => true ) );
    $output .= '            <span><b>[Note: %site_title%,%site_url% are variables used for your website title and website url]</b></span>';
    $output .= '            </p>';    
  $output .= '  </div>';
  $output .= '</div>';
  //END OF WELCOME MESSAGE


//START OF CERTIFICATE MESSAGE
  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  $output .= '<h3>Certificate Message<a class="description fright section-expander" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  //START OF CUSTOMER 
  $output .= '  <div class="inside" style="display: inline-block;width: 45.5%;">';
  $output .= '     <p>';
  $output .= '          <label><b>Customer</b></label><br><br>'; 
  $output .= '      </p>';
  $output .= '     <p>';
  $output .= '          <label><b>Subject</b></label><br>';
  if(isset($data['certificate_subject']) && trim($data['certificate_subject'])!='') $certificate_subject = $data['certificate_subject'];
  else $certificate_subject = '%quiztitle%';
  
  $output .= '             <input type="text" class="" name="certificate_subject" value="'.$certificate_subject.'">';
  $output .= '            <span><b>[Note: %quiztitle% variable used for Quiz Title.]</b></span><br><br>';	
  $output .= '      </p>';

  $output .= '     <p>';
  $output .= '          <label><b>Enable Dynamic Message</b></label>&nbsp;&nbsp;&nbsp;';
  if(isset($data['certificate_status']) && $data['certificate_status']=='on'){
  $output .= '             <input type="checkbox" class="toggleCheck" name="certificate_status" checked>';
  }
  else {
  $output .= '             <input type="checkbox" class="toggleCheck" name="certificate_status">';
  }
  $output .= '      </p>';
    //Textarea field
	$default_certificate_text = '<div><p>Congratulations, you successfully completed this module! Your score was %score% (%correct_val%/%total_questions%)</p></div>';
    $output .= '            <p>';
    $output .= '              <label><b>'. __( 'Message Text', 'balance' ) .':</b></label><br/>';
    $output .=                textarea_field( $data['certificate_text'], 'certificate_text', false, 10, $default_certificate_text, '', array('media_buttons' => false, 'quicktags' => true ) );
	$output .= '            <span><br><br><b>[Note: %score%, %correct_val%, %total_questions% variable used for your quiz score,correct answers and total questions.]</b></span>';
    $output .= '            </p>';
  $output .= '  </div>';
  //END OF CUSTOMER

  //START OF ADMIN/SUPERADMIN 
  $output .= '  <div class="inside" style="display: inline-block;width: 45.5%;">'; 
  $output .= '     <p>';
  $output .= '          <label><b>Admin / Superadmin</b></label><br><br>';
 $output .= '     </p>';
 $output .= '     <p>';
  $output .= '          <label><b>Subject</b></label><br>';
  if(isset($data['certificate_admin_subject']) && trim($data['certificate_admin_subject'])!='') $certificate_admin_subject = $data['certificate_admin_subject'];
  else $certificate_admin_subject = '%quiztitle%';
  $output .= '             <input type="text" class="" name="certificate_admin_subject" value="'.$certificate_admin_subject.'">';
  $output .= '            <span><b>[Note: %quiztitle% variable used for Quiz Title.]</b></span>';	
  $output .= '      </p>';

  $output .= '      </p>';

  $output .= '     <p>';
  $output .= '          <label><b>Enable Dynamic Message</b></label>&nbsp;&nbsp;&nbsp;';
  if(isset($data['certificate_admin_status']) && $data['certificate_admin_status']=='on'){
  $output .= '             <input type="checkbox" class="toggleCheck" name="certificate_admin_status" checked>';
  }
  else {
  $output .= '             <input type="checkbox" class="toggleCheck" name="certificate_admin_status">';
  }
  $output .= '      </p>';
    //Textarea field
	$default_certificate_admin_text = '<div><p>%user% has completed the quiz successfully.</p><BR>
				The score was %score% (%correct_val%/%total_questions%)
				<BR><BR>Website : %site_title%<BR>
				<BR><BR>Thanks<BR>
				Balancepro.org<BR></div>';
    $output .= '            <p>';
    $output .= '              <label><b>'. __( 'Message Text', 'balance' ) .':</b></label><br/>';
    $output .=                textarea_field( $data['certificate_admin_text'], 'certificate_admin_text', false, 10, $default_certificate_admin_text, '', array('media_buttons' => false, 'quicktags' => true ) );
	
	$output .= '        <span><b>[Note: %user%, %site_title%, %score%, %correct_val%, %total_questions% variable used for your quiz user name, website title, score,correct answers and total questions.]</b></span>';
    $output .= '            </p>';
  $output .= '  </div>';
  //END OF ADMIN/SUPERADMIN


  $output .= '</div>';
  //END OF CERTIFICATE MESSAGE

$output .= '      </div>';
		  $output .= '  </div>';
 $output .= '</div>';
		
  $output .= '</div>';

  /*END OF MESSAGES*/


/*START OF CHAT*/



  $output .= '<style>
	.box-div-settings{border: solid 3px #cccccc;padding: 10px 20px 20px 20px;margin-bottom: 10px;}
	</style>';
 
  $output .= '<div class="module-wrapper wlw-programs-module-wrapper-0 hidden" data-visible-on="tab-10">';
  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  $output .= '<h3>Chat<a class="description fright section-expander is-expanded" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  $output .= '  <div class="inside">';


  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  $output .= '        <br><p style="padding-left: 10px;">';
  $output .= '          <label><b>'. __( 'Enable chat', 'balance' ) .':</b></label>&nbsp;&nbsp;&nbsp;';
if($data['wlw_general_info_module'][ $key ]['disable_chat'] == "on" || $data['wlw_general_info_module'][ $key ]['disable_chat'] == "Yes") {
$output .= '<input type="checkbox" class="toggleCheck" name="wlw_general_info_module['.$key.'][disable_chat]" checked="checked" style="padding-top:10px !important">';
  } else {
$output .= '<input type="checkbox" class="toggleCheck" name="wlw_general_info_module['.$key.'][disable_chat]" style="padding-top:10px !important">';
  }
  //$output .=              radiobuttonlist_field( $data['wlw_general_info_module'][ $key ]['disable_chat'], 'wlw_general_info_module['.$key.'][disable_chat]', array( 'y' => __( 'Yes', 'balance' ), 'n' => __( 'No', 'balance' ) ), 'n', true);
  
  $output .= '        </p>';
  $output .= '</div>';

  $output .= '
  <a id="openLinkButton" class="add-holiday" onclick="openChat()">Chat now</a><br>
  
  <!-- Modal HTML -->
  <div id="chatModal" class="modal">
    <div class="modal-content">
      <span class="close">&times;</span>
      <p id="chatMessageContent"></p>
    </div>
  </div>';

  $output .='<p class="headings">Chat time settings</p>';
  $output .= '     <div class="box-div-settings"> ';

    // Define times array (12-hour format with AM/PM)
    $array_of_times = array();
    for ($i = 1; $i <= 12; $i++) {
        $v = ($i < 10) ? '0' . $i : $i; // Format hours with leading zero
        $array_of_times[] = array('id' => $v, 'title' => $v); // Only hours
    }

    $array_of_minutes = array();
    for ($i = 0; $i < 60; $i++) {
        $v = ($i < 10) ? '0' . $i : $i; // Format minutes with leading zero
        $array_of_minutes[] = array('id' => $v, 'title' => $v);
    }

    $time_am_pm = array(
        array('id' => 'AM', 'title' => 'AM'),
        array('id' => 'PM', 'title' => 'PM')
    );

// Get current day of the week (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
  $current_day_index = date('N') - 1; // PHP date('N') returns 1 (Monday) to 7 (Sunday)
  $current_day = $days[$current_day_index];
  $current_status = isset($data['timesettings'][$current_day . '_status']) && $data['timesettings'][$current_day . '_status'] == 'on' ? 'available' : 'unavailable';

  $output .='<br><p class="headings">Our normal business hours are in (UTC-08:00) Pacific Time (US)</p>';
  $output .= '<div style="margin-top: 20px;">';

  // Days of the week and statuses (for each day)
  $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
  $days_full = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

    foreach ($days as $key => $day) {
        $day_full_name = $days_full[$key];
        $status_var = isset($data['timesettings'][$day . '_status']) && $data['timesettings'][$day . '_status'] == 'on' ? 'checked' : '';

        $output .= '<div style="padding: 10px;
        display: grid;
        gap: 0px;
        align-items: center;
        grid-template-columns: 0.5fr 0.3fr 2fr;
        width: 55%; "><div>';
        $output .= '<b>' . $day_full_name . ':</b></div>';
        $output .= '<div><input type="checkbox" class="toggleCheck" name="timesettings[' . $day . '_status]" ' . $status_var . ' style="padding-top:10px !important"></div>';
        $output.='<div>';
        // Start Time
        $output .= dropdown_field($data['timesettings'][$day . '_start'], 'timesettings[' . $day . '_start]', '', $array_of_times, '', false, $day . '_start', 'time-dropdown', 'onchange="validateTime(\'' . $day . '\')"'); // Hour only
        $output .= dropdown_field($data['timesettings'][$day . '_start_minutes'], 'timesettings[' . $day . '_start_minutes]', '', $array_of_minutes, '', false, $day . '_start_minutes', 'time-dropdown', 'onchange="validateTime(\'' . $day . '\')"'); // Minutes
        $output .= dropdown_field($data['timesettings'][$day . '_start_ap'], 'timesettings[' . $day . '_start_ap]', '', $time_am_pm, '', false, $day . '_start_ap', 'time-dropdown', 'onchange="validateTime(\'' . $day . '\')"'); // AM/PM
        $output .= ' to ';
        // End Time
        $output .= dropdown_field($data['timesettings'][$day . '_end'], 'timesettings[' . $day . '_end]', '', $array_of_times, '', false, $day . '_end', 'time-dropdown', 'onchange="validateTime(\'' . $day . '\')"'); // Hour only
        $output .= dropdown_field($data['timesettings'][$day . '_end_minutes'], 'timesettings[' . $day . '_end_minutes]', '', $array_of_minutes, '', false, $day . '_end_minutes', 'time-dropdown', 'onchange="validateTime(\'' . $day . '\')"'); // Minutes
        $output .= dropdown_field($data['timesettings'][$day . '_end_ap'], 'timesettings[' . $day . '_end_ap]', '', $time_am_pm, '', false, $day . '_end_ap', 'time-dropdown', 'onchange="validateTime(\'' . $day . '\')"'); // AM/PM
        $output .= '</div>';
        $output .= '</div>';
    }

      $output .= '</div>';
      $output .= '</div>';
      // Pass PHP days array to JavaScript
      $days_js = json_encode($days);

      $output .= '
      <script>
      var days = ' . $days_js . ';

      // Real-time validation for all days
      days.forEach(function(day) {
          document.querySelector("[name=\'timesettings[" + day + "_start]\']").addEventListener("change", function() {
              validateTime(day);
          });
          document.querySelector("[name=\'timesettings[" + day + "_start_minutes]\']").addEventListener("change", function() {
              validateTime(day);
          });
          document.querySelector("[name=\'timesettings[" + day + "_start_ap]\']").addEventListener("change", function() {
              validateTime(day);
          });
          document.querySelector("[name=\'timesettings[" + day + "_end]\']").addEventListener("change", function() {
              validateTime(day);
          });
          document.querySelector("[name=\'timesettings[" + day + "_end_minutes]\']").addEventListener("change", function() {
              validateTime(day);
          });
          document.querySelector("[name=\'timesettings[" + day + "_end_ap]\']").addEventListener("change", function() {
              validateTime(day);
          });
      });

      // Time validation function
      function validateTime(day) {
          // Get selected values for start time
          var startHour = document.querySelector("[name=\'timesettings[" + day + "_start]\']").value;
          var startMinute = document.querySelector("[name=\'timesettings[" + day + "_start_minutes]\']").value;
          var startAP = document.querySelector("[name=\'timesettings[" + day + "_start_ap]\']").value;

          // Get selected values for end time
          var endHour = document.querySelector("[name=\'timesettings[" + day + "_end]\']").value;
          var endMinute = document.querySelector("[name=\'timesettings[" + day + "_end_minutes]\']").value;
          var endAP = document.querySelector("[name=\'timesettings[" + day + "_end_ap]\']").value;

          // Convert to 24-hour format for easier comparison
          var start24Hour = convertTo24HourFormat(startHour, startAP);
          var end24Hour = convertTo24HourFormat(endHour, endAP);

          // Combine hours and minutes for comparison
          var startTime = start24Hour + ":" + startMinute;
          var endTime = end24Hour + ":" + endMinute;

          // Get dropdown fields
          var startDropdowns = document.querySelectorAll("[name=\'timesettings[" + day + "_start]\'], [name=\'timesettings[" + day + "_start_minutes]\'], [name=\'timesettings[" + day + "_start_ap]\']");
          var endDropdowns = document.querySelectorAll("[name=\'timesettings[" + day + "_end]\'], [name=\'timesettings[" + day + "_end_minutes]\'], [name=\'timesettings[" + day + "_end_ap]\']");

          // Compare times
          if (startTime >= endTime) {
              // Invalid time range, set border to red
              startDropdowns.forEach(function(dropdown) { dropdown.style.border = "1px solid red"; });
              endDropdowns.forEach(function(dropdown) { dropdown.style.border = "1px solid red"; });
          } else {
              // Valid time range, remove red border
              startDropdowns.forEach(function(dropdown) { dropdown.style.border = ""; });
              endDropdowns.forEach(function(dropdown) { dropdown.style.border = ""; });
          }
      }

      // Helper function to convert to 24-hour format
      function convertTo24HourFormat(hour, ampm) {
          hour = parseInt(hour);
          if (ampm === "PM" && hour !== 12) {
              hour += 12;
          } else if (ampm === "AM" && hour === 12) {
              hour = 0; // Midnight case
          }
          return (hour < 10 ? "0" + hour : hour); // Ensure two digits
      }
      </script>';
      function calculateHolidayDates($year) {
        $holidays = array(
            'New Year\'s Day' => date('Y-m-d', strtotime("$year-01-01")),
            'Martin Luther King Birthday' => date('Y-m-d', strtotime("third Monday of January $year")),
            'Presidents\' Day' => date('Y-m-d', strtotime("third Monday of February $year")),
            'Memorial Day' => date('Y-m-d', strtotime("last Monday of May $year")),
            'Independence Day' => date('Y-m-d', strtotime("$year-07-04")),
            'Labor Day' => date('Y-m-d', strtotime("first Monday of September $year")),
            'Columbus Day' => date('Y-m-d', strtotime("second Monday of October $year")),
            'Veterans\' Day' => date('Y-m-d', strtotime("$year-11-11")),
            'Thanksgiving Day' => date('Y-m-d', strtotime("fourth Thursday of November $year")),
            'Christmas Day' => date('Y-m-d', strtotime("$year-12-25"))
        );
        return $holidays;
    }    

      // Get current year and today's date
      $currentYear = date('Y');
      $today = date('Y-m-d');

      // Get holiday dates for the current year
      $holidays = calculateHolidayDates($currentYear);

      // Check if today's date is past each holiday, and if so, update it for the next year
      foreach ($holidays as $holiday => $date) {
        if ($today > $date) {
            // Update holiday to next year's date
            $holidays[$holiday] = calculateHolidayDates($currentYear + 1)[$holiday];
        }
      }

      // Start output
      $output .='<p class="headings">Holiday Settings</p>';
      $output .= '<div class="box-div-settings">';
      $output .='<p class="headings">Federal Holidays</p>';

      // Output holiday name, date, and short day name (Mon, Tue, etc.)
      foreach ($holidays as $holiday => $date) {
        // Get the short day name for the holiday
        $shortDayName = strtolower(date('D', strtotime($date))); // 'D' gives short textual representation of the day (e.g., 'Mon')
        
        // Create output with holiday name, date, and short day name
        $output .= '<div style="display: grid; grid-template-columns: auto auto; justify-content: space-between; width:35%;">';
        $output .= '<label><b>' . $holiday . ':</b></label>';
        $output .= '<input style="width:auto !important; color:#898989;" type="text" value="' . $date . '" readonly><br></div>';
      }

// Merge federal holidays and custom holidays for easier processing
foreach ($custom_holidays as $custom_holiday) {
  $holidays[$custom_holiday['name']] = $custom_holiday['date'];
}

// Days of the week and statuses (for each day)
$days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
$days_full = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

foreach ($days as $key => $day) {
  $day_full_name = $days_full[$key];

  // Default status: set to the current status in $data (could be checked or unchecked)
  $status_var = isset($data['timesettings'][$day . '_status']) && $data['timesettings'][$day . '_status'] == 'on' ? 'checked' : '';

  // Check if any holiday (federal or custom) matches today's date
  foreach ($holidays as $holiday_name => $holiday_date) {
      if ($today == $holiday_date) {
          // If today is a holiday, and it falls on this weekday, set status to off
          $holiday_day_of_week = strtolower(date('D', strtotime($holiday_date))); // Get the holiday's day of the week

          if ($holiday_day_of_week == $day) {
              $status_var = ''; // Uncheck the checkbox (status is off)
              $data['timesettings'][$day . '_status'] = 'off'; // Set the status for this day to "off"
              break; // Exit the loop since we found a holiday for today
          }
      }
  }

  // Output the day settings with the hidden container
  $output .= '<div style="padding: 10px; display:none; grid-template-columns: 0.3fr 0.3fr 2fr; width: 55%;">';
  $output .= '<div><b>' . $day_full_name . ':</b></div>';
  $output .= '<div><input type="checkbox" class="toggleCheck" name="timesettings[' . $day . '_status]" ' . $status_var . '></div>';
  $output .= '</div>';
}


//       $output .='<p class="holidayheading">Custom Holidays</p>';
//       $output .= '
//       <button id="addHolidayBtn">Add Holiday</button>
      
//       <!-- Hidden form to add a holiday -->
//       <div id="holidayFormContainer" style="display:none;">
//       <p id="error-msg" style="color:red; font-size:16px; display:none;">Holiday already exists!</p>
//           <form id="holidayForm">
//               <span class="hlabels">Holiday name:</span>
//               <input type="text" name="holiday_name" placeholder="Holiday Name" required />
//               <span class="hlabels"> On: </span>
//               <input type="date" name="holiday_date" required />
//               <button class="add-holiday" type="submit" id="saveHolidayBtn">Save</button>
//           </form>
//       </div>
      
//       <!-- Existing holidays -->
//       <div id="holidayList">';
      
//       // Reverse the order of holidays
//       $custom_holidays = array_reverse($custom_holidays);
      
//       $existing_holidays = [];
//       foreach ($custom_holidays as $index => $holiday) {
//           $output .= '
//           <div class="holiday-item" data-index="' . $index . '">
//               <span class="hlabels">Holiday name:</span>
//               <input type="text" value="' . esc_attr($holiday['name']) . '" readonly/>
//               <span class="hlabels"> On: </span>
//               <input type="date" value="' . esc_attr($holiday['date']) . '" readonly/>
//               <button class="remove-holiday">Remove</button>
//           </div>';
          
//           // Store existing holidays in a JS array
//           $existing_holidays[] = ['name' => esc_attr($holiday['name']), 'date' => esc_attr($holiday['date'])];
//       }
      
//       // Add the existing holidays array to a JS variable
//       $output .= '</div>';

//       // Add CSS for greyed-out disabled button
//         $output .= '<style>
//         button:disabled {
//             background-color: #ccc;
//             color: #666;
//             cursor: not-allowed;
//         }
//         </style>';

//         $output .= '<script>
//         jQuery(document).ready(function($) {
//             // Array of existing holidays
//             let existingHolidays = ' . json_encode($existing_holidays) . ';

//             // Toggle the visibility of the holiday form
//             $("#addHolidayBtn").on("click", function(e) {
//                 e.preventDefault();
//                 $("#holidayFormContainer").toggle(); // Show/hide the form without reloading
//             });

//             // Validation: Check if the holiday already exists
//             function holidayExists(name, date) {
//                 for (let i = 0; i < existingHolidays.length; i++) {
//                     if (existingHolidays[i].name.toLowerCase() === name.toLowerCase() || existingHolidays[i].date === date) {
//                         return true;
//                     }
//                 }
//                 return false;
//             }

//             // Check for existing holiday on form input change
//             $("#holidayForm input").on("input", function() {
//                 let holidayName = $("input[name=\'holiday_name\']").val();
//                 let holidayDate = $("input[name=\'holiday_date\']").val();
                
//                 if (holidayExists(holidayName, holidayDate)) {
//                     $("#error-msg").show(); // Show error message
//                     $("#saveHolidayBtn").prop("disabled", true); // Disable save button
//                 } else {
//                     $("#error-msg").hide(); // Hide error message
//                     $("#saveHolidayBtn").prop("disabled", false); // Enable save button
//                 }
//             });

//             // Save holiday using AJAX
//             $("#holidayForm").on("submit", function(e) {
//                 e.preventDefault(); // Prevent form submission (no reload)
                
//                 let holidayName = $("input[name=\'holiday_name\']").val();
//                 let holidayDate = $("input[name=\'holiday_date\']").val();

//                 // Double check before submitting
//                 if (holidayExists(holidayName, holidayDate)) {
//                     $("#error-msg").show(); // Show error message
//                     return; // Do not proceed with AJAX request
//                 }

//                 $.ajax({
//                     url: "' . admin_url('admin-ajax.php') . '",
//                     type: "POST",
//                     data: {
//                         action: "save_custom_holiday",
//                         post_id: ' . $post_id . ',
//                         holiday_name: holidayName,
//                         holiday_date: holidayDate
//                     },
//                     success: function(response) {
//                         $("#holidayList").append(response);
//                         $("#holidayForm")[0].reset(); // Clear form fields
//                         $("#holidayFormContainer").hide(); // Hide the form again after submission
//                         $("#error-msg").hide(); // Hide error message
//                         $("#saveHolidayBtn").prop("disabled", false); // Re-enable save button
//                         // Add the new holiday to existingHolidays array
//                         existingHolidays.push({name: holidayName, date: holidayDate});
//                     }
//                 });
//             });

//             // Remove holiday using AJAX
//             $("#holidayList").on("click", ".remove-holiday", function(e) {
//                 e.preventDefault(); // Prevent page reload on button click

//                 let index = $(this).closest(".holiday-item").data("index");

//                 $.ajax({
//                     url: "' . admin_url('admin-ajax.php') . '",
//                     type: "POST",
//                     data: {
//                         action: "remove_custom_holiday",
//                         post_id: ' . $post_id . ',
//                         index: index
//                     },
//                     success: function(response) {
//                         if (response.success) {
//                             $("[data-index=\'" + index + "\']").remove();
//                             // Remove the holiday from the existingHolidays array
//                             existingHolidays.splice(index, 1);
//                         }
//                     }
//                 });
//             });
//         });
//     </script>';

// $output .= '</div>';

// END HOLIDAY 

  //START OF CHAT MESSAGE
  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  //$output .= '<h3>Chat Message<a class="description fright section-expander" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  $output .= '  <div class="inside">';  
  $output .= '     <p>';
  $tooltip_text=$data['tooltip_text'];
  $output .= '<label><p class="headings">Tooltip Text</p></label>';
  $output .= '             <input type="text" class="" disabled=disabled name="tooltip_text" value="'.$tooltip_text.'">';
  $output .= '  <span><b>[Note: This text will appear as tooltip(title) after Available/Unavailable.]</b></span>';
  $output .= '      </p>';
  
  $output .= '     <p>';
  $output .= '<label><p class="headings">Message Text:</p>';
   if(isset($data['chat_message']) && trim($data['chat_message'])!='') $chat_message = $data['chat_message'];
   else $chat_message = '<p>BALANCE chat is currently unavailable. Our normal business hours are in Pacific Time (US):<br> %timesettings%</p>';
  
  $output .=  textarea_field( $data['chat_message'], 'chat_message', false, 10, $chat_message, '', array('media_buttons' => false, 'quicktags' => true ) );
  $output .= '  <span><b>[Note: This text message will appear in new window when chat is unavailable. %sitetitle%  and %timesettings% as specified in settings tab.]</b></span>';
  $output .= '  </p>';  
  $output .= '  </div>';
  $output .= '</div>';
  
  //END OF CHAT MESSAGE

  

  $output .= '  </div>';
  $output .= '</div>';
  $output .= '</div>';

  /*END OF SETTINGS*/

  $days = ['mon', 'tue', 'wed', 'thu', 'fri', 'sat', 'sun'];
  $timesettings = $data['timesettings']; // Make sure this contains all the dropdown values

  $$output .= '<script>';
  $output .= 'var timesettings = ' . json_encode($timesettings) . ';';
  $output .= '</script>';

  $output .= '<script>
  function openChat() {
      var modal = document.getElementById("chatModal");
      var currentDay = new Date().getDay(); // Get the current day (0 = Sunday, 6 = Saturday)
      var currentHour = new Date().getHours(); // Get current hour (24-hour format)
      var currentMinute = new Date().getMinutes(); // Get current minutes
      
      var timesettings = ' . json_encode($data['timesettings']) . ';
      var days = ["sun", "mon", "tue", "wed", "thu", "fri", "sat"];
      // Get current day settings from PHP
      var dayStatus = timesettings[days[currentDay] + "_status"];
      var startTime = {
          hour: parseInt(timesettings[days[currentDay] + "_start"]),
          minutes: parseInt(timesettings[days[currentDay] + "_start_minutes"]),
          ampm: timesettings[days[currentDay] + "_start_ap"]
      };
      var endTime = {
          hour: parseInt(timesettings[days[currentDay] + "_end"]),
          minutes: parseInt(timesettings[days[currentDay] + "_end_minutes"]),
          ampm: timesettings[days[currentDay] + "_end_ap"]
      };

      // Convert start and end times to 24-hour format for comparison
      startTime.hour = (startTime.ampm === "PM" && startTime.hour !== 12) ? startTime.hour + 12 : startTime.hour;
      endTime.hour = (endTime.ampm === "PM" && endTime.hour !== 12) ? endTime.hour + 12 : endTime.hour;9969967
      if (startTime.ampm === "AM" && startTime.hour === 12) startTime.hour = 0;
      if (endTime.ampm === "AM" && endTime.hour === 12) endTime.hour = 0;

      var startMinutes = startTime.hour * 60 + startTime.minutes;
      var endMinutes = endTime.hour * 60 + endTime.minutes;
      var currentMinutes = currentHour * 60 + currentMinute;

      if (dayStatus === "on" && currentMinutes >= startMinutes && currentMinutes <= endMinutes) {
          // Chat is available, load the chat URL inside the modal
          document.getElementById("chatMessageContent").innerHTML = \'<iframe src="https://chat.balancepro.org/i3root/chat_cccs/index.html" style="width:100%;height:400px;"></iframe>\';
      } else {
            // Chat is unavailable, display a message with business hours
            var unavailableMessage = "BALANCE chat is currently unavailable. Our normal business hours are in Pacific Time (US):<br><b>Schedule:</b><br>";
            
            days.forEach(function(day) {
                var daySchedule = timesettings[day + "_status"] === "on" ?
                    timesettings[day + "_start"] + " " + timesettings[day + "_start_ap"] + " - " + timesettings[day + "_end"] + " " + timesettings[day + "_end_ap"] :
                    "Closed";
                unavailableMessage += "<b>" + day.charAt(0).toUpperCase() + day.slice(1) + ":</b> " + daySchedule + "<br>";
            });

            if (dayStatus === "off") unavailableMessage += " (Closed Today)";
            
            document.getElementById("chatMessageContent").innerHTML = unavailableMessage;
        }

      // Show the modal
      modal.style.display = "block";
  }

  // Close modal when clicking the close button
  document.querySelector(".close").onclick = function() {
      document.getElementById("chatModal").style.display = "none";
  };

  // Close modal when clicking outside of the modal
  window.onclick = function(event) {
      var modal = document.getElementById("chatModal");
      if (event.target == modal) {
          modal.style.display = "none";
      }
  };
</script>';

$output.='
  <style>
    /* The Modal (background) */
    .modal {
      display: none; /* Hidden by default */
      position: fixed; /* Stay in place */
      z-index: 1; /* Sit on top */
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto; /* Enable scroll if needed */
      background-color: rgba(0, 0, 0, 0.4); /* Black with opacity */
    }
  
    /* Modal Content */
    .modal-content {
      background-color: #fefefe;
      margin: 15% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%;
      max-width: 500px; /* Width of the modal */
    }
  
    /* The Close Button */
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }
  
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }
  </style>';

	return $output;
}
?>
