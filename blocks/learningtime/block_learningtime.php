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

        // Get filter (default: 6 months)
        $filter = optional_param('lt_filter', '6months', PARAM_ALPHA);

        // Set time filter based on selected value
        if ($filter === '1year') {
            $timefilter = time() - (365 * 24 * 60 * 60);
        } else if ($filter === 'all') {
            $timefilter = 0;
        } else {
            $filter = '6months';
            $timefilter = time() - (180 * 24 * 60 * 60);
        }

        // SQL to get user view log data
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

        // Prepare chart data
        $labels = [];
        $values = [];

        foreach ($logs as $log) {
            $labels[] = $log->day;
            $values[] = min(180, $log->views * 2); // cap at 180 minutes per day
        }

        // Create line chart
        $chart = new \core\chart_line();
        $series = new \core\chart_series(get_string('learningtime', 'block_learningtime'), $values);
        $chart->add_series($series);
        $chart->set_labels($labels);

        // Create Bootstrap button filters
        $output = '<form method="get" class="d-flex gap-2 mb-2 p-1" style="justify-content: flex-end;">';
        $filters = [
            '6months' => 'Last 6 Months',
            '1year' => 'Last 1 Year',
            'all' => 'All Time'
        ];

        foreach ($filters as $value => $label) {
            $isactive = ($filter == $value) ? 'btn-sm' : 'btn-outline-Secondary';
            $output .= '<button type="submit" name="lt_filter" value="' . $value . '" class="btn btn-sm ' . $isactive . '">' . $label . '</button>';
        }
        $output .= '</form>';

        // Render chart
        $output .= '<div class="mt-3">';
        $output .= $OUTPUT->render($chart);
        $output .= '</div>';

        // Set content
        $this->content = new stdClass();
        $this->content->text = $output;

        return $this->content;
    }
}
