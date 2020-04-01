<div class="feedback">
	<div class="panel panel-default">
		<div class="panel-heading">Feedback</div>
		<div class="panel-body">
			<div class="overflow"></div>
			<hr/>
			<form id="form-message" class="form-submit" action="javascript:messageCreate()" enctype="multipart/form-data">
				<label>Adicionar comentário</label>
				<textarea name="message"></textarea>
				<div class="custom-file">
					<input type="file" class="custom-file-input" id="file" name="file" onchange="uploadFile()">
					<label class="custom-file-label" for="file">Nenhum arquivo selecionado</label>
				</div>
				<input type="submit" class="btn btn-default" value="Enviar" />
			</form>
		</div>
	</div>
</div>