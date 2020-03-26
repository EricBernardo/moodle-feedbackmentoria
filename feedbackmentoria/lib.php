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

// Functions that require some SQL.

/**
 * @global object
 * @param int $chatid
 * @param int $groupid
 * @param int $groupingid
 * @return array
 */
function feedbackmentoria_get_students($courseid) {
    global $DB;
    $contextid = get_context_instance(CONTEXT_COURSE, $courseid);

    $sql = "
        SELECT u.id, u.firstname, u.lastname
        FROM mdl_user u, mdl_role_assignments r
        WHERE u.id = r.userid AND r.contextid = {$contextid->id}
    ";

    return $DB->get_records_sql($sql);
}

/**
 * @global object $DB
 * @global object $CFG
 * @global object $COURSE
 * @global object $OUTPUT
 * @param object $students
 * @param object $course
 * @return array return formatted user list
 */
function feedbackmentoria_format_studentlist($students, $course) {
    global $CFG, $DB, $COURSE, $OUTPUT;
    $result = array();
    foreach ($students as $user) {
        $item = array();
        $item['id'] = $user->id;
        $item['fullname'] = $user->firstname . ' ' . $user->lastname;        
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