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
 * Server-side script for generating response to AJAX search request
 *
 * @package    block_quickcourselist
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2010 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

//define('AJAX_SCRIPT', true);
require_once('../../config.php');
require_once($CFG->dirroot . '/blocks/moodleblock.class.php');
require_once($CFG->dirroot . '/blocks/quickcourselist/block_quickcourselist.php');

$instanceid = required_param('instanceid', PARAM_INT);
$context = context_block::instance($instanceid);
$course = required_param('course', PARAM_TEXT);
$pagecontextid = required_param('contextid', PARAM_INT);
$config = get_config('block_quickcourselist');

if (isloggedin() && has_capability('block/quickcourselist:use', $context) && confirm_sesskey()) {

    $output = array();
    if (!empty($course)) {
    $catcontext = context::instance_by_id($pagecontextid, IGNORE_MISSING);
    $courses = block_quickcourselist::get_courses($course, $context, $config->splitterms, 
                                                  $config->restrictcontext, $catcontext);
        
        if (!empty($courses)) {
            foreach ($courses as $course) {
                $output[] = $course;
            }
            $courses->close();
        }
    }
    header('Content-Type: application/json');
    echo json_encode($output);

} else {
    header('HTTP/1.1 401 Not Authorized');
}
