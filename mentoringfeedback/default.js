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
	getMessages();
}

function getActions() {
	
	let el = $('.actions .panel-body .overflow');
		
	el.html('Carregando...');

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

	    setTimeout(function(){
	    	el.html(html);
	    }, 1000);

	}).fail(function() {
	}).always(function() {
	});

}

function getMessages() {

	let count = $('.feedback .panel-body .overflow p').length;

	let el = $('.feedback .panel-body .overflow');
		
	el.html('Carregando...');

	$.ajax('ajax.php', { 
		data: { 
			id: __id,
			action: 'messages',
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

	    data.messages.map(function(value) {

	    	if(count > 0) {
		    	html += '<hr />';
		    }

			html += '<p>'
				html +='<b>' + value.user_send + ' <span class="date">(' + value.date + ')</span></b>';
				html += value.message;
				if(value.attachment) {
					html += '<a target="_blank" class="attachment" href="' + value.attachment + '">Baixar Arquivo</a>'
				}
			html += '</p>'

			count++;

	    });

	    setTimeout(function(){
	    	el.html(html);
	    	scrollBottom(el);
	    }, 1000);

	}).fail(function() {
	}).always(function() {
	});

}

function setModal(title, description, button1 = null, button2 = null) {
	
	const el = $('.modal');

	el.find('.modal-title').text(title);
	el.find('.modal-body').text(description);

	if(button1) {
		el.find('.btn-primary').attr('onClick', button1.onClick).html(button1.text).show();
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

function actionCreate() {
	
	const input = $('#form-action').find('input[name="action-name"]');

	let value = input.val();

	if(!value) {
		setModal('Atenção', 'Preencha o campo "Adicionar ações".', null, 'Fechar')
		return;
	}
	
	$('#form-action').find('input[type="submit"]').attr('disabled', true);

	$.ajax('ajax.php', { 
		data: { 
			id: __id,
			action: 'action_create',
			student_id: $('select[name="student"]').val(),
			teacher_id: $('select[name="teacher"]').val(),
			name: value,
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
    	html += ('<div class="btn-remover" onClick="confirmDelete(' + data.action.id + ')"><i class="icon fa fa-trash fa-fw " aria-hidden="true"></i><span class="menu-action-text" id="actionmenuaction-13">Apagar</span></div>');
    	html += ('</td>');

    	html += ('</tr>');

		$('.actions .overflow').find('table tbody').append(html)

		input.val('');

	}).fail(function() {
	}).always(function() {
		$('#form-action').find('input[type="submit"]').attr('disabled', false);
	});

}

function messageCreate() {

	let el = $('.feedback .panel-body .overflow');
	
	const textarea = $('#form-message').find('textarea[name="message"]');

	let value = textarea.val();

	if(!value) {
		setModal('Atenção', 'Preencha o campo "Adicionar comentário".', null, 'Fechar')
		return;
	}

	var formData = new FormData($('#form-message')[0]);

	formData.append('action', 'message_create');
	formData.append('id', __id);
	formData.append('action', 'message_create');
	formData.append('student_id', $('select[name="student"]').val());
	formData.append('teacher_id', $('select[name="teacher"]').val());
	formData.append('message', value);
	formData.append('sesskey', M.cfg.sesskey);

    $.ajax({
        url: 'ajax.php',
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function(data) {
            
			if(typeof(data.error) == 'string') {
				setModal('Error', data.error, null, 'Fechar'); return;
			}

		    var html = '';

			if(el.find('p').length > 0) {
		    	html += '<hr />';
		    }

			html += '<p>'
				html +='<b>' + data.message.user_send + ' <span class="date">(' + data.message.date + ')</span></b>';
				html += data.message.message;
				if(data.message.attachment) {
					html += '<a target="_blank" class="attachment" href="' + data.message.attachment + '">Baixar Arquivo</a>'
				}
			html += '</p>';

			el.append(html);

			textarea.val('');

			$('#file').val('');
			uploadFile();

			scrollBottom(el);
        },
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', function() {
                }, false);
            }
            return myXhr;
        }
    });
    
}

function actionChecked(el) {

	el.attr('disabled', true);
	
	$.ajax('ajax.php', { 
		data: { 
			action: 'action_checked',
			id: __id,
			mentoringfeedback_action_id: el.val(),
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

	var button1 = {
		text: 'Sim',
		onClick: 'actionDelete(' + action_id + '); $(\'.modal\').modal(\'hide\')'
	}

	setModal('Confirmação', 'Tem a certeza de que pretende apagar essa ação?', button1, 'Não');
}

function actionDelete(action_id) {

	var parents = $('.actions [type="checkbox"][value="' + action_id + '"]').parents('tr');
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

function scrollBottom(el) {
	el.scrollTop(el[0].scrollHeight);
}

function uploadFile() {
	var el = $('#file');

	if(el.val()) {
		$('.custom-file .custom-file-label').text(el.val());
	} else {
		$('.custom-file .custom-file-label').text('Nenhum arquivo selecionado');
	}
}

$(document).ready(function(){
	getOptionsFilter();
});