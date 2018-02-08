$(document).ready(function() {
	if($('#replace').length > 0){
		CKEDITOR.replace('replace', {
			filebrowserUploadUrl : '/ajax/ckupload'
		});
		
		// CKEDITOR.config.extraPlugins = 'gallery,documents';	
		//CKEDITOR.config.extraPlugins = 'gallery_legato';	
	}
});