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

	    getListActions();

	}).fail(function() {
	    // alert( "error" );
	}).always(function() {
	    select_student.attr('disabled', false);
	    select_teacher.attr('disabled', false);
	});

}

function getListActions() {
	
	let el = $('.acoes .panel-body .overflow');
		
	el.html('')

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

	    data.actions.map(function(value) {
	    	el.append('<div class="checkbox"><label><input type="checkbox" value="' + value.id + '">' + value.name + '</label></div>')
	    });

	}).fail(function() {
	}).always(function() {
	});

}

$(document).ready(function(){
	getOptionsFilter();
});