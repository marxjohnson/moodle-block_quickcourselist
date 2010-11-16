<?php

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
