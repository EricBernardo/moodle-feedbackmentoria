<?php
// global $USER, $SESSION;

// $roleassignment = $DB->get_record('role_assignments', ['userid' => $USER->id]);
// $role = $DB->get_record('role', ['id' => $roleassignment->roleid]);


// if($role->shortname == 'student') {
// 	$actions = $DB->get_records('feedbackmentoria_actions', array('student_id' => $USER->id));
// }
// print_r2($actions);

// $id      = required_param('id', PARAM_ALPHANUM);
// $cm = \context_module::instance($id);
// $students = get_enrolled_users($cm, null, 0, 'u.*', null, 0, 0, true);
// print_r2($USER->id);

?>

<div class="acoes">
	<div class="panel panel-default">
	  <div class="panel-heading">Ações</div>
	  <div class="panel-body">
	  	<div class="overflow"></div>
	  	<hr/>
	  	<div class="form-submit">
	  		<label>Adicionar ações</label>
			<input type="text" name="action-name" />
			<input type="button" class="btn btn-default" onClick="setAction()" value="Adicionar" />
		</div>
	  </div>
	</div>
</div>