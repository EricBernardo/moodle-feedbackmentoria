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
		data: { action: 'options_filter', id: __id },
		type: 'get',
		dataType: 'json'
	}).done(function(data) {
				
	    data.students.map(function(value) {
	    	select_student.append('<option value="' + value.id + '">' + value.fullname + '</option>')
	    });

	    data.teachers.map(function(value) {
	    	select_teacher.append('<option value="' + value.id + '">' + value.fullname + '</option>')
	    });

	    onSubmit();

	}).fail(function() {
	    // alert( "error" );
	}).always(function() {
	    select_student.attr('disabled', false);
	    select_teacher.attr('disabled', false);
	});

}

function onSubmit() {
	getListActions();
}

function getListActions() {
	
	let el = $('.acoes .panel-body .overflow');
		
	el.html('');

	$.ajax('ajax.php', { 
		data: { 
			id: __id,
			action: 'actions',
			student_id: $('select[name="student"]').val(),
			teacher_id: $('select[name="teacher"]').val()			
		},
		type: 'get',
		dataType: 'json'
	}).done(function(data) {				

		let html = '';

		html += ('<table style="width:99%"><tbody>');

	    data.actions.map(function(value) {
	    	
	    	html += ('<tr>');
	    	
	    	html += ('<td>');
	    	html += ('<input onClick="setChecked($(this))" type="checkbox" ' + (value.is_checked == 1 ? 'checked' : '') + ' value="' + value.id + '"/>');
	    	html += ('</td>');

	    	html += ('<td>');
	    	html += (value.name);
	    	html += ('</td>');

	    	html += ('<td>');
	    	html += ('<button class="btn btn-danger btn-remover">x</button>');
	    	html += ('</td>');

	    	html += ('</tr>');

	    });

	    html += ('</tbody></table>');

	    el.html(html);

	}).fail(function() {
	}).always(function() {
	});

}

function setAction() {

	let name = $('input[name="action-name"]').val();

	if(!name) {
		alert('Preencha o campo "Adicionar ações".')
		return;
	}
	
	let el = $('.acoes .panel-body .overflow');

	$.ajax('ajax.php', { 
		data: { 
			id: __id,
			action: 'set_action',
			student_id: $('select[name="student"]').val(),
			teacher_id: $('select[name="teacher"]').val(),
			name: name
		},
		type: 'post',
		dataType: 'json'
	}).done(function(data) {

		let html = '';				

		html += ('<tr>');
	    	
    	html += ('<td>');
    	html += ('<input onClick="setChecked($(this))" type="checkbox" ' + (data.action.is_checked == 1 ? 'checked' : '') + ' value="' + data.action.id + '"/>');
    	html += ('</td>');

    	html += ('<td>');
    	html += (data.action.name);
    	html += ('</td>');

    	html += ('<td>');
    	html += ('<button class="btn btn-danger btn-remover">x</button>');
    	html += ('</td>');

    	html += ('</tr>');

		el.find('table tbody').append(html)

	}).fail(function() {
	}).always(function() {
	});

}

function setChecked(el) {
	
	$.ajax('ajax.php', { 
		data: { 
			action: 'checked_action',
			id: __id,
			feedbackmentoria_action_id: el.val(),
			is_checked: el.is(':checked') ? 1 : 0
		},
		type: 'post',
		dataType: 'json'
	}).done(function() {				
	}).fail(function() {
	}).always(function() {
	});

}

$(document).ready(function(){
	getOptionsFilter();
});