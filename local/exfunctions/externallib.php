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

	/**
	 * Returns description of method parameters
	 * @return external_function_parameters
	 */
	public static function get_private_files_list_parameters() {
		return new external_function_parameters(
				array(
						'sort'       => new external_value(PARAM_TEXT, 'A fragment of SQL to use for sorting', VALUE_DEFAULT, "sortorder, itemid, filepath, filename"),
						'includedirs'=> new external_value(PARAM_BOOL, 'Whether or not include directories', VALUE_DEFAULT, true),
						'directory'  => new external_value(PARAM_TEXT, 'Directory to start serching if not specified all', VALUE_DEFAULT, '')

				)
		);
	}

	/**
	 * Returns all the filenames
	 * @return array with all the filenames
	 */
	public static function get_private_files_list($sort = "sortorder, itemid, filepath, filename", $includedirs = true, $directory = '/') {
		global $CFG, $USER;
		require_once("$CFG->libdir/moodlelib.php");
		require_once("$CFG->libdir/filestorage/file_storage.php");
		$params = self::validate_parameters(self::get_private_files_list_parameters(),
				array('sort' => $sort, 'includedirs' => $includedirs, 'directory' => $directory));

		$context = get_context_instance(CONTEXT_USER, $USER->id);
		$contextid = $context->id;
		$component = "user";
		$filearea  = "private";
		$itemid    = 0;
		
		$fs = get_file_storage();
		$files = $fs->get_area_files($contextid, $component, $filearea, $itemid, $sort, $includedirs);
		$list;
		foreach ($files as $f) {
			$filepath = $f->get_filepath();
			$filename = $f->get_filename();
			if (($filename != ".") && preg_match("/$directory/", $filepath)) {
				//	$list[] = json_encode(array('filepath'=>$filepath, 'filename'=>$filename));
				$list[] = $filepath.$filename;
			}
		}
		return $list;
	}

	/**
	 * Returns description of method result value
	 * @return external_description
	 */
	public static function get_private_files_list_returns() {
		return new external_multiple_structure(
				new external_value(PARAM_TEXT, 'An array with all the filenames in all subdirectories')
		);
	}
	
	//-----------------------------------------------------------------------------------------

	public static function download_parameters() {
		return new external_function_parameters(
				array(
						'downloadpath' => new external_value(PARAM_PATH, ''),
				)
		);
	}
	
	public static function download($downloadpath) {
		global $CFG, $USER;
// 		$params = self::validate_parameters(self::get_private_files_list_parameters(), array(
// 				'downloadpath' => $downloadpath));

		$context = get_context_instance(CONTEXT_USER, $USER->id);
		$contextid = $context->id;
		$token = $_GET['wstoken'];
		$url = "http://localhost/moodle/webservice/pluginfile.php/$contextid/user/private/$downloadpath?token=$token";
		/* $fp = fopen($savepath, 'w');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_FILE, $fp);
		curl_setopt($ch, CURLOPT_HEADER, FALSE );
		$data = curl_exec($ch);
		curl_close($ch);
		fclose($fp);*/
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename='.basename($downloadpath));
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		ob_clean();
		flush();
		readfile($url);
		exit;
	}
	
	public static function download_returns() {
	}
	
	//-----------------------------------------------------------------------------------------

	/**
	 * Returns description of upload parameters
	 *
	 * @return external_function_parameters
	 * @since Moodle 2.2
	 */
	public static function upload_parameters() {
		return new external_function_parameters(
				array(
						'uploadfile'  => new external_value(PARAM_TEXT, 'path to upload file'),
						'filepath'  => new external_value(PARAM_PATH, 'file path', VALUE_DEFAULT, "/"),
				)
		);
	}

	public static function upload($uploadfile, $filepath) {
		global $USER, $CFG;
		require_once("$CFG->dirroot/config.php");
		require_once("$CFG->libdir/moodlelib.php");
		require_once("$CFG->libdir/accesslib.php");
		require_once("$CFG->libdir/filelib.php");
		require_once("$CFG->libdir/filebrowser/file_info_stored.php");

		self::validate_parameters(self::upload_parameters(), array(
				'uploadfile'=>$uploadfile, 'filepath'=>$filepath,));
		
		$context = get_context_instance(CONTEXT_USER, $USER->id);
		$contextid = $context->id;
		$component = "user";
		$filearea  = "private";
		$itemid    = 0;

		if (!file_exists($uploadfile)){
			throw new moodle_exception("$uploadfile does not exist");
		}

		$context = get_context_instance(CONTEXT_USER, $USER->id);

		$fs = get_file_storage();

		$fs->get_area_files($context->id, 'user', 'private', false, 'id', false);

		$file_record = new stdClass;
		$file_record->component = 'user';
		$file_record->contextid = $context->id;
		$file_record->userid    = $USER->id;
		$file_record->filearea  = 'private';
		$file_record->filename  = basename($uploadfile);
		$file_record->filepath  = $filepath;
		$file_record->itemid    = 0;
		$file_record->license   = $CFG->sitedefaultlicense;
		$file_record->author    = $USER->firstname." ".$USER->lastname;
		$file_record->source    = '';

		//Check if the file already exist
		$existingfile = $fs->file_exists($file_record->contextid, $file_record->component, $file_record->filearea,
				$file_record->itemid, $file_record->filepath, $file_record->filename);
		if ($existingfile) {
			throw new moodle_exception(basename($uploadfile) . " already exist");
		} else {
			$stored_file = $fs->create_file_from_pathname($file_record, $uploadfile);
			return true;
		}
	}


	/**
	 * Returns description of upload returns
	 *
	 * @return external_single_structure
	 * @since Moodle 2.2
	 */
	public static function upload_returns() {
		return new external_value(PARAM_BOOL, '');

	}

	//--------------------------------------------------------------------------------------

	public static function view_assignment_parameters() {
		return new external_function_parameters(
				array(
						'name' => new external_value(PARAM_TEXT, 'assign name', VALUE_DEFAULT, ""),
						'id'   => new external_value(PARAM_INT, 'id', VALUE_DEFAULT, 0),
						'viewAll' => new external_value(PARAM_BOOL, '', VALUE_DEFAULT, 0),
				)
		);
	}

	public static function view_assignment($name="", $id=0, $viewAll = 0) {
		global $CFG, $DB, $PAGE;
		require_once("$CFG->dirroot/config.php");
		require_once("$CFG->dirroot/mod/assign/locallib.php");
		require_once("$CFG->libdir/datalib.php");
		require_once("$CFG->libdir/dml/moodle_database.php");
		
		self::validate_parameters(self::view_assignment_parameters(), array('name'=>$name, 'id'=>$id, 'viewAll'=>$viewAll));

		if($name!="" && $id==0){
			$id = self::get_course_module_id_from_assign_name($name);
		}
		elseif ($name=="" && $id!=0){
			
		}
		else{
			throw new moodle_exception('invalid params');
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

		header("Content-type: text/plain; charset=UTF-8");

		// Get the assign to render the page
		return $assign->view_assign_status($viewAll);
	}

	public static function view_assignment_returns() {
		return new external_single_structure(
				array(
						'Name'				=> new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Intro'				=> new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Submission status' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Grading status'    => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Due date'          => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Time remaining'    => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Last modified'     => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Online text'       => new external_value(PARAM_RAW, '', VALUE_OPTIONAL),
				)
		);
	}

	//--------------------------------------------------------------------------------------

	public static function submit_assignment_parameters() {
		return new external_function_parameters(
				array(
						'name' => new external_value(PARAM_RAW, 'assign name', VALUE_DEFAULT, ""),
						'id'   => new external_value(PARAM_INT, 'id', VALUE_DEFAULT, 0),
						'text' => new external_value(PARAM_RAW, 'text')
				)
		);
	}

	public static function submit_assignment($name, $id, $text) {
		global $CFG, $DB, $PAGE;
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
			$id = $id;
		}
		else{
			new moodle_exception('invalid_parameter_exception');
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
		return $assign->submit_assign($id, $text);
	}

	public static function submit_assignment_returns() {
		return new external_single_structure(
				array(
						'Name'				=> new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Intro'				=> new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Submission status' => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Grading status'    => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Due date'          => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Time remaining'    => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Last modified'     => new external_value(PARAM_TEXT, '', VALUE_OPTIONAL),
						'Online text'       => new external_value(PARAM_RAW,  '', VALUE_OPTIONAL),
				)
		);
	}

	//------------------------------------------------------------------------------------------------

	public static function self_enrol_parameters() {
		return new external_function_parameters(
				array(
						'name' => new external_value(PARAM_TEXT, 'assign name', VALUE_DEFAULT, ""),
						'id' => new external_value(PARAM_INT, 'id', VALUE_DEFAULT, 0),
				)
		);
	}

	public static function self_enrol($name="", $id=0) {
		global $CFG, $DB, $PAGE, $USER;
		/** config.php */
		require_once("$CFG->dirroot/config.php");
		/** Include library */
		require_once("$CFG->dirroot/enrol/self/lib.php");
		require_once("$CFG->libdir/enrollib.php");
		require_once("$CFG->libdir/dml/moodle_database.php");
		require_once("$CFG->libdir/accesslib.php");

		self::validate_parameters(self::self_enrol_parameters(), array('name'=>$name, 'id'=>$id));
		if($name!="" && $id==0){
			require_once("$CFG->libdir/datalib.php");
			$searchterms = array("$name");
			$courses = get_courses_search($searchterms);
			foreach ($courses as $course) {
				if($course->fullname == $name || $course->shortname == $name){
					$id = $course->id;
					break;
				}
			}
		}
		elseif ($name=="" && $id!=0){
			
		}
		else{
			new moodle_exception('invalid_parameter_exception');
		}
		$context = get_context_instance(CONTEXT_COURSE, $id, MUST_EXIST);
		// get all enrol forms available in this course
		$enrols = enrol_get_plugins(true);
		$enrolinstances = enrol_get_instances($course->id, true);

		if (is_enrolled($context, $USER, '', true)) {
			return false;
		}

		foreach($enrolinstances as $instance) {
			if (!isset($enrols[$instance->enrol]) || $instance->enrol != "self") {
				continue;
			}
			return $enrols[$instance->enrol]->execute_enrol($instance);
		}
	}

	public static function self_enrol_returns() {
		return new external_value(PARAM_BOOL, '');
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
}