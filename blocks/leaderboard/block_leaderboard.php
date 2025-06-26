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

        // ---------------------------------------------------------------------
        // 1.  Pull the top-5 users by total badges already issued
        // ---------------------------------------------------------------------
        $sql = "
            SELECT u.id,
                   CONCAT(u.firstname, ' ', u.lastname) AS fullname,
                   COUNT(bi.id) AS badgecount
              FROM {badge_issued} bi
              JOIN {user} u ON bi.userid = u.id
          GROUP BY u.id, u.firstname, u.lastname
            HAVING COUNT(bi.id) > 0
          ORDER BY badgecount DESC
             LIMIT 5";
        $users = $DB->get_records_sql($sql);

        // ---------------------------------------------------------------------
        // 2.  Prepare leaderboard rows with rank-specific badge caps
        // ---------------------------------------------------------------------
        $MAX_BADGES = 3;               // hard cap visible anywhere
        $ranked     = [];
        $rank       = 1;

        // Custom caps for each of the first five ranks
        //   • ranks 1-3 → 3 badges
        //   • rank 4   → 2 badges
        //   • rank 5   → 1 badge
        $badgeCapByRank = [
            1 => 3,
            2 => 3,
            3 => 3,
            4 => 2,
            5 => 1,
        ];

        foreach ($users as $user) {
            // Determine how many badges to *show*
            $cap            = $badgeCapByRank[$rank] ?? $MAX_BADGES;
            $displayBadges  = min((int)$user->badgecount, $cap);  // never show more icons than the user owns

            $ranked[] = [
                'rank'       => $rank,
                'fullname'   => $user->fullname,
                'badgecount' => $displayBadges,
                'badgeicons' => str_repeat('🏅', $displayBadges),
            ];

            $rank++;
        }

        // ---------------------------------------------------------------------
        // 3.  Render through the Mustache template
        // ---------------------------------------------------------------------
        $this->content            = new stdClass();
        $this->content->text      = $OUTPUT->render_from_template(
            'block_leaderboard/leaderboard',
            ['users' => $ranked]
        );
        // $this->page->requires->css('/blocks/leaderboard/styles.css');  // enable if you have custom CSS
        return $this->content;
    }
}
