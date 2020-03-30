<?php

require(__DIR__.'/../../config.php');
require_once(__DIR__.'/lib.php');

// Course_module ID, or
$id = optional_param('id', 0, PARAM_INT);

// ... module instance id.
$f  = optional_param('f', 0, PARAM_INT);

if ($id) {
    $cm             = get_coursemodule_from_id('feedbackmentoria', $id, 0, false, MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $cm->course), '*', MUST_EXIST);
    $moduleinstance = $DB->get_record('feedbackmentoria', array('id' => $cm->instance), '*', MUST_EXIST);
} else if ($f) {
    $moduleinstance = $DB->get_record('feedbackmentoria', array('id' => $n), '*', MUST_EXIST);
    $course         = $DB->get_record('course', array('id' => $moduleinstance->course), '*', MUST_EXIST);
    $cm             = get_coursemodule_from_instance('feedbackmentoria', $moduleinstance->id, $course->id, false, MUST_EXIST);
} else {
    print_error(get_string('missingidandcmid', 'mod_feedbackmentoria'));
}

require_login($course, true, $cm);

$modulecontext = context_module::instance($cm->id);

$event = \mod_feedbackmentoria\event\course_module_viewed::create(array(
    'objectid' => $moduleinstance->id,
    'context' => $modulecontext
));
$event->add_record_snapshot('course', $course);
$event->add_record_snapshot('feedbackmentoria', $moduleinstance);
$event->trigger();

$PAGE->set_url('/mod/feedbackmentoria/view.php', array('id' => $cm->id));
$PAGE->set_title(format_string($moduleinstance->name));
$PAGE->set_heading(format_string($course->fullname));
$PAGE->set_context($modulecontext);

$PAGE->requires->css('/mod/feedbackmentoria/default.css?time=' . time());

$PAGE->requires->js('/mod/feedbackmentoria/default.js?time=' . time());

echo $OUTPUT->header();

include($CFG->dirroot.'/mod/feedbackmentoria/detailsview.php');

include($CFG->dirroot.'/mod/feedbackmentoria/filterview.php');

include($CFG->dirroot.'/mod/feedbackmentoria/feedbackview.php');

include($CFG->dirroot.'/mod/feedbackmentoria/actionsview.php');

include($CFG->dirroot.'/mod/feedbackmentoria/modalview.php');

echo $OUTPUT->footer();