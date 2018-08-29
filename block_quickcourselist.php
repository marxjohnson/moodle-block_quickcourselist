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
 * @package    block_quickcourselist
 * @author      Mark Johnson <mark.johnson@tauntons.ac.uk>
 * @copyright   2010 Tauntons College, UK
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class definition for the Quick Course List Block
 *
 * @uses block_base
 */
class block_quickcourselist extends block_base {

    private $globalconf;
    
    public function init() {
        $this->content_type = BLOCK_TYPE_TEXT;
        $this->globalconf = get_config('block_quickcourselist');
        if (isset($this->globalconf->title) && !empty($this->globalconf->title)) {
            $this->title = $this->globalconf->title;
        } else {
            $this->title = get_string('quickcourselist', 'block_quickcourselist');
        }
    }

    //stop it showing up on any add block lists
    public function applicable_formats() {
        return (array(  
            'all' => false, 
            'site' => true, 
            'my' => true, 
            'course-index' => true
        ));
    }

    public function has_config() {
        return true;
    }

    /**
     * Displays the form for searching courses, and the results if a search as been submitted
     *
     * @access public
     * @return
     */
    public function get_content() {
        global $CFG, $DB;
        if ($this->content !== null) {
            return $this->content;
        }
        
        $this->content = new stdClass();
        $context_block = context_block::instance($this->instance->id);
        $search = optional_param('quickcourselistsearch', '', PARAM_TEXT);
        $quickcoursesubmit = optional_param('quickcoursesubmit', false, PARAM_TEXT);
        if (has_capability('block/quickcourselist:use', $context_block)) {

            $list_contents = '';
            $anchor = html_writer::tag('a', '', array('name' => 'quickcourselistanchor'));
            $inputattrs = array(
                'autocomplete' => 'off',
                'name' => 'quickcourselistsearch',
                'id' => 'quickcourselistsearch',
                'value' => $search
            );
            $input = html_writer::empty_tag('input', $inputattrs);
            $progressattrs = array(
                'src' => $this->page->theme->image_url('i/loading_small', 'moodle'),
                'class' => 'quickcourseprogress',
                'id' => 'quickcourseprogress',
                'alt' => get_string('loading', 'block_quickcourselist')
            );
            $progress = html_writer::empty_tag('img', $progressattrs);
            $submitattrs = array(
                'type' => 'submit',
                'name' => 'quickcoursesubmit',
                'class' => 'submitbutton',
                'value' => get_string('search')
            );
            $submit = html_writer::empty_tag('input', $submitattrs);
            $formattrs = array(
                'id' => 'quickcourseform',
                'method' => 'post',
                'action' => $this->page->url->out().'#quickcourselistanchor'
            );
            $form = html_writer::tag('form', $input.$progress.$submit, $formattrs);

            if (!empty($quickcoursesubmit)) {

                $courses = self::get_courses($search, $context_block, $this->globalconf->splitterms, 
                                             $this->globalconf->restrictcontext, $this->page->context); 
                if (!empty($courses)) {
                    foreach ($courses as $course) {
                        $url = new moodle_url('/course/view.php', array('id' => $course->id));
                        $resultstr = null;
                        if (isset($this->globalconf->displaymode)) {
                            $displaymode = $this->globalconf->displaymode;
                        } else {
                            $displaymode = 3;
                        }
                        switch ($displaymode):
                            case 1: $resultstr = $course->shortname; break;
                            case 2: $resultstr = $course->fullname; break;
                            default: $resultstr = $course->shortname.': '.$course->fullname; break;
                        endswitch;
                        
                        $link = html_writer::tag('a',
                                                 $resultstr,
                                                 array('href' => $url->out()));
                        $li = html_writer::tag('li', $link);
                        $list_contents .= $li;
                    }
                }
            }
            if(!isset($this->globalconf->displaymode)) {
                $this->globalconf->displaymode = '3';
            }
            $list = html_writer::tag('ul', $list_contents, array('id' => 'quickcourselist'));

            $this->content->text = $anchor.$form.$list;

            $jsmodule = array(
                'name'  =>  'block_quickcourselist',
                'fullpath'  =>  '/blocks/quickcourselist/module.js',
                'requires'  =>  array('base', 'node', 'json', 'io')
            );
            $jsdata = array(
                'instanceid' => $this->instance->id,
                'sesskey' => sesskey(),
                'displaymode' => $this->globalconf->displaymode,
                'contextid' => $this->page->context->id
            );

            $this->page->requires->js_init_call('M.block_quickcourselist.init',
                                                $jsdata,
                                                false,
                                                $jsmodule);
        }
        $this->content->footer='';
        return $this->content;
    }

    public static function get_courses($search, $blockcontext, $splitterms = false, 
                                       $restrictcontext = false, $pagecontext = null) {
        global $DB;
        $params = array(SITEID);
        $where = 'id != ? AND (';
        if ($splitterms) {
            $terms = explode(' ', $search);
            $like = '%1$s LIKE';
            foreach ($terms as $key => $term) {
                $like .= ' ?';
                if ($key < count($terms)-1) {
                    $like .= ' AND %1$s LIKE';
                }
                $terms[$key] = '%'.$term.'%';
            }
            $params = array_merge($params, $terms, $terms);
            $where .= sprintf($like, 'shortname').' OR '.sprintf($like, 'fullname');
        } else {
            $params = array_merge($params, array("%$search%", "%$search%"));
            $where .= 'shortname LIKE ? OR fullname LIKE ?';
        }
        $where .= ')';
        if (!has_capability('moodle/course:viewhiddencourses', $blockcontext)) {
            $where .= ' AND visible=1';
        }
        if ($restrictcontext) {
            if ($pagecontext && $pagecontext->get_level_name() == get_string('category')) {
                $where .= ' AND category = ?';
                $params[] = $pagecontext->instanceid;
            }
        }

        $order = 'shortname';
        $fields = 'id, shortname, fullname';

        $courses = $DB->get_recordset_select('course', $where, $params, $order, $fields);
    return $courses;
    }
}

