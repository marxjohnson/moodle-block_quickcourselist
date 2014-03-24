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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

	$settings->add(new admin_setting_heading('block_quickcourselist/displaymode', get_string('displaymode', 'block_quickcourselist'), ''));

	// Possible display modes
	$displaymode[1] = get_string('shortnamecourse', 'moodle');
	$displaymode[2] = get_string('fullnamecourse', 'moodle');
	$displaymode[3] = $displaymode[2].' & '.$displaymode[1];

	$settings->add(new admin_setting_configselect('block_quickcourselist/displaymode', get_string('displaymode', 'block_quickcourselist'), 
						get_string('displaymodedescription', 'block_quickcourselist'), $displaymode[3], $displaymode));

	$settings->add(new admin_setting_heading('block_quickcourselist/title', get_string('title', 'block_quickcourselist'), ''));

	$settings->add(new admin_setting_configtext('block_quickcourselist/title', get_string('title', 'block_quickcourselist'), '', get_string('blockname', 'block_quickcourselist')));
    
	$settings->add(new admin_setting_heading('block_quickcourselist/splitterms', get_string('splitterms', 'block_quickcourselist'), ''));

	$settings->add(new admin_setting_configcheckbox('block_quickcourselist/splitterms', get_string('splitterms', 'block_quickcourselist'), get_string('splittermsdescription', 'block_quickcourselist'), 0));
    
	$settings->add(new admin_setting_heading('block_quickcourselist/restrictcontext', get_string('restrictcontext', 'block_quickcourselist'), ''));

	$settings->add(new admin_setting_configcheckbox('block_quickcourselist/restrictcontext', get_string('restrictcontext', 'block_quickcourselist'), get_string('restrictcontextdescription', 'block_quickcourselist'), 0));
}