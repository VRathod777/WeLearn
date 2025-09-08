# local_deptcohort - Department Cohort Sync

**What it does**
- Periodically syncs Moodle users into cohorts whose **name** matches the user's **profile 'department'** field (case-insensitive).
- Removes users from other department-named cohorts so they remain only in the cohort matching their current department.
- Runs as a scheduled task (default every 5 minutes) — adjust schedule in Site administration → Scheduled tasks.

**Installation**
1. Copy the folder `local_deptcohort` into your Moodle `local/` directory.
2. Go to Site administration → Notifications to install the plugin.
3. Make sure cohorts exist with names exactly matching department values (case-insensitive).
4. Run cron (or wait for scheduled task): `php admin/cli/cron.php`

**Notes**
- The plugin matches cohort names to department text exactly (case-insensitive). If you need fuzzy matching or use idnumbers, customize `classes/task/sync.php`.
- The plugin will not remove users from cohorts that do not have names matching any cohort name in the site (to avoid touching unrelated cohorts).
