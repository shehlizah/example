
function get_nth_weekday_of_month($nth, $weekday, $month) {
  $year = date('Y'); // Ensure the correct year is used
  $date = new DateTime("first day of $year-$month");
  $count = 0;
  while ($date->format('n') == $month) {
      if ($date->format('N') == $weekday) {
          $count++;
          if ($count == $nth) {
              return $date->format('Y-m-d'); // Return full date
          }
      }
      $date->modify('+1 day');
  }
  return false;
}

function get_last_weekday_of_month($weekday, $month) {
  $year = date('Y'); // Ensure the correct year is used
  $date = new DateTime("last day of $year-$month");
  while ($date->format('N') != $weekday) {
      $date->modify('-1 day');
  }
  return $date->format('Y-m-d'); // Return full date
}

function adjust_holiday_date($holiday_date) {
  $current_date = date('Y-m-d');
  if ($holiday_date < $current_date) {
      // Move the holiday to the next year
      $new_date = new DateTime($holiday_date);
      $new_date->modify('+1 year');
      return $new_date->format('Y-m-d');
  }
  return $holiday_date;
}



function update_holidays_for_next_year() {
  global $holidays;
  $current_year = date('Y');
  $next_year = $current_year + 1;
  
  foreach ($holidays as $holiday => $date) {
      $holiday_timestamp = strtotime($date);
      if ($holiday_timestamp < time()) {
          // Update to next year
          $holidays[$holiday] = str_replace($current_year, $next_year, $date);
      }
  }
}

foreach ($holidays as $name => $date) {
      $adjusted_date = adjust_holiday_date($date); // Use the date directly, don't prepend year again
      $holidays[$name] = $adjusted_date;
  }


  $output .= '<script>
jQuery(document).ready(function($) {
    // Array of existing holidays
    let existingHolidays = ' . json_encode($existing_holidays) . ';
    existingHolidays = existingHolidays || [];

    // Toggle the visibility of the holiday form
    $("#addHolidayBtn").on("click", function(e) {
        e.preventDefault();
        $("#holidayFormContainer").toggle(); // Show/hide the form without reloading
    });

    // Validation: Check if the holiday already exists    if(existingHolidays!=null){

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