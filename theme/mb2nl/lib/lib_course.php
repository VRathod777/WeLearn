<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 *
 * @package   theme_mb2nl
 * @copyright 2017 - 2025 Mariusz Boloz (lmsstyle.com)
 * @license   PHP and HTML: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later. Other parts: http://themeforest.net/licenses
 *
 */


/**
 * Method to check if is custom enrolment page
 *
 * @return bool true if yes, false if not.
 */
function theme_mb2nl_is_cenrol_page() {

    global $PAGE, $COURSE;

    // 1 = enrolement page but with other course formats
    // 2 = enrolemnt page with 'mb2sections' course format

    $enrollayout = theme_mb2nl_theme_setting($PAGE, 'enrollayout');

    if (theme_mb2nl_is_enrol_page() && $enrollayout) {

        if ($COURSE->format === 'mb2sections') {
            return 2;
        } else {
            return 1;
        }
    }

    return 0;
}



/**
 *
 * Method to check if is enrolment page
 *
 * @return bool true if yes, false if not.
 */
function theme_mb2nl_is_enrol_page() {

    global $PAGE, $COURSE, $SITE;

    if (theme_mb2nl_is_course() && $PAGE->pagetype === 'enrol-index') {
        return true;
    }

    return false;

}




/**
 *
 * Method to check if user set custom course page
 *
 * @return int|false
 */
function theme_mb2nl_course_layout($module = false, $ccourse = null) {
    global $PAGE;

    if ($PAGE->user_is_editing() || !theme_mb2nl_is_myformat($ccourse) || (!$module && !theme_mb2nl_is_cmainpage())) {
        return;
    }

    $cid = !is_null($ccourse) ? $ccourse->id : 0;

    $fieldcourselayout = theme_mb2nl_mb2fields_filed('mb2courselayout', $cid);
    $courselayout = (!is_null($fieldcourselayout) && $fieldcourselayout !== '') ?
    $fieldcourselayout : theme_mb2nl_theme_setting($PAGE, 'courselayout');

    if ($courselayout) {
        return $courselayout;
    }

    return false;
}





/**
 *
 * Method to check if is course main page
 *
 * @return bool
 */
function theme_mb2nl_is_cmainpage($ccourse = null, $cplayout = null, $cpagetype = null, $section = null) {

    global $PAGE;

    // ...preg match '|/course/view.php\?id=[\d]+$|'
    $isplayout = !is_null($cplayout) ? $cplayout : $PAGE->pagelayout;

    if ($isplayout === 'course' && !theme_mb2nl_is_section($ccourse, $cpagetype, $section)) {
        return true;
    }

    return false;

}



/**
 *
 * Method to check current course format is in predefined array
 *
 * @return bool
 */
function theme_mb2nl_is_myformat($ccourse = null) {

    global $COURSE;

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;
    $formats = ['topics', 'weeks', 'mb2sections'];

    if (isset($cobj->format) && in_array($cobj->format, $formats)) {
        return true;
    }

    return false;

}





/**
 *
 * Method to get course links on a course page
 *
 * @return HTML
 */
function theme_mb2nl_course_boxes($cls = 'bars', $ccourse = null, $tgsdb = false) {

    $output = '';
    $links = theme_mb2nl_course_links($ccourse);
    $cls = $cls ? ' l'. $cls : '';

    $srcls = preg_match('@circle@', $cls) ? ' label tsizexxsmall' : '';
    $acls = preg_match('@circle@', $cls) ? theme_mb2nl_bsfcls(1, 'nowrap', 'center', 'center') : '';

    $cls .= $tgsdb ? theme_mb2nl_bsfcls(1, '', 'start') : theme_mb2nl_bsfcls(1, '');

    $output .= '<div class="course-link-list' . $cls . '">';

    foreach ($links as $item) {

        if (!$item['link']) {
            continue;
        }

        $output .= '<div class="course-link-item item-' . $item['id'] . '">';
        $output .= '<a href="' . $item['link'] . '" class="' . $item['class'] . $acls . theme_mb2nl_bsfcls(1, '', '', 'center') .
        '" title="' . $item['title'] . '">';
        $output .= '<div class="course-link-item-inner' . theme_mb2nl_bsfcls(1) . '">';
        $output .= '<div class="course-link-item-icon lhsmall' . theme_mb2nl_bsfcls(2, '', 'center', 'center') .
        '" aria-hidden="true"><i class="' . $item['icon'] . '"></i></div>';
        $output .= '<div class="course-link-item-title' . $srcls . '">' . $item['title'] . '</div>';
        $output .= '</div>'; // ...course-link-item-inner
        $output .= '</a>';
        $output .= '</div>'; // ...course-link-item
    }

    $output .= '</div>'; // ...course-links

    return $output;

}



/**
 *
 * Method to get course links on a course page
 *
 * @return array
 */
function theme_mb2nl_course_links($ccourse = null) {

    global $COURSE, $PAGE;

    $cobj = $ccourse ? $ccourse : $COURSE;
    $fieldvideo = theme_mb2nl_mb2fields_filed('mb2video', $cobj->id); // Web video url.
    $videofile = theme_mb2nl_mb2fields_filed('mb2video_local', $cobj->id); // Video file.
    $cvideo = theme_mb2nl_theme_setting($PAGE, 'cvideo');

    $videolink = $videofile ? $videofile : theme_mb2nl_get_video_url($fieldvideo, true);

    // Get selected activities.
    $forum = theme_mb2nl_get_activities($cobj, 'forum');
    $resources = theme_mb2nl_get_activities($cobj, 'resources');
    $quiz = theme_mb2nl_get_activities($cobj, 'quiz');
    $assign = theme_mb2nl_get_activities($cobj, 'assign');

    $links = [
        [
            'id' => 'video',
            'link' => $cvideo ? $videolink : '',
            'class' => $videofile ? 'theme-popup-link popup-html_video' : 'theme-popup-link popup-iframe',
            'title' => get_string('courseintrovideo', 'theme_mb2nl'),
            'icon' => 'ri-play-large-line',
        ],
        [
            'id' => 'grades',
            'link' => new moodle_url('/grade/report/index.php', ['id' => $cobj->id]),
            'class' => '',
            'title' => get_string('grades'),
            'icon' => 'ri-graduation-cap-line',
        ],
        [
            'id' => 'forum',
            'link' => !empty($forum) ? new moodle_url('/mod/forum/index.php', ['id' => $cobj->id]) : '',
            'class' => '',
            'title' => !empty($forum) ? $forum[0]['title'] : '',
            'icon' => 'ri-discuss-line',
        ],
        [
            'id' => 'resources',
            'link' => !empty($resources) ? new moodle_url('/course/resources.php', ['id' => $cobj->id]) : '',
            'class' => '',
            'title' => !empty($resources) ? $resources[0]['title'] : '',
            'icon' => 'ri-database-line',
        ],
        [
            'id' => 'assign',
            'link' => !empty($assign) ? new moodle_url('/mod/assign/index.php', ['id' => $cobj->id]) : '',
            'class' => '',
            'title' => !empty($assign) ? $assign[0]['title'] : '',
            'icon' => 'ri-file-check-line',
        ],
        [
            'id' => 'quiz',
            'link' => !empty($quiz) ? new moodle_url('/mod/quiz/index.php', ['id' => $cobj->id]) : '',
            'class' => '',
            'title' => !empty($quiz) ? $quiz[0]['title'] : '',
            'icon' => 'ri-checkbox-multiple-line',
        ],
    ];

    return $links;

}








/**
 *
 * Method to check if course require pay
 *
 * @return int|false
 */
function theme_mb2nl_is_course_price($courseid=0) {

    $enrolements = theme_mb2nl_get_course_enrolements($courseid);
    $paymethods = theme_mb2nl_pay_enrolements();

    foreach ($enrolements as $enrol) {
        if (in_array($enrol->enrol, $paymethods)) {
            return $enrol->enrol;
        }
    }

    return false;

}





/**
 *
 * Method to get course enrolements methods
 *
 * @return array
 */
function theme_mb2nl_get_course_enrolements($courseid=0) {

    global $DB, $COURSE;

    $iscourseid = $courseid ? $courseid : $COURSE->id;

    // Get cache..
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'course_enrolements_' . $iscourseid;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $enrolements = $DB->get_records('enrol', ['courseid' => $iscourseid, 'status' => ENROL_INSTANCE_ENABLED],
    '', 'id, enrol, name, sortorder');

    // Set cache.
    $cache->set($cacheid, $enrolements);

    return $enrolements;

}





/**
 *
 * Method to get course content tab
 *
 * @return array
 */
function theme_mb2nl_pay_enrolements() {

    return ['paypal', 'fee', 'stripepayment'];

}






/**
 *
 * Method to get course price
 *
 * @return int
 */
function theme_mb2nl_get_course_price($courseid = 0) {

    global $DB, $COURSE;

    $iscourseid = $courseid ? $courseid : $COURSE->id;
    $payenrol = theme_mb2nl_is_course_price($iscourseid);

    if (!$payenrol) {
        return 0;
    }

    // Get cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'course_price_' . $iscourseid;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $recordsql = 'SELECT cost, currency FROM {enrol} WHERE courseid=? AND enrol=?';
    $price = $DB->get_record_sql($recordsql, [$iscourseid, $payenrol]);

    // Set cache.
    $cache->set($cacheid, $price);

    return $price;
}






/**
 *
 * Method to get course price on course list
 *
 * @return HTML
 */
function theme_mb2nl_course_price_html($courseid = 0, $options = []) {
    global $PAGE, $COURSE;

    $output = '';
    $iscid = $courseid ? $courseid : $COURSE->id;
    $courseprice = theme_mb2nl_theme_setting($PAGE, 'courseprice');

    if (isset($options['courseprice'])) {
        $courseprice = $options['courseprice'];
    }

    // Hide course price on course list and course shortcode.
    // On enrolment page always show course price ($courseid is set to 0).
    if (!$courseprice && $courseid != 0) {
        return;
    }

    $iscourseprice = theme_mb2nl_is_course_price($iscid);
    $priceobj = theme_mb2nl_get_course_price($iscid);
    $currency = '';

    if (!$iscourseprice || !$priceobj || $priceobj->cost == 0) {
        // Display the "free" price.
        if (theme_mb2nl_theme_setting($PAGE, 'cpricefree')) {
            return '<div class="course-price price free">' . get_string('noprice', 'theme_mb2nl') . '</div>';
        }
        return;
    } else {
        $price = $priceobj->cost;
        $currency = theme_mb2nl_get_currency_symbol($priceobj->currency);
    }

    $roundnum = theme_mb2nl_is_decimal($price) ? 2 : 0;
    $price = number_format($price, $roundnum, theme_mb2nl_theme_setting($PAGE, 'cpricedecimal'), theme_mb2nl_thousands_sep());
    $reverse = theme_mb2nl_theme_setting($PAGE, 'cpricereverse') ? ' reverse' : '';

    $output .= '<div class="course-price' . $reverse . '">';
    $output .= '<span class="sr-only">' . get_string('price', 'theme_mb2nl') . ': ' . $priceobj->currency . ' ' . $price
    . '</span>';
    $output .= '<span class="price" aria-hidden="true">';
    $output .= '<span class="currency" aria-hidden="true">' . $currency . '</span>';
    $output .= '<span class="cost" aria-hidden="true">' . $price . '</span>';
    $output .= '</span>'; // ...price
    $output .= '</div>'; // ...course-price

    return $output;

}




/**
 *
 * Method to get course price on course list
 *
 * @return HTML
 */
function theme_mb2nl_thousands_sep() {

    global $PAGE, $COURSE;

    $sep = theme_mb2nl_theme_setting($PAGE, 'pricesep');

    switch ($sep) {
        case '':
            return '';
        break;
        case 'nbsp':
              return '&#160;';
        break;
        default:
            return $sep;
    }

}



/**
 *
 * Method to get course price on course list
 *
 * @return bool
 */
function theme_mb2nl_is_decimal($num) {

    $numfoolr = floor($num);

    if ($num != $numfoolr) {
        return true;
    }

    return false;

}





/**
 *
 * Method to get course date on course list
 *
 * @return HTML
 */
function theme_mb2nl_course_list_date($course) {

    $output = '';
    $isdate = theme_mb2nl_course_date($course);

    if (! $isdate) {
        return;
    }

    $userdate = userdate($isdate, get_string('strdatecourseshort', 'theme_mb2nl'));
    $icon = '<i class="ri-refresh-line" aria-hidden="true"></i>';

    $output .= '<div class="date' . theme_mb2nl_bsfcls(2, '', '', 'center') . '">';
    $output .= $icon . $userdate;
    $output .= '</div>';

    return $output;

}





/**
 *
 * Method to get course date on course list
 *
 * @return HTML
 */
function theme_mb2nl_course_list_catname($course) {
    global $PAGE;
    $output = '';

    return;

    if (! theme_mb2nl_theme_setting($PAGE, 'catname')) {
        return;
    }

    $catname = theme_mb2nl_get_category_record($course->category)->name;

    $output .= '<div class="course-catname'. theme_mb2nl_bsfcls(2, '', '', 'center') . '">';
    $output .= theme_mb2nl_format_str($catname);
    $output .= '</div>';

    return $output;

}






/**
 *
 * Method to get course date
 *
 * @return timestamp
 */
function theme_mb2nl_course_date($course) {
    global $PAGE, $DB;

    if (! theme_mb2nl_theme_setting($PAGE, 'cupdatedate')) {
        return;
    }

    $modify = isset($course->timemodified) && $course->timemodified ? $course->timemodified : 0;
    $maxdate = theme_mb2nl_course_maxtime($course);

    if ($maxdate && $maxdate > $modify) {
        return $maxdate;
    } else if ($modify) {
        return $modify;
    }

    return $course->startdate;

}






/**
 *
 * Method to get max editing time of course activities
 *
 * @return timestamp
 */
function theme_mb2nl_course_maxtime($course) {

    global $DB;

    // Cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'course_maxdate_' . $course->id;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    // Get latest update date of course modules.
    // ...the 'edulevel' variable based on LEVEL_TEACHING constant, which is set to 1.
    $sql = 'SELECT MAX(timecreated) FROM {logstore_standard_log} WHERE courseid=:courseid AND edulevel=:edulevel';
    $sql .= ' AND (action=:action1 OR action=:action2)';
    $sqlparams = ['courseid' => $course->id, 'edulevel' => 1, 'action1' => 'created', 'action2' => 'updated'];

    $maxdate = $DB->get_field_sql($sql, $sqlparams);

    // Set cache.
    $cache->set($cacheid, $maxdate);

    return $maxdate;

}






/**
 *
 * Method to get course price on course list
 *
 * @return HTML
 */
function theme_mb2nl_course_list_students($courseid, $options = []) {
    global $PAGE;

    $output = '';
    $coursestudentscount = theme_mb2nl_theme_setting($PAGE, 'coursestudentscount');

    if (isset($options['coursestudentscount'])) {
        $coursestudentscount = $options['coursestudentscount'];
    }

    if (! $coursestudentscount) {
        return;
    }

    $output .= '<div class="students' . theme_mb2nl_bsfcls(2, '', '', 'center') . '">';
    $output .= '<i class="ri-graduation-cap-line"></i>' . theme_mb2nl_get_sudents_count(context_course::instance($courseid));
    $output .= '<span class="sr-only"> ' . get_string('defaultcoursestudents') . '</span>';
    $output .= '</div>';

    return $output;

}






/**
 *
 * Method to get course students count
 *
 * @return int
 */
function theme_mb2nl_get_sudents_count($context=null) {
    global $COURSE;

    $iscontext = !is_null($context) ? $context : context_course::instance($COURSE->id);

    // Get cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'sudents_count_' . $iscontext->id;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $students = get_role_users(theme_mb2nl_user_roleid(), $iscontext, false, 'u.id,u.firstname,u.lastname');

    // Set cache.
    $cache->set($cacheid, count($students));

    return count($students);
}







/**
 *
 * Method to get users enrolled to paid courses
 *
 * @return array
 */
function theme_mb2nl_get_payenrolled_users($categoryid) {
    global $DB;

    // Get cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'payenrolled_users_' . $categoryid;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $params = [];
    $sqlwhere = ' WHERE 1=1';

    $sqlquery = 'SELECT DISTINCT ra.id, ra.contextid FROM {role_assignments} ra';

    $sqlquery .= ' JOIN {context} cx ON cx.id=ra.contextid';
    $sqlquery .= ' JOIN {enrol} er ON er.courseid=cx.instanceid';
    $sqlquery .= ' JOIN {course} c ON er.courseid=c.id';

    list($payenrolinsql, $payenrolparams) = $DB->get_in_or_equal(theme_mb2nl_pay_enrolements());
    $params = array_merge($params, $payenrolparams);
    $sqlwhere .= ' AND er.enrol ' . $payenrolinsql;
    $sqlwhere .= ' AND er.status = ' . ENROL_INSTANCE_ENABLED;
    $sqlwhere .= ' AND c.visible = 1';

    if ($categoryid) {
        $sqlwhere .= ' AND c.category = ' . $categoryid;
    }

    $sqlwhere .= ' AND ra.roleid = ' . theme_mb2nl_user_roleid();

    $users = $DB->get_records_sql($sqlquery . $sqlwhere, $params);

    // Set cache.
    $cache->set($cacheid, $users);

    return $users;

}



/**
 *
 * Method to get bestseller courses array
 *
 * @return array
 */
function theme_mb2nl_bestsellers($itemsnum, $categoryid) {

    $payenrolledroles = theme_mb2nl_get_payenrolled_users($categoryid);
    $bestsellers = [];

    if (!count($payenrolledroles)) {
        return [];
    }

    foreach ($payenrolledroles as $role) {
        $bestsellers[] = $role->contextid;
    }

    $bestsellers = array_count_values($bestsellers);

    arsort($bestsellers);

    $bestsellers = array_slice($bestsellers, 0, $itemsnum, true);

    return $bestsellers;

}







/**
 *
 * Method to get bestseller courses array
 *
 * @return bool
 */
function theme_mb2nl_is_bestseller($instanceid, $categoryid = 0) {
    $bestsellers = theme_mb2nl_bestsellers(3, $categoryid);

    if (array_key_exists($instanceid, $bestsellers)) {
        return true;
    }

    return false;

}



/**
 *
 * Method to to check if there is section navigation for no-toc course layout
 *
 * @return bool
 */
function theme_mb2nl_is_sectionnav() {

    $clayout = theme_mb2nl_course_layout(true);

    if (theme_mb2nl_is_cmainpage() && in_array($clayout, theme_mb2nl_nosectionnav())) {
        return false;
    }

    if (theme_mb2nl_is_cmainpage() && in_array($clayout, theme_mb2nl_notoc()) && !optional_param('ctab', '', PARAM_ALPHANUMEXT)) {
        return true;
    }

    return false;

}



/**
 *
 * Method to to check if current page is a course section
 *
 * @return bool
 */
function theme_mb2nl_is_section($ccourse=null, $ptype=null, $uparamsection=null) {
    global $CFG, $PAGE;

    $ispagetype = !is_null($ptype) ? $ptype : $PAGE->pagetype;

    $snu = preg_match('@section-@', $PAGE->pagetype) ? optional_param('id', 0, PARAM_INT) : 0;

    if ($CFG->version < 2024042200) {
        $snu = optional_param('section', 0, PARAM_INT);
    }

    // This is for externallib function.
    $snu = !$snu && !is_null($uparamsection) ? $uparamsection : $snu;

    if (theme_mb2nl_is_course($ccourse) && $snu) {
        return true;
    }

    return false;

}






/**
 *
 * Method to get section
 *
 * @return int
 */
function theme_mb2nl_current_sectionid() {
    global $CFG, $COURSE;

    $snu = optional_param('section', 0, PARAM_INT);
    $mb2sct = optional_param('mb2sct', 0, PARAM_INT);

    if (!theme_mb2nl_is_section() && !theme_mb2nl_is_sectionnav()) {
        return;
    }

    // Older than 4.4 Moodle versions - 'section'.
    // and none-toc course layout - 'mb2sct'.
    $snu = $snu ? $snu : $mb2sct;
    $modinfo = get_fast_modinfo($COURSE);
    $section = $modinfo->get_section_info($snu);
    $sid = $section->id;

    if (theme_mb2nl_is_section() && $CFG->version >= 2024042200) {
        $sid = optional_param('id', 0, PARAM_INT);
    }

    return $sid;

}





/**
 *
 * Method to get section
 *
 * @return section
 */
function theme_mb2nl_near_section($prev = true) {
    global $PAGE, $COURSE;

    $sections = theme_mb2nl_sections_for_nav();

    if (count($sections) == 1) {
        return;
    }

    $sid = theme_mb2nl_current_sectionid();

    // Get section keys and current section position.
    $sids = array_keys($sections);
    $position = array_search($sid, $sids);

    if ($prev && $position > 0) {
        return $sections[$sids[$position - 1]];
    }

    if (!$prev && $position < (count($sections) - 1)) {
        return $sections[$sids[$position + 1]];
    }

    return;

}





/**
 *
 * Method to get section activities
 *
 * @return module
 */
function theme_mb2nl_near_module($prev = true, $cobj = null) {
    global $PAGE;

    $modids = theme_mb2nl_modules_for_nav($cobj);
    $modules = theme_mb2nl_get_section_activities(0, true, true, $cobj);

    // Check for empty subsections. We need to unset them.
    $emptysubse = array_diff(array_keys($modules), $modids);
    foreach ($emptysubse as $s) {
        unset($modules[$s]);
    }

    $countmod = count($modules);

    if ($countmod == 1) {
        return;
    }

    if (!isset($PAGE->cm->id)) {
        return;
    }

    // We need additional array of course modules including modules in subsections.
    $position = array_search($PAGE->cm->id, $modids);

    if ($prev && $position > 0) {
        return $modules[$modids[$position - 1]];
    }

    if (!$prev && $position < ($countmod - 1)) {
        return $modules[$modids[$position + 1]];
    }

    return;
}







/**
 *
 * Method to get section activities
 *
 * @return array
 */
function theme_mb2nl_get_section_activities($sectionid = 0, $onlyuservisible = true, $nav = false, $cobj = null) {
    global $OUTPUT, $COURSE;

    $iscobj = !is_null($cobj) ? $cobj : $COURSE;
    $coursecontext = context_course::instance($iscobj->id);
    $viewhidden = has_capability('moodle/course:viewhiddenactivities', $coursecontext);
    $modinfo = get_fast_modinfo($iscobj->id);
    $modules = [];

    foreach ($modinfo->get_cms() as $cm) {
        if ($sectionid != 0 && $cm->section != $sectionid) {
            continue;
        }

        if (!$cm->visible && !$viewhidden) {
            continue;
        }

        // This is required for custom navigation feature.
        // Skip module which is not visible for user.
        // We need this to get correct position in modules array.
        if ($nav && !$cm->uservisible) {
            continue;
        }

        if (!$onlyuservisible && !$cm->uservisible && !theme_mb2nl_mod_show_res($cm)) {
            continue;
        }

        if ($cm->deletioninprogress) {
            continue;
        }

        $modules[$cm->id] = [
            'id' => $cm->id,
            'mod' => $cm,
            'name' => $cm->name,
            'modname' => $cm->modname,
            'icon' => $cm->icon ? $OUTPUT->image_url($cm->icon) : $OUTPUT->image_url('icon', $cm->modname),
            'url' => $cm->url,
            'section' => $cm->section,
            'visible' => $cm->visible,
            'uservisible' => $cm->uservisible,
            'restriction' => theme_mb2nl_module_has_restrictions($cm, $iscobj),
            'issubsection' => $cm->modname === 'subsection',
            'indent' => $cm->indent,
            'purpose' => plugin_supports('mod', $cm->modname, FEATURE_MOD_PURPOSE),
        ];
    }

    return $modules;

}




/**
 *
 * Method to get section activities
 *
 * @return bool
 */
function theme_mb2nl_mod_show_res($mod) {

    if (is_null($mod->availability)) {
        return true;
    }

    $availability = json_decode($mod->availability, true);

    if (!isset($availability['showc'])) {
        return true;
    }

    $showc = $availability['showc'];
    $criteria = $availability['c'];

    foreach ($showc as $c) {
        if (!$c) {
            return false;
        }
    }

    return true;

}






/**
 *
 * Method to get section activities
 *
 * @return HTML
 */
function theme_mb2nl_section_module_list($sectionid, $link = false, $active = false, $uservisible = true, $sectionnum = -1,
$cobj = null, $modid = 0, $ptype = null, $playout = null, $viewhidden = 0) {
    global $PAGE, $USER;
    $output = '';
    $subsectioncounter = 0;

    // Hide not visible for user items on enrolment page.
    // We use the '$nav" variable because of the function 'theme_mb2nl_get_section_activities' attribute.
    $nav = !$link;
    $modules = theme_mb2nl_get_section_activities($sectionid, $uservisible, $nav, $cobj);
    $sections = theme_mb2nl_get_course_sections($cobj);
    $section = $sections[$sectionid];

    // Check for available info. Hide items if users can't see it.
    $sectionavailableinfo = $section['availableinfo'];
    $sectionnotavailable = $section['notavailable'];

    if (!count($modules) || (!$viewhidden && $sectionavailableinfo) || $sectionnotavailable) {
        if ($sectionnotavailable) {
            return '<span class="info d-block pt-3 pb-3"><i class="ri-information-line"></i> ' .
            get_string('notavailable') . '</span>';
        } else if (!$viewhidden && $sectionavailableinfo) {
            return '<span class="info d-block pt-3 pb-3"><i class="ri-information-line"></i> ' .
            get_string('accessrestrictions', 'availability') . '</span>';
        } else {
            return '<span class="info d-block pt-3 pb-3"><i class="ri-information-line"></i> ' .
            get_string('nothingtodisplay') . '</span>';
        }
    }

    $output .= '<ul class="section-modules">';

    foreach ($modules as $k => $m) {
        // Hide label module on course nerolment page.
        // On this page the $link is set to false.
        if (!$link && $m['modname'] === 'label') {
            continue;
        }

        // Subsections.
        if ($m['issubsection'] && isset($sections[$sectionid]['subsections'][$subsectioncounter])) {

            $subsection = $sections[$sectionid]['subsections'][$subsectioncounter];
            $submodules = theme_mb2nl_get_section_activities($subsection['id'], $uservisible, false, $cobj);
            $subsectionavailableinfo = $subsection['availableinfo'];

            $sname = empty($subsection['name']) ? get_string('section') : theme_mb2nl_format_str($subsection['name']);
            $sectionurl = theme_mb2nl_sectionurl($subsection, $cobj, true);

            $output .= '<li class="coursetoc-subsection-listitem">';

            $output .= '<div class="coursetoc-subsection coursetoc-section-' . $subsection['num'] . '"
            data-sid="' . $subsection['id'] . '">';
            $output .= '<div class="coursetoc-subsection-tite' . theme_mb2nl_bsfcls(1, '', 'between', 'center') . '">';
            $output .= '<span class="title-text text' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">';
            $output .= $link && $sectionurl ? '<a href="' . $sectionurl . '">' . $sname . '</a>' : $sname;
            $output .= theme_mb2nl_section_badges($subsection, true);
            $output .= '</span>';
            $output .= '<button class="coursetoc-subsection-toggle themereset' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '"
            aria-label="'. get_string('togglesection', 'theme_mb2nl', $sname) . '" aria-controls="coursetoc-subsection-modules-' .
            $subsection['id'] . '" aria-expanded="false">';
            $output .= '<span class="toggle-icon"></span>';
            $output .= '</button>';
            $output .= '</div>';

            $output .= '<div id="coursetoc-subsection-modules-' . $subsection['id'] . '" class="coursetoc-subsection-modules">';

            // Check for submodules and section availability info info. Hide items if users can't see it.
            if (!count($submodules) || (!$viewhidden && $subsectionavailableinfo)) {
                if (!$viewhidden && $subsectionavailableinfo) {
                    $output .= '<span class="info d-block pt-3"><i class="ri-information-line"></i> ' .
                    get_string('accessrestrictions', 'availability') .
                    '</span>';
                } else { // No submodules.
                    $output .= '<span class="info d-block pt-3"><i class="ri-information-line"></i> ' .
                    get_string('nothingtodisplay') . '</span>';
                }
            } else if (count($submodules)) {
                $output .= '<ul class="subsection-modules">';
                foreach ($submodules as $k => $subm) {
                    $output .= theme_mb2nl_section_module_list_item($subm, $link, $sectionnum, $subsection['id'], $cobj, $ptype,
                    $playout);
                }
                $output .= '</ul>';
            }

            $output .= '</div>';
            $output .= '</div>';
            $output .= '</li>';

            $subsectioncounter++;

        } else if (!$m['issubsection']) {
            $output .= theme_mb2nl_section_module_list_item($m, $link, $sectionnum, $sectionid, $cobj, $ptype, $playout);
        }
    }

    $output .= '</ul>';

    return $output;

}




/**
 *
 * Method to get course sections ids
 *
 * @return array
 */
function theme_mb2nl_sections_for_nav() {

    global $PAGE, $COURSE;

    $navsections = [];
    $clayout = theme_mb2nl_course_layout();

    // Use cache.
    $cache = cache::make('theme_mb2nl', 'courses');
    $cacheid = 'navsections_' . $COURSE->id . '_cl_' . $clayout;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    // Check if user can view hidden items.
    $coursecontext = context_course::instance($COURSE->id);
    $viewhidden = has_capability('moodle/course:viewhiddenactivities', $coursecontext);

    $sections = theme_mb2nl_get_course_sections();

    if (!count($sections)) {
        return [];
    }

    foreach ($sections as $section) {

        // Unset not available sections.
        // We have to remove not available section from section navigation.
        if (!$viewhidden && ($section['notavailable'] != 0 || $section['availableinfo'])) {
            continue;
        }

        $navsections[$section['id']] = ['id' => $section['id'], 'num' => $section['num'], 'name' => $section['name'],
        'notavailable' => $section['notavailable'], 'availableinfo' => $section['availableinfo']];

        if ($section['subsections']) {
            foreach ($section['subsections'] as $subsection) {

                // Unset not available sections.
                // We have to remove not available subsection from section navigation.
                if (!$viewhidden && ($subsection['notavailable'] != 0 && $subsection['availableinfo'])) {
                    continue;
                }

                $navsections[$subsection['id']] = ['id' => $subsection['id'], 'num' => $subsection['num'],
                'name' => $subsection['name'], 'notavailable' => $subsection['notavailable'],
                'availableinfo' => $subsection['availableinfo']];
            }
        }
    }

    $cache->set($cacheid, $navsections);

    return $navsections;

}





/**
 *
 * Method to get ids of all sub sections
 *
 * @return array
 */
function theme_mb2nl_subsections_ids() {

    global $PAGE, $COURSE;

    $subsections = [];

    // Use cache.
    $cache = cache::make('theme_mb2nl', 'courses');
    $cacheid = 'subsections_' . $COURSE->id;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $sections = theme_mb2nl_get_course_sections();

    if (!count($sections)) {
        return [];
    }

    foreach ($sections as $section) {
        if (empty($section['subsections'])) {
            continue;
        }

        foreach ($section['subsections'] as $subsection) {
            $subsections[] = $subsection['id'];
        }
    }

    $cache->set($cacheid, $subsections);

    return $subsections;

}




/**
 *
 * Method to get course module id's
 *
 * @return array
 */
function theme_mb2nl_modules_for_nav($cobj) {

    global $COURSE, $PAGE;

    $navmodules = [];
    $modules = theme_mb2nl_get_section_activities(0, true, true, $cobj);

    // Use cache.
    $cache = cache::make('theme_mb2nl', 'courses');
    $cacheid = 'modnav_' . $COURSE->id . '_' . serialize(array_keys($modules));

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $sections = theme_mb2nl_get_course_sections($cobj);

    foreach ($sections as $section) {
        $modules = theme_mb2nl_get_section_activities($section['id'], true, true, $cobj);
        $subsectioncounter = 0;

        if (!count($modules)) {
            continue;
        }

        foreach ($modules as $mod) {
            if ($mod['issubsection']) {
                $subsections = theme_mb2nl_get_course_sections($cobj)[$section['id']]['subsections'];
                $subsection = $subsections[$subsectioncounter];
                $submodules = theme_mb2nl_get_section_activities($subsection['id'], true, true, $cobj);

                if (!count($submodules)) {
                    continue;
                }

                foreach ($submodules as $submod) {
                    $navmodules[] = $submod['id'];
                }

                $subsectioncounter++;
                continue;
            }

            $navmodules[] = $mod['id'];
        }
    }

    $cache->set($cacheid, $navmodules);
    return $navmodules;

}




/**
 *
 * Method to get section activities
 *
 * @return HTML
 */
function theme_mb2nl_section_module_list_item($m, $link, $sectionnum, $sectionid, $cobj, $ptype, $playout) {

    $output = '';

    $linktag = !$m['uservisible'] ? false : $link;
    $modlink = theme_mb2nl_activityurl($m['url'], $m['id'], $sectionnum, $sectionid, $cobj, $ptype, $playout);
    $cmplstate = theme_mb2nl_module_complete($m['id'], $cobj);
    $modcomplete = $cmplstate ? ' complete' . $cmplstate : '';
    $hiddenicon = '';
    $hiddencls = '';
    $modname = get_string('pluginname', 'mod_' . $m['modname']);

    if (!$m['visible']) {
        $hiddencls = ' hiddenmodule';
        $hiddenicon .= '<span class="hiddenicon" title="' . get_string('hiddenfromstudents') . '"><i class="fa fa-eye-slash"></i>
        </span>';
    }

    $output .= '<li class="module-item indent' . $m['indent'] . ' module-' . $m['modname'] . $modcomplete . $hiddencls .
    theme_mb2nl_bsfcls(1, '', '', 'center') . '" data-cmid="' . $m['id'] . '">';
    $output .= $linktag ? '<a href="' . $modlink . '" class="' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">' : '';

    $output .= $link ? theme_mb2nl_module_mark($cmplstate, $cobj) : '';

    $output .= '<span class="itemimage ' . $m['purpose'] . '" aria-hidden="true"><img class="activityicon" src="' . $m['icon'] .
    '" alt="' . $modname . '"></span>';

    // Prevent to autolink.
    $itemname = theme_mb2nl_format_str($m['name']);
    $output .= '<span class="itemname">' . $itemname . $hiddenicon . '</span>';
    $output .= $m['restriction'] && $link ? '<div class="course-badges icons' .
    theme_mb2nl_bsfcls(2) . '"><span class="restriction" title="' .
    get_string('accessrestrictions', 'availability') . '"><i class="ri-lock-line"></i></span></div>' : '';
    $output .= $linktag ? '</a>' : '';
    $output .= '</li>';

    return $output;

}






/**
 *
 * Method to get course activities
 * Thanks for Fordson theme (https://moodle.org/plugins/theme_fordson)
 *
 * @return array
 */
function theme_mb2nl_get_course_activities($ccourse = false, $count = false) {

    global $CFG, $PAGE, $OUTPUT, $COURSE;

    $course = $ccourse ? $ccourse : $COURSE;

    // Get cache.
    $cache = cache::make('theme_mb2nl', 'courses');
    $cacheid = 'course_activities_' . $course->id;
    $cacheidc = 'course_activities_c_' . $course->id;

    if (!$count && $cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    if ($count && $cache->get($cacheidc)) {
        return $cache->get($cacheidc);
    }

    // A copy of block_activity_modules.
    $content = new stdClass();
    $modinfo = get_fast_modinfo($course);
    $modfullnames = [];
    $archetypes = [];
    $rx = 0;
    $ax = 0;

    // Check if user can see hidden activities.
    $coursecontext = context_course::instance($COURSE->id);
    $viewhidden = has_capability('moodle/course:viewhiddenactivities', $coursecontext);

    foreach ($modinfo->cms as $cm) {
        // Exclude activities which are not visible and user can't see hidden activities or have no link (=label).
        if ((!$viewhidden && ! $cm->visible) || ($count && !$viewhidden && !$cm->uservisible && !theme_mb2nl_mod_show_res($cm))
        || !$cm->has_view()) {
            continue;
        }

        if (!$count && array_key_exists($cm->modname, $modfullnames)) {
            continue;
        }

        if ($cm->deletioninprogress) {
            continue;
        }

        if (!array_key_exists($cm->modname, $archetypes)) {
            $archetypes[$cm->modname] = plugin_supports('mod', $cm->modname, FEATURE_MOD_ARCHETYPE, MOD_ARCHETYPE_OTHER);
        }

        if ($archetypes[$cm->modname] == MOD_ARCHETYPE_RESOURCE) {
            if (!array_key_exists('resources', $modfullnames)) {
                $modfullnames['resources'] = get_string('resources');
            }
            $rx++;
        } else {
            $modfullnames[$cm->modname] = $cm->modplural;
            $ax++;
        }
    }

    if ($count) {
        $modcount = ['activities' => $ax, 'resources' => $rx];

        // Set cache.
        $cache->set($cacheidc, $modcount);
        return $modcount;
    }

    asort($modfullnames);

    // Set cache.
    $cache->set($cacheid, $modfullnames);
    return $modfullnames;

}





/**
 *
 * Method to display course activities
 *
 * @return array
 */
function theme_mb2nl_get_activities($ccourse = false, $name = '') {

    global $OUTPUT, $COURSE;

    $cobj = $ccourse ? $ccourse : $COURSE;
    $list = [];
    $data = theme_mb2nl_get_course_activities($ccourse);

    foreach ($data as $mname => $mfullname) {
        if ($name !== '' && $name !== $mname) {
            continue;
        }

        if ($mname === 'resources') {
            $purpose = plugin_supports('mod', 'resource', FEATURE_MOD_PURPOSE);
            $iconurl = $OUTPUT->image_url('icon', 'resource');
            $list[] = ['url' => new moodle_url('/course/resources.php', ['id' => $cobj->id]), 'title' => $mfullname, 'purpose' =>
            $purpose, 'icon' => $iconurl];
        } else {
            $purpose = plugin_supports('mod', $mname, FEATURE_MOD_PURPOSE);
            $iconurl = $OUTPUT->image_url('icon', $mname);
            $list[] = ['url' => new moodle_url('/mod/' . $mname . '/index.php', ['id' => $cobj->id]), 'title' => $mfullname,
            'purpose' => $purpose, 'icon' => $iconurl];
        }
    }

    return $list;

}







/**
 *
 * Method to display course activities
 *
 * @return HTML
 */
function theme_mb2nl_activities_list($ccourse = false, $links = false) {
    $activities = theme_mb2nl_get_activities($ccourse);
    $output = '';

    $output .= '<ul class="course-activities">';

    foreach ($activities as $a) {

        $output .= '<li class="course-activities-group' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">';
        $output .= '<span class="activitiesimage ' . $a['purpose'] . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '">';
        $output .= '<img class="activityicon" src="' . $a['icon'] . '" alt="' . $a['title'] . '">';
        $output .= '</span>';
        $output .= $links ? '<a href="' . $a['url'] . '">' : '';
        $output .= $a['title'];
        $output .= $links ? '</a>' : '';
        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output;
}





/**
 *
 * Method to display course shares icons
 *
 * @return HTML
 */
function theme_mb2nl_course_share_list($id, $title, $blog = false) {
    global $PAGE;

    $links = theme_mb2nl_course_get_share_links($id, $title, $blog);
    $output = '';

    $output .= '<ul class="share-list' . theme_mb2nl_bsfcls(2) . '">';

    foreach ($links as $k => $link) {
        if (!theme_mb2nl_theme_setting($PAGE, $k)) {
            continue;
        }

        $dataurl = isset($link['url']) ? ' data-url="'. $link['url'] . '"' : '';
        $islink = $link['link'] ? $link['link'] : '#';
        $target = $link['link'] ? ' target="_blank"' : '';

        $output .= '<li class="' . $k . '">';
        $output .= '<a href="' . $islink . '" class="' . $k . '_link' .
        theme_mb2nl_bsfcls(2, '', 'center', 'center') . '" aria-label="' . $link['title'] . '"' . $target . $dataurl . '>';
        $output .= '<i class="icon1 ' . $link['icon'] . '"></i>';
        $output .= isset($link['icon2']) ? '<i class="icon2 ' . $link['icon2'] . '"></i>' : '';
        $output .= '</a>';
        $output .= '</li>';
    }

    $output .= '</ul>';

    if (theme_mb2nl_theme_setting($PAGE, 'shareurl')) {
        $PAGE->requires->js_call_amd('theme_mb2nl/social', 'shareUrl');
    }

    return $output;

}






/**
 *
 * Method to display course shares icons
 *
 * @return array
 */
function theme_mb2nl_course_get_share_links($id, $title, $blog=false) {

    $links = [];

    $itemtype = $blog ? get_string('post') : get_string('course');
    $url = new moodle_url('/enrol/index.php', ['id' => $id]);

    if ($blog) {
        $url = new moodle_url('/blog/index.php', ['entryid' => $id]);
    }

    $links['sharetwitter'] = [
        'title' => 'Twitter',
        'link' => 'https://twitter.com/intent/tweet?text=' . urlencode($title . ' ' . $url),
        'icon' => 'ri-twitter-x-fill',
    ];

    $links['sharefacebook'] = [
        'title' => 'Facebook',
        'link' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url) . '&title=' . urlencode($title),
        'icon' => 'ri-facebook-fill',
    ];

    $links['sharelinkedin'] = [
        'title' => 'LinkedIn',
        'link' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . $url . '&title=' . urlencode($title) . '&source=' .
        urlencode($url) . '',
        'icon' => 'ri-linkedin-fill',
    ];

    $links['shareemail'] = [
        'title' => get_string('shareemail', 'theme_mb2nl'),
        'link' => 'mailto:?subject=' . strip_tags($title) . '&body=' . $itemtype . ': ' . $url,
        'icon' => 'ri-mail-send-line',
    ];

    $links['shareurl'] = [
        'title' => get_string('shareurl', 'theme_mb2nl'),
        'link' => '',
        'icon' => 'ri-link',
        'icon2' => 'ri-check-line',
        'url' => $url,
    ];

    return $links;

}






/**
 *
 * Method to get enrol hero image url
 *
 * @return url|null
 */
function theme_mb2nl_get_enroll_hero_url() {
    global $COURSE, $PAGE;

    $fieldimage = theme_mb2nl_mb2fields_filed('mb2image');
    $courseimg = theme_mb2nl_course_image_url($COURSE->id);

    if ($fieldimage) {
        return $fieldimage;
    } else if ($courseimg && theme_mb2nl_theme_setting($PAGE, 'cbanner')) {
        return $courseimg;
    }

    return null;

}





/**
 *
 * Method to get options theme and format
 *
 * @return string
 */
function theme_mb2nl_get_course_slogan($text = '', $ccourse = null) {
    global $COURSE;

    $content = '';
    $iscourse = !is_null($ccourse) ? $ccourse : $COURSE;

    if ($text) {
        $content = strip_tags($text);
    } else if (!is_null($iscourse->summary)) {
        $content = strip_tags($iscourse->summary);
    }

    if (!$content) {
        return;
    }

    $pos = strpos($content, '.');
    $pos1 = strpos($content, '!');
    $pos2 = strpos($content, '?');

    if ($pos !== false) {
        $ispos = $pos;
    } else if ($pos1 !== false) {
        $ispos = $pos1;
    } else if ($pos2 !== false) {
        $ispos = $pos2;
    } else {
        // Sometimes user don't add dot or other character.
        // To prevent return null, we have to set the ispos variable to 999.
        $ispos = 999;
    }

    return substr($content, 0, ($ispos + 1));
}






/**
 *
 * Method to get currency array
 *
 * @return array
 */
function theme_mb2nl_get_currency_arr() {

    return ['ALL:4c,65,6b' => 'ALL', 'AFN:60b' => 'AFN', 'ARS:24' => 'ARS', 'AWG:192' => 'AWG', 'AUD:24' => 'AUD', 'AZN:43c,430,43d'
    => 'AZN', 'BSD:24' => 'BSD', 'BBD:24' => 'BBD', 'BYR:70,2e' => 'BYR', 'BZD:42,5a,24' => 'BZD', 'BMD:24' => 'BMD', 'BOB:24,62'
    => 'BOB', 'BAM:4b,4d' => 'BAM', 'BWP:50' => 'BWP', 'BGN:43b,432' => 'BGN', 'BRL:52,24' => 'BRL', 'BND:24' => 'BND', 'KHR:17db'
    => 'KHR', 'CAD:24' => 'CAD', 'KYD:24' => 'KYD', 'CLP:24' => 'CLP', 'CNY:a5' => 'CNY', 'COP:24' => 'COP', 'CRC:20a1'
    => 'CRC', 'HRK:6b,6e' => 'HRK', 'CUP:20b1' => 'CUP', 'CZK:4b,10d' => 'CZK', 'DKK:6b,72' => 'DKK', 'DOP:52,44,24'
    => 'DOP', 'XCD:24' => 'XCD', 'EGP:a3' => 'EGP', 'SVC:24' => 'SVC', 'EEK:6b,72' => 'EEK', 'EUR:20ac' => 'EUR', 'FKP:a3'
    => 'FKP', 'FJD:24' => 'FJD', 'GHC:a2' => 'GHC', 'GIP:a3' => 'GIP', 'GTQ:51' => 'GTQ', 'GGP:a3' => 'GGP', 'GYD:24'
    => 'GYD', 'HNL:4c' => 'HNL', 'HKD:24' => 'HKD', 'HUF:46,74' => 'HUF', 'ISK:6b,72' => 'ISK', 'INR:20a8' => 'INR', 'IDR:52,70'
    => 'IDR', 'IRR:fdfc' => 'IRR', 'IMP:a3' => 'IMP', 'ILS:20aa' => 'ILS', 'JMD:4a,24' => 'JMD', 'JPY:a5' => 'JPY', 'JEP:a3'
    => 'JEP', 'KZT:43b,432' => 'KZT', 'KES:4b,73,68,73' => 'KES', 'KGS:43b,432' => 'KGS', 'LAK:20ad' => 'LAK', 'LVL:4c,73'
    => 'LVL', 'LBP:a3' => 'LBP', 'LRD:24' => 'LRD', 'LTL:4c,74' => 'LTL', 'MKD:434,435,43d' => 'MKD', 'MYR:52,4d'
    => 'MYR', 'MUR:20a8' => 'MUR', 'MXN:24' => 'MXN', 'MNT:20ae' => 'MNT', 'MZN:4d,54' => 'MZN', 'NAD:24' => 'NAD', 'NPR:20a8'
    => 'NPR', 'ANG:192' => 'ANG', 'NZD:24' => 'NZD', 'NIO:43,24' => 'NIO', 'NGN:20a6' => 'NGN', 'KPW:20a9' => 'KPW', 'NOK:6b,72'
    => 'NOK', 'OMR:fdfc' => 'OMR', 'PKR:20a8' => 'PKR', 'PAB:42,2f,2e' => 'PAB', 'PYG:47,73' => 'PYG', 'PEN:53,2f,2e'
    => 'PEN', 'PHP:50,68,70' => 'PHP', 'PLN:7a,142' => 'PLN', 'QAR:fdfc' => 'QAR', 'RON:6c,65,69' => 'RON', 'RUB:440,443,431'
    => 'RUB', 'SHP:a3' => 'SHP', 'SAR:fdfc' => 'SAR', 'RSD:414,438,43d,2e' => 'RSD', 'SCR:20a8' => 'SCR', 'SGD:24'
    => 'SGD', 'SBD:24' => 'SBD', 'SOS:53' => 'SOS', 'ZAR:52' => 'ZAR', 'KRW:20a9' => 'KRW', 'LKR:20a8' => 'LKR', 'SEK:6b,72'
    => 'SEK', 'CHF:43,48,46' => 'CHF', 'SRD:24' => 'SRD', 'SYP:a3' => 'SYP', 'TWD:4e,54,24' => 'TWD', 'THB:e3f'
    => 'THB', 'TTD:54,54,24' => 'TTD', 'TRY:54,4c' => 'TRY', 'TRL:20a4' => 'TRL', 'TVD:24' => 'TVD', 'UAH:20b4' => 'UAH', 'GBP:a3'
    => 'GBP', 'USD:24' => 'USD', 'UYU:24,55' => 'UYU', 'UZS:43b,432' => 'UZS', 'VEF:42,73' => 'VEF', 'VND:20ab' => 'VND', 'YER:fdfc'
    => 'YER', 'ZWD:5a,24' => 'ZWD'];

}




/**
 *
 * Method to get currency symbol
 *
 * @return string
 */
function theme_mb2nl_get_currency_symbol($currency) {

    $currencyarr = theme_mb2nl_get_currency_arr();
    $output = '';

    foreach ($currencyarr as $k => $c) {
        $curr = explode(':', $k);

        if ($c === $currency) {
            $curr2 = explode(',', $curr[1]);

            foreach ($curr2 as $c) {
                $output .= '&#x' . $c . ';';
            }

        }
    }

    return $output;

}





/**
 *
 * Method to get course sections accordion
 *
 * @return HTML
 */
function theme_mb2nl_course_sections_accordion() {

    global $COURSE, $PAGE;

    if ($COURSE->format === 'singleactivity') {
        return;
    }

    $output = '';

    $output .= '<div class="coursetoc-sectionlist enrol-sectionlist">';
    $output .= theme_mb2nl_toc_tools(false, true);
    $output .= '<div class="coursetoc-sectionlist-content" data-courseid="' . $COURSE->id . '" data-start="0" data-limit="' .
    theme_mb2nl_theme_setting($PAGE, 'coursetoclimit') . '" data-container=".block_coursetoc"></div>'; // Content loaded via js.
    $output .= '</div>';

    $PAGE->requires->js_call_amd('theme_mb2nl/toc', 'enrolmentTocLoad');
    $PAGE->requires->js_call_amd('theme_mb2nl/toc', 'loadAll');
    $PAGE->requires->js_call_amd('theme_mb2nl/toc', 'toggleAll');

    return $output;

}





/**
 *
 * Method to get course sections
 *
 * @return HTML
 */
function theme_mb2nl_module_enrolment_section_items($courseobj = null, $viewhidden = 0, $start = 0, $limit = 0) {
    $output = '';
    $i = 0;
    $sections = theme_mb2nl_get_course_sections($courseobj);

    foreach ($sections as $section) {

        // Skip section if there is some available info.
        if (!$viewhidden && $section['availableinfo']) {
            continue;
        }

        if ($start && $i <= ($start - 1)) {
            $i++;
            continue;
        } else if ($limit && $i > ($limit - 1)) {
            continue;
        }

        $collid = 'panel-' . $courseobj->id . '-' . $section['num'];
        $isactive = $i == 0 ? ' active' : '';
        $expanded = $i == 0 ? 'true' : 'false';

        // Highlighter section class for course editors.
        // We don't want to show section badges for visitors on enrolment pages.
        $highlightedcls = ($viewhidden && $section['highlighted']) ? ' highlighted' : '';

        $i++;

        $sname = empty($section['name']) ? get_string('section') : theme_mb2nl_format_str($section['name']);

        $output .= '<div class="coursetoc-section coursetoc-section-' . $section['num'] . $isactive  . '">';
        $output .= '<div class="coursetoc-section-tite' . theme_mb2nl_bsfcls(1, '', 'between', 'center') . '">';
        $output .= '<span class="' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">';

        $output .= theme_mb2nl_section_mark(false, $i);
        $output .= '<span class="title-text text">' . $sname;
        $output .= $viewhidden ? theme_mb2nl_section_badges($section, true) : '';
        $output .= '</span>'; // ...text
        $output .= '</span>'; // ...title-text
        $output .= '<button class="coursetoc-section-toggle themereset' . $highlightedcls .
        theme_mb2nl_bsfcls(2, '', 'center', 'center') . '" aria-label="'. get_string('togglesection', 'theme_mb2nl', $sname) .
        '" aria-controls="' . $collid. '" aria-expanded="' . $expanded . '">';
        $output .= '<span class="toggle-icon"></span>';
        $output .= '</button>';
        $output .= '</div>'; // ...coursetoc-section-tite

        $output .= '<div id="' . $collid . '" class="coursetoc-section-modules">';
        $output .= theme_mb2nl_section_module_list($section['id'], false, false, false, -1, $courseobj, 0, null, null, $viewhidden);
        $output .= '</div>'; // ...coursetoc-section-modules
        $output .= '</div>'; // ...coursetoc-section
    }

    return $output;

}






/**
 *
 * Method to get course format settings
 *
 * @return object
 */
function theme_mb2nl_format_opts($ccourse = null) {

    global $CFG, $COURSE;

    require_once($CFG->dirroot . '/course/lib.php');

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;

    // Get course settings.
    $format = course_get_format($cobj);
    $options = $format->get_format_options();

    return $options;

}




/**
 *
 * Method to get course tabs for custom course layout
 *
 * @return HTML
 */
function theme_mb2nl_course_tabs($layout = 'ver', $content = true) {
    global $PAGE, $COURSE, $CFG;

    $i = 0;
    $output = '';
    $menucls = '';
    $uniqid = uniqid('section_list_');
    $progresstr = $CFG->version >= 2023100900 ? get_string('progress') : get_string('progress', 'theme_mb2nl');

    // Get URL params.
    $urlid = optional_param('id', 0, PARAM_INT);
    $urlsection = optional_param('mb2sct', 0, PARAM_INT);
    $urlctab = optional_param('ctab', '', PARAM_ALPHANUMEXT);

    $sections = theme_mb2nl_get_course_sections();
    $reviews = theme_mb2nl_is_review_plugin();

    // Get reviews variables.
    $canrate = '';
    $ratealready = '';
    $ratingstars = '';
    $courserating = '';
    $reviewsummary = '';

    if ($reviews) {
        if (!class_exists('Mb2reviewsHelper')) {
            require($CFG->dirroot . '/local/mb2reviews/classes/helper.php');
        }

        $ratingobj = theme_mb2nl_review_obj($COURSE->id);
        $ratingdetailsobj = theme_mb2nl_review_obj($COURSE->id, true);
        $rhlpr = new Mb2reviewsHelper;

        $courserating = $ratingobj->rating;
        $reviewsummary = Mb2reviewsHelper::review_summary($ratingobj, $ratingdetailsobj);
        $canrate = Mb2reviewsHelper::can_rate($COURSE->id);
        $ratealready = Mb2reviewsHelper::rate_already($COURSE->id);
        $ratingstars = Mb2reviewsHelper::rating_stars($ratingobj->rating, 'xs');
    }

    // Custom section.
    $mb2section = theme_mb2nl_mb2fields_filed('mb2section') && theme_mb2nl_theme_setting($PAGE, 'csection');

    // Get menu state based on the js.
    if ($CFG->version < 2023100900) { // Up to Moodle 4.3.
        user_preference_allow_ajax_update('mb2_cnavstate', PARAM_ALPHA);
    }

    $menustate = theme_mb2nl_user_preference('mb2_cnavstate', 'close');

    // Define css classes.
    $menucls .= ' layout-' . $layout;
    $menucls .= ($layout === 'ver' && $menustate === 'open') ? ' open' : ''; // We need it only on the vertical layout.
    $expanded = ($layout === 'ver' && $menustate === 'open') ? 'true' : 'false';

    $output .= '<div class="course-nav-list-container' . $menucls . '">';

    // Strt list.
    $output .= '<ul class="course-nav-list">';

    if ($content) {

        // Start course content list item.
        $output .= '<li class="course-nav-list-item course-nav-list-ccontent">';
        $output .= '<button type="button" class="themereset course-nav-list-item-toggle' .
        theme_mb2nl_bsfcls(1, 'wrap', 'between', 'center') . '" aria-controls="' . $uniqid . '" aria-expanded="' .
        $expanded . '" aria-label="' . get_string('togglesections', 'theme_mb2nl') . '">';
        $output .= '<span class="toggle-text">' . get_string('headingsections', 'theme_mb2nl') . '</span>';
        $output .= '<span class="toggle-icon' . theme_mb2nl_bsfcls(1, '', 'center', 'center') . '"></span>';
        $output .= '</button>';

        $output .= '<div id="' . $uniqid . '" class="course-nav-list-item-list-container">';
        $output .= '<ul class="course-nav-list-item-list">';

        foreach ($sections as $section) {
            $i++;

            // Completion.
            $completepctg = theme_mb2nl_section_complete($section);
            $completetotal = theme_mb2nl_section_complete($section, false, null, true);
            $iscomplete = theme_mb2nl_section_complete($section, true);
            $datacompletepctg = $completepctg !== false ? $completepctg : 'false';

            // Section link.
            $sname = theme_mb2nl_format_str($section['name']);
            $link = theme_mb2nl_sectionurl($section);
            $itemtag = $link ? 'a' : 'span';
            $linkhref = $link ? ' href="' . $link . '"' : '';

            // Classess.
            $activecls = $section['num'] == $urlsection && $urlctab === '' ? ' active' : '';
            $completecls = $iscomplete ? ' complete' : '';
            $highlightedcls = $section['highlighted'] ? ' highlighted' : '';

            $output .= '<li class="course-nav-list-item-list-item" data-complete="' . $datacompletepctg . '">';
            $output .= '<' . $itemtag . $linkhref . ' class="course-nav-button' . $activecls . $completecls . $highlightedcls .
            theme_mb2nl_bsfcls(1, 'row', '', 'center') . '">';
            $output .= theme_mb2nl_section_mark($completepctg, $i);
            $output .= '<div class="item-text">' . $sname;
            $output .= theme_mb2nl_section_badges($section, true);
            $output .= $completepctg !== false ? '<span class="title-complete text-nowrap ml-1' .
            theme_mb2nl_tcls('small', 'normal') . '">(<span class="sr-only">' . $progresstr . '</span> ' .
            $completetotal['complete'] . ' / ' . $completetotal['total'] . ')</span>' : '';
            $output .= '</div>'; // ...course-nav-list-item-list-item-text
            $output .= '</' . $itemtag . '>'; // ...course-nav-list-item-list-item-button
            $output .= '</li>'; // ...course-nav-list-item-list-item
        }

        $output .= '</ul>'; // ...course-nav-list-item-list
        $output .= '</div>'; // ...course-nav-list-item-list-container
        $output .= '</li>'; // ...course-nav-list-item

    }

    if (!$content) {
        // Course info item.
        $link = new moodle_url('/course/view.php', ['id' => $urlid, 'ctab' => 'general']);
        $activecls = $urlctab === 'general' || (theme_mb2nl_course_layout() == 3 && $urlctab === '') ? ' active' : '';
        $output .= '<li class="course-nav-list-item course-nav-list-general">';
        $output .= '<a href="' . $link . '" class="course-nav-button' . $activecls . theme_mb2nl_bsfcls(2, '', '', 'center') . '">';
        $output .= '<div class="item-text">' . get_string('overview', 'theme_mb2nl') . '</div>';
        $output .= '</a>';
        $output .= '</li>'; // ...course-nav-list-courseinfo
    }

    // Course info item.
    $link = new moodle_url('/course/view.php', ['id' => $urlid, 'ctab' => 'courseinfo']);
    $activecls = $urlctab === 'courseinfo' ? ' active' : '';
    $output .= '<li class="course-nav-list-item course-nav-list-courseinfo">';
    $output .= '<a href="' . $link . '" class="course-nav-button' . $activecls . theme_mb2nl_bsfcls(2, '', '', 'center') . '">';
    $output .= '<div class="item-text">' . get_string('courseinfo') . '</div>';
    $output .= '</a>';
    $output .= '</li>'; // ...course-nav-list-courseinfo

    // Custom section items.
    $csections = theme_mb2nl_field_sections();
    if (count($csections) && theme_mb2nl_theme_setting($PAGE, 'csection')) {
        foreach ($csections as $csection) {
            $link = new moodle_url('/course/view.php', ['id' => $urlid, 'ctab' => $csection['id']]);
            $activecls = $urlctab === $csection['id'] ? ' active' : '';
            $output .= '<li class="course-nav-list-item course-nav-list-csection">';
            $output .= '<a href="' . $link . '" class="course-nav-button' . $activecls . theme_mb2nl_bsfcls(2, '', '', 'center') .
            '">';
            $output .= '<div class="item-text">' . theme_mb2nl_mb2sectionfiledname($csection['id']) . '</div>';
            $output .= '</a>';
            $output .= '</li>'; // ...course-nav-list-csection
        }
    }

    if ($reviewsummary || $canrate || $ratealready) {
        $link = new moodle_url('/course/view.php', ['id' => $urlid, 'ctab' => 'reviews']);
        $activecls = $urlctab === 'reviews' ? ' active' : '';
        $output .= '<li class="course-nav-list-item course-nav-list-reviews">';
        $output .= '<a href="' . $link . '" class="course-nav-button' . $activecls . theme_mb2nl_bsfcls(2, '', '', 'center') . '">';
        $output .= '<div class="item-text">' . get_string('reviews', 'theme_mb2nl') . '</div>';
        $output .= $courserating ? $ratingstars . '<span class="course-rating">' . $courserating . '</span>' : '';
        $output .= '</a>';
        $output .= '</li>'; // ...course-nav-list-reviews
    }

    $output .= '</ul>'; // ...course-nav-list
    $output .= '</div>'; // ...course-nav-list-container

    return $output;

}




/**
 *
 * Method to get course section boxes
 *
 */
function theme_mb2nl_course_section_boxes() {
    global $CFG, $COURSE, $PAGE;

    $output = '';
    $counter = 0;

    if (optional_param('ctab', '', PARAM_ALPHANUMEXT) !== '') {
        return;
    }

    $sections = theme_mb2nl_get_course_sections();
    $opts = theme_mb2nl_format_opts();
    $modalsections = isset($opts['coursedisplay']) ? $opts['coursedisplay'] != 1 : 0;

    if (!count($sections)) {
        return;
    }

    $output .= '<div class="course-section-list theme-boxes gutter-normal">';

    $output .= theme_mb2nl_course_box();

    foreach ($sections as $section) {
        $counter++;
        $output .= theme_mb2nl_course_section_box($section, $modalsections, $counter);
    }

    $output .= '</div>';

    $output .= $modalsections ? theme_mb2nl_course_modal_sections() : '';

    return $output;

}




/**
 *
 * Method to get course box
 *
 */
function theme_mb2nl_course_box($coursepage = true) {

    global $CFG, $PAGE, $COURSE;

    $output = '';
    $urlid = optional_param('id', 0, PARAM_INT);
    $urlctab = optional_param('ctab', '', PARAM_ALPHANUMEXT);

    if ($coursepage && $urlctab !== '') {
        return;
    }

    $reviews = theme_mb2nl_is_review_plugin();

    // Get reviews variables.
    $canrate = '';
    $ratealready = '';
    $ratingstars = '';
    $courserating = '';
    $reviewsbtn = '';
    $reviewsummary = '';

    if ($reviews) {
        if (!class_exists('Mb2reviewsHelper')) {
            require($CFG->dirroot . '/local/mb2reviews/classes/helper.php');
        }

        $ratingobj = theme_mb2nl_review_obj($COURSE->id);
        $ratingdetailsobj = theme_mb2nl_review_obj($COURSE->id, true);
        $rhlpr = new Mb2reviewsHelper;

        $courserating = $ratingobj->rating;
        $reviewsummary = Mb2reviewsHelper::review_summary($ratingobj, $ratingdetailsobj);
        $canrate = Mb2reviewsHelper::can_rate($COURSE->id);
        $ratealready = Mb2reviewsHelper::rate_already($COURSE->id);
        $ratingstars = Mb2reviewsHelper::rating_stars($ratingobj->rating, 'xs');
    }

    if ($reviewsummary || $canrate || $ratealready) {
        $link = new moodle_url('/course/view.php', ['id' => $urlid, 'ctab' => 'reviews']);
        $reviewsbtn = '<a href="' . $link . '" class="link-item reviews-item' . theme_mb2nl_bsfcls(2, '', 'center', 'center') .'">';
        $reviewsbtn .= '<div class="item-text">' . get_string('reviews', 'theme_mb2nl') . '</div>';
        $reviewsbtn .= $courserating ? $ratingstars . '<span class="course-rating">' . $courserating . '</span>' : '';
        $reviewsbtn .= '</a>';
    }

    $linkinfo = new moodle_url('/course/view.php', ['id' => $urlid, 'ctab' => 'courseinfo']);

    $output .= '<div class="section-box course-box theme-box">';
    $output .= '<div class="section-box-inner position-relative' . theme_mb2nl_bsfcls(1, 'column') . '">';
    $output .= '<div class="section-box-a position-relative lazy pt-0' . theme_mb2nl_bsfcls(2, 'column', 'center') . '" data-bg="' .
    theme_mb2nl_course_image_url($COURSE->id, true) . '">';
    $output .= '<div class="section-buttons fwbold' . theme_mb2nl_bsfcls(1, 'wrap', 'center', 'center') . '">';
    $output .= '<a href="' . $linkinfo . '" class="link-item' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '">' .
    get_string('courseinfo') . '</a>';

    $csections = theme_mb2nl_field_sections();
    if (count($csections) && theme_mb2nl_theme_setting($PAGE, 'csection')) {
        foreach ($csections as $csection) {
            $link = new moodle_url('/course/view.php', ['id' => $urlid, 'ctab' => $csection['id']]);
            $output .= '<a href="' . $link . '" class="link-item' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '">';
            $output .= theme_mb2nl_mb2sectionfiledname($csection['id']);
            $output .= '</a>';
        }
    }

    $output .= $reviewsbtn;
    $output .= '</div>';
    $output .= '<div class="section-box-progress' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '">';
    $output .= theme_mb2nl_course_progressbar(['align' => true]);
    $output .= '</div>'; // ...section-box-progress
    $output .= '</div>'; // ...section-box-a
    $output .= '<div class="section-box-b' . theme_mb2nl_bsfcls(1, 'row', '', 'center') . '">';
    $output .= theme_mb2nl_course_boxes('circle');
    $output .= '</div>'; // ...section-box-b
    $output .= '</div>'; // ...section-box-inner
    $output .= '</div>';

    return $output;

}





/**
 *
 * Method to get course section box
 *
 */
function theme_mb2nl_course_section_box($section, $modalsections, $counter) {

    global $CFG, $PAGE, $OUTPUT;

    $output = '';

    $progresstr = $CFG->version >= 2023100900 ? get_string('progress') : get_string('progress', 'theme_mb2nl');
    $completepctg = theme_mb2nl_section_complete($section, false);
    $completetotal = theme_mb2nl_section_complete($section, false, null, true);
    $gotosectiontext = $CFG->version >= 2024042200 ? get_string('gotosection', 'course', $section['name']) :
    get_string('gotosection', 'theme_mb2nl', $section['name']);
    $sname = theme_mb2nl_character_limit($section['name'], theme_mb2nl_theme_setting($PAGE, 'snamelimit'));
    $cls = '';

    $cls .= $section['hiddenfromstudents'] || $section['notavailable'] ? ' hiddenfromstudents' : '';
    $cls .= $section['highlighted'] ? ' highlighted' : '';
    $cls .= $section['restriction'] ? ' restriction' : '';

    $output .= '<div class="section-box theme-box' . $cls . '">';
    $output .= '<div class="section-box-inner position-relative' . theme_mb2nl_bsfcls(1, 'column') . '">';
    $output .= '<div class="section-box-a dark' . theme_mb2nl_bsfcls(1, 'column', 'between') . '">';
    $output .= '<div class="box-a-1' . theme_mb2nl_bsfcls(1, '', 'between', 'center') . '">';
    $output .= '<div class="section-box-progress' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '">';
    $output .= theme_mb2nl_section_mark($completepctg, $counter, false, ['s' => 30, 'fcolor' => 'transparent',
    'bcolor' => 'rgba(255,255,255,.15']);

    if (is_array($completetotal)) {
        $output .= '<div class="completetotal' . theme_mb2nl_bsfcls(1, 'row', '', 'center') . '">';
        $output .= '<span>' . $progresstr . ':</span> ';
        $output .= '<span class="item-progress">' . $completetotal['complete'] . ' / ' . $completetotal['total'] .
        '</span>';
        $output .= '</div>';
    }

    $output .= '</div>'; // ...section-box-progress
    $output .= theme_mb2nl_section_badges($section, true);
    $output .= '</div>'; // ...box-a-1

    $output .= '<div class="box-a-2">';
    $output .= theme_mb2nl_section_modcounts_html($section);
    $output .= '</div>'; // ...box-a-2

    $output .= '</div>'; // ...section-box-a
    $output .= '<div class="section-box-b w-100' . theme_mb2nl_bsfcls(1, 'rw', '', 'center') . '">';

    $titlehtml = '<span class="section-title d-block h5 mb-0 lhmedium"><span class="sr-only">' .
    get_string('sectionname') . '</span> ' . $sname . '</span>';

    if ($modalsections) {
        $output .= '<button class="section-link themereset mb2modalsection p-0 d-block w-100" type="button" data-toggle="modal"';
        $output .= ' data-bs-toggle="modal" data-target="#mb2modalsection" data-bs-target="#mb2modalsection" data-counter="' .
        $counter . '" aria-label="' . $gotosectiontext . '">';
        $output .= $titlehtml;
        $output .= '</button>';
    } else {
        $output .= '<a class="section-link" href="' . theme_mb2nl_sectionurl($section) . '" aria-label="' . $gotosectiontext . '">';
        $output .= $titlehtml;
        $output .= '</a>';
    }

    $output .= '</div>'; // ...section-box-b
    $output .= '</div>'; // ...section-box-inner
    $output .= '</div>';

    return $output;

}





/**
 *
 * Method to get course section box
 *
 */
function theme_mb2nl_section_modcounts_html($section) {

    $output = '';
    $modcounts = theme_mb2nl_section_modcounts($section);

    if (!count($modcounts)) {
        return;
    }

    $output .= '<ul class="section-activities lhsmall mb-0 p-0' . theme_mb2nl_bsfcls(1, 'wrap', '', 'center') . '">';

    foreach ($modcounts as $k => $v) {
        $output .= '<li class="section-activity' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">';
        $output .= '<span class="name">' . get_string('modulenameplural', $k) . ':</span> ';
        $output .= '<span class="value">' . $v . '</span>';
        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output;

}


/**
 *
 * Method to get course sections array
 *
 */
function theme_mb2nl_course_modal_sections() {

    global $PAGE, $COURSE;

    $output = '';
    $headercls = theme_mb2nl_theme_setting($PAGE, 'fsmoddh') ? ' dark' : ' light';

    $output .= '<div class="modal fade fixedh" id="mb2modalsection" role="dialog" tabindex="0" aria-modal="true">';
    $output .= '<div class="modal-dialog modal-lg" role="document">';
    $output .= '<div class="modal-content course-content">';

    $output .= '<div class="modal-section-header' . $headercls . theme_mb2nl_bsfcls(1, 'row', 'between', 'center') . '">';
    $output .= '<div class="course-name h5 mb-0">';
    $output .= theme_mb2nl_format_str($COURSE->fullname);
    $output .= '</div>';
    $output .= '<button type="button" class="themereset p-0' . theme_mb2nl_bsfcls(2, '', 'center', 'center') .
    '" data-dismiss="modal" data-bs-dismiss="modal"><i class="bi bi-x-lg"></i></button>';
    $output .= '</div>';

    $output .= theme_mb2nl_modal_sections();

    $output .= '<div class="modal-section-nav' . theme_mb2nl_bsfcls(1, 'row', 'between', 'center') . '">';
    $output .= '<button type="button" class="modal-section_prev themereset' . theme_mb2nl_bsfcls(2, '', 'center', 'center') .
    '"><span>' . get_string('previoussection') . '</span></button>';
    $output .= '<div class="navtool' . theme_mb2nl_bsfcls(1, 'row', '', 'center') . '">';
    $output .= '<span class="nav-xof"></span>';
    $output .= '<div class="navtool-sections position-relative">';
    $output .= '<button type="button" class="toggle-modal-nav-sections p-0 themereset lhsmall' . theme_mb2nl_bsfcls(2, '', 'center',
    'center') . '" aria-controls="modal-nav-sections" aria-expanded="false">';
    $output .= '<span class="sr-only">' . get_string('sections') . '</span>';
    $output .= '<i class="bi bi-chevron-compact-up"></i>';
    $output .= '</button>';
    $output .= theme_mb2nl_modal_nav_sections();
    $output .= '</div>';
    $output .= '</div>';
    $output .= '<button type="button" class="modal-section_next themereset' . theme_mb2nl_bsfcls(2, '', 'center', 'center') .
    '"><span>' . get_string('nextsection') . '</span></button>';
    $output .= '</div>';

    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';

    return $output;

}




/**
 *
 * Method to get modal sections
 *
 */
function theme_mb2nl_modal_sections() {
    global $COURSE;

    $counter = 0;
    $output = '';
    $sections = theme_mb2nl_get_course_sections();

    $output .= '<div class="modal-sections' . theme_mb2nl_bsfcls(1, 'column') . '">';

    foreach ($sections as $section) {
        $counter++;
        $output .= '<div id="section-' . $COURSE->id . '_' . $section['num'] . '" class="single-section modal-section section-' .
        $section['num'] . '" data-snum="' . $section['num'] . '" data-counter="' . $counter . '">';
        $output .= '<ul class="topics"><li>' . theme_mb2nl_preload() . '</li></ul>'; // List of sections loaded via js.
        $output .= '</div>';
    }

    $output .= '</div>';

    return $output;

}


/**
 *
 * Method to get sections for modal navigation
 *
 */
function theme_mb2nl_modal_nav_sections() {
    global $CFG;
    $counter = 0;
    $output = '';
    $sections = theme_mb2nl_get_course_sections();
    $progresstr = $CFG->version >= 2023100900 ? get_string('progress') : get_string('progress', 'theme_mb2nl');

    $output .= '<ul id="modal-nav-sections" class="modal-nav-sections-list">';

    foreach ($sections as $section) {
        $counter++;
        $completepctg = theme_mb2nl_section_complete($section, false);
        $completetotal = theme_mb2nl_section_complete($section, false, null, true);
        $output .= '<li class="modal-nav-section-item' . theme_mb2nl_bsfcls(1, 'row', '', 'center') . '">';
        $output .= theme_mb2nl_section_mark($completepctg, $counter, true);
        $output .= '<button type="button" class="modal-nav-section-jump themereset w-100 p-0" data-counter="' . $counter . '">';
        $output .= $section['name'];
        $output .= theme_mb2nl_section_badges($section, true);
        $output .= $completepctg !== false ? '<span class="title-complete text-nowrap ml-1' . theme_mb2nl_tcls('small', 'normal') .
        '">(<span class="sr-only">' . $progresstr . '</span> ' . $completetotal['complete'] . ' / ' .
        $completetotal['total'] . ')</span>' : '';
        $output .= '</button>';
        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output;

}




/**
 *
 * Method to get course sections array
 *
 */
function theme_mb2nl_tabcontent_topics($showall = false, $modal = false) {
    global $CFG, $COURSE, $PAGE;

    $output = '';

    // Get URL params.
    $urlsection = optional_param('mb2sct', 0, PARAM_INT);
    $urlctab = optional_param('ctab', '', PARAM_ALPHANUMEXT);
    $sections = theme_mb2nl_get_course_sections();

    if (!count($sections)) {
        return;
    }

    // Get course sections for tabs.
    $format = course_get_format($COURSE);
    $modinfo = get_fast_modinfo($COURSE);
    $renderer = $PAGE->get_renderer('format_topics');

    // For Moodle 4+.
    $outputclass = $format->get_output_classname('content\\section\\cmlist');
    $outputsummary = $format->get_output_classname('content\\section\\summary');
    $outputavailability = $format->get_output_classname('content\\section\\availability');

    $htmlid = $modal ? 'course-modal-section-topics' : 'course-nav-section-topics';
    $htmlcls = $modal ? 'course-section-modal' : 'course-section-tabcontent';
    $htmllicls = $modal ? 'modal' : 'nav';

    $output .= '<div id="' . $htmlid . '" class="' . $htmlcls . ' course-content">';
    $output .= '<ul class="topics">';

    foreach ($sections as $s) {
        $i = $s['num'];

        if (!$showall && $urlctab) {
            continue;
        }

        if (!$showall && $urlsection != $i) {
            continue;
        }

        $section = $modinfo->get_section_info($i);

        // Render content for Moodel 4+.
        $cmlist = new $outputclass($format, $section);
        $summary = new $outputsummary($format, $section);
        $availability = new $outputavailability($format, $section);

        $rendersummary = $renderer->render($summary);
        $rendercmlist = $renderer->render($cmlist);
        $renderavlblt = $renderer->render($availability);

        $highlightedcls = $s['highlighted'] ? ' highlighted' : '';
        $output .= '<li data-snum="' . $i . '" id="course-' . $htmllicls . '-section-' . $i .
        '" class="course-' . $htmllicls . '-section course-' . $htmllicls . '-section-' . $i . $highlightedcls . '">';
        $output .= '<div class="course-nav-header">';
        $output .= '<h2 class="section-heading h3">' . $s['name'] . '</h2>';
        $output .= theme_mb2nl_section_badges($s);
        $output .= '</div>'; // ...course-nav-header
        $output .= '<div class="content course-nav-content">';
        $output .= $rendersummary;
        $output .= theme_mb2nl_section_avalibility($section) ? $renderavlblt : '';
        $output .= $rendercmlist;
        $output .= '</div>'; // ...content
        $output .= '</li>'; // ...course-section-tabcontent-item
    }

    $output .= '</ul>'; // ...topics
    $output .= '</div>'; // ...course-section-tabcontent

    return $output;

}







/**
 *
 * Method to get course sections array
 *
 */
function theme_mb2nl_section_badges($section, $icons = false) {

    $output = '';
    $cls = $icons ? ' icons' : ' text';
    $inlflex = theme_mb2nl_bsfcls(2);

    if (!$section['highlighted'] && !$section['hiddenfromstudents'] && !$section['notavailable'] && !$section['restriction']) {
        return;
    }

    $highlightedhtml = $icons ? '<span class="' . $inlflex . '" title="' .
    $section['highlighted'] . '"><i class="fa fa-lightbulb-o"></i></span>' :
    '<span class="badge badge-pill badge-primary">' . $section['highlighted'] . '</span>';

    $hiddenfromstudentshtml = $icons ? '<span class="' . $inlflex . '" title="' .
    $section['hiddenfromstudents'] . '"><i class="fa fa-eye-slash"></i></span>' :
    '<span class="badge badge-pill badge-warning">' . $section['hiddenfromstudents'] . '</span>';

    $notavailablehtml = $icons ? '<span class="' . $inlflex . '" title="' .
    $section['notavailable'] . '"><i class="fa fa-ban"></i></span>' :
    '<span class="badge badge-pill badge-secondary">' . $section['notavailable'] . '</span>';

    $restrictionhtml = $icons ? '<span class="restriction' . $inlflex . '" title="' .
    get_string('accessrestrictions', 'availability') . '"><i class="ri-lock-line"></i></span>' :
    '<span class="badge badge-pill badge-warning">' . get_string('accessrestrictions', 'availability') . '</span>';

    $output .= '<span class="course-badges' . $cls . theme_mb2nl_bsfcls(2, '', '', 'center') . '">';
    $output .= $section['highlighted'] ? $highlightedhtml : '';
    $output .= $section['hiddenfromstudents'] ? $hiddenfromstudentshtml : '';
    $output .= $section['notavailable'] ? $notavailablehtml : '';
    $output .= $section['restriction'] ? $restrictionhtml : '';
    $output .= '</span>'; // ...course-nav-badges

    return $output;

}




/**
 *
 * Method to get course sections array
 *
 */
function theme_mb2nl_get_course_sections($ccourse = null, $sectionid = 0) {
    global $CFG, $COURSE, $PAGE;

    $csections = [];
    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;

    // Check only if thete is a course object. Don't check if course ID != SITEID,
    // because We have to include the front page's course.
    // This is require for the front page's module navigation.
    // This is why We don't use here the 'theme_mb2nl_is_course' function here.
    if (!$cobj) {
        return [];
    }

    // Get course format options.
    $opts = theme_mb2nl_format_opts($cobj);

    // 0 = Hidden sections are shown as not available.
    // 1 = Hidden sections are completely invisible.
    $hiddensections = isset($opts['hiddensections']) ? $opts['hiddensections'] : 1;

    // Use cache.
    $cache = cache::make('theme_mb2nl', 'courses');
    $cacheid = 'csections_' . serialize(['ccourse' => $cobj->id, 'sectionid' => $sectionid, 'hiddensections' => $hiddensections]);

    // Get course count cache.
    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $coursecontext = context_course::instance($cobj->id);
    $viewhidden = has_capability('moodle/course:viewhiddensections', $coursecontext);

    $modinfo = get_fast_modinfo($cobj);
    $sections = $modinfo->get_section_info_all();

    foreach ($sections as $section) {
        // Check for avlibility for students.
        $availability = (!$viewhidden && $section->visible && !$section->uservisible && empty($section->availableinfo));

        // Check for hidden sections.
        $ishiddensections = (!$viewhidden && !$section->visible && $hiddensections);

        // Section by ID.
        $sectionbyid = ($sectionid > 0 && $sectionid != $section->id);

        if ($availability || $ishiddensections || $sectionbyid) {
            continue;
        }

        $sectionsarr = [
            // Basic section variables.
            'id' => $section->id,
            'num' => $section->section,
            'name' => get_section_name($cobj, $section),

            // Section badges.
            // Badges info are displayed if user can see it.
            'highlighted' => ($cobj->marker != 0 && $cobj->marker == $section->section) ? get_string('highlighted') : 0,
            'hiddenfromstudents' => !$section->visible && $viewhidden ? get_string('hiddenfromstudents') : 0,
            'notavailable' => !$section->visible && !$viewhidden ? get_string('notavailable') : 0,

            // Get avalibility info to hide section modules on TOC,
            // and hide whole section on enrolment page.
            'availableinfo' => !empty($section->availableinfo),

            // Section restrictions.
            'restriction' => theme_mb2nl_section_has_restrictions($section),

            // Subsection.
            'subsections' => [],
        ];

        $csections[$section->id] = $sectionsarr;

        // Subsection for Moodle 4.5+.
        if ($CFG->version >= 2024100700) {
            if ($sectiondelegated = $section->get_component_instance()) {
                unset($csections[$section->id]);

                if ($parentsection = $sectiondelegated->get_parent_section()) {
                    $csections[$parentsection->id]['subsections'][] = $sectionsarr;
                }
            }
        }
    }

    // Set cache.
    $cache->set($cacheid, $csections);

    return $csections;

}




/**
 *
 * Method to get course sections array
 *
 */
function theme_mb2nl_section_avalibility($section) {

    global $CFG, $PAGE, $COURSE;

    $format = course_get_format($COURSE);
    $renderer = $PAGE->get_renderer('format_topics');

    $outputavailability = $format->get_output_classname('content\\section\\availability');
    $availability = new $outputavailability($format, $section);
    $renderavlblt = $renderer->render($availability);

    if (theme_mb2nl_empty_text($renderavlblt)) {
        return true;
    }

    return false;

}




/**
 *
 * Method to get course sections array
 *
 */
function theme_mb2nl_module_avalibility($mod, $cobj=null) {

    global $PAGE, $COURSE;

    $iscobj = !is_null($cobj) ? $cobj : $COURSE;

    $format = course_get_format($iscobj);
    $renderer = $PAGE->get_renderer('format_topics');
    $modinfo = $format->get_modinfo();
    $mod = $modinfo->get_cm($mod->id);

    $availabilityclass = $format->get_output_classname('content\\cm\\availability');
    $availability = new $availabilityclass($format, $mod->get_section_info(), $mod);
    $renderavlblt = $renderer->render($availability);

    if (theme_mb2nl_empty_text($renderavlblt)) {
        return true;
    }

    // If there is an older Moodle version OR module has NO avalibility info, retrun false.
    return false;

}






/**
 *
 * Method to get course sections array
 *
 */
function theme_mb2nl_section_has_restrictions($section) {

    global $CFG, $PAGE, $COURSE;

    $renderer = $PAGE->get_renderer('format_topics');

    // Hidden sections have no restriction indicator displayed.
    if (empty($section->visible) || empty($CFG->enableavailability)) {
        return false;
    }

    if (theme_mb2nl_section_avalibility($section)) {
        return true;
    }

    return false;

}






/**
 *
 * Method to get course sections array
 *
 */
function theme_mb2nl_module_has_restrictions($mod, $cobj=null) {
    global $CFG, $PAGE, $COURSE;

    $iscobj = !is_null($cobj) ? $cobj : $COURSE;
    $cache = cache::make('theme_mb2nl', 'courses');
    $cacheid = 'restrictions_' . $iscobj->id . '_' . $mod->id;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid)['has_restrictions'];
    }

    $renderer = $PAGE->get_renderer('format_topics');

    // Hidden sections have no restriction indicator displayed.
    if (empty($mod->visible) || empty($CFG->enableavailability)) {
        return false;
    }

    if (theme_mb2nl_module_avalibility($mod, $iscobj)) {
        $cache->set($cacheid, ['has_restrictions' => true]);

        return true;
    }

    $cache->set($cacheid, ['has_restrictions' => false]);

    return false;

}



/**
 *
 * Method to get section activities and resources
 *
 */
function theme_mb2nl_section_modcounts($section) {

    global $COURSE, $USER;
    $i = 0;
    $cache = cache::make('theme_mb2nl', 'courses');
    $cacheid = 'section_items_' . $COURSE->id . '_' . $section['id'];

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $modcounts = [];
    $modinfo = get_fast_modinfo($COURSE);

    if (!isset($modinfo->sections[$section['num']])) {
        return [];
    }

    foreach ($modinfo->sections[$section['num']] as $cmid) {

        $thismod = $modinfo->cms[$cmid];

        if (!$thismod->is_visible_on_course_page()) {
            continue;
        }

        $modcounts[] = $thismod->modname;

    }

    // Subsections in Moodle 4.5.
    if (!empty($section['subsections'])) {
        foreach ($section['subsections'] as $subsection) {
            if (isset($modinfo->sections[$subsection['num']])) {
                foreach ($modinfo->sections[$subsection['num']] as $cmid) {
                    $thismod = $modinfo->cms[$cmid];
                    if (!$thismod->is_visible_on_course_page()) {
                        continue;
                    }
                    $modcounts[] = $thismod->modname;
                }
            }
        }
    }

    $modcounts = array_count_values($modcounts);

    $cache->set($cacheid, $modcounts);

    return $modcounts;

}




/**
 *
 * Method to get section complete percentage
 *
 */
function theme_mb2nl_section_complete($section, $iscomplete = false, $ccourse = null, $totalcomplete = false) {
    global $COURSE, $USER;

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;
    $total = 0;
    $complete = 0;
    $modinfo = get_fast_modinfo($cobj);
    $completioninfo = new completion_info($cobj);
    $cancomplete = isloggedin() && ! isguestuser();
    $issection = $section['num'];

    if (!$cancomplete || !$completioninfo->is_tracked_user($USER->id) || !isset($modinfo->sections[$issection])) {
        return false;
    }

    foreach ($modinfo->sections[$issection] as $cmid) {
        $thismod = $modinfo->cms[$cmid];

        if ($thismod->uservisible) {
            if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                $total++;
                $completiondata = $completioninfo->get_data($thismod, true);
                if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                    $complete++;
                }
            }
        }
    }

    // Get completion of subsections.
    if ($section['subsections']) {
        foreach ($section['subsections'] as $subsection) {
            if (!isset($modinfo->sections[$subsection['num']])) {
                continue;
            }

            foreach ($modinfo->sections[$subsection['num']] as $cmid) {
                $thismod = $modinfo->cms[$cmid];
                if ($thismod->uservisible) {
                    if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
                        $total++;
                        $completiondata = $completioninfo->get_data($thismod, true);
                        if ($completiondata->completionstate == COMPLETION_COMPLETE ||
                        $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                            $complete++;
                        }
                    }
                }
            }
        }
    }

    if (!$iscomplete && $totalcomplete && $total > 0) {
        return ['complete' => $complete, 'total' => $total];
    }

    if ($iscomplete && $total > 0 && $total == $complete) {
        return true;
    }

    if (!$iscomplete && $total > 0) {
        return round(($complete / $total) * 100);
    }

    return false;

}






/**
 *
 * Method to get course info table
 *
 */
function theme_mb2nl_course_info_table($ccourse = null) {
    global $CFG, $COURSE;

    $output = '';
    $cobj = $ccourse ? $ccourse : $COURSE;
    $fields = theme_mb2nl_get_course_fields($cobj->id);
    $courserating = '';

    if (!count($fields)) {
        return;
    }

    if (theme_mb2nl_is_review_plugin()) {
        if (!class_exists('Mb2reviewsHelper')) {
            require($CFG->dirroot . '/local/mb2reviews/classes/helper.php');
        }

        $ratingobj = theme_mb2nl_review_obj($cobj->id);
        $courserating = $ratingobj->rating;
        $ratingstars = Mb2reviewsHelper::rating_stars($ratingobj->rating);
        $ratingcount = $ratingobj->rating_count;
    }

    $output .= '<div class="course-fileds-table course-section-part theme-table-wrap">';
    $output .= '<table class="table table-bordered table-striped">';
    $output .= '<tbody>';

    foreach ($fields as $f) {

        // Hide mb2 fileds.
        if (in_array($f['shortname'], theme_mb2nl_mb2fields())) {
            continue;
        }

        // It's required for local video.
        // Or for course banner image.
        $editortext = theme_mb2nl_get_content_field_textarea($f['value'], 0, $f['id']);

        if (strip_tags($editortext) === '') {
            continue;
        }

        $output .= '<tr>';
        $output .= '<td class="field-name">';
        $output .= theme_mb2nl_format_str($f['name']);
        $output .= '</td>';
        $output .= '<td class="field-value">';
        $output .= $editortext;
        $output .= '</td>';
        $output .= '</tr>';
    }

    if ($courserating) {
        $output .= '<tr>';
        $output .= '<td class="field-name">';
        $output .= get_string('courserating', 'local_mb2reviews');
        $output .= '</td>';
        $output .= '<td class="field-value">';
        $output .= '<div class="rating-filed">';
        $output .= $ratingstars . $courserating . ' (' . get_string('ratingscount', 'local_mb2reviews',
        ['ratings' => $ratingcount]) . ')';
        $output .= '</div>';
        $output .= '</td>';
        $output .= '</tr>';
    }

    $output .= '</tbody>';
    $output .= '</table>';
    $output .= '</div>'; // ...course-fileds-table

    return $output;

}






/**
 *
 * Method to check if module is complete
 *
 */
function theme_mb2nl_module_complete($mod, $ccourse=null) {
    global $COURSE, $USER;

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;

    $completioninfo = new completion_info($cobj);
    $cancomplete = isloggedin() && ! isguestuser();
    $modinfo = get_fast_modinfo($cobj);
    $thismod = $modinfo->cms[$mod];

    if (! $cancomplete || ! $completioninfo->is_tracked_user($USER->id)) {
        return;
    }

    if ($thismod->uservisible) {
        if ($cancomplete && $completioninfo->is_enabled($thismod) != COMPLETION_TRACKING_NONE) {
            $completiondata = $completioninfo->get_data($thismod, true);

            if ($completiondata->completionstate == COMPLETION_COMPLETE ||
            $completiondata->completionstate == COMPLETION_COMPLETE_PASS) {
                return 1;
            } else {
                return -1;
            }
        }
    }

    return 2;

}





/**
 *
 * Method to get course teachers
 *
 */
function theme_mb2nl_course_teachers_data($courseid=0, $singlecourse=0) {
    global $COURSE, $USER, $OUTPUT, $CFG;

    $results = [];
    $teacherroleid = theme_mb2nl_user_roleid(true);
    $iscourseid = $courseid ? $courseid : $COURSE->id;

    // Get cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'courseteachers_' . $iscourseid . '_' . $singlecourse;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $context = context_course::instance($iscourseid);
    $teachers = get_role_users($teacherroleid, $context, false, 'u.id,u.firstname,u.firstnamephonetic,u.lastnamephonetic,
    u.middlename,u.alternatename,u.email,u.lastname,u.picture,u.imagealt,u.description');
    $hiddenuserfields = explode(',', $CFG->hiddenuserfields);
    $isdesc = !in_array('description', $hiddenuserfields);

    foreach ($teachers as $teacher) {
        $teacherdata = [
            'id' => $teacher->id,
            'firstname' => $teacher->firstname,
            'lastname' => $teacher->lastname,
            'picture' => $OUTPUT->user_picture($teacher, ['size' => 100, 'link' => 0]),
            'email' => $teacher->email,
        ];

        if ($singlecourse) {
            $teacherdata['description'] = $isdesc ? $teacher->description : '';
            $teacherdata['coursescount'] = theme_mb2nl_get_instructor_courses_count($teacher->id);
            $teacherdata['studentscount'] = theme_mb2nl_instructor_students_count($teacher->id);
        }

        $results[$teacher->id] = $teacherdata;
    }

    // Set cache.
    $cache->set($cacheid, $results);

    return $results;

}






/**
 *
 * Method to get course teacher list
 *
 */
function theme_mb2nl_course_teachers_list($morelessh = 200, $ccourse = null) {
    global $PAGE, $CFG, $COURSE;

    $output = '';
    $uniqid = uniqid('moreless_');
    $email = theme_mb2nl_email_display($ccourse);
    $teachermessage = theme_mb2nl_message_display();

    $output .= '<div>';
    $output .= '<div id="' . $uniqid . '">';
    $output .= '<div>';
    $output .= '<ul class="course-instructors pt-1">';
    $output .= '</ul>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= theme_mb2nl_moreless('', $morelessh, $uniqid);
    $output .= '</div>';

    $PAGE->requires->js_call_amd('theme_mb2nl/course', 'teacherListLoad', [$COURSE->id]);

    return $output;

}




/**
 *
 * Method to get course teachers on course list
 *
 */
function theme_mb2nl_course_teachers($courseid, $options = []) {
    global $PAGE;

    $output = '';
    $teachers = theme_mb2nl_course_teachers_data($courseid);
    $coursinstructor = theme_mb2nl_theme_setting($PAGE, 'coursinstructor');

    if (isset($options['coursinstructor'])) {
        $coursinstructor = $options['coursinstructor'];
    }

    if (!count($teachers) || !$coursinstructor) {
        return;
    }

    $otherteachers = count($teachers) - 1;
    $mainteacher = array_shift($teachers);

    $output .= '<div class="teacher'. theme_mb2nl_bsfcls(2, '', '', 'center') . '">';

    if (isset($options['image'])) {
        $output .= $mainteacher['picture'];
    }

    $output .= $mainteacher['firstname'];
    $output .= ' ' . $mainteacher['lastname'];

    if ($otherteachers) {
        $output .= ' <span class="info ml-1">(';
        $output .= get_string('xmoreteachers', 'theme_mb2nl', ['teachers' => $otherteachers]);
        $output .= ')</span>';
    }

    $output .= '</div>';

    return $output;

}





/**
 *
 * Method to get teacher courses count
 *
 */
function theme_mb2nl_get_instructor_courses_count($userid, $visible = false) {
    global $DB, $PAGE, $USER;

    // Check for cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = $visible ? 'teacher_ccount_' . $userid . '_' . $USER->id : 'teacher_ccount_' . $userid;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $teacherroleid = theme_mb2nl_user_roleid(true);
    $excatwhere = '';
    $andcourses = '';
    $params = [];

    $params[] = CONTEXT_COURSE;
    $params[] = $userid;
    $params[] = $teacherroleid;

    if ($visible) {
        $andcourses = ' AND c.visible=1';

        // Filter hidden categories.
        list($canseecatsql, $canseecatparams) = $DB->get_in_or_equal(array_keys(theme_mb2nl_get_categories()));
        $params = array_merge($params, $canseecatparams);
        $excatwhere .= ' AND c.category ' . $canseecatsql;
    }

    $sqlquery = 'SELECT COUNT(ra.id) FROM {role_assignments} ra JOIN {context} cx ON ra.contextid=cx.id JOIN {course} c';
    $sqlquery .= ' ON cx.instanceid=c.id AND cx.contextlevel=? WHERE ra.userid=? AND ra.roleid=?' . $excatwhere .
    $andcourses;

    $count = $DB->count_records_sql($sqlquery, $params);

    // Set cache.
    $cache->set($cacheid, $count);

    return $count;

}






/**
 *
 * Method to get courses count in category
 *
 */
function theme_mb2nl_get_category_course_count($catid, $visible = false, $opt = []) {
    global $DB, $PAGE, $USER;

    // Check for cache.
    $cache = cache::make('theme_mb2nl', 'categories');
    $cacheid = 'cat_course_count_' . $catid . '_' . serialize($opt);

    // If ther is cache, return it.
    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $andcourses = '';
    $anddate = '';
    $extags = theme_mb2nl_course_extags();
    $params = [];
    $canviewhiddencats = has_capability('moodle/category:viewhiddencategories', context_system::instance());

    $sqlquery = 'SELECT COUNT(c.id) FROM {course} c WHERE 1=1';

    if ($catid) {
        $params[] = $catid;
        $sqlquery .= ' AND c.category=?';
    }

    if ($visible && !$canviewhiddencats) {
        $sqlquery .= ' AND c.visible=1';
    }

    if ($visible && ! theme_mb2nl_theme_setting($PAGE, 'expiredcourses')) {
        $params[] = theme_mb2nl_get_user_date();
        $sqlquery .= ' AND (c.enddate=0 OR c.enddate>?)';
    }

    // Filter hidden categories.
    list($canseecatsql, $canseecatparams) = $DB->get_in_or_equal(array_keys(theme_mb2nl_get_categories()));
    $params = array_merge($params, $canseecatparams);
    $sqlquery .= ' AND c.category ' . $canseecatsql;

    // Exclude categories.
    if (isset($opt['catids']) &&  $opt['catids'] && $opt['excats']) {
        $isnot = '';
        $opt['catids'] = explode(',', $opt['catids']);

        if ($opt['excats'] === 'exclude') {
            $isnot = count($opt['catids']) > 1 ? 'NOT ' : '!';
        }

        list($excatinsql, $excatparams) = $DB->get_in_or_equal($opt['catids']);
        $params = array_merge($params, $excatparams);
        $sqlquery .= ' AND c.category ' . $isnot . $excatinsql;
    }

    // Exlude tags.
    if ($extags[0]) {
        list($extaginsql, $extagparams) = $DB->get_in_or_equal($extags);
        $params = array_merge($params, $extagparams);

        $sqlquery .= ' AND NOT EXISTS(SELECT t.id FROM {tag} t JOIN {tag_instance} ti ON ti.tagid=t.id JOIN {context} cx';
        $sqlquery .= ' ON cx.id=ti.contextid WHERE c.id=cx.instanceid';
        $sqlquery .= ' AND cx.contextlevel = ' . CONTEXT_COURSE;
        $sqlquery .= ' AND t.id ' . $extaginsql;
        $sqlquery .= ')';
    }

    // Filter tags for shortcodes. This is require for the "All" course tab.
    if (isset($opt['tagids']) && $opt['tagids'] && $opt['extags']) {
        $isnot = '';

        $tagsarr = explode(',', $opt['tagids']);

        if ($opt['extags'] === 'exclude') {
            $isnot = 'NOT ';
        }

        list($extaginsql, $extagparams) = $DB->get_in_or_equal($tagsarr);
        $params = array_merge($params, $extagparams);

        $sqlquery .= ' AND ' . $isnot. 'EXISTS(SELECT DISTINCT t.id FROM {tag} t JOIN {tag_instance} ti ON ti.tagid=t.id';
        $sqlquery .= ' JOIN {context} cx ON cx.id=ti.contextid WHERE c.id=cx.instanceid';
        $sqlquery .= ' AND cx.contextlevel = ' . CONTEXT_COURSE;
        $sqlquery .= ' AND t.id ' . $extaginsql;
        $sqlquery .= ')';
    }

    $count = $DB->count_records_sql($sqlquery, $params);

    // Set cache.
    $cache->set($cacheid, $count);

    return $count;

}







/**
 *
 * Method to get teacher students count
 *
 */
function theme_mb2nl_instructor_students_count($userid=null, $active=false) {
    global $USER, $DB;

    $isuserid = $userid ? $userid : $USER->id;

    // Check for cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'teacher_student_count_' . $isuserid;

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $students = 0;
    $teacherroleid = theme_mb2nl_user_roleid(true);

    $sqlquery = 'SELECT id FROM {role_assignments} WHERE userid=? AND roleid=?';

    if (! $DB->record_exists_sql($sqlquery, [$isuserid, $teacherroleid])) {
        return 0;
    }

    $sql = 'SELECT DISTINCT id, contextid FROM {role_assignments} WHERE userid=:userid AND roleid=:roleid';
    $courscontexts = $DB->get_records_sql($sql, ['userid' => $isuserid, 'roleid' => $teacherroleid]);

    foreach ($courscontexts as $courscontext) {
        $students += theme_mb2nl_students_by_course($courscontext->contextid, $active);
    }

    // Set cache.
    $cache->set($cacheid, $students);

    return $students;

}





/**
 *
 * Method to get studends count on a course
 *
 */
function theme_mb2nl_students_by_course($contextid=null, $active=false) {

    global $DB;

    $sqlwhere = ' WHERE 1=1';
    $params = [
        'contextid' => $contextid,
        'roleid' => theme_mb2nl_user_roleid(),
    ];

    $sql = 'SELECT count(DISTINCT u.id) AS num FROM {user} u JOIN {role_assignments} ra ON ra.userid=u.id';
    $sqlwhere .= ' AND ra.contextid=:contextid';
    $sqlwhere .= ' AND ra.roleid=:roleid';

    if ($active) {
        $params['confirmed'] = 1;
        $params['deleted'] = 0;
        $params['suspended'] = 0;
        $params['lastaccess'] = theme_mb2nl_dashboard_user_active_time();

        $sqlwhere .= ' AND u.confirmed=:confirmed';
        $sqlwhere .= ' AND u.deleted=:deleted';
        $sqlwhere .= ' AND u.suspended=:suspended';
        $sqlwhere .= ' AND u.lastaccess>=:lastaccess';
    }

    return $DB->count_records_sql($sql . $sqlwhere, $params);

}





/**
 *
 * Method to update get course description
 *
 */
function theme_mb2nl_get_course_description($courseid = 0, $content = '') {
    global $COURSE, $CFG;
    require_once($CFG->libdir . '/filelib.php');

    $iscourseid = $courseid ? $courseid : $COURSE->id;
    $iscontent = $content ? $content : $COURSE->summary;
    $context = context_course::instance($iscourseid);
    $desc = file_rewrite_pluginfile_urls($iscontent, 'pluginfile.php', $context->id, 'course', 'summary', null);
    $desc = theme_mb2nl_format_txt($desc, FORMAT_HTML);

    return $desc;

}






/**
 *
 * Method to update get course description
 *
 */
function theme_mb2nl_get_section_desc($section, $format = true) {
    global $COURSE, $CFG;
    require_once($CFG->libdir . '/filelib.php');

    $context = context_course::instance($COURSE->id);
    $desc = file_rewrite_pluginfile_urls($section->summary, 'pluginfile.php', $context->id, 'course', 'section', $section->id);
    $desc = $format ? theme_mb2nl_format_txt($desc, FORMAT_HTML) : $desc;

    return $desc;

}





/**
 *
 * Method to update get course description
 *
 */
function theme_mb2nl_get_content_field_textarea($content = '', $courseid = 0, $fieldid = null) {
    global $COURSE, $CFG;
    require_once($CFG->libdir . '/filelib.php');

    $iscourseid = $courseid ? $courseid : $COURSE->id;
    $context = context_course::instance($iscourseid);
    $desc = file_rewrite_pluginfile_urls($content, 'pluginfile.php', $context->id, 'customfield_textarea', 'value', $fieldid);
    $desc = theme_mb2nl_format_txt($desc, FORMAT_HTML);

    return $desc;

}





/**
 *
 * Method to update get course description
 *
 */
function theme_mb2nl_get_mb2course_description($ccourse = null) {
    global $COURSE, $CFG;
    require_once($CFG->libdir . '/filelib.php');

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;

    $context = context_course::instance($cobj->id);
    $iscontent = $cobj->summary;
    $iscomponent = 'course';
    $isarea = 'summary';

    $desc = file_rewrite_pluginfile_urls($iscontent, 'pluginfile.php', $context->id, $iscomponent, $isarea, null);
    $desc = theme_mb2nl_format_txt($desc, FORMAT_HTML);

    return $desc;

}






/**
 *
 * Method to update get course description
 *
 */
function theme_mb2nl_get_user_description($description, $userid) {
    global $CFG;
    require_once($CFG->libdir . '/filelib.php');

    $usercontext = context_user::instance($userid);
    $desc = file_rewrite_pluginfile_urls($description, 'pluginfile.php', $usercontext->id, 'user', 'profile', null);
    $desc = theme_mb2nl_format_txt($desc, FORMAT_HTML);

    return $desc;

}






/**
 *
 * Method to update get course intro video
 *
 */
function theme_mb2nl_course_video() {
    global $COURSE;

    $output = '';
    $formatvideo = theme_mb2nl_get_format_video_url();
    $videofile = $formatvideo ? $formatvideo : theme_mb2nl_mb2fields_filed('mb2video_local');
    $videourl = theme_mb2nl_mb2fields_filed('mb2video');

    if (!$videourl && !$videofile) {
        return;
    }

    if ($videourl) {
        $videourl = theme_mb2nl_get_video_url($videourl);
    }

    $output .= '<div class="course-video" title="' . $COURSE->fullname . '">';

    if ($videofile) {
        $output .= '<video class="lazy" controls="true" title="" width="1900">';
        $output .= '<source data-src="' . $videofile . '">' . $videofile;
        $output .= '</video>';
    } else {
        $output .= '<div class="embed-responsive-wrap">';
        $output .= '<div class="embed-responsive-wrap-inner">';
        $output .= '<div class="embed-responsive embed-responsive-16by9">';
        $output .= '<iframe class="videowebiframe lazy" data-src="' . $videourl . '?showinfo=0&rel=0" allowfullscreen></iframe>';
        $output .= '</div>'; // ...embed-responsive embed-responsive-16by9
        $output .= '</div>'; // ...embed-responsive-wrap-inner
        $output .= '</div>'; // ...embed-responsive-wrap
    }

    $output .= '</div>';

    return $output;

}




/**
 *
 * Method to get course custom fields array
 *
 */
function theme_mb2nl_mb2fields() {

    return [
        'mb2video',
        'mb2skills',
        'mb2requirements',
        'mb2video_local',
        'mb2intro',
        'mb2section',
        'mb2section2',
        'mb2section3',
        'mb2section4',
        'mb2section5',
        'mb2promo',
        'mb2badge',
        'mb2link',
        'mb2button',
        // Required for theme demo page.
        'mb2layout',
        'mb2header',
        'mb2scheme',
        'mb2css',
        'mb2image',
        'mb2courselayout',
        'mb2sidebarpos',
        'mb2tabalt',
        'mb2onepagenav',
        'mb2tgsdb',
        'mb2cinfo',
    ];

}


/**
 *
 * Method to get mb2 filed value
 *
 */
function theme_mb2nl_mb2sectionfiledname($field = 'mb2section', $courseid = 0) {

    $fields = theme_mb2nl_get_course_fields($courseid);

    if (!count($fields)) {
        return null;
    }

    foreach ($fields as $k => $f) {
        if ($f['shortname'] !== $field) {
            continue;
        }

        return theme_mb2nl_format_str($f['name']);
    }

    return null;

}




/**
 *
 * Method to get mb2 filed value
 *
 */
function theme_mb2nl_mb2fields_filed($name, $courseid = 0) {

    $fields = theme_mb2nl_get_course_fields($courseid);

    if (!count($fields)) {
        return null;
    }

    foreach ($fields as $k => $f) {
        if ($f['shortname'] !== $name) {
            continue;
        }

        $val = theme_mb2nl_get_content_field_textarea($f['value'], $courseid, $f['id']);

        // This is require for skills filed with Atto editor.
        if (preg_match('@<p@', $f['value']) && ($name === 'mb2skills' || $name === 'mb2requirements')) {
            return theme_mb2nl_paragraph_content($val);
        } else if ($name === 'mb2video_local' || $name === 'mb2image') {
            return theme_mb2nl_url_from_text($val);
            // Strip_tags to make sure if there is some content.
        } else if ((preg_match('@mb2section@', $name) || $name === 'mb2promo') && strip_tags($val)) {
            return $val;
        } else {
            return strip_tags($val);
        }
    }

    return null;

}








/**
 *
 * Method to get course video
 *
 */
function theme_mb2nl_get_format_video_url($raw = false) {
    global $CFG, $COURSE;

    if ($COURSE->format !== 'mb2sections') {
        return;
    }

    require_once($CFG->libdir . '/filelib.php');
    $coursecontext = context_course::instance($COURSE->id);
    $url = '';
    $fs = get_file_storage();
    $files = $fs->get_area_files($coursecontext->id, 'format_mb2sections', 'mb2sectionsvideo', 0);

    foreach ($files as $f) {
        if (! str_replace('.', '', $f->get_filename())) {
            continue;
        }

        $url = moodle_url::make_pluginfile_url(
            $f->get_contextid(), $f->get_component(), $f->get_filearea(), $f->get_itemid(), $f->get_filepath(),
            $f->get_filename(), false);

        // Required for aria-lable attriibute in course lightbox video.
        if ($raw) {
            $url = $CFG->wwwroot . '/pluginfile.php/' . $f->get_contextid() . '/' .
            $f->get_component() . '/' . $f->get_filearea()  . '/' . $f->get_itemid() . '/' . rawurlencode($f->get_filename());
        }
    }

    return $url;

}




/**
 *
 * Method to update get course updated date
 *
 */
function theme_mb2nl_course_updatedate($ccourse = false) {
    global $PAGE, $COURSE;

    $iscourse = $ccourse ? $ccourse : $COURSE;

    $isdate = theme_mb2nl_course_date($iscourse);

    if (!$isdate) {
        return;
    }

    $userdate = userdate($isdate, get_string('strdatecourse', 'theme_mb2nl'));
    return get_string('coursesupdated', 'theme_mb2nl', ['updatedate' => $userdate]);
}





/**
 *
 * Method to get featured reviews
 *
 */
function theme_mb2nl_get_featured_reviews($opts=[]) {
    global $DB;

    if (!theme_mb2nl_is_review_plugin()) {
        return [];
    }

    // Get cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'featured_reviews_' . $opts['limit'];

    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $params = [];
    $sqlwhere = ' WHERE 1=1';
    $sqlquery = 'SELECT r.* FROM {local_mb2reviews_items} r';

    $sqlwhere .= ' AND r.enable=1';
    $sqlwhere .= ' AND r.featured=1';

    // Check if reviewd course exists.
    $sqlwhere .= ' AND EXISTS(SELECT c.id FROM {course} c WHERE c.id=r.course)';

    $sqlorder = ' ORDER BY id DESC';

    $featuredreviews = $DB->get_records_sql($sqlquery . $sqlwhere . $sqlorder, $params, 0, $opts['limit']);

    // Set cache.
    $cache->set($cacheid, $featuredreviews);

    return $featuredreviews;

}






/**
 *
 * Method to get course quickview
 *
 */
function theme_mb2nl_course_quickview($courseid) {
    global $USER;
    $output = '';
    $skills = '';
    $course = get_course($courseid);
    $coursecontext = context_course::instance($courseid);
    $courselink = new moodle_url('/course/view.php', ['id' => $course->id]);
    $linkstr = is_enrolled($coursecontext, $USER->id) ? get_string('entercourse', 'theme_mb2nl') :
    get_string('viewcourse', 'theme_mb2nl');
    $update = theme_mb2nl_course_updatedate($course);
    $hiddenicon = ! $course->visible ? ' <span class="hidden-icon"><span class="sr-only">' .
    get_string('hidden', 'theme_mb2nl') . '</span><i class="ri-eye-off-line"></i></span>' : '';

    // Get course intro.
    $intro = theme_mb2nl_course_intro($course);

    // Get course skills.
    $fieldskills = theme_mb2nl_mb2fields_filed('mb2skills', $courseid);

    // Get slikks list.
    if ($fieldskills) {
        $skills = theme_mb2nl_sr_list($fieldskills, false, 3);
    }

    $output .= '<div class="course-quick">';
    $output .= '<div class="course-quick-header">';
    $output .= '<h4 class="course-quick-title h5">' . theme_mb2nl_format_str($course->fullname) . $hiddenicon . '</h4>';
    $output .= '<div class="course-quick-meta">';
    $output .= theme_mb2nl_course_badges($course);
    $output .= $update ? '<span class="course-date">' . $update . '</span>' : '';
    $output .= '</div>'; // ...course-quick-meta
    $output .= '</div>'; // ...course-quick-header
    $output .= '<div class="course-quick-content">';
    $output .= $intro;
    $output .= '</div>'; // ...course-content

    if ($skills) {
        $output .= '<div class="course-quick-skills">';
        $output .= $skills;
        $output .= '</div>'; // ...course-quick-skills
    }

    $output .= '<div class="course-quick-footer">';
    $output .= '<a href="' . $courselink . '" class="btn btn-primary btn-lg w-100">' . $linkstr . '</a>';
    $output .= '</div>'; // ...course-quick-footer
    $output .= '</div>'; // ...course-quick

    return $output;

}




/**
 *
 * Method to get course badges
 *
 */
function theme_mb2nl_course_badges($course) {

    $output = '';
    $coursecontext = context_course::instance($course->id);
    $bestseller = theme_mb2nl_is_bestseller($coursecontext->id, $course->category);
    $newcourse = theme_mb2nl_is_new_course($course);
    $fieldbadge = theme_mb2nl_mb2fields_filed('mb2badge', $course->id);
    $flexcls = theme_mb2nl_bsfcls(2, '', '', 'center');
    $mycourses = theme_mb2nl_get_mycourses();
    $ismycourse = in_array($course->id, array_keys($mycourses));

    if (!$bestseller && !$fieldbadge && !$ismycourse && !$newcourse) {
        return;
    }

    $output .= '<div class="course-cbadges' . $flexcls . '">';
    $output .= $ismycourse ? '<span class="course-badge badge-mycourse' . $flexcls . '">' .
    get_string('mycourse', 'theme_mb2nl') . '</span>' : '';
    $output .= $newcourse ? '<span class="course-badge badge-new' . $flexcls . '">' .
    get_string('new') . '</span>' : '';
    $output .= $bestseller ? '<span class="course-badge badge-bestseller' . $flexcls . '">' .
    get_string('bestseller', 'theme_mb2nl') . '</span>' : '';
    $output .= $fieldbadge ? '<span class="course-badge badge-mb2badge' . $flexcls . '">' .
    theme_mb2nl_format_str($fieldbadge) . '</span>' : '';
    $output .= '</div>';

    return $output;

}




/**
 *
 * Method to check if current course is new
 *
 */
function theme_mb2nl_is_new_course($course) {

    global $PAGE;

    $cnewbadge = theme_mb2nl_theme_setting($PAGE, 'cnewbadge');

    if (!$cnewbadge || !$course->startdate) {
        return 0;
    }

    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'isnewc_' . $course->startdate;

    if (is_int($cache->get($cacheid))) {
        return $cache->get($cacheid);
    }

    $weeks = (60 * 60 * 24 * 7 * $cnewbadge);
    $userdate = theme_mb2nl_get_user_date();

    if (($course->startdate + $weeks) >= $userdate) {
        $cache->set($cacheid, 1);
        return 1;
    }

    $cache->set($cacheid, 0);
    return 0;

}








/**
 *
 * Method to check full screen mode
 *
 */
function theme_mb2nl_full_screen_module() {
    global $COURSE, $PAGE, $SITE;

    if ($PAGE->user_is_editing() || theme_mb2nl_builder_has_page() || $COURSE->id == $SITE->id ||
    ($COURSE->format === 'singleactivity' && !theme_mb2nl_theme_setting($PAGE, 'fsmodsingle'))) {
        return false;
    }

    if (theme_mb2nl_theme_setting($PAGE, 'fullscreenmod') && theme_mb2nl_is_module_context() && $PAGE->pagelayout === 'incourse') {
        return true;
    }

    return false;
}






/**
 *
 * Method to set toc search
 *
 */
function theme_mb2nl_toc_search() {
    global $PAGE;

    $output = '';
    $uniqid = uniqid('coursetoc_s_');
    $PAGE->requires->js_call_amd('theme_mb2nl/toc', 'searchToc');

    $output .= '<div class="coursetoc-search">';
    $output .= '<form id="' . uniqid('coursetoc_sform_') . '" class="position-relative" method="post">';
    $output .= '<label class="position-absolute' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '" for="' .
    $uniqid. '"><span class="sr-only">' . get_string('search') . '</span><i class="ri-search-line"></i></label>';
    $output .= '<input class="w-100" id="' . $uniqid . '" name="coursetoc-sinput" type="text" placeholder="' .
    get_string('search') . '">';
    $output .= '<input class="sr-only" type="button" value="' . get_string('search') . '" tabindex="-1">';
    $output .= '</form>';
    $output .= '</div>';

    return $output;

}



/**
 *
 * Method to set toc toggle all
 *
 */
function theme_mb2nl_toc_toggleall() {
    global $PAGE;

    $output = '';

    $PAGE->requires->js_call_amd('theme_mb2nl/toc', 'toggleAll');

    $output .= '<div class="coursetoc-tglall">';
    $output .= '<button class="themereset coursetoc-toggleall toctool-toggleall collapsed" type="button" aria-label="' .
    get_string('expandall') . '" aria-expanded="false"><i class="bi bi-chevron-expand"></i></button>';
    $output .= '</div>';

    return $output;

}




/**
 *
 * Method to set toc tools
 *
 */
function theme_mb2nl_toc_tools($toggle = true, $enrolpage = false) {

    if (!theme_mb2nl_is_toc() && !$enrolpage) {
        return;
    }

    $output = '';

    $output .= '<div class="coursetoc-tools position-relative px-2' . theme_mb2nl_bsfcls(1, '', 'between', 'center')  . '">';
    $output .= theme_mb2nl_toc_search();
    $output .= $toggle ? theme_mb2nl_toc_toggleall() : '';
    $output .= '</div>';

    return $output;

}





/**
 *
 * Method to get course sections
 *
 */
function theme_mb2nl_module_section_items($courseobj = null, $modid = 0, $sid = 0, $ptype = null, $playout = null,
$viewhidden = 0) {
    global $CFG;
    $output = '';
    $i = 0;
    $sections = theme_mb2nl_get_course_sections($courseobj);
    $progresstr = $CFG->version >= 2023100900 ? get_string('progress') : get_string('progress', 'theme_mb2nl');

    foreach ($sections as $section) {
        $i++;

        $completepctg = theme_mb2nl_section_complete($section, false, $courseobj);
        $completetotal = theme_mb2nl_section_complete($section, false, $courseobj, true);
        $iscomplete = theme_mb2nl_section_complete($section, true, $courseobj);
        $completecls = $iscomplete ? ' complete' : '';
        $hiddencls = '';
        $highlightedcls = $section['highlighted'] ? ' highlighted' : '';
        $datacompletepctg = $completepctg !== false ? $completepctg : 'false';

        // Use strip tags to prevent auto-link filtering.
        $sname = empty($section['name']) ? get_string('section') : theme_mb2nl_format_str($section['name']);
        $sectionurl = theme_mb2nl_sectionurl($section, $courseobj);
        $sectionlink = $sectionurl ? '<a href="' . $sectionurl . '">' . $sname . '</a>' : $sname;

        $output .= '<div class="coursetoc-section coursetoc-section-' . $section['num'] . $completecls . $hiddencls .
        $highlightedcls . '" data-sid="' . $section['id'] . '" data-snum="' . $section['num'] . '" data-complete="' .
        $datacompletepctg . '">';
        $output .= '<div class="coursetoc-section-tite' . theme_mb2nl_bsfcls(1, 'row', 'between', 'center') . '">';

        $output .= '<div class="' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">';
        $output .= theme_mb2nl_section_mark($completepctg, $i, true);
        $output .= '<span class="title-text' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">';
        $output .= '<span class="text">' . $sectionlink . '</span>';
        $output .= theme_mb2nl_section_badges($section, true);
        $output .= $completepctg !== false ? '<span class="title-complete text-nowrap ml-1' . theme_mb2nl_tcls('small', 'normal') .
        '">(<span class="sr-only">' . $progresstr . '</span> ' . $completetotal['complete'] . ' / ' .
        $completetotal['total'] . ')</span>' : '';
        $output .= '</span>'; // ...title-text
        $output .= '</div>';
        $output .= '<button type="button" class="coursetoc-section-toggle themereset' .
        theme_mb2nl_bsfcls(2, '', 'center', 'center') . '" aria-controls="coursetoc-section-modules-' . $section['id'] . '"
        aria-label="'.get_string('togglesection', 'theme_mb2nl', $sname) . '" aria-expanded="false">';
        $output .= '<span class="toggle-icon"></span>';
        $output .= '</button>';
        $output .= '</div>'; // ...coursetoc-section-tite
        $output .= '<div id="coursetoc-section-modules-' . $section['id'] . '" class="coursetoc-section-modules">';
        $output .= theme_mb2nl_section_module_list($section['id'], true, true, true, $section['num'], $courseobj, $modid, $ptype,
        $playout, $viewhidden);
        $output .= '</div>'; // ...coursetoc-section-modules
        $output .= '</div>'; // ...coursetoc-section
    }

    return $output;

}




/**
 *
 * Method to get course sections
 *
 */
function theme_mb2nl_module_sections($block = false, $progress = true) {
    global $PAGE, $COURSE, $USER;
    $output = '';

    $psection = optional_param('section', 0, PARAM_INT);
    $pid = optional_param('id', 0, PARAM_INT);

    $blockstyle = theme_mb2nl_theme_setting($PAGE, 'blockstyle2');
    $blockstyle = $blockstyle === 'classic' ? 'default' : $blockstyle;
    $progressbar = $progress ? theme_mb2nl_course_progressbar() : false;

    if ($block) {
        $output .= $progressbar;
        $output .= '<div class="block block_coursetoc">';
        $output .= '<h4>' . get_string('coursetoc', 'theme_mb2nl') . '</h4>';

        $output .= '<div class="coursetoc-tool">';
        $output .= '<button type="button" class="themereset coursetoc-toggleall collapsed" aria-expanded="false" aria-label="' .
        get_string('expandall') . '">' . get_string('expandall') . '</button>';
        $output .= '</div>';
        $PAGE->requires->js_call_amd('theme_mb2nl/toc', 'toggleAll');
    }

    $output .= '<div class="coursetoc-sectionlist">';
    $output .= $block ? theme_mb2nl_toc_tools(false) : '';
    $output .= '<div class="coursetoc-sectionlist-content" data-courseid="' . $COURSE->id . '" data-start="0" data-limit="0"
    data-container=".coursetoc-sectionlist"></div>'; // Content loaded via js.
    $output .= '</div>'; // ...coursetoc-sectionlist

    if ($block) {
        $output .= '</div>'; // ...block block_coursetoc
    }

    $PAGE->requires->js_call_amd('theme_mb2nl/toc', 'courseTocLoad', [$COURSE->id]);

    return $output;

}



/**
 *
 * Method to set data attributes for ajax requests
 *
 */
function theme_mb2nl_ajax_data_atts() {
    global $CFG, $PAGE, $COURSE;

    $data = '';

    $coursecontext = context_course::instance($COURSE->id);
    $viewhidden = has_capability('moodle/course:viewhiddensections', $coursecontext);
    $viewhidden = $viewhidden ? 1 : 0;
    $sectionid = preg_match('@section-@', $PAGE->pagetype) ? optional_param('id', 0, PARAM_INT) : 0;

    if ($CFG->version < 2024042200) {
        $sectionid = optional_param('section', 0, PARAM_INT);
    }

    $modid = is_object($PAGE->cm) && $PAGE->cm->id ? $PAGE->cm->id : 0;
    $sid = is_object($PAGE->cm) ? $PAGE->cm->section : 0;

    $data .= ' data-mod_id="' . $modid . '"';
    $data .= ' data-mod_sid="' . $sid . '"';

    $data .= ' data-viewhidden="' . $viewhidden . '"';

    $data .= ' data-playout="' . $PAGE->pagelayout . '"';
    $data .= ' data-ptype="' . $PAGE->pagetype . '"';

    $data .= ' data-wwwroot="' . $CFG->wwwroot . '"';
    $data .= ' data-themedir="' . theme_mb2nl_themedir() . '"';

    $data .= ' data-uparam_sid="'. $sectionid .'"';
    $data .= ' data-uparam_categoryid="'. optional_param('categoryid', 0, PARAM_INT) .'"';
    $data .= ' data-uparam_tagid="'. optional_param('tagid', '', PARAM_RAW) .'"';
    $data .= ' data-uparam_teacherid="'. optional_param('teacherid', 0, PARAM_INT) .'"';

    $data .= ' data-cfields="'. theme_mb2nl_cfileds_for_data_att() .'"';

    return $data;
}








/**
 *
 * Method to get course sections
 *
 */
function theme_mb2nl_section_mark($progress, $num = false, $tgsdb = false, $opts = []) {
    global $CFG, $COURSE, $PAGE;

    $output = '';
    $isprogress = $progress === false ? 0 : $progress;
    $bcolor = $progress === false ? $tgsdb && theme_mb2nl_theme_setting($PAGE, 'tgsdbdark') ? 'rgba(255,255,255,.15)' :
    'rgba(0,0,0,.085)' : 'rgba(37,161,142,.16)';
    $ttext = get_string('section') . ' ' . $num;
    $fcolor = $tgsdb && theme_mb2nl_theme_setting($PAGE, 'tgsdbdark') ? 'transparent' : '#ffffff';
    $fcolor = isset($opts['fcolor']) ? $opts['fcolor'] : $fcolor;
    $bcolor = isset($opts['bcolor']) ? $opts['bcolor'] : $bcolor;

    if (is_numeric($progress)) {
        $ttext = get_string('completepercent', 'block_myoverview', $progress);
    }

    $title = ' title="' . $ttext . '"';
    $s = isset($opts['s']) ? $opts['s'] : 30;
    $bs = isset($opts['bs']) ? $opts['bs'] : 2;

    $output .= '<span class="section-mark position-relative' . theme_mb2nl_bsfcls(2, '', '', 'center') . '"' . $title . '>';
    $output .= theme_mb2nl_chart_circle($isprogress, ['s' => $s, 'bs' => $bs, 'bcolor' => $bcolor, 'fcolor' =>
    $fcolor]);
    $output .= is_numeric($num) ? '<span class="sectionnum tsizesmall position-absolute' .
    theme_mb2nl_bsfcls(2, '', 'center', 'center') . '" aria-hidden="true">' . $num . '</span>' : '';
    $output .= '</span>';

    return $output;

}



/**
 *
 * Method to get course sections
 *
 */
function theme_mb2nl_module_mark($complete='', $ccourse=null) {
    global $COURSE;

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;
    $completion = new completion_info($cobj);

    if (! $completion->is_enabled() || ! method_exists('\core_completion\progress', 'get_course_progress_percentage')) {
        return;
    }

    $output = '';

    if ($complete == 1) {
        $label = get_string('completed', 'completion');
    } else if ($complete == -1) {
        $label = get_string('completion-n', 'completion');
    } else {
        $label = get_string('completion-no', 'theme_mb2nl');
    }

    $output .= '<span class="module-mark' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '" title="' . $label . '"></span>';

    return $output;

}





/**
 *
 * Method to get course sections
 *
 */
function theme_mb2nl_full_screen_module_backlink($ccourse = null) {
    global $COURSE, $PAGE;

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;

    if (theme_mb2nl_is_cmainpage($ccourse)) {
        return;
    }

    $output = '';
    $snum = 0;
    $params = ['id' => $cobj->id];
    $linkstr = get_string('course');
    $backlink = '/course/view.php';

    if ($cobj->format === 'singleactivity') {
        $homepage = get_home_page();
        $linkstr = get_string('myhome');

        if ($homepage == HOMEPAGE_SITE) {
            $linkstr = get_string('home');
        } else if ($homepage == HOMEPAGE_MYCOURSES) {
            $linkstr = get_string('mycourses');
        }

        $backlink = '/';
        $params = [];
    }

    if (is_object($PAGE->cm) && $PAGE->cm->sectionnum && in_array(theme_mb2nl_course_layout(true, $ccourse),
    theme_mb2nl_notoc())) {
        // This is required for full screen mode close button to back to the current course section.
        // For the link in the toggle sidebar we don't need to get section number.
        $params['mb2sct'] = $PAGE->cm->sectionnum;
    }

    $cls = 'fsmod-backlink';

    $output .= '<a href="' . new moodle_url($backlink, $params) . '" class="' . $cls . '" aria-label="' . $linkstr . '">';
    $output .= '<i class="bi bi-x-lg"></i>';
    $output .= '</a>';

    return $output;

}




/**
 *
 * Method to get course completion
 *
 */
function theme_mb2nl_course_completion_percentage($cc=0) {

    global $CFG, $COURSE, $USER, $SITE;

    require_once($CFG->libdir . '/completionlib.php');

    $iscourse = $cc ? get_course($cc) : $COURSE;

    $completion = new completion_info($iscourse);
    $context = context_course::instance($iscourse->id);
    $cancomplete = isloggedin() && ! isguestuser();
    $enrolled = ($iscourse->id != $SITE->id && is_enrolled($context, $USER->id, '', true));

    if (!$completion->is_enabled() || !method_exists('\core_completion\progress', 'get_course_progress_percentage') || !$enrolled) {
        return '';
    }

    $progress = \core_completion\progress::get_course_progress_percentage($iscourse);

    if (is_null($progress)) {
        return 0;
    }

    return floor($progress);

}









/**
 *
 * Method to get body class for toc and navigation
 *
 */
function theme_mb2nl_toc_class() {
    return theme_mb2nl_is_toc();
}




/**
 *
 * Method to check if toc appears
 *
 */
function theme_mb2nl_is_toc() {
    global $PAGE, $COURSE;

    // Do not display toc.
    if (!theme_mb2nl_is_course() || $PAGE->user_is_editing() || !count(theme_mb2nl_get_course_sections())) {
        return false;
    }

    // On module context page always show toc if is not single activity format.
    if (theme_mb2nl_is_module_context() && !theme_mb2nl_theme_setting($PAGE, 'coursetocnomod') &&
    $COURSE->format !== 'singleactivity') {
        return true;
    }

    // On course section.
    if (theme_mb2nl_is_section() && theme_mb2nl_theme_setting($PAGE, 'coursetoc')) {
        return true;
    }

    // On course homepage.
    if (theme_mb2nl_is_cmainpage() && theme_mb2nl_theme_setting($PAGE, 'coursetoc') &&
    !in_array(theme_mb2nl_course_layout(), theme_mb2nl_notoc())) {
        return true;
    }

    return false;

}




/**
 *
 * Method to check if there is navigation block
 *
 */
function theme_mb2nl_nonav() {

    global $PAGE, $COURSE;

    if ((theme_mb2nl_tgsdb_setting() || theme_mb2nl_theme_setting($PAGE, 'coursetoc')) &&
    (theme_mb2nl_is_cmainpage() || theme_mb2nl_is_module_context() || theme_mb2nl_is_cenrol_page() > 0)) {
        return true;
    }

    return false;

}



/**
 *
 * Method to get custom course navigation
 *
 */
function theme_mb2nl_custom_sectionnav() {
    global $PAGE, $COURSE;

    $output = '';

    if (!theme_mb2nl_theme_setting($PAGE, 'coursenav') || (!theme_mb2nl_is_section() && !theme_mb2nl_is_sectionnav())) {
        return;
    }

    $cls = '';
    $subsections = theme_mb2nl_subsections_ids();
    $sectionprev = theme_mb2nl_near_section();
    $sectionnext = theme_mb2nl_near_section(false);

    $cls .= ($sectionprev && !$sectionnext) ? ' onlyprev' : '';
    $cls .= (!$sectionprev && $sectionnext) ? ' onlynext' : '';

    $output .= '<div class="theme-coursenav flexcols' . $cls . '">';

    if ($sectionprev) {
        $subsection = in_array($sectionprev['id'], $subsections);
        $prevlink = theme_mb2nl_sectionurl($sectionprev, $COURSE, $subsection);

        $output .= '<div class="coursenav-prev">';
        $output .= '<a href="' . $prevlink . '" class="coursenav-link w-100' .
        theme_mb2nl_bsfcls(1, 'column', 'center', 'end') . '">';
        $output .= '<span class="coursenav-item coursenav-text' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">' .
        get_string('previoussection') . '</span>';

        $previtemname = theme_mb2nl_format_str($sectionprev['name']);
        $output .= '<span class="coursenav-modname d-inline-block">' . $previtemname . '</span>';
        $output .= '</a>'; // ...nav-link
        $output .= '</div>'; // ...nav-prev
    }

    if ($sectionnext) {
        $subsection = in_array($sectionnext['id'], $subsections);
        $nextlink = theme_mb2nl_sectionurl($sectionnext, $COURSE, $subsection);

        $output .= '<div class="coursenav-next">';
        $output .= '<a href="' . $nextlink . '" class="coursenav-link w-100' .
        theme_mb2nl_bsfcls(1, 'column', 'center', 'start') . '">';
        $output .= '<span class="coursenav-item coursenav-text' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">' .
        get_string('nextsection') . '</span>';

        $nextitemname = theme_mb2nl_format_str($sectionnext['name']);
        $output .= '<span class="coursenav-modname d-inline-block">' . $nextitemname . '</span>';
        $output .= '</a>'; // ...coursenav-link
        $output .= '</div>'; // ...coursenav-next
    }

    $output .= '</div>'; // ...theme-coursenav

    return $output;

}





/**
 *
 * Method to get custom course navigation
 *
 */
function theme_mb2nl_customnav($cobj = null) {
    global $PAGE;
    $output = '';
    $cls = '';
    $prevmod = theme_mb2nl_near_module(true, $cobj);
    $nextmod = theme_mb2nl_near_module(false, $cobj);

    $cls .= ($prevmod && !$nextmod) ? ' onlyprev' : '';
    $cls .= (!$prevmod && $nextmod) ? ' onlynext' : '';

    $output .= '<div class="theme-coursenav flexcols' . $cls . '">';

    if ($prevmod) {
        $prevlink = theme_mb2nl_activityurl($prevmod['url'], $prevmod['id']);

        $output .= '<div class="coursenav-prev">';
        $output .= '<a href="' . $prevlink . '" class="coursenav-link w-100 bg-white' .
        theme_mb2nl_bsfcls(1, 'column', 'center', 'end') . '">';
        $output .= '<span class="coursenav-item coursenav-text' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">' .
        get_string('previous') . '</span>';
        $previtemname = theme_mb2nl_format_str($prevmod['name']);
        $output .= '<span class="coursenav-modname d-inline-block">' . $previtemname . '</span>';
        $output .= '</a>'; // ...nav-link
        $output .= '</div>'; // ...nav-prev
    }

    if ($nextmod) {
        $nextlink = theme_mb2nl_activityurl($nextmod['url'], $nextmod['id']);

        $output .= '<div class="coursenav-next">';
        $output .= '<a href="' . $nextlink . '" class="coursenav-link w-100 bg-white' .
        theme_mb2nl_bsfcls(1, 'column', 'center', 'start') . '">';
        $output .= '<span class="coursenav-item coursenav-text' . theme_mb2nl_bsfcls(1, '', '', 'center') . '">' .
        get_string('next') . '</span>';
        $nextitemname = theme_mb2nl_format_str($nextmod['name']);
        $output .= '<span class="coursenav-modname d-inline-block">' . $nextitemname . '</span>';
        $output .= '</a>'; // ...coursenav-link
        $output .= '</div>'; // ...coursenav-next
    }

    $output .= '</div>'; // ...theme-coursenav

    return $output;

}



/**
 *
 * Method to get section URL
 *
 */
function theme_mb2nl_sectionurl($section, $ccourse = null, $subsection = false) {
    global $CFG, $COURSE;

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;
    $clayout = theme_mb2nl_course_layout(true, $ccourse);
    $coursecontext = context_course::instance($cobj->id);
    $viewhidden = has_capability('moodle/course:viewhiddenactivities', $coursecontext);

    // Disable link to not available sections.
    if (!$viewhidden && ($section['notavailable'] != 0 || $section['availableinfo'])) {
        return;
    }

    // For custom course layout set different url.
    // For subsections always open the core Moodle's section view (!$subsection).
    if (!$subsection && in_array($clayout, theme_mb2nl_notoc()) && !in_array($clayout, theme_mb2nl_nosectionnav())) {
        return new moodle_url('/course/view.php', ['id' => $cobj->id, 'mb2sct' => $section['num']]);
    }

    // For Moodle versions < 4.4.
    if ($CFG->version < 2024042200) {
        return new moodle_url('/course/view.php', ['id' => $cobj->id, 'section' => $section['num']]);
    }

    return new moodle_url('/course/section.php', ['id' => $section['id']]);

}







/**
 *
 * Method to get activity header in Moodle 4
 *
 */
function theme_mb2nl_activityurl($url='', $id=0, $snum=-1, $sid=0, $ccourse=null, $ptype=null, $playout=null) {
    global $CFG, $COURSE, $PAGE;

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;
    $opts = theme_mb2nl_format_opts($ccourse);
    $onespp = isset($opts['coursedisplay']) && $opts['coursedisplay'] == 1;
    $m44 = $CFG->version >= 2024042200;
    $courselayout = theme_mb2nl_course_layout(true, $ccourse);

    // Check for activities or resources without URL.
    if (!$url) {
        // Get section ID and NUMBER.
        $modinfo = get_fast_modinfo($cobj->id);
        $snum = $snum > 1 ? $snum : $modinfo->get_cms()[$id]->sectionnum;
        $sid = $sid ? $sid : $modinfo->get_cms()[$id]->section;

        if (theme_mb2nl_is_cmainpage($ccourse, $playout, $ptype) && !$onespp) {
            return '#module-' . $id;
        } else if (in_array($courselayout, theme_mb2nl_notoc())) {
            return new moodle_url('/course/view.php', ['id' => $cobj->id, 'mb2sct' => $snum]) . '#module-' . $id;
        } else if ($onespp) {
            if (!$m44) {
                return new moodle_url('/course/view.php', ['id' => $cobj->id, 'section' => $snum]) . '#module-' . $id;
            }
            return new moodle_url('/course/section.php', ['id' => $sid]) . '#module-' . $id;
        } else {
            return new moodle_url('/course/view.php', ['id' => $cobj->id]) . '#module-' . $id;
        }
    }

    return new moodle_url($url, ['forceview' => 1]);

}





/**
 *
 * Method to get activity header in Moodle 4
 *
 */
function theme_mb2nl_activityheader($fsmode = false) {
    global $CFG, $PAGE;

    $output = '';

    $header = $PAGE->activityheader;
    $headercontent = $header->export_for_template($PAGE->get_renderer('core'));

    // This is require because this function is included in the 'columns2' layout
    // and We have to prevent to duplicate the '<span id="maincontent"></span>' div.
    // This condition was addedd because to set activity header on the 'columns2' layout for the 'Poodll MiniLessons' module.
    if (empty($headercontent)) {
        return;
    }

    $notesbtn = !$fsmode ? theme_mb2nl_note_link2form(true) : '';
    $cls = $notesbtn ? ' notesbtn' : '';

    $output .= '<span id="maincontent"></span>';

    if (isset($headercontent['title']) && $headercontent['title']) {
        $output .= '<div class="page-context-header m-0 p-0">';
        $output .= '<h2 class="activity-name">' . theme_mb2nl_format_str($headercontent['title']) . '</h2>';
        $output .= '</div>';
    }

    $output .= '<div class="activity-header' . theme_mb2nl_bsfcls(1, '', 'between', 'center') .
    $cls . '" data-for="page-activity-header">';

    $output .= '<div class="activity-header-moo">';

    if (isset($headercontent['completion']) && $headercontent['completion']) {
        $output .= '<span class="sr-only">' . get_string('overallaggregation', 'completion') . '</span>';
        $output .= $headercontent['completion'];
    }

    if (isset($headercontent['description']) && $headercontent['description']) {
        // Moodle doesn't allow to display iframe in this place with theme_mb2nl_format_txt.
        // This is require to convert youtube video to HTML video and [stash shortcodes.
        $nofilter = preg_match('@<iframe@', $headercontent['description']) ||
        preg_match('@<video@', $headercontent['description']) ||
        preg_match('@stash@', $headercontent['description']);
        $desctext = $nofilter ? $headercontent['description'] : theme_mb2nl_format_txt($headercontent['description']);

        $output .= '<div class="activity-description" id="intro">' . $desctext . '</div>';
    }

    if (isset($headercontent['additional_items']) && $headercontent['additional_items']) {
        $output .= $headercontent['additional_items'];
    }

    $output .= '</div>'; // ...activity-header-moo

    $output .= $notesbtn;

    $output .= '</div>'; // ...activity-header

    return $output;

}



/**
 *
 * Method to get video lightbox link
 *
 */
function theme_mb2nl_course_video_lightbox($shorttext=false, $cls='') {
    global $PAGE, $COURSE, $CFG;
    require_once($CFG->libdir . '/filelib.php');

    $formatvideo = theme_mb2nl_get_format_video_url(true);

    $fieldvideo = theme_mb2nl_mb2fields_filed('mb2video');
    $videofile = $formatvideo ? $formatvideo : theme_mb2nl_mb2fields_filed('mb2video_local'); // Video file.
    $videourl = $fieldvideo; // Video url.

    $videotext = $shorttext ? get_string('preview') : get_string('courseintrovideo', 'theme_mb2nl');

    if (!$videofile && !$videourl) {
        return;
    }

    if ($videourl) {
        // Generate correct video URL.
        $videourl = theme_mb2nl_get_video_url($videourl, true);
    }

    if ($videofile) {
        return '<a class="theme-popup-link popup-html_video' . $cls . '" href="'. $videofile . '" aria-label="' .
        get_string('lightboxvideo', 'theme_mb2nl', ['videourl' => $videofile]) . '"><span>' . $videotext . '</span></a>';
    } else {
        return '<a class="theme-popup-link popup-iframe' . $cls . '" href="' . $videourl . '" aria-label="' .
        get_string('lightboxvideo', 'theme_mb2nl', ['videourl' => $videourl]) . '"><span>' . $videotext . '</span></a>';
    }

}





/**
 *
 * Method to get video lightbox link
 *
 */
function theme_mb2nl_block_enrol($video = false) {
    global $PAGE, $COURSE;

    $output = '';
    $lvideo = theme_mb2nl_course_video_lightbox(true, ' mb2-pb-btn sizelg typedefault btnborder1');
    $videocls = ($video && $lvideo) ? ' isvideo' : '';

    $output .= '<div class="enrol-info' . $videocls . theme_mb2nl_bsfcls(1, 'row', '', 'center') . '">';
    $output .= theme_mb2nl_course_price_html();
    $output .= '</div>'; // ...enrol-info

    // Define button link.
    $mb2link = theme_mb2nl_mb2fields_filed('mb2link');
    $btnhref = $mb2link ? $mb2link : '#page-content';

    $output .= $video && $lvideo ? '<div class="enrol-info-video mb-3">' . $lvideo . '</div>' : '';

    $output .= '<div class="enrol-buttons' . theme_mb2nl_bsfcls(1, 'column', '', 'center') . '">';

    if (theme_mb2nl_is_enrolbtn()) {
        $output .= '<a href="' . $btnhref . '" class="mb2-pb-btn typeprimary sizelg course-enrolbtn sidebar-btn">';
        $output .= get_string('enroltextfree', 'theme_mb2nl');
        $output .= '</a>';
    }

    // Additional button.
    $mb2button = theme_mb2nl_mb2fields_filed('mb2button');

    if ($mb2button) {
        $btncls = theme_mb2nl_is_enrolbtn() ? ' typeinverse btnborder1 mt-3' : ' typeprimary';

        $output .= '<a href="' . $mb2button . '" class="mb2-pb-btn sizelg course-enrolbtn sidebar-btn' . $btncls . '">';
        $output .= theme_mb2nl_mb2sectionfiledname('mb2button');
        $output .= '</a>';
    }

    $output .= '</div>';

    return $output;

}




/**
 *
 * Method to get skills layout
 *
 */
function theme_mb2nl_is_enrolbtn() {
    global $PAGE, $COURSE;

    if (theme_mb2nl_theme_setting($PAGE, 'enrolbtn') || theme_mb2nl_mb2fields_filed('mb2link')) {
        return true;
    }

    $enrols = enrol_get_plugins(true);
    $enrolinstances = enrol_get_instances($COURSE->id, true);
    $forms = [];

    foreach ($enrolinstances as $instance) {
        if (!isset($enrols[$instance->enrol])) {
            continue;
        }

        $form = $enrols[$instance->enrol]->enrol_page_hook($instance);
        if ($form) {
            $forms[$instance->id] = $form;
        }
    }

    if (!empty($forms)) {
        return true;
    }

    return false;

}




/**
 *
 * Method to get skills layout
 *
 */
function theme_mb2nl_sr_list($text, $columns = true, $limit = 999, $sr = 1) {

    $output = '';
    $content = theme_mb2nl_line_content($text);
    $cls = '';
    $i = 0;

    $cls .= $columns ? ' horizontal2' : ' horizontal0';
    $iconcls = $sr == 2 ? 'bi bi-exclamation-lg' : 'bi bi-check-lg';

    $output .= '<ul class="theme-listicon' . $cls . '">';

    foreach ($content as $item) {
        $i++;

        if ($item['text'] === '') {
            continue;
        }

        if ($limit < $i) {
            continue;
        }

        $output .= '<li class="mb2-pb-listicon_item">';
        $output .= '<div class="item-content">';
        $output .= '<span class="iconel' . theme_mb2nl_bsfcls(2, '', 'center', 'center') . '" aria-hidden="true">';
        $output .= '<i class="' . $iconcls . '"></i>';
        $output .= '</span>';
        $output .= '<span class="list-text">';
        $output .= $item['text'];
        $output .= '</span>';
        $output .= '</div>';
        $output .= '</li>';
    }

    $output .= '</ul>';

    return $output;

}



/**
 *
 * Method to get course intro text
 *
 */
function theme_mb2nl_course_intro($ccourse = null) {
    $intro = '';

    $courseid = !is_null($ccourse) ? $ccourse->id : 0;

    $introfiled = theme_mb2nl_mb2fields_filed('mb2intro', $courseid);

    if (!is_null($introfiled) && $introfiled !== '') {
        $intro = $introfiled;
    } else {
        $intro = theme_mb2nl_get_course_slogan('', $ccourse);
    }

    return theme_mb2nl_format_txt($intro, FORMAT_HTML);

}




/**
 *
 * Method to get course edit link
 *
 */
function theme_mb2nl_course_edit_link($courseid = 0) {
    global $COURSE;

    $output = '';
    $isid = $courseid ? $courseid : $COURSE->id;
    $context = context_course::instance($isid);
    $canedit = has_capability('moodle/course:update', $context);

    if (!$canedit) {
        return;
    }

    $url = new moodle_url('/course/edit.php', ['id' => $isid]);

    $output .= '<a href="' . $url . '" title="'.get_string('editcoursesettings').'" style="font-size:1rem;margin-left:.45rem;">';
    $output .= '<i class="fa fa-pencil"></i>';
    $output .= '</a>';

    return $output;

}



/**
 *
 * Method to get enrolment page layout type
 *
 */
function theme_mb2nl_enrol_layout() {
    global $PAGE;

    return theme_mb2nl_mb2fields_filed('mb2layout') ? theme_mb2nl_mb2fields_filed('mb2layout') :
    theme_mb2nl_theme_setting($PAGE, 'enrollayout');

}




/**
 *
 * Method to get course progressbar
 *
 */
function theme_mb2nl_course_progressbar($opts = [], $courseid = 0) {
    $output = '';
    $courseprogress = theme_mb2nl_course_completion_percentage($courseid);

    if ($courseprogress === '' && ! isset($opts['circle'])) {
        return;
    }

    $opts['ttext'] = get_string('completepercent', 'block_myoverview', $courseprogress);

    if (isset($opts['circle']) && $opts['circle']) {
        return theme_mb2nl_chart_circle($courseprogress, $opts);
    }

    $output .= '<div class="theme-course-progress">';
    $output .= isset($opts['align']) ? '<div class="progress-text-wrap" style="width:' . $courseprogress . '%;">' : '';
    $output .= '<div class="theme-course-progress-text position-relative">';
    $output .= '<span class="progress-text">' . get_string('yourprogress', 'theme_mb2nl') . '</span>';
    $output .= ' <span class="progress-value">' . $courseprogress . '%</span>';
    $output .= '</div>';
    $output .= isset($opts['align']) ? '</div>' : '';
    $output .= '<div class="fsmod-progress-bar"><div class="fsmod-progress-progress" style="width:' . $courseprogress .
    '%;"></div></div>';
    $output .= '</div>';

    return $output;

}



/**
 *
 * Method to get course tags
 */
function theme_mb2nl_ccourse_tags($id) {

    global $DB;

    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'ctags_' . $id;

    // If there is a cache, return it.
    if ($cache->get($cacheid)) {
        return $cache->get($cacheid);
    }

    $params = [
        'cid' => $id,
        'itemtype' => 'course',
    ];

    $recordsql = 'SELECT t.id, t.tagcollid, t.name, t.rawname FROM {tag} t JOIN {tag_instance} ti ON ti.tagid=t.id JOIN {context}';
    $recordsql .= ' cx ON cx.id=ti.contextid JOIN {course} c ON c.id=cx.instanceid WHERE c.id=:cid AND ' .
    $DB->sql_like('ti.itemtype', ':itemtype') . ' AND t.flag=0';

    $ctags = $DB->get_records_sql($recordsql, $params);

    // Set cache.
    $cache->set($cacheid, $ctags);

    return $ctags;

}



/**
 *
 * Method to get course tags block
 */
function theme_mb2nl_course_tags_block($id, $tgsdb = false) {

    $output = '';
    $tags = theme_mb2nl_ccourse_tags($id);

    if (!count($tags)) {
        return;
    }

    $coursecontext = context_course::instance($id);
    $mcls = $tgsdb ? 'mt-5' : 'fake-block block_tags';

    $output .= '<div class="' . $mcls . '">';
    $output .= '<h4 class="block-heading h5">' . get_string('coursetags', 'tag') . '</h4>';
    $output .= '<ul class="tag_list course-tags-list">';

    foreach ($tags as $t) {
        // TO DO: add link to ajax course page.
        $link = new moodle_url('/tag/index.php', ['tc' => $t->tagcollid, 'tag' => $t->rawname, 'from' => $coursecontext->id]);
        $output .= '<li><a href="' . $link . '" class="badge badge-info">' . $t->rawname . '</a></li>';
    }

    $output .= '</ul>';
    $output .= '</div>';

    return $output;

}


/**
 *
 * Method to check if there is one page navigation on course nerolment page
 */
function theme_mb2nl_is_eopn() {

    global $PAGE;

    $mb2onepagenav = theme_mb2nl_mb2fields_filed('mb2onepagenav');

    if ($mb2onepagenav) {
        return $mb2onepagenav;
    }

    return theme_mb2nl_theme_setting($PAGE, 'onepagenav');

}





/**
 *
 * Method to get custom filed icon
 */
function theme_mb2nl_cf_icons() {
    global $PAGE;

    // Defin array and setting.
    $icons = [];
    $cficons = theme_mb2nl_theme_setting($PAGE, 'cficons');

    // Get array from theme setting.
    // Each line is an array element.
    $line = preg_split('/\r\n|\n|\r/', trim($cficons));

    // Prepare the icons array.
    // As a key set custom filed shortname.
    // As a vaue set icon classname.
    foreach ($line as $l) {
        if (empty($l) || ! preg_match('@|@', $l)) {
            continue;
        }

        $litem = explode('|', $l);
        $lkey = $litem[0];
        $pref = theme_mb2nl_font_icon_prefix($litem[1]);
        $icons[$lkey] = $pref . $litem[1];
    }

    return $icons;

}





/**
 *
 * Method to get custom filed icon
 */
function theme_mb2nl_cf_icon($fieldname) {
    $icons = theme_mb2nl_cf_icons();

    if (array_key_exists($fieldname, $icons)) {
        return $icons[$fieldname];
    }

    return false;
}






/**
 *
 * Method to get course layouts without table of contents
 *
 */
function theme_mb2nl_notoc() {
    return [1, 2, 3];
}



/**
 *
 * Method to get course layouts without section navigation on the course main page
 *
 */
function theme_mb2nl_nosectionnav() {
    return [0, 3];
}



/**
 *
 * Method to get course layouts without rating block
 *
 */
function theme_mb2nl_noratingblock() {
    return [1, 2, 3];
}


/**
 *
 * Method to check if there is course ID
 *
 */
function theme_mb2nl_is_course($ccourse = null) {
    global $COURSE, $SITE;

    $cobj = !is_null($ccourse) ? $ccourse : $COURSE;

    if ($cobj && $cobj->id != $SITE->id) {
        return true;
    }

    return false;

}


/**
 *
 * Method to check if there is course home scetion in the sidebar
 *
 */
function theme_mb2nl_is_chome() {

    global $COURSE, $PAGE;

    if (!theme_mb2nl_is_course()) {
        return false;
    }

    if (in_array(theme_mb2nl_course_layout(), theme_mb2nl_notoc())) {
        return false;
    }

    if (theme_mb2nl_theme_setting($PAGE, 'fsmodhome') && !theme_mb2nl_is_enrol_page()) {
        return true;
    }

    return false;

}



/**
 *
 * Method to set add course url
 *
 */
function theme_mb2nl_addcourse_url() {

    global $CFG, $COURSE;

    if (file_exists($CFG->dirroot . '/local/course_templates/index.php')) {
        return new moodle_url('/local/course_templates/index.php');
    }

    $categoryid = optional_param('categoryid', 0, PARAM_INT);
    $params = [];

    if ($categoryid) {
        $params['category'] = $categoryid;
    } else if (!$categoryid && theme_mb2nl_is_course()) {
        $params['category'] = $COURSE->category;
    }

    return new moodle_url('/course/edit.php', $params);

}





/**
 *
 * Method to set add course url
 *
 */
function theme_mb2nl_citem_cls($box, $opts) {
    global $PAGE;

    $cls = '';

    $cls .= $box ? ' theme-box' : '';
    $cls .= theme_mb2nl_theme_setting($PAGE, 'quickview') ? ' quickview' : ' noquickview';
    $cls .= theme_mb2nl_theme_setting($PAGE, 'ccimgs') ? ' objfit' : '';

    // Style class.
    $cistyle = isset($opts['cistyle']) ? $opts['cistyle'] : theme_mb2nl_theme_setting($PAGE, 'cistyle');
    $cls .= ' cstyle-' . $cistyle;

    $cirounded = isset($opts['crounded']) ? $opts['crounded'] : theme_mb2nl_theme_setting($PAGE, 'crounded');
    $cls .= ' crounded' . $cirounded;

    return $cls;

}







/**
 *
 * Method to get section config from the 'sectionconfig' shortcode
 *
 */
function theme_mb2nl_course_info_html($ccourse = null) {

    global $COURSE;

    $output = '';
    $cobj = $ccourse ? $ccourse : $COURSE;
    $skills = theme_mb2nl_mb2fields_filed('mb2skills', $cobj->id);
    $requirements = theme_mb2nl_mb2fields_filed('mb2requirements', $cobj->id);

    $output .= '<div class="course-nav-section csection_courseinfo">';
    $output .= '<div id="course-mb2section" class="details-section mb2section">';
    $output .= '<div class="section-content">';
    $output .= '<div class="content-inner">';
    $output .= '<div class="course-summary course-section-part">';
    $output .= '<h2 class="section-heading h3">' . get_string('coursesummary') . '</h2>';
    $output .= theme_mb2nl_moreless(theme_mb2nl_get_mb2course_description($cobj));
    $output .= '</div>';

    if ($skills) {
        $output .= '<div class="skills course-skills course-section-part">';
        $output .= '<h2 class="section-heading h3">' . get_string('headingwhatlearn', 'theme_mb2nl') . '</h2>';
        $output .= '<div class="content-inner">' . theme_mb2nl_sr_list($skills, false) . '</div>';
        $output .= '</div>';
    }

    if ($requirements) {
        $output .= '<div class="course-requirements course-section-part">';
        $output .= '<h2 class="section-heading h3">' . get_string('headingrequirements', 'theme_mb2nl') . '</h2>';
        $output .= '<div class="content-inner">' . theme_mb2nl_sr_list($requirements, false, 999, 2) . '</div>';
        $output .= '</div>';
    }

    $output .= '<div class="course-section-part">';
    $output .= '<h2 class="section-heading h3">' . get_string('headinginstructors', 'theme_mb2nl') . '</h2>';
    $output .= theme_mb2nl_course_teachers_list(200, $cobj);
    $output .= '</div>';
    $output .= theme_mb2nl_course_info_table($cobj);
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';

    return $output;

}



/**
 *
 * Method to get section config from the 'sectionconfig' shortcode
 *
 */
function theme_mb2nl_course_csection_html($csection = 'mb2section') {

    $output = '';

    $output .= '<div class="course-nav-section csection_csection">';
    $output .= '<div id="course-mb2section" class="details-section mb2section">';
    $output .= '<h2 class="section-heading h3">' . theme_mb2nl_mb2sectionfiledname($csection) . '</h2>';
    $output .= '<div class="details-content">';
    $output .= '<div class="content-inner">' . theme_mb2nl_mb2fields_filed($csection) . '</div>';
    $output .= '</div>';
    $output .= '</div>';
    $output .= '</div>';

    return $output;

}




/**
 *
 * Method to get section config from the 'sectionconfig' shortcode
 *
 */
function theme_mb2nl_course_reviews_html($ccourse = null) {

    global $CFG, $COURSE;

    $output = '';
    $cobj = $ccourse ? $ccourse : $COURSE;

    $reviews = theme_mb2nl_is_review_plugin();
    $reviewlist = '';
    $reviewscount = '';
    $reviewsummary = '';
    $startslink = '';
    $canrate = '';

    if ($reviews) {
        if (!class_exists('Mb2reviewsHelper')) {
            require($CFG->dirroot . '/local/mb2reviews/classes/helper.php');
        }

        $ratingobj = theme_mb2nl_review_obj($cobj->id);
        $ratingdetailsobj = theme_mb2nl_review_obj($cobj->id, true);

        $canrate = Mb2reviewsHelper::can_rate($cobj->id);
        $ratealready = Mb2reviewsHelper::rate_already($cobj->id);
        $reviewscount = $ratingdetailsobj->reviews_count;
        $reviewsummary = Mb2reviewsHelper::review_summary($ratingobj, $ratingdetailsobj);
        $courserating = $ratingobj->rating;
        $startslink = Mb2reviewsHelper::rating_stars_link();
    }

    if ($reviewsummary || $reviewscount || $canrate || $ratealready) {
        $output .= '<div class="course-nav-section csection_reviews">';
        $output .= '<div id="course-ratings" class="details-section reviews-starslink">';
        $output .= $startslink;
        $output .= '</div>';

        if ($reviewsummary) {
            $output .= '<div id="course-ratings" class="details-section reviews-summary">';
            $output .= '<h2 class="section-heading h3">' . get_string('courserating', 'local_mb2reviews') . '</h2>';
            $output .= $reviewsummary;
            $output .= '</div>';
        }

        if ($reviewscount) {
            $output .= '<div class="details-section reviews">';
            $output .= '<h2 class="section-heading h3">' . get_string('reviews', 'local_mb2reviews') . '</h2>';
            $output .= Mb2reviewsHelper::review_list($reviewscount);
            $output .= '</div>';
        }

        $output .= '</div>';
    }

    return $output;

}




/**
 *
 * Method to check if there is course tabs feature
 *
 */
function theme_mb2nl_is_coursetabs() {

    global $PAGE;

    $cinfofield = theme_mb2nl_mb2fields_filed('mb2cinfo');
    $cinfo = (!is_null($cinfofield) && $cinfofield !== '') ? $cinfofield : theme_mb2nl_theme_setting($PAGE, 'cinfo');

    if (theme_mb2nl_is_course() && theme_mb2nl_course_layout() === false && $cinfo) {
        return $cinfo;
    }

    return false;

}



/**
 *
 * Method to define course tab items
 *
 */
function theme_mb2nl_field_sections() {

    $sections = [];

    for ($i = 1; $i <= 5; $i++) {

        $num = $i == 1 ? '' : $i;

        if (!theme_mb2nl_mb2fields_filed('mb2section' . $num)) {
            continue;
        }

        $sections[] = ['id' => 'mb2section' . $num, 'str' => theme_mb2nl_mb2sectionfiledname('mb2section' . $num),
        'icon' => theme_mb2nl_cf_icon('mb2section' . $num) ? theme_mb2nl_cf_icon('mb2section' . $num) : 'ri-file-list-2-line'];
    }

    return $sections;

}



/**
 *
 * Method to define course tab items
 *
 */
function theme_mb2nl_is_coursetab_items() {

    global $CFG, $PAGE, $COURSE;

    $tabs = [];

    // Get cache.
    $cache = cache::make('theme_mb2nl', 'course');
    $cacheid = 'course_tabs_' . $COURSE->id . '_' . $COURSE->timemodified;

    if (!$cache->get($cacheid)) {

        $tabs[] = ['id' => '', 'str' => get_string('headingsections', 'theme_mb2nl'), 'icon' => 'ri-align-left'];
        $tabs[] = ['id' => 'courseinfo', 'str' => get_string('courseinfo'), 'icon' => 'ri-information-line'];

        $sections = theme_mb2nl_field_sections();
        if (count($sections) && theme_mb2nl_theme_setting($PAGE, 'csection')) {
            foreach ($sections as $section) {
                $tabs[] = $section;
            }
        }

        if (theme_mb2nl_is_review_plugin()) {
            if (!class_exists('Mb2reviewsHelper')) {
                require($CFG->dirroot . '/local/mb2reviews/classes/helper.php');
            }

            $ratingobj = theme_mb2nl_review_obj($COURSE->id);
            $ratingdetailsobj = theme_mb2nl_review_obj($COURSE->id, true);
            $rhlpr = new Mb2reviewsHelper;

            $courserating = $ratingobj->rating;
            $canrate = Mb2reviewsHelper::can_rate($COURSE->id);
            $ratealready = Mb2reviewsHelper::rate_already($COURSE->id);
            $reviewsummary = Mb2reviewsHelper::review_summary($ratingobj, $ratingdetailsobj);
            $ratingstars = Mb2reviewsHelper::rating_stars($ratingobj->rating, 'xs');

            if ($reviewsummary || $canrate || $ratealready) {
                $tabs[] = ['id' => 'reviews', 'str' => get_string('reviews', 'theme_mb2nl'), 'icon' => 'ri-star-line'];
            }
        }

        $cache->set($cacheid, $tabs);
    }

    return $cache->get($cacheid);

}
