
<?php
/*
 *  WP Edit module: White Label Websites Programs
 */
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
session_start();

function white_label_websites_programs_module_form( $key, $visible_on = 'all', $module_title = '', $custom_settings = array() ) {
	global $data;
  global $wpdb,$post_id;
  $current_date = date('Y-m-d');
  
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return $post_id; }
		if ( ! current_user_can( 'edit_post', $post_id ) ) { return $post_id; }
		if ( ! current_user_can( 'edit_page', $post_id ) ) { return $post_id; }
  // print_r($post_id);
  $prefix = $wpdb->prefix;
  $white_label_websites_table_name = $prefix . 'white_label_websites';
  $post_table_id = $prefix . 'post_id';
  $white_label_website_id = $wpdb->get_var( "SELECT white_label_website_id FROM $white_label_websites_table_name WHERE $post_table_id = '$post_id';" );
  // $query="SELECT custom_holidays FROM $white_label_websites_table_name WHERE $post_table_id = '$post_id';";
  $custom_holidays_array = $wpdb->get_var( "SELECT custom_holidays FROM $white_label_websites_table_name WHERE $post_table_id = '$post_id';" );
  $custom_holidays = json_decode($custom_holidays_array,  true);
  if ($custom_holidays != NULL) {
    $count_custom_holidays = count($custom_holidays);
} else {
    $count_custom_holidays = 0;
}

	if ( empty( $data['wlw_programs_module'] ) ) {
		$data = init_array_on_var_and_array_key($data, 'wlw_programs_module');
		$data['wlw_programs_module'][ $key ] = array(
			'programs' => '',
			'programs_protected' => 'individually'
		);
	}


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

function is_today_a_holiday($holidays, $custom_holidays) {
  // Set the current date
  $current_date = date('m/d/Y'); // Adjust format as needed (e.g., "10/06/2024")
  $current_date_custom = date('Y-m-d');

  // Check static holidays
  if (isset($holidays) && is_array($holidays)) {
      foreach ($holidays as $holiday) {
          if ($holiday == $current_date) {
              return true; // If today matches a holiday, return true
          }
      }
  }

  // Check custom holidays
  if (isset($custom_holidays) && is_array($custom_holidays)) {
      foreach ($custom_holidays as $custom_holiday) {
          if (isset($custom_holiday['date']) && $custom_holiday['date'] == $current_date || $custom_holiday['date']==$current_date_custom) {
              return true; // If today matches a custom holiday, return true
          }
      }
  }
  else
  return false; // No matching holiday found
}

function generateDaySettings($day, $dayLabel, $data, $array_of_times, $array_of_minutes, $time_am_pm) {
  $status_key = strtolower($day) . '_status';
  $start_key = strtolower($day) . '_start';
  $start_min_key = strtolower($day) . '_start_min';
  $start_ap_key = strtolower($day) . '_start_ap';
  $end_key = strtolower($day) . '_end';
  $end_min_key = strtolower($day) . '_end_min';
  $end_ap_key = strtolower($day) . '_end_ap';

  $status = isset($data['timesettings'][$status_key]) && $data['timesettings'][$status_key] == 'on' ? 'checked' : '';  
  
  $output = '<div class="day-settings">';
  $output .= "<b>$dayLabel:</b>";
  $output .= '<input type="checkbox" class="toggleCheck" name="timesettings[' . $status_key . ']" ' . $status . '>';
  // $output .= '<script>console.log("validateDayTimeFrame('.$day.')")</script>';
  // Start Time Dropdowns with onchange event to trigger validateDayTimeFrame
  $output .= dropdown_field_new($data['timesettings'][$start_key], 'timesettings[' . $start_key . ']', '', $array_of_times, '', false, false,  'validateDayTimeFrame(\'' . $day . '\')');

  $output .= dropdown_field_new($data['timesettings'][$start_min_key], 'timesettings[' . $start_min_key . ']', '', $array_of_minutes, '', false, false, 'validateDayTimeFrame(\'' . $day . '\')');
  $output .= dropdown_field_new($data['timesettings'][$start_ap_key], 'timesettings[' . $start_ap_key . ']', '', $time_am_pm, '', false, false,'validateDayTimeFrame(\'' . $day . '\')');
  
  $output .= ' to ';
  
  // End Time Dropdowns with onchange event to trigger validateDayTimeFrame
  $output .= dropdown_field_new($data['timesettings'][$end_key], 'timesettings[' . $end_key . ']', '', $array_of_times, '', false, false, 'validateDayTimeFrame(\'' . $day . '\')');
  $output .= dropdown_field_new($data['timesettings'][$end_min_key], 'timesettings[' . $end_min_key . ']', '', $array_of_minutes, '', false, false, 'validateDayTimeFrame(\'' . $day . '\')');
  $output .= dropdown_field_new($data['timesettings'][$end_ap_key], 'timesettings[' . $end_ap_key . ']', '', $time_am_pm, '', false, false, 'validateDayTimeFrame(\'' . $day . '\')');
  
  // Embed the script for validation inside the output
  $output .= "
<script>
    function validateDayTimeFrame(day) {
        // Get the dropdowns by their name
        let startHour = document.querySelector('select[name=\"timesettings[' + day + '_start]\"]');
        let startMin = document.querySelector('select[name=\"timesettings[' + day + '_start_min]\"]');
        let startAP = document.querySelector('select[name=\"timesettings[' + day + '_start_ap]\"]');
        
        let endHour = document.querySelector('select[name=\"timesettings[' + day + '_end]\"]');
        let endMin = document.querySelector('select[name=\"timesettings[' + day + '_end_min]\"]');
        let endAP = document.querySelector('select[name=\"timesettings[' + day + '_end_ap]\"]');
        
        // Get the checkbox for this day
        let toggleCheck = document.querySelector('input[name=\"timesettings[' + day + '_status]\"]');
        
        // Check if any of the dropdowns or checkbox are not found
        if (!startHour || !startMin || !startAP || !endHour || !endMin || !endAP || !toggleCheck) {
            console.warn('One or more dropdowns or checkbox not found for', day);
            return false;
        }

        // Convert start and end times to comparable 24-hour format
        let startTime = convertTo24Hour(startHour.value, startMin.value, startAP.value);
        let endTime = convertTo24Hour(endHour.value, endMin.value, endAP.value);
        
     // Time limits in minutes: 12:00 AM = 0, 11:59 PM = 1439
        const startTimeLimit = 0;     // 12:00 AM
        const endTimeLimit = 1439;    // 11:59 PM

        let isValid = true;

        // Check for illogical times
        if (startTime < startTimeLimit || startTime > endTimeLimit || endTime < startTimeLimit || endTime > endTimeLimit || startTime >= endTime) {
            // Add red border to indicate error
            startHour.style.border = '2px solid red';
            startMin.style.border = '2px solid red';
            startAP.style.border = '2px solid red';
            endHour.style.border = '2px solid red';
            endMin.style.border = '2px solid red';
            endAP.style.border = '2px solid red';
            
            // Uncheck the checkbox if time is illogical
            // toggleCheck.checked = false;  // Uncheck the checkbox
            isValid = false;
        } else {
            // Reset the border if the time is logical
            startHour.style.border = '';
            startMin.style.border = '';
            startAP.style.border = '';
            endHour.style.border = '';
            endMin.style.border = '';
            endAP.style.border = '';
            
            // Optionally, keep the checkbox checked if it was previously checked
            // toggleCheck.checked = toggleCheck.checked;  // Retain the current state
        }

        return isValid;
    }

    // Check only the specific day time frame for toggles
    function checkDayTimeFrame(day) {
        validateDayTimeFrame(day);
    }

    // Call this function on input change for validation in real-time
    document.querySelectorAll('select').forEach(function(element) {
        element.addEventListener('change', function() {
            let day = this.name.match(/timesettings\[(.+?)_(start|end|min|ap)\]/)[1]; // Extract the day from the select name
            checkDayTimeFrame(day); // Validate only the relevant day
        });
    });

    // Initial validation check for each day when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        let days = ['mon', 'tues', 'wed', 'thurs', 'fri','sat','sun']; // Add relevant days
        days.forEach(function(day) {
            validateDayTimeFrame(day);  // Validate all time fields for each day on load
        });
    });
</script>";

  
  $output .= '</div>';
  
  return $output;
}
function OLD_generateDaySettings($day, $dayLabel, $data, $array_of_times, $array_of_minutes, $time_am_pm) {
    $status_key = strtolower($day) . '_status';
    $start_key = strtolower($day) . '_start';
    $start_min_key = strtolower($day) . '_start_min';
    $start_ap_key = strtolower($day) . '_start_ap';
    $end_key = strtolower($day) . '_end';
    $end_min_key = strtolower($day) . '_end_min';
    $end_ap_key = strtolower($day) . '_end_ap';

    $status = isset($data['timesettings'][$status_key]) && $data['timesettings'][$status_key] == 'on' ? 'checked' : '';
    
    $output = '<div class="day-settings">';
    $output .= "<b>$dayLabel:</b>";
    $output .= '<input type="checkbox" class="toggleCheck" name="timesettings['.$status_key.']" '.$status.'>';
    
        $output .= dropdown_field($data['timesettings'][$start_key], 'timesettings['.$start_key.']', '', $array_of_times, '', false);
    $output .= dropdown_field($data['timesettings'][$start_min_key], 'timesettings['.$start_min_key.']', '', $array_of_minutes, '', false);
    $output .= dropdown_field($data['timesettings'][$start_ap_key], 'timesettings['.$start_ap_key.']', '', $time_am_pm, '', false);
    
    $output .= ' to ';
    
    $output .= dropdown_field($data['timesettings'][$end_key], 'timesettings['.$end_key.']', '', $array_of_times, '', false);
    $output .= dropdown_field($data['timesettings'][$end_min_key], 'timesettings['.$end_min_key.']', '', $array_of_minutes, '', false);
    $output .= dropdown_field($data['timesettings'][$end_ap_key], 'timesettings['.$end_ap_key.']', '', $time_am_pm, '', false);
    
    $output .= '</div>';
    
    return $output;
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
  $output .= '       &nbsp;&nbsp;';
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
 // $output .= '       &nbsp;&nbsp;';
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

//   <script>
//     function openChat() {
//         window.open("' . admin_url('chatmessage.php') . '", "_blank"); // Use admin_url() for the correct path
//     }
// </script>

/*START OF CHAT*/
$output .= '
<button id="openLinkButton" class="blue-btn" onclick="openChat()">Open Link</button>

<!-- Modal HTML -->
<div id="chatModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <p id="chatMessageContent"></p>
  </div>
</div>';


$output .=  '<div class="time-settings">';

$output .= '<div class="settings-container">';

  // $output .= '<br><style>
	// .box-div-settings{border: solid 3px #cccccc;padding: 10px 20px 20px 20px;margin-bottom: 10px;}
	// </style>';
 
  $output .= '<div class="module-wrapper wlw-programs-module-wrapper-0 hidden" data-visible-on="tab-10">';
  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  $output .= '<h3 style="padding-left: 10px;font-weight: 700;font-size: 17px !important;" >Chat<a class="description fright section-expander is-expanded" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  $output .= '  <div class="inside">';


  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  $output .= '        <br><p class="day-settings" style="padding-left: 10px;font-weight: 700;font-size:15px !important;">';
  $output .= '          <label>'. __( 'Enable Chat', 'balance' ) .'</label>&nbsp;&nbsp;&nbsp;';
    if($data['wlw_general_info_module'][ $key ]['disable_chat'] == "on" || $data['wlw_general_info_module'][ $key ]['disable_chat'] == "Yes") {
    $output .= '<input type="checkbox" class="toggleCheck" name="wlw_general_info_module['.$key.'][disable_chat]" checked="checked" style="padding-top:10px !important">';
      } else {

    $output .= '<input type="checkbox" class="toggleCheck" name="wlw_general_info_module['.$key.'][disable_chat]" style="padding-top:10px !important">';
        
     }   

  // $output .=              radiobuttonlist_field( $data['wlw_general_info_module'][ $key ]['disable_chat'], 'wlw_general_info_module['.$key.'][disable_chat]', array( 'y' => __( 'Yes', 'balance' ), 'n' => __( 'No', 'balance' ) ), 'n', true);
  
  $output .= '        </p>';
  // $output .= '</div>';
  //style="font-size: 15px;margin-left: 10px;font-weight:600
  
  $output .= '  <b style="margin-left: 10px;font-weight:600">Chat time settings</b><br><br> ';
  $output .= '  <div class="box-div-settings"> ';
  $array_of_times = array();
  for($i=1;$i<=12;$i++){
	  if($i<10) $v='0'.$i.''; else $v=$i.'';
	  $array_of_times[] = array('id'=>$v,'title'=>$v);
	  // if($i<10) $k='0'.$i.''; else $k=$i.'';
	  // $array_of_times[] = array('id'=>$k,'title'=>$k);
  }

  // 	    // Create Minutes Array (0 to 59)
        $array_of_minutes = array();
        for ($j=0; $j<60; $j++) {
          $minute_value = str_pad($j, 2, '0', STR_PAD_LEFT); // Pad single digits with a leading zero
          $array_of_minutes[] = array('id' => $minute_value, 'title' => $minute_value);
        }

    $time_am_pm = array(
    0 => array(
      'id' => 'AM',
      'title' => 'AM',
    ),
    1 => array(
      'id' => 'PM',
      'title' => 'PM',
    ));
    $currentDate = new DateTime(); // Get the current date

    // $holidays_org = array(
    //   'New Year\'s Day' => date('Y').'-01-01',
    //   'Martin Luther King Jr. Day' => date('Y').'-'.get_nth_weekday_of_month(3, 1, 1), // 3rd Monday of January
    //   'Presidents\' Day' => date('Y').'-'.get_nth_weekday_of_month(3, 1, 2), // 3rd Monday of February
    //   'Memorial Day' => date('Y').'-'.get_last_weekday_of_month(1, 5), // Last Monday of May
    //   'Independence Day' => date('Y').'-07-04',
    //   'Labor Day' => date('Y').'-'.get_nth_weekday_of_month(1, 1, 9), // 1st Monday of September
    //   'Columbus Day' => date('Y').'-'.get_nth_weekday_of_month(2, 1, 10), // 2nd Monday of October
    //   'Veterans\' Day' => date('Y').'-11-11',
    //   'Thanksgiving Day' => date('Y').'-'.'11-28', // 4th Thursday of November
    //   'Christmas Day' => date('Y').'-12-25'  );

      $year = date('Y');
      $today = date('Y-m-d');
     
    
    
    // Adjust holidays to next year if they are in the past
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

 
  $output .= '<p style="font-weight:700;font-size:15px!important;">Our normal business hours are in (UTC-08:00) Pacific Time (US)</p><br>';
 

  // Generate settings for each day using the function
  $output .= generateDaySettings('mon', 'Monday', $data, $array_of_times, $array_of_minutes, $time_am_pm);
  $output .= generateDaySettings('tue', 'Tuesday', $data, $array_of_times, $array_of_minutes, $time_am_pm);
  $output .= generateDaySettings('wed', 'Wednesday', $data, $array_of_times, $array_of_minutes, $time_am_pm);
  $output .= generateDaySettings('thu', 'Thursday', $data, $array_of_times, $array_of_minutes, $time_am_pm);
  $output .= generateDaySettings('fri', 'Friday', $data, $array_of_times, $array_of_minutes, $time_am_pm);
  $output .= generateDaySettings('sat', 'Saturday', $data, $array_of_times, $array_of_minutes, $time_am_pm);
  $output .= generateDaySettings('sun', 'Sunday', $data, $array_of_times, $array_of_minutes, $time_am_pm);  
 
  $output .= '</div>';
  $output .=  '</div>';
  // $status_var = isset($data['timesettings'][$day .' status']) && $data[ 'timesettings'] [$day .'status'] ='on' ? 'checked' :

  
                  $output .= '<div class="settings-container">';

                  $output .= '<h3 style="font-size:15px;font-weight: 700";>Federal Holidays</h3><br>';
                  $output .= '<table>'; // Begin table for layout
                  
                  foreach ($holidays as $holiday => $date) {
                      $output .= '<tr>';
             
                    $day = strtolower(date('D', strtotime($date))); // returns short day (e.g., "mon", "tue")
                      // Display holiday name in bold
                      $output .= '<td style="font-weight: bold; padding-right: 15px;">' . $holiday . ':</td>';
                      
                      // Display date in a disabled input calendar
                      $output .= '<td><input name="timesettings[' . $day . '_status]" type="date" value="' . $date . '" disabled="disabled" style="width: 150px;"></td>';
                      
                      $output .= '</tr>';
                  }
                  
                  $output .= '</table>'; // End table layout
                  $output .=  '</div>';
                 // Section for adding custom holidays
 

$output .= '<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>';                
                 $output .= '<div class="settings-container">';
        // $output.='<button id="openFormBtn" class="blue-btn" onclick="">Add Holiday</button>';
  $output.='<button class="blue-btn" id="addHolidayBtn">Add Holiday</button>';

// Div to display the list of holidays
$output .= '<div id="holidayList">';

$output.='<br> <div id="holidayFormContainer" style="display:none;">
      <p id="error-msg" style="color:red; font-size:16px; display:none;">Holiday Name/Date already exists!</p>
          <form id="holidayForm">
              <span style="font-weight:bold" class="hlabels">Holiday name:</span>
              <input type="text" name="holiday_name" placeholder="Holiday Name" required />
              <span style="font-weight:bold" class="hlabels"> On: </span>
              <input type="date" name="holiday_date" required />
              <button class="add-holiday" type="submit" id="saveHolidayBtn">Save</button>
          </form>
      </div><br>';

if ($custom_holidays) {
  $custom_holidays = array_reverse($custom_holidays);
  $existing_holidays = [];
  foreach ($custom_holidays as $index => $holiday) {
      $output .= '<div class="holiday-item" data-index="' . $index . '">
          <span class="hlabels">Holiday name: </span>
          <input type="text" value="' . esc_attr($holiday['name']) . '" readonly/>
          <span class="hlabels"> On: </span>
          <input type="date" value="' . esc_attr($holiday['date']) . '" readonly/>
          <button class="red-btn remove-holiday">Remove</button>
      </div><br>';
      
      // Store existing holidays in a JS array
      $existing_holidays[] = ['name' => esc_attr($holiday['name']), 'date' => esc_attr($holiday['date'])];
  }
}


$output .= '<script>
jQuery(document).ready(function($) {
    // Array of existing holidays
    let existingHolidays = ' . json_encode($existing_holidays) . ' || [];

    // Toggle the visibility of the holiday form
    $("#addHolidayBtn").on("click", function(e) {
        e.preventDefault();
        $("#holidayFormContainer").toggle(); // Show/hide the form without reloading
    });

    // Validation: Check if the holiday already exists
    function holidayExists(name, date) {
        for (let i = 0; i < existingHolidays.length; i++) {
            if (existingHolidays[i].name.toLowerCase() === name.toLowerCase() || existingHolidays[i].date === date) {
                return true;
            }
        }
        return false;
    }

    // Check for existing holiday on form input change
    $("#holidayForm input").on("input", function() {
        let holidayName = $("input[name=\'holiday_name\']").val();
        let holidayDate = $("input[name=\'holiday_date\']").val();
        
        if (holidayExists(holidayName, holidayDate)) {
            $("#error-msg").show(); // Show error message
            $("#saveHolidayBtn").prop("disabled", true); // Disable save button
        } else {
            $("#error-msg").hide(); // Hide error message
            $("#saveHolidayBtn").prop("disabled", false); // Enable save button
        }
    });

    // Save holiday using AJAX
    $("#holidayForm").on("submit", function(e) {
        e.preventDefault(); // Prevent form submission (no reload)
        
        let holidayName = $("input[name=\'holiday_name\']").val();
        let holidayDate = $("input[name=\'holiday_date\']").val();

        // Double check before submitting
        if (holidayExists(holidayName, holidayDate)) {
            $("#error-msg").show(); // Show error message
            return; // Do not proceed with AJAX request
        }

        $.ajax({
            url: "' . admin_url('admin-ajax.php') . '",
            type: "POST",
            data: {
                action: "save_custom_holiday",
                post_id: ' . $post_id . ',
                holiday_name: holidayName,
                holiday_date: holidayDate
            },
            success: function(response) {
                $("#holidayList").append(response);
                $("#holidayForm")[0].reset(); // Clear form fields
                $("#holidayFormContainer").hide(); // Hide the form again after submission
                $("#error-msg").hide(); // Hide error message
                $("#saveHolidayBtn").prop("disabled", false); // Re-enable save button
                // Add the new holiday to existingHolidays array
                existingHolidays.push({name: holidayName, date: holidayDate});
            }
        });
    });

    // Remove holiday using AJAX
    $("#holidayList").on("click", ".remove-holiday", function(e) {
        e.preventDefault(); // Prevent page reload on button click

        let index = $(this).closest(".holiday-item").data("index");

        $.ajax({
            url: "' . admin_url('admin-ajax.php') . '",
            type: "POST",
            data: {
                action: "remove_custom_holiday",
                post_id: ' . $post_id . ',
                index: index
            },
            success: function(response) {
                if (response.success) {
                    $("[data-index=\'" + index + "\']").remove();
                    // Remove the holiday from the existingHolidays array
                    existingHolidays.splice(index, 1);
                }
            }
        });
    });
});
</script>';



// End of the output
$output .= '</div></div>'; // End inside div

                  
  //START OF CHAT MESSAGE
  $output .= '<div class="postbox postbox-custom types-selector-wrapper wlw-programs-module-list-wrapper-0">';
  //$output .= '<h3>Chat Message<a class="description fright section-expander" data-toggle-title="Expand" href="javascript:;">Collapse</a></h3> ';
  $output .= '  <div class="inside">';  
  $output .= '     <p>';
  $tooltip_text= $data['tooltip_text'];
  $output .= '<label><b>Tooltip Text</b></label><br>';
  $output .= '             <input type="text" class="" disabled=disabled name="tooltip_text" value="'.$tooltip_text.'">';
  $output .= '  <span><b>[Note: This text will appear as tooltip(title) after Available/Unavailable.]</b></span>';
  $output .= '      </p>';
  
  $output .= '     <p>';
  $output .= '<label><b>Message Text:</b></label><br>';
  $chat_message = "We are available now!";
   if(isset($data['chat_message']) && trim($data['chat_message'])!='') {
    $chat_message = $data['chat_message'];
  }
   else 
  $chat_message = '<p>BALANCE chat is currently unavailable. Our normal business hours are in Pacific Time (US):<br> %timesettings%</p>';
   if (is_today_a_holiday($holidays, $custom_holidays)) {
    $chat_message = '<p> Due to a holiday today, BALANCE chat is currently unavailable.';
    } 
  
  $output .=  textarea_field( $data['chat_message'], 'chat_message', false, 10, $chat_message, '', array('media_buttons' => false, 'quicktags' => true ) );
  $output .= '  <span><b>[Note: This text message will appear in new window when chat is unavailable. %sitetitle%  and %timesettings% as specified in settings tab.]</b></span>';
  $output .= '  </p>';  
  $output .= '  </div>';
  $output .= '</div>';
  //END OF CHAT MESSAGE

  

  $output .= '  </div>';
  $output .= '</div>';
  $output .= '</div>';
  $output.='<style>.wp-core-ui select{font-size:smaller !important;}</style>';      

  //custom holiday
    $output.='<style>

    .hlabels{
    font-weight:bold;
  }
#holidayList input[type="text"] {
    width: 30% !important;
}
.blue-btn {
    background-color: #007aff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.blue-btn:hover {
    background-color: darkblue;
}

#saveHolidayBtn{
    background-color: #28a745;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
#saveHolidayBtn.disabled {
    background-color: #45464B;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: not-allowed; 
    opacity: 0.6; 
}


.red-btn {
    background-color: #ff4d4d;
    color: white;
    padding: 5px 10px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    // margin-left: 10px;
}

.red-btn:hover {
    background-color: #cc0000;
}

// #holidayList > div {
//     display: flex;               /* Use flexbox for layout */
//     align-items: center;        /* Center items vertically */
//     gap: 10px;                  /* Equal spacing between items */
// }
</style>';

  /*END OF SETTINGS*/


$output.='
<script>
  // Function to open the modal and display the chat message
  function openChat() {
    var modal = document.getElementById("chatModal");
    var chatMessage = "' . addslashes($chat_message) . '"; // Escaping PHP variable for JS

    // Set chat message content inside the modal
    document.getElementById("chatMessageContent").innerHTML = chatMessage;

    // Show the modal
    modal.style.display = "block";
  }

  // Close modal when clicking the close button
  document.querySelector(".close").onclick = function() {
    document.getElementById("chatModal").style.display = "none";
  };

  // Close modal when clicking outside of the modal
  window.onclick = function(event) {
  event.preventDefault();

    var modal = document.getElementById("chatModal");
    if (event.target == modal) {
      modal.style.display = "none";
    }
  };
</script>

<!-- Modal Styles -->
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

  // $data['FederalHolidays']=$holidays;
  $data['custom_holidays'] = $custom_holidays;  

  // print_r($data);
	return $output;
}
?>
