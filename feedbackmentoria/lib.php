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

function feedbackmentoria_users($course_id, $type) {
    global $DB, $USER;

    $where = '';

    $roleassignment = $DB->get_record('role_assignments', ['userid' => $USER->id]);
    $role = $DB->get_record('role', ['id' => $roleassignment->roleid]);

    if($type == 'teacher') {
        $where .= " AND r.shortname IN('editingteacher', 'teacher')";//GESTOR: manager            
    }

    if($type == 'student') {

        if($role->shortname == 'student') {
            $where .= " AND u.id = {$USER->id}";
        }else {
            $where .= " AND r.shortname IN('student')";                
        }

    }

    $contextid = get_context_instance(CONTEXT_COURSE, $course_id);

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

function feedbackmentoria_users_list($data) {
    $result = array();
    foreach ($data as $user) {
        $item = array();
        $item['id'] = $user->id;
        $item['fullname'] = $user->firstname . ' ' . $user->lastname;        
        $result[] = $item;
    }
    return $result;
}

function feedbackmentoria_actions($feedbackmentoria_id, $teacher, $student) {
    global $DB;
    
    $actions = $DB->get_records('feedbackmentoria_actions', array('feedbackmentoria_id' => $feedbackmentoria_id, 'student_id' => $student, 'teacher_id' => $teacher));

    return $actions;

}

function feedbackmentoria_actions_list($actions) {
    $result = array();
    foreach ($actions as $action) {
        $item = array();
        $item['id'] = $action->id;
        $item['name'] = $action->name;
        $item['is_checked'] = $action->is_checked;
        $result[] = $item;
    }
    return $result;
}

function feedbackmentoria_comments($feedbackmentoria_id, $teacher, $student) {
    global $DB;
    
    $sql = "
        SELECT 
            c.id, 
            c.comment, 
            c.timecreated,
            concat(us.firstname, ' ', us.lastname) user_send
        FROM mdl_feedbackmentoria_comments c
        JOIN mdl_user us on us.id = c.user_send_id
        ORDER BY
            c.timecreated asc
    ";

    return $DB->get_records_sql($sql);

}

function feedbackmentoria_comments_list($comments) {
    
    $result = array();
    foreach ($comments as $action) {
        $item = array();
        $item['id'] = $action->id;
        $item['comment'] = nl2br($action->comment);
        $item['user_send'] = $action->user_send;
        $item['date'] = date('d/m/Y H:i', $action->timecreated);
        $result[] = $item;
    }
    return $result;
}

function feedbackmentoria_action_create($feedbackmentoria_id, $teacher_id, $student_id, $name) {
    global $DB;

    $data->feedbackmentoria_id = $feedbackmentoria_id;
    $data->teacher_id = $teacher_id;
    $data->student_id = $student_id;
    $data->user_send_id = $USER->id;
    $data->name = $name;
    $data->is_checked = 0;
    $data->timecreated = time();
    
    $id = $DB->insert_record('feedbackmentoria_actions', $data);
    
    return array(
        'id' => $id,
        'name' => $name,
        'is_checked' => 0
    );
}

function feedbackmentoria_action_delete($action_id) {
    global $DB;

    $exists = $DB->get_record('feedbackmentoria_actions', array('id' => $action_id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('feedbackmentoria_actions', array('id' => $action_id));

    return true;
}

function feedbackmentoria_action_checked($feedbackmentoria_action_id, $is_checked) {
    global $DB;
    
    $data->id = $feedbackmentoria_action_id;
    $data->is_checked = $is_checked;
    
    if($DB->update_record('feedbackmentoria_actions', $data)) {
        return true;
    }

    return false;
}

function feedbackmentoria_comment_create($feedbackmentoria_id, $teacher_id, $student_id, $comment) {
    global $DB, $USER;

    $data->feedbackmentoria_id = $feedbackmentoria_id;
    $data->teacher_id = $teacher_id;
    $data->student_id = $student_id;
    $data->user_send_id = $USER->id;
    $data->comment = $comment;
    $data->timecreated = time();
    
    $id = $DB->insert_record('feedbackmentoria_comments', $data);

    $user_send = $DB->get_record('user', array('id' => $data->user_send_id));
    
    return array(
        'id' => $id,
        'comment' => nl2br($comment),
        'date' => date('d/m/Y H:i', $data->timecreated),
        'user_send' => $user_send->firstname . ' ' . $user_send->lastname
    );
}


function dd($var, $die = true) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die;
}