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
 * Library of interface functions and constants.
 *
 * @package     mod_feedbackmentoria
 * @copyright   2020 Eric Bernardo <eric.sousa@cwi.com.br>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Return if the plugin supports $feature.
 *
 * @param string $feature Constant representing the feature.
 * @return true | null True if the feature is supported, null otherwise.
 */
function feedbackmentoria_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_feedbackmentoria into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_feedbackmentoria_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function feedbackmentoria_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('feedbackmentoria', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_feedbackmentoria in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_feedbackmentoria_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function feedbackmentoria_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('feedbackmentoria', $moduleinstance);
}

/**
 * Removes an instance of the mod_feedbackmentoria from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function feedbackmentoria_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('feedbackmentoria', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('feedbackmentoria', array('id' => $id));

    return true;
}

function feedbackmentoria_get($p_courseid, $p_type) {
    global $DB, $USER;

    $where = '';

    $roleassignment = $DB->get_record('role_assignments', ['userid' => $USER->id]);
    $role = $DB->get_record('role', ['id' => $roleassignment->roleid]);

    if($role->shortname == 'student') {

        $where .= " AND u.id = {$USER->id}";

    } else {

        if($p_type == 'teacher') {
            $where .= " AND r.shortname IN('editingteacher', 'teacher')";
        }

    }

    if($p_type == 'student') {
        $where .= " AND r.shortname IN('student')";
    }

    $contextid = get_context_instance(CONTEXT_COURSE, $p_courseid);

    $sql = "
        SELECT u.id, u.firstname, u.lastname
        FROM mdl_user u
        JOIN mdl_role_assignments ra ON u.id = ra.userid
        JOIN mdl_role r ON r.id = ra.roleid
        WHERE ra.contextid = {$contextid->id}
        $where
        ORDER BY
            u.firstname, u.lastname
    ";

    return $DB->get_records_sql($sql);
}

function feedbackmentoria_format_list($data) {
    $result = array();
    foreach ($data as $user) {
        $item = array();
        $item['id'] = $user->id;
        $item['fullname'] = $user->firstname . ' ' . $user->lastname;        
        $result[] = $item;
    }
    return $result;
}

function feedbackmentoria_get_actions($courseid) {

    global $DB, $USER;

    $roleassignment = $DB->get_record('role_assignments', ['userid' => $USER->id]);
    $role = $DB->get_record('role', ['id' => $roleassignment->roleid]);

    if($role->shortname == 'student') {
      $actions = $DB->get_records('feedbackmentoria_actions', array('student_id' => $USER->id));
    }

    return $actions;
}

function feedbackmentoria_format_actionlist($actions) {
    $result = array();
    foreach ($actions as $action) {
        $item = array();
        $item['id'] = $action->id;
        $item['name'] = $action->name;
        $result[] = $item;
    }
    return $result;
}

function print_r2($var, $die = true) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die;
}