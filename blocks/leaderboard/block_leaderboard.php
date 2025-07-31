<?php
class block_leaderboard extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_leaderboard');
    }

    public function get_content() {
        global $DB, $OUTPUT;

        if ($this->content !== null) {
            return $this->content;
        }

        $sql = "SELECT u.id, CONCAT(u.firstname, ' ', u.lastname) AS fullname, COUNT(bi.id) AS badgecount
                FROM {badge_issued} bi
                JOIN {user} u ON bi.userid = u.id
                GROUP BY u.id, u.firstname, u.lastname
                HAVING COUNT(bi.id) > 0
                ORDER BY badgecount DESC
                LIMIT 7";

        $users = $DB->get_records_sql($sql);

        $ranked_users = [];
        $rank = 1;
        foreach ($users as $user) {
            $ranked_users[] = [
                'rank' => $rank++,
                'fullname' => $user->fullname,
                'badgecount' => $user->badgecount,
                'badgeicons' => str_repeat('🏅', $user->badgecount)
            ];
        }

        $this->content = new stdClass;
        $this->content->text = $OUTPUT->render_from_template('block_leaderboard/leaderboard', [
            'users' => $ranked_users
        ]);
        // $this->page->requires->css('/blocks/leaderboard/styles.css');
        return $this->content;
    }
}
