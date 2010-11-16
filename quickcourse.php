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
 * @package    block
 * @subpackage  quickcourselist
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2010 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */  

require_once('../../config.php');

$instanceid = required_param('instanceid', PARAM_INT);
$context_block = get_context_instance(CONTEXT_BLOCK, $instanceid);
$course = required_param('course', PARAM_TEXT);

if (has_capability('block/quickcourselist:use', $context_block)) {

    $output = '';
    if(!empty($course)) {
        $query='SELECT id,shortname,fullname FROM '.$CFG->prefix.'course WHERE id <>'.SITEID.' AND (shortname LIKE \'%'.$course.'%\' OR fullname LIKE \'%'.$course.'%\')';
            if(!has_capability('moodle/course:viewhiddencourses',$context_block)){$query.=' AND visible=1';}

            if($courses=get_records_sql($query)) {
                foreach ($courses as $course) {
                    $output .= '<div><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.': '.$course->fullname.'</a></div>'."\n";
                }
            }
    }
    echo $output;

} else {
	header('HTTP/1.1 401 Not Authorized');
}

?>
