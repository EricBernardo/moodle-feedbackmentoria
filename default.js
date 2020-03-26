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

const params = getParams(window.location.href);

const id = params.id;

function getListStudents() {
	
	let select = $('select[name="student"]');
	
	select.attr('disabled', true);

	$.ajax('ajax.php', { 
		data: { action: 'students', id: id },
		type: 'post',
		dataType: 'json'
	}).done(function(data) {
				
		select.html('<option value="">Aluno</option>')

	    data.students.map(function(value) {
	    	select.append('<option value="' + value.id + '">' + value.fullname + '</option>')
	    });

	}).fail(function() {
	    // alert( "error" );
	}).always(function() {
	    select.attr('disabled', false);
	});

}

$(document).ready(function(){
	getListStudents();
});