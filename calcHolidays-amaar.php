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