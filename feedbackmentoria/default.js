var getParams = function (url) {
	var params = {};
	var parser = document.createElement('a');
	parser.href = url;
	var query = parser.search.substring(1);
	var vars = query.split('&');
	for (var i = 0; i < vars.length; i++) {
		var pair = vars[i].split('=');
		params[pair[0]] = decodeURIComponent(pair[1]);
	}
	return params;
}

const __params = getParams(window.location.href);

const __id = __params.id;

function getOptionsFilter() {
	
	let select_student = $('select[name="student"]');	
	select_student.attr('disabled', true);

	let select_teacher = $('select[name="teacher"]');	
	select_teacher.attr('disabled', true);

	$.ajax('ajax.php', { 
		data: { action: 'users_list', id: __id, sesskey: M.cfg.sesskey },
		type: 'get',
		dataType: 'json'
	}).done(function(data) {
		
		if(typeof(data.error) == 'string') {
			setModal('Error', data.error, null, 'Fechar'); return;
		}
		 
	    data.students.map(function(value) {
	    	select_student.append('<option value="' + value.id + '">' + value.fullname + '</option>')
	    });
	    data.teachers.map(function(value) {
	    	select_teacher.append('<option value="' + value.id + '">' + value.fullname + '</option>')
	    });

	    onSubmit();

	}).fail(function() {
	}).always(function() {
	    select_student.attr('disabled', false);
	    select_teacher.attr('disabled', false);
	});

}

function onSubmit() {
	getActions();
}

function getActions() {
	
	let el = $('.acoes .panel-body .overflow');
		
	el.html('');

	$.ajax('ajax.php', { 
		data: { 
			id: __id,
			action: 'actions',
			student_id: $('select[name="student"]').val(),
			teacher_id: $('select[name="teacher"]').val(),
			sesskey: M.cfg.sesskey
		},
		type: 'get',
		dataType: 'json'
	}).done(function(data) {

		if(typeof(data.error) == 'string') {
			setModal('Error', data.error, null, 'Fechar'); return;
		}				

		let html = '';

		html += ('<table style="width:99%"><tbody>');

	    data.actions.map(function(value) {
	    	
	    	html += ('<tr>');
	    	
	    	html += ('<td width="2%">');
	    	html += ('<input onClick="actionChecked($(this))" type="checkbox" ' + (value.is_checked == 1 ? 'checked' : '') + ' value="' + value.id + '"/>');
	    	html += ('</td>');

	    	html += ('<td>');
	    	html += (value.name);
	    	html += ('</td>');

	    	html += ('<td width="20%">');
	    	html += ('<div class="btn-remover" onClick="confirmDelete(' + value.id + ')"><i class="icon fa fa-trash fa-fw " aria-hidden="true"></i><span class="menu-action-text" id="actionmenuaction-13">Apagar</span></div>');
	    	html += ('</td>');

	    	html += ('</tr>');

	    });

	    html += ('</tbody></table>');

	    el.html(html);

	}).fail(function() {
	}).always(function() {
	});

}

function setModal(title, description, button1 = null, button2 = null) {
	
	const el = $('.modal');

	el.find('.modal-title').text(title);
	el.find('.modal-body').text(description);

	if(button1) {
		el.find('.btn-primary').html(button1).show();
	} else {
		el.find('.btn-primary').hide();
	}
	
	if(button2) {
		el.find('.btn-secondary').html(button2).show();
	} else {
		el.find('.btn-secondary').hide();
	}

	el.modal()

}

function actionCreate(el) {

	const input = el.parents('form').find('input[name="action-name"]');

	let name = input.val();

	if(!name) {
		setModal('Atenção', 'Preencha o campo "Adicionar ações".', null, 'Fechar')
		return;
	}
	
	el.attr('disabled', true);

	$.ajax('ajax.php', { 
		data: { 
			id: __id,
			action: 'action_create',
			student_id: $('select[name="student"]').val(),
			teacher_id: $('select[name="teacher"]').val(),
			name: name,
			sesskey: M.cfg.sesskey
		},
		type: 'post',
		dataType: 'json'
	}).done(function(data) {

		if(typeof(data.error) == 'string') {
			setModal('Error', data.error, null, 'Fechar'); return;
		}

		let html = '';				

		html += ('<tr>');
	    	
    	html += ('<td width="2%">');
    	html += ('<input onClick="actionChecked($(this))" type="checkbox" ' + (data.action.is_checked == 1 ? 'checked' : '') + ' value="' + data.action.id + '"/>');
    	html += ('</td>');

    	html += ('<td>');
    	html += (data.action.name);
    	html += ('</td>');

    	html += ('<td width="20%">');
    	html += ('<div class="btn-remover" onClick="actionDelete($(this))"><i class="icon fa fa-trash fa-fw " aria-hidden="true"></i><span class="menu-action-text" id="actionmenuaction-13">Apagar</span></div>');
    	html += ('</td>');

    	html += ('</tr>');

		$('.acoes .overflow').find('table tbody').append(html)

		input.val('');

	}).fail(function() {
	}).always(function() {
		el.attr('disabled', false);
	});

}

function actionChecked(el) {

	el.attr('disabled', true);
	
	$.ajax('ajax.php', { 
		data: { 
			action: 'action_checked',
			id: __id,
			feedbackmentoria_action_id: el.val(),
			is_checked: el.is(':checked') ? 1 : 0,
			sesskey: M.cfg.sesskey
		},
		type: 'post',
		dataType: 'json'
	}).done(function(data) {
		if(typeof(data.error) == 'string') {
			setModal('Error', data.error, null, 'Fechar'); return;
		}
	}).fail(function() {
	}).always(function() {
		el.attr('disabled', false);
	});

}

function confirmDelete(action_id) {
	setModal('Atenção', 'Tem a certeza de que pretende apagar essa ação?', '<span onClick="actionDelete(' + action_id + '); $(\'.modal\').modal(\'hide\');">Sim</span>', 'Não');
}

function actionDelete(action_id) {

	var parents = $('.acoes [type="checkbox"][value="' + action_id + '"]').parents('tr');
	var action_id = parents.find('input[value]').val();
	
	$.ajax('ajax.php', { 
		data: { 
			action: 'action_delete',
			id: __id,
			action_id: action_id,
			sesskey: M.cfg.sesskey
		},
		type: 'post',
		dataType: 'json'
	}).done(function(data) {
		if(typeof(data.error) == 'string') {
			setModal('Error', data.error, null, 'Fechar'); return;
		} else {
			parents.remove();
		}
	}).fail(function() {
	}).always(function() {			
	});

}

$(document).ready(function(){
	getOptionsFilter();
});