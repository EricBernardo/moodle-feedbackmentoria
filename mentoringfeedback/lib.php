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
 * @package     mod_mentoringfeedback
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
function mentoringfeedback_supports($feature) {
    switch ($feature) {
        case FEATURE_MOD_INTRO:
            return true;
        default:
            return null;
    }
}

/**
 * Saves a new instance of the mod_mentoringfeedback into the database.
 *
 * Given an object containing all the necessary data, (defined by the form
 * in mod_form.php) this function will create a new instance and return the id
 * number of the instance.
 *
 * @param object $moduleinstance An object from the form.
 * @param mod_mentoringfeedback_mod_form $mform The form.
 * @return int The id of the newly inserted record.
 */
function mentoringfeedback_add_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timecreated = time();

    $id = $DB->insert_record('mentoringfeedback', $moduleinstance);

    return $id;
}

/**
 * Updates an instance of the mod_mentoringfeedback in the database.
 *
 * Given an object containing all the necessary data (defined in mod_form.php),
 * this function will update an existing instance with new data.
 *
 * @param object $moduleinstance An object from the form in mod_form.php.
 * @param mod_mentoringfeedback_mod_form $mform The form.
 * @return bool True if successful, false otherwise.
 */
function mentoringfeedback_update_instance($moduleinstance, $mform = null) {
    global $DB;

    $moduleinstance->timemodified = time();
    $moduleinstance->id = $moduleinstance->instance;

    return $DB->update_record('mentoringfeedback', $moduleinstance);
}

/**
 * Removes an instance of the mod_mentoringfeedback from the database.
 *
 * @param int $id Id of the module instance.
 * @return bool True if successful, false on failure.
 */
function mentoringfeedback_delete_instance($id) {
    global $DB;

    $exists = $DB->get_record('mentoringfeedback', array('id' => $id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('mentoringfeedback', array('id' => $id));

    return true;
}

function mentoringfeedback_users($course_id, $type) {
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

function mentoringfeedback_users_list($data) {
    $result = array();
    foreach ($data as $user) {
        $item = array();
        $item['id'] = $user->id;
        $item['fullname'] = $user->firstname . ' ' . $user->lastname;        
        $result[] = $item;
    }
    return $result;
}

function mentoringfeedback_actions($mentoringfeedback_id, $teacher, $student) {
    global $DB;
    
    $actions = $DB->get_records('mentoringfeedback_actions', array('mentoringfeedback_id' => $mentoringfeedback_id, 'student_id' => $student, 'teacher_id' => $teacher));

    return $actions;

}

function mentoringfeedback_actions_list($actions) {
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

function mentoringfeedback_messages($mentoringfeedback_id, $teacher_id, $student_id) {
    global $DB;
    
    $sql = "
        SELECT 
            c.id, 
            c.message, 
            c.timecreated,
            c.attachment,
            concat(us.firstname, ' ', us.lastname) user_send
        FROM mdl_mentoringfeedback_messages c
        JOIN mdl_user us on us.id = c.user_send_id
        WHERE c.teacher_id = {$teacher_id}
        AND c.student_id = {$student_id}
        AND c.mentoringfeedback_id = {$mentoringfeedback_id}
        ORDER BY
            c.timecreated asc
    ";

    return $DB->get_records_sql($sql);

}

function mentoringfeedback_messages_list($messages) {
    global $CFG;    
    $result = array();
    foreach ($messages as $message) {
        $item = array();
        $item['id'] = $message->id;
        $item['message'] = nl2br($message->message);
        $item['user_send'] = $message->user_send;
        $item['attachment'] = $message->attachment ? $CFG->wwwroot . '/mod/mentoringfeedback/uploads/' . $message->attachment : null;
        $item['date'] = date('d/m/Y H:i', $message->timecreated);
        $result[] = $item;
    }
    return $result;
}

function mentoringfeedback_action_create($mentoringfeedback_id, $teacher_id, $student_id, $name) {
    global $DB;

    $data->mentoringfeedback_id = $mentoringfeedback_id;
    $data->teacher_id = $teacher_id;
    $data->student_id = $student_id;
    $data->user_send_id = $USER->id;
    $data->name = $name;
    $data->is_checked = 0;
    $data->timecreated = time();
    
    $id = $DB->insert_record('mentoringfeedback_actions', $data);
    
    return array(
        'id' => $id,
        'name' => $name,
        'is_checked' => 0
    );
}

function mentoringfeedback_action_delete($action_id) {
    global $DB;

    $exists = $DB->get_record('mentoringfeedback_actions', array('id' => $action_id));
    if (!$exists) {
        return false;
    }

    $DB->delete_records('mentoringfeedback_actions', array('id' => $action_id));

    return true;
}

function mentoringfeedback_action_checked($mentoringfeedback_action_id, $is_checked) {
    global $DB;
    
    $data->id = $mentoringfeedback_action_id;
    $data->is_checked = $is_checked;
    
    if($DB->update_record('mentoringfeedback_actions', $data)) {
        return true;
    }

    return false;
}

function mentoringfeedback_message_create($mentoringfeedback_id, $teacher_id, $student_id, $message) {
    global $DB, $USER, $CFG;

    $data->mentoringfeedback_id = $mentoringfeedback_id;
    $data->teacher_id = $teacher_id;
    $data->student_id = $student_id;
    $data->user_send_id = $USER->id;
    $data->message = $message;    
    $data->timecreated = time();
    $data->attachment = null;

    if(isset($_FILES['file']['name'])) {

        $file_name = md5($data->timecreated . '-' . $USER->id) . '-' . basename($_FILES['file']['name']);
        
        $uploadfile = './uploads/' . $file_name;

        if(move_uploaded_file($_FILES['file']['tmp_name'], $uploadfile)) {
            $data->attachment = $file_name;
        }

    }
    
    $id = $DB->insert_record('mentoringfeedback_messages', $data);

    $user_send = $DB->get_record('user', array('id' => $data->user_send_id));
    
    return array(
        'id' => $id,
        'message' => nl2br($message),
        'date' => date('d/m/Y H:i', $data->timecreated),
        'user_send' => $user_send->firstname . ' ' . $user_send->lastname,
        'attachment' =>  $data->attachment ? $CFG->wwwroot . '/mod/mentoringfeedback/uploads/' . $data->attachment : null
    );
}


function dd($var, $die = true) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
    die;
}