<?php
class block_learningtime extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_learningtime');
    }

    public function get_content() {
        global $OUTPUT, $USER, $DB;

        if ($this->content !== null) {
            return $this->content;
        }
       
        // Filter dropdown selection (default: 6months)
        $filter = optional_param('lt_filter', '6months', PARAM_ALPHA);

        // Determine time range
        if ($filter === '1year') {
            $timefilter = time() - (365 * 24 * 60 * 60);
        } else if ($filter === 'all') {
            $timefilter = 0;
        } else {
            $filter = '6months';
            $timefilter = time() - (180 * 24 * 60 * 60);   #days hr min sec
        }

        // SQL to get daily views in time range
        $sql = "SELECT FROM_UNIXTIME(timecreated, '%Y-%m-%d') AS day,
                       COUNT(*) AS views
                  FROM {logstore_standard_log}
                 WHERE userid = ?
                   AND eventname LIKE '%viewed%'";

        $params = [$USER->id];

        if ($timefilter > 0) {
            $sql .= " AND timecreated >= ?";
            $params[] = $timefilter;
        }

        $sql .= " GROUP BY day ORDER BY day";

        $logs = $DB->get_records_sql($sql, $params);
// print_r($logs);


        $labels = [];
        $values = [];

        foreach ($logs as $log) {
            $labels[] = $log->day;
            $values[] = min(180, $log->views * 2); // 2 mins per view, max 60        
        }

        // Create chart
        $chart = new \core\chart_line();
        $series = new \core\chart_series(get_string('learningtime', 'block_learningtime'), $values);
        $chart->add_series($series);
        $chart->set_labels($labels);

        // Create filter dropdown
        $output = '<form method="get">';
        $output .= '<select name="lt_filter" onchange="this.form.submit()">';
        $output .= '<option value="6months"' . ($filter == '6months' ? ' selected' : '') . '>Last 6 Months</option>';
        $output .= '<option value="1year"' . ($filter == '1year' ? ' selected' : '') . '>Last 1 Year</option>';
        $output .= '<option value="all"' . ($filter == 'all' ? ' selected' : '') . '>All Time</option>';
        $output .= '</select>';
        $output .= '</form>';

    //    $output .= '<div style="display: block;box-sizing: border-box;height: 349.6px;width: 578.4px;">';
       $output .= $OUTPUT->render($chart);
    //    $output .= '</div>';

        // Set block content  line bar pie  
        $this->content = new stdClass();
        $this->content->text = $output;
        return $this->content;
    }
}
