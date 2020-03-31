<?php

define('AJAX_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once(__DIR__ . '/lib.php');

$action = required_param('action', PARAM_TEXT);

$id = required_param('id', PARAM_ALPHANUM);

if (!confirm_sesskey()) {
    throw new moodle_exception('invalidsesskey', 'error');
}

if (!$cm = get_coursemodule_from_id('mentoringfeedback', $id, 0, false, MUST_EXIST)) {
    throw new moodle_exception('invalidcoursemoduleid', 'error');
}

if (!$mentoringfeedback = $DB->get_record('mentoringfeedback', array('id' => $cm->instance))) {
    throw new moodle_exception('notlogged', 'mentoringfeedback');
}

if (!$course = $DB->get_record('course', array('id' => $mentoringfeedback->course))) {
    throw new moodle_exception('invalidcourseid', 'error');
}

if (!$cm = get_coursemodule_from_instance('mentoringfeedback', $mentoringfeedback->id, $course->id)) {
    throw new moodle_exception('invalidcoursemodule', 'error');
}

if (!isloggedin()) {
    throw new moodle_exception('notlogged', 'mentoringfeedback');
}

ob_start();
header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

switch ($action) {

    case 'users_list':
        $students = mentoringfeedback_users($course->id, 'student');        
        $teachers = mentoringfeedback_users($course->id, 'teacher');
        $response['teachers'] = mentoringfeedback_users_list($teachers);
        $response['students'] = mentoringfeedback_users_list($students);
        echo json_encode($response);
    break;
    
    case 'actions':
        $teacher_id = required_param('teacher_id', PARAM_ALPHANUM);
        $student_id = required_param('student_id', PARAM_ALPHANUM);
        $actions = mentoringfeedback_actions($mentoringfeedback->id, $teacher_id, $student_id);        
        $response['actions'] = mentoringfeedback_actions_list($actions);
        echo json_encode($response);
    break;

    case 'action_create':
        $teacher_id = required_param('teacher_id', PARAM_ALPHANUM);
        $student_id = required_param('student_id', PARAM_ALPHANUM);
        $name    = required_param('name', PARAM_TEXT);
        $response['action'] = mentoringfeedback_action_create($mentoringfeedback->id, $teacher_id, $student_id, $name);;
        echo json_encode($response);
    break;

    case 'action_delete':
        $action_id = required_param('action_id', PARAM_ALPHANUM);
        $result = mentoringfeedback_action_delete($action_id);
        if($result) {
            die(json_encode(['success' => true, 'message' => 'Ação removida com sucesso']));
        }
        die(json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde']));
    break;

    case 'action_checked':
        $mentoringfeedback_action_id = required_param('mentoringfeedback_action_id', PARAM_ALPHANUM);
        $is_checked = required_param('is_checked', PARAM_ALPHANUM);
        $result = mentoringfeedback_action_checked($mentoringfeedback_action_id, $is_checked);
        if($result) {
            die(json_encode(['success' => true, 'message' => 'Check com sucesso']));
        }
        die(json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde']));
    break;

    case 'messages':
        $teacher_id = required_param('teacher_id', PARAM_ALPHANUM);
        $student_id = required_param('student_id', PARAM_ALPHANUM);
        $messages = mentoringfeedback_messages($mentoringfeedback->id, $teacher_id, $student_id);        
        $response['messages'] = mentoringfeedback_messages_list($messages);
        echo json_encode($response);
    break;

    case 'message_create':
        $teacher_id = required_param('teacher_id', PARAM_ALPHANUM);
        $student_id = required_param('student_id', PARAM_ALPHANUM);
        $message    = required_param('message', PARAM_TEXT);
        $response['message'] = mentoringfeedback_message_create($mentoringfeedback->id, $teacher_id, $student_id, $message);
        echo json_encode($response);
    break;

    default:
        break;
}
