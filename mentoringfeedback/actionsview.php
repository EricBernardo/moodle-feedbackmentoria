<?php

$role_shortname = null;

$roleassignment = $DB->get_record('role_assignments', ['userid' => $USER->id]);

$role = $DB->get_record('role', ['id' => $roleassignment->roleid]);

if(isset($role)) {
	$role_shortname = $role->shortname;
}

if($role_shortname !== 'student') : ?>

<div class="actions">
	<div class="panel panel-default">
	  <div class="panel-heading">Ações</div>
	  <div class="panel-body">
	  	<div class="overflow"></div>
	  	<hr/>
	  	<form id="form-action" class="form-submit" action="javascript:actionCreate()">
	  		<label>Adicionar ações</label>
			<input type="text" name="action-name" />
			<input type="submit" class="btn btn-default" value="Enviar" />
		</form>
	  </div>
	</div>
</div>

<?php endif; ?>