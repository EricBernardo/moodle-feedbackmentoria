<?php

define('AJAX_SCRIPT', true);

require(__DIR__.'/../../config.php');
require_once(__DIR__ . '/lib.php');

$message_id = required_param('message_id', PARAM_ALPHANUM);

$message = $DB->get_record('mentoringfeedback_messages', array('id' => $message_id));

if($message->file_name) {
	header("Content-Disposition: attachment; filename={$message->file_name}");
	ob_clean();
	flush();
	echo ($message->file);
	exit;
} else {
	die("Arquivo não encontrado");
}