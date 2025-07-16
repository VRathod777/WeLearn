<?php
class block_learningpath extends block_base {

    public function init() {
        $this->title = get_string('pluginname', 'block_learningpath');
    }

    public function get_content() {
        global $OUTPUT, $DB, $PAGE;

        if ($this->content !== null) {
            return $this->content;
        }

        $this->content = new stdClass();
        $context = context_system::instance();

        // Only allow admins to view
        if (!has_capability('moodle/site:config', $context)) {
            $this->content->text = "You do not have permission to view this block.";
            return $this->content;
        }

        // Load custom CSS
        $PAGE->requires->css(new moodle_url('/blocks/learningpath/styles.css'));

        // Load JavaScript
        $PAGE->requires->js_init_code("
            function toggleLPLearners() {
                var box = document.getElementById('lp-learners-box');
                var btn = document.getElementById('lp-more-btn');
                box.classList.toggle('show');
                btn.innerText = box.classList.contains('show') ? 'Show Less' : 'Show More';
            }
        ");

        // Fetch all users with enrolments
        $users = $DB->get_records_sql("SELECT u.id, u.firstname, u.lastname
                                       FROM {user} u
                                       JOIN {user_enrolments} ue ON ue.userid = u.id
                                       JOIN {enrol} e ON e.id = ue.enrolid
                                       WHERE u.deleted = 0 AND u.suspended = 0
                                       GROUP BY u.id");

        $userprogress = [];

        foreach ($users as $user) {
            $enrolled_courses = enrol_get_users_courses($user->id);
            $total = count($enrolled_courses);
            $completed = 0;

            foreach ($enrolled_courses as $course) {
                $completion = $DB->get_record('course_completions', [
                    'course' => $course->id,
                    'userid' => $user->id
                ]);
                if ($completion && $completion->timecompleted) {
                    $completed++;
                }
            }

            $percent = $total ? round(($completed / $total) * 100) : 0;

            $userprogress[] = [
                'name' => "{$user->firstname} {$user->lastname}",
                'total' => $total,
                'completed' => $completed,
                'percent' => $percent
            ];
        }

        // Sort by highest progress
        usort($userprogress, function ($a, $b) {
            return $b['percent'] <=> $a['percent'];
        });

        // Build HTML
        $output = "<table class='generaltable'><thead>
                        <tr>
                            <th>Learner Name</th>
                            <th>Total Courses</th>
                            <th>Completed</th>
                            <th>Progress</th>
                        </tr>
                    </thead><tbody>";

        $limit = 10;
        $index = 0;
        foreach ($userprogress as $u) {
            if ($index == $limit) {
                $output .= "</tbody></table><div id='lp-learners-box' class='lp-table-wrapper'><table class='generaltable'><tbody>";
            }

            $bar = "<div class='progress'><div class='progress-bar' style='width: {$u['percent']}%;'>{$u['percent']}%</div></div>";

            $output .= "<tr>
                            <td>{$u['name']}</td>
                            <td>{$u['total']}</td>
                            <td>{$u['completed']}</td>
                            <td>{$bar}</td>
                        </tr>";
            $index++;
        }

        if ($index > $limit) {
            $output .= "</tbody></table></div>";
            $output .= "<div id='lp-more-btn' class='lp-more-btn' onclick='toggleLPLearners()'>Show More</div>";
        } else {
            $output .= "</tbody></table>";
        }

        $this->content->text = $output;
        return $this->content;
    }
}
