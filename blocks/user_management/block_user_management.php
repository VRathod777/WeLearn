<?php
class block_user_management extends block_base {
    public function init() {
        $this->title = get_string('pluginname', 'block_user_management');
    }

    public function get_content() {
        global $OUTPUT, $DB, $USER, $CFG;

        if (!is_siteadmin()) {
            $this->content = new stdClass;
            $this->content->text = get_string('notadmin', 'block_user_management');
            return $this->content;
        }

        require_once($CFG->libdir . '/accesslib.php');

        $users = $DB->get_records_sql("
            SELECT u.id, u.username, u.firstname, u.lastname, u.email, u.suspended, ra.roleid
            FROM {user} u
            LEFT JOIN {role_assignments} ra ON ra.userid = u.id AND ra.contextid = 1
            WHERE u.deleted = 0
            ORDER BY u.id ASC
        ");

        $html = '<style>
            .user-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            .user-table th, .user-table td { border: 1px solid #ddd; padding: 8px; }
            .user-table th { background-color: #f5f5f5; text-align: left; }
        </style>';

        $html .= '<table class="user-table"><thead><tr>';
        $html .= '<th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($users as $user) {
            if ($user->id == 1) continue;
            $role = $user->roleid ? $DB->get_field('role', 'shortname', ['id' => $user->roleid]) : '-';
            $status = $user->suspended ? 'Suspended' : 'Active';

            $profileurl = new moodle_url('/user/editadvanced.php', ['id' => $user->id]);
            $deleteurl = new moodle_url('/blocks/user_management/delete.php', ['id' => $user->id, 'sesskey' => sesskey()]);

            $actions = '<a href="' . $profileurl . '">' . get_string('edit') . '</a> | ';
            $actions .= '<a href="' . $deleteurl . '" onclick="return confirm(\'Are you sure you want to delete this user?\')">'
                        . get_string('delete') . '</a>';

            $html .= "<tr>
                        <td>{$user->id}</td>
                        <td>{$user->firstname}</td>
                        <td>{$user->username}</td>
                        <td>{$user->email}</td>
                        <td>{$role}</td>
                        <td>{$status}</td>
                        <td>{$actions}</td>
                      </tr>";
        }

        $html .= '</tbody></table>';

        $this->content = new stdClass;
        $this->content->text = $html;
        return $this->content;
    }
}
