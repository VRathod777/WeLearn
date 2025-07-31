<?php
function block_user_management_ajax_output($users, $page, $totalpages) {
    global $DB;

    $html = '<table class="user-table"><thead><tr>';
    $html .= '<th>Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Actions</th>';
    $html .= '</tr></thead><tbody>';

    foreach ($users as $user) {
        if ($user->id == 1) continue;
        $role = $user->roleid ? $DB->get_field('role', 'shortname', ['id' => $user->roleid]) : '-';
        $status = $user->suspended ? 'Suspended' : 'Active';

        $profileurl = new moodle_url('/user/editadvanced.php', ['id' => $user->id]);
        $deleteurl = new moodle_url('/blocks/user_management/delete.php', ['id' => $user->id, 'sesskey' => sesskey()]);

        $actions = '<a href="' . $profileurl . '">Edit</a> | ';
        $actions .= '<a href="' . $deleteurl . '" onclick="return confirm(\'Are you sure?\')">Delete</a>';

        $html .= "<tr>
                    <td>{$user->firstname}</td>
                    <td>{$user->username}</td>
                    <td>{$user->email}</td>
                    <td>{$role}</td>
                    <td>{$status}</td>
                    <td>{$actions}</td>
                  </tr>";
    }

    $html .= '</tbody></table>';

    $html .= '<div class="pagination">';
    if ($page > 1) {
        $html .= '<a href="#" class="ajax-page-link" data-page="' . ($page - 1) . '">&laquo; Prev</a> ';
    }
    for ($i = 1; $i <= $totalpages; $i++) {
        if ($i == $page) {
            $html .= '<span class="current-page">' . $i . '</span> ';
        } else {
            $html .= '<a href="#" class="ajax-page-link" data-page="' . $i . '">' . $i . '</a> ';
        }
    }
    if ($page < $totalpages) {
        $html .= '<a href="#" class="ajax-page-link" data-page="' . ($page + 1) . '">Next &raquo;</a>';
    }
    $html .= '</div>';

    return $html;
}
