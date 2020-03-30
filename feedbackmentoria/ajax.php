<?php

define('AJAX_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once(__DIR__ . '/lib.php');

$action = required_param('action', PARAM_TEXT);

$id = required_param('id', PARAM_ALPHANUM);

if (!confirm_sesskey()) {
    throw new moodle_exception('invalidsesskey', 'error');
}

if (!$cm = get_coursemodule_from_id('feedbackmentoria', $id, 0, false, MUST_EXIST)) {
    throw new moodle_exception('invalidcoursemoduleid', 'error');
}

if (!$feedbackmentoria = $DB->get_record('feedbackmentoria', array('id' => $cm->instance))) {
    throw new moodle_exception('notlogged', 'feedbackmentoria');
}

if (!$course = $DB->get_record('course', array('id' => $feedbackmentoria->course))) {
    throw new moodle_exception('invalidcourseid', 'error');
}

if (!$cm = get_coursemodule_from_instance('feedbackmentoria', $feedbackmentoria->id, $course->id)) {
    throw new moodle_exception('invalidcoursemodule', 'error');
}

if (!isloggedin()) {
    throw new moodle_exception('notlogged', 'feedbackmentoria');
}

ob_start();
header('Expires: Sun, 28 Dec 1997 09:32:45 GMT');
header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');
header('Content-Type: text/html; charset=utf-8');

switch ($action) {

    case 'users_list':
        $students = feedbackmentoria_users($course->id, 'student');        
        $teachers = feedbackmentoria_users($course->id, 'teacher');
        $response['students'] = feedbackmentoria_users_list($students);
        $response['teachers'] = feedbackmentoria_users_list($teachers);
        echo json_encode($response);
    break;
    
    case 'actions':
        $teacher_id = required_param('teacher_id', PARAM_ALPHANUM);
        $student_id = required_param('student_id', PARAM_ALPHANUM);
        $actions = feedbackmentoria_actions($feedbackmentoria->id, $teacher_id, $student_id);        
        $response['actions'] = feedbackmentoria_actions_list($actions);
        echo json_encode($response);
    break;

    case 'action_create':
        $teacher_id = required_param('teacher_id', PARAM_ALPHANUM);
        $student_id = required_param('student_id', PARAM_ALPHANUM);
        $name    = required_param('name', PARAM_TEXT);
        $response['action'] = feedbackmentoria_action_create($feedbackmentoria->id, $teacher_id, $student_id, $name);;
        echo json_encode($response);
    break;

    case 'action_delete':
        $action_id = required_param('action_id', PARAM_ALPHANUM);
        $result = feedbackmentoria_action_delete($action_id);
        if($result) {
            die(json_encode(['success' => true, 'message' => 'Ação removida com sucesso']));
        }
        die(json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde']));
    break;

    case 'action_checked':
        $feedbackmentoria_action_id = required_param('feedbackmentoria_action_id', PARAM_ALPHANUM);
        $is_checked = required_param('is_checked', PARAM_ALPHANUM);
        $result = feedbackmentoria_action_checked($feedbackmentoria_action_id, $is_checked);
        if($result) {
            die(json_encode(['success' => true, 'message' => 'Check com sucesso']));
        }
        die(json_encode(['success' => false, 'message' => 'Ocorreu um erro. Tente novamente mais tarde']));
    break;

    case 'comments':
        $teacher_id = required_param('teacher_id', PARAM_ALPHANUM);
        $student_id = required_param('student_id', PARAM_ALPHANUM);
        $comments = feedbackmentoria_comments($feedbackmentoria->id, $teacher_id, $student_id);        
        $response['comments'] = feedbackmentoria_comments_list($comments);
        echo json_encode($response);
    break;

    case 'comment_create':
        $teacher_id = required_param('teacher_id', PARAM_ALPHANUM);
        $student_id = required_param('student_id', PARAM_ALPHANUM);
        $comment    = required_param('comment', PARAM_TEXT);
        $response['comment'] = feedbackmentoria_comment_create($feedbackmentoria->id, $teacher_id, $student_id, $comment);
        echo json_encode($response);
    break;

    default:
        break;
}
