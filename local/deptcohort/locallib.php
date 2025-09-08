<?php
// Helper library for local_deptcohort plugin.
defined('MOODLE_INTERNAL') || die();

/**
 * Optional: public function to trigger a single-user sync (can be used by UI/observers).
 */
function local_deptcohort_sync_user_by_id(int $userid) : void {
    global $DB, $CFG;
    require_once($CFG->dirroot . '/cohort/lib.php');

    $user = $DB->get_record('user', ['id' => $userid, 'deleted' => 0, 'suspended' => 0], 'id, department', MUST_EXIST);

    $cohorts = $DB->get_records('cohort');
    $cohortmap = [];
    foreach ($cohorts as $c) {
        $cohortmap[mb_strtolower(trim($c->name))] = $c;
    }

    $dept = trim($user->department);
    $deptkey = mb_strtolower($dept);
    $desired = $cohortmap[$deptkey] ?? null;

    // Remove memberships in department-named cohorts that don't match desired.
    $existing = $DB->get_records('cohort_members', ['userid' => $user->id]);
    foreach ($existing as $cm) {
        if (isset($cohorts[$cm->cohortid])) {
            $cohname = mb_strtolower(trim($cohorts[$cm->cohortid]->name));
            if ($cohname !== $deptkey && array_key_exists($cohname, $cohortmap)) {
                cohort_remove_member($cm->cohortid, $user->id);
            }
        }
    }

    if ($desired && !$DB->record_exists('cohort_members', ['cohortid' => $desired->id, 'userid' => $user->id])) {
        cohort_add_member($desired->id, $user->id);
    }
}
