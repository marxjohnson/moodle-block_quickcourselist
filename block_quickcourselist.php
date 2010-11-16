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
 * Defines the class for the Quick Course List block
 *
 * @package    block
 * @subpackage  quickcourselist
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2010 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */  

class block_quickcourselist extends block_base {

    function init() {
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->title = '<a name="quickcourselistanchor"></a>'.get_string('quickcourselist','block_quickcourselist');
    }

    //stop it showing up on any add block lists
    function applicable_formats() {
        return (array('all' => false,'site'=>true));
    }

    function preferred_width() {
      // The preferred value is in pixels
      return 180;
    }

    function get_content() {
        global $CFG;

        $context_block = get_context_instance(CONTEXT_BLOCK, $this->instance->id);
        $course = optional_param('quickcourselistsearch', '', PARAM_TEXT);
        $submit = optional_param('quickcoursesubmit', false, PARAM_TEXT);

        if (has_capability('block/quickcourselist:use', $context_block)) {
            $this->content->text = '<form action="'.$CFG->wwwroot.$_SERVER['REQUEST_URI'].'#quickcourselistanchor" method="post">
            <input style="width:120px;" autocomplete="off" onkeyup="quickcoursesearch('.$course.')" name="quickcourselistsearch" id="quickcourselistsearch" value="'.$course.'" />
            <span id="quickcourseprogress" style="visibility:hidden;"><img src="'.$CFG->wwwroot.'/blocks/quickcourselist/pix/ajax-loader.gif" alt="Loading.." /></span>
            <noscript><input type="submit" name="quickcoursesubmit" value="Search" /></noscript></form>
            <div id="quickcourselist">';

            if(!empty($submit)) {
                $query='SELECT id,shortname,fullname FROM '.$CFG->prefix.'course WHERE id <>'.SITEID.' AND (shortname LIKE \'%'.$course.'%\' OR fullname LIKE \'%'.$course.'%\')';
                    if(!has_capability('moodle/course:viewhiddencourses',$context_block)){$query.=' AND visible=1';}

                    if($courses=get_records_sql($query)) {
                        foreach ($courses as $course) {
                            $this->content->text .= '<div><a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->shortname.': '.$course->fullname.'</a></div>';
                        }
                    }
            }
            $this->content->text .='</div>';

            require_js(array($CFG->wwwroot.'/blocks/quickcourselist/quickcourselist.js',
            'yui_yahoo',
            'yui_dom',
            'yui_event',
            'yui_connection'));
            $this->content->text.='<script type="text/javascript">var wwwroot = "'.$CFG->wwwroot.'"; var xhr; var instanceid = '.$this->instance->id.'</script>';
        }
        $this->content->footer='';
        return $this->content;

    }
}
?>
