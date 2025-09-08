<?php
namespace local_deptcohort\task;

defined('MOODLE_INTERNAL') || die();

use core\task\scheduled_task;
use cohort;

class sync extends scheduled_task {

    public function get_name() : string {
        return get_string('taskname', 'local_deptcohort');
    }

    public function execute() {
        global $DB, $CFG;

        // Load cohort functions.
        require_once($CFG->dirroot . '/cohort/lib.php');

        // Fetch all cohorts and index them by lowercase name for case-insensitive matching.
        $cohorts = $DB->get_records('cohort');
        $cohortmap = [];
        foreach ($cohorts as $c) {
            $cohortmap[mb_strtolower(trim($c->name))] = $c;
        }

        // If there are no cohorts, nothing to do.
        if (empty($cohortmap)) {
            mtrace('[local_deptcohort] No cohorts found. Nothing to sync.');
            return;
        }

        // Fetch users who have a non-empty department field and are active.
        $sql = "SELECT id, department FROM {user} WHERE deleted = 0 AND suspended = 0 AND department IS NOT NULL AND department <> ''";
        $users = $DB->get_records_sql($sql);

        // For performance, we will prepare a set of cohort ids that are considered "department cohorts"
        $deptcohortids = array_map(function($c){ return $c->id; }, $cohorts);

        foreach ($users as $user) {
            $dept = trim($user->department);
            $deptkey = mb_strtolower($dept);

            // find desired cohort by exact name match (case-insensitive)
            $desired = isset($cohortmap[$deptkey]) ? $cohortmap[$deptkey] : null;

            // Find existing memberships of this user in any of the department cohorts (cohorts with names in our map).
            $existingmemberships = $DB->get_records('cohort_members', ['userid' => $user->id]);

            // Remove user from cohorts that are in dept cohort list but not the desired one.
            if ($existingmemberships) {
                foreach ($existingmemberships as $cm) {
                    if (isset($cohorts[$cm->cohortid])) {
                        // This cohort exists in our full list of cohorts; check whether its name is one of department cohorts
                        $cohname = mb_strtolower(trim($cohorts[$cm->cohortid]->name));
                        if ($cohname !== $deptkey) {
                            // Only remove if the cohort name exists in cohortmap — i.e., it's considered a department cohort.
                            // This avoids removing users from unrelated cohorts.
                            if (array_key_exists($cohname, $cohortmap)) {
                                cohort_remove_member($cm->cohortid, $user->id);
                            }
                        }
                    } else {
                        // If cohort record not in our fetched list, skip.
                    }
                }
            }

            // Add to desired cohort if exists and user not already member
            if ($desired) {
                $exists = $DB->record_exists('cohort_members', ['cohortid' => $desired->id, 'userid' => $user->id]);
                if (!$exists) {
                    cohort_add_member($desired->id, $user->id);
                }
            } else {
                // optionally log missing cohort for this department name
                mtrace('[local_deptcohort] No cohort named "' . $dept . '" found for user id ' . $user->id);
            }
        }

        mtrace('[local_deptcohort] Sync completed.');
    }
}
