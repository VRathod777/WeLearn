<?php

/**
 * Adding learning program link in sidebar
 * @param navigation_node $nav navigation node
 */
function local_custom_report_extend_navigation(navigation_node $nav) {
    global $CFG, $PAGE, $COURSE,$USER, $DB;

    // Check if users is logged in to extend navigation.
    if (!isloggedin()) {
        return;
    }
 
    // training video
    $nodes = explode("\n", $CFG->custommenuitems);
    $pluginnode = array_shift($nodes);

    if (is_siteadmin($USER)) {
        $node = get_string('training_video', 'local_custom_report');
        $node .= "|";
        $node .= $CFG->wwwroot.'/local/staticpage/view.php?page=beforeyoustart';
        array_unshift($nodes, $node);
        array_unshift($nodes, $pluginnode);
        $CFG->custommenuitems = implode("\n", $nodes);
    }



    $checkIfManger = "SELECT d.userid FROM {user_info_data} d WHERE d.fieldid = 6 
    AND d.data = ? limit 1";
    $dataR = $DB->get_record_sql($checkIfManger, [$USER->email]);

    if ($dataR) {
        $node = get_string('report_heading12', 'local_custom_report');
        $node .= "|";
        $node .= $CFG->wwwroot . '\local\custom_report\manager.php';
        array_unshift($nodes, $node);
        array_unshift($nodes, $pluginnode);
        $CFG->custommenuitems = implode("\n", $nodes);
    }
 

    if (is_siteadmin($USER)) {   
        

        $node = get_string('report_heading', 'local_custom_report');
        $node .= "|";
        $node .= $CFG->wwwroot.'\local\custom_report\index.php';
        array_unshift($nodes, $node);
        array_unshift($nodes, $pluginnode);
        $CFG->custommenuitems = implode("\n", $nodes);

        
        // Course Usage Report
        $node = get_string('report_heading_usage', 'local_custom_report');
        $node .= "|";
        $node .= $CFG->wwwroot.'\local\custom_report\course_usage.php';
        array_unshift($nodes, $node);
        array_unshift($nodes, $pluginnode);
        $CFG->custommenuitems = implode("\n", $nodes);
    }



}