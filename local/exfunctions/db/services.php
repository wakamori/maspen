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

/**
 * Web service local plugin template external functions and service definitions.
 *
 * @package    localwstemplate
 * @copyright  2011 Jerome Mouneyrac
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// We defined the web service functions to install.
$functions = array(
		'local_exfunctions_view_assignment' => array(
				'classname'   => 'local_exfunctions_external',
				'methodname'  => 'view_assignment',
				'classpath'   => 'local/exfunctions/externallib.php',
				'description' => 'view assignment status',
				'type'        => 'read',
		),
		'local_exfunctions_submit_assignment' => array(
				'classname'   => 'local_exfunctions_external',
				'methodname'  => 'submit_assignment',
				'classpath'   => 'local/exfunctions/externallib.php',
				'description' => 'submit assignment',
				'type'        => 'write',
		),
);

// We define the services to install as pre-build services. A pre-build service is not editable by administrator.
$services = array(
		'exfunctions' => array(
				'functions' => array (
						'local_exfunctions_view_assignment',
						'local_exfunctions_submit_assignment',),
				'requiredcapability' =>'',
				'restrictedusers' => 0,
				'enabled'=>1,
				'shortname' => "exfunctions",
				'downloadfiles' => 1
		)
);
