<?php

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

require_once($CFG->libdir . "/externallib.php");
require_once($CFG->libdir . "/filelib.php");

class local_exfunctions_external extends external_api {

	public static function view_assignment_parameters() {
		return new external_function_parameters(
				array(
						'name' => new external_value(PARAM_TEXT, 'assign name', VALUE_DEFAULT, ""),
						'id'   => new external_value(PARAM_INT, 'id', VALUE_DEFAULT, 0),
						'userid' => new external_value(PARAM_INT, 'userid'),
				)
		);
	}

	public static function view_assignment($name="", $id=0, $userid=0) {
		global $CFG, $USER, $DB;

 		require_once("$CFG->dirroot/config.php");
		require_once("$CFG->dirroot/mod/assign/locallib.php");
		require_once("$CFG->libdir/datalib.php");
		require_once("$CFG->libdir/dml/moodle_database.php");

	    //self::validate_parameters(self::view_assignment_parameters(), array('name'=>$name, 'id'=>$, 'userid'=>$userid));

 		if($name!="" && $id==0){
			$id = self::get_course_module_id_from_assign_name($name);
		}
		elseif ($name=="" && $id!=0){
			
		}
		else{
			throw new moodle_exception('invalid params');
		}

		$cm = get_coursemodule_from_id('assign', $id, 0, false, MUST_EXIST);

		$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

		$context = context_module::instance($cm->id);

		require_capability('mod/assign:view', $context);
		
		$assign = new assign($context,$cm,$course);
		
		// Mark as viewed
		$completion=new completion_info($course);
		$completion->set_module_viewed($cm);
		
		$instance = $assign->get_instance();
 		$data = $DB->get_record('assign_submission', array('assignment'=>$instance->id, 'userid'=>$userid), '*', MUST_EXIST);
		$text = $DB->get_record('assignsubmission_onlinetext', array('assignment'=>$instance->id, 'submission'=>$data->id), 'onlinetext', MUST_EXIST);
		
		$list = array();
		$list['name']         = $instance->name;
		$list['intro']        = $instance->intro;
		$list['status']       = $data->status;
		$list['duedate']      = self::decode_timestamp($instance->duedate);
		$list['timemodified'] = self::decode_timestamp($instance->timemodified);
		$list['text']         = $text->onlinetext;
		return $list;
	}

	public static function view_assignment_returns() {
		return new external_single_structure(
				array(
						'name'    => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'intro'   => new external_value(PARAM_RAW, '', VALUE_OPTIONAL),
						'status'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'duedate' => new external_single_structure(
							array(
								'year'  => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'month' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'day'   => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'hours' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'minutes' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'seconds' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'weekday' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
							)
						),
						'timemodified' => new external_single_structure(
							array(
								'year'  => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'month' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'day'   => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'hours' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'minutes' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'seconds' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'weekday' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
							)
						),
						'text'    => new external_value(PARAM_RAW, '', VALUE_OPTIONAL)
				)
		);
	}

	//--------------------------------------------------------------------------------------

	public static function submit_assignment_parameters() {
		return new external_function_parameters(
				array(
						'name'   => new external_value(PARAM_RAW, 'assign name', VALUE_DEFAULT, ""),
						'id'     => new external_value(PARAM_INT, 'id', VALUE_DEFAULT, 0),
						'userid' => new external_value(PARAM_INT, 'userid'),
						'text'   => new external_value(PARAM_RAW, 'text')
				)
		);
	}

	public static function submit_assignment($name="", $id=0, $userid, $text) {
		global $CFG, $DB;
		/** config.php */
		require_once("$CFG->dirroot/config.php");
		/** Include library */
		require_once("$CFG->dirroot/mod/assign/locallib.php");
		require_once("$CFG->dirroot/mod/assign/lib.php");
		
		//self::validate_parameters(self::submit_assignment_parameters(), array('name'=>$name, 'id'=>$id, 'text'=>$text));
		if($name!="" && $id==0){
			$id = (int)self::get_course_module_id_from_assign_name($name);
		}
		elseif ($name=="" && $id!=0){
			
		}
		else{
			throw new moodle_exception('invalid_parameter_exception');
		}

		//	$url = new moodle_url('/mod/assign/view.php', array('id' => $id)); // Base URL

		// get the request parameters
		$cm = get_coursemodule_from_id('assign', $id, 0, false, MUST_EXIST);

		$course = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);

		// Auth
		//	$PAGE->set_url($url);

		$context = context_module::instance($cm->id);

		require_capability('mod/assign:view', $context);

		$assign = new assign($context,$cm,$course);

		// Mark as viewed
		$completion=new completion_info($course);
		$completion->set_module_viewed($cm);
		
		// Get the assign to render the page
		$assign->submit_assign($id, $userid, $text);
		return self::view_assignment('', $id, $userid);
	}

	public static function submit_assignment_returns() {
		return new external_single_structure(
				array(
						'name'    => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'intro'   => new external_value(PARAM_RAW, '', VALUE_OPTIONAL),
						'status'  => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'duedate' => new external_single_structure(
							array(
								'year'  => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'month' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'day'   => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'hours' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'minutes' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'seconds' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'weekday' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
							)
						),
						'timemodified' => new external_single_structure(
							array(
								'year'  => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'month' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'day'   => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'hours' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'minutes' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'seconds' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
								'weekday' => new external_value(PARAM_INT, '', VALUE_OPTIONAL),
							)
						),
						'text'    => new external_value(PARAM_RAW, '', VALUE_OPTIONAL)
				)
		);
	}

	//--------------------------------------------------------------------------------------------

	function get_course_module_id_from_assign_name($name) {
		global $CFG, $DB;
		require_once("$CFG->libdir/dml/moodle_database.php");
		$assign = $DB->get_record_sql("SELECT * FROM mdl_assign WHERE name='$name'");
		$instance = $assign->id;
		$course_modules = $DB->get_record_sql("SELECT * FROM mdl_course_modules WHERE module=1 AND instance=$instance");
		$id = $course_modules->id;
		return $id;
	}
	
	//--------------------------------------------------------------------------------------------
	
	function decode_timestamp($timestamp) {
		$list = array();
		$time = getdate($timestamp);
		$list['year']  = $time['year'];
		$list['month'] = $time['mon'];
		$list['day']   = $time['mday'];
		$list['hours'] = $time['hours'];
		$list['minutes'] = $time['minutes'];
		$list['seconds'] = $time['seconds'];
		$list['weekday'] = $time['wday'];
		return $list;
	}
}