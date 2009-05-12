$(document).ready(function() {
	
	multiUpload.init();
	

	$("#removefield").click(function(e){
		$("#body div.file:last").prev().remove();
		e.preventDefault();
		return( false );
	});
	
});


var multiUpload = {
	errorMessages: {
		noUploadFile: 'You need to upload at least one file.', 
		wrongFileType: 'Supported file types are .js or .css only.',
		mixedFile: 'You can only compress one file type at a time, please upload either .js or .css files.',
		noOutputFile: 'Please provide an output file name.'
	},
	currentIndex: 0,
	hints: [],
	
	init: function () {
		var that = this;
		var container = $('#body');
		
		$('#hint-content li').each(function (i) {
			that.hints[i] = this.innerHTML;
		});
		
		that.populate($('div.file:last', container), true);
		
		$('#add-file').click(function(e) {
			var prev = $('div.file:last', container);
			container.append(prev.clone());
			that.populate(container.find('div.file:last'));
			e.preventDefault();
			this.blur();
		});
		
		$('#compress-button').click(that.validateInput);
		
	},
	populate: function (row, initial) {
		var that = this;
		var index = that.currentIndex;
		var hint = index > 3 ? that.hints[3] : that.hints[index];
		index++;
		$('span.index', row).html(index);
		$('span.hint', row).remove();
		if (hint) $('p.row', row).before('<span class="hint">'+ hint +'</span>');
		$("input[type='file']", row)
			.val('')
			.change(function (e) {
				$('#name-suffix').val($(this).val().toLowerCase().split('.').pop());
			});
		
		$('a.remove-field', row).click(function (e) {
			$(this).parents('div.file').remove();
			that.enableRemoveButtons();
			e.preventDefault();
		});
		
		if (!initial) {
			row.hide();
			row.fadeIn();
		}

		that.enableRemoveButtons();
		
		that.currentIndex++;
	},
	
	enableRemoveButtons: function () {
		var buttons = $('#body .file a.remove-field');
		if (buttons.length === 1) {
			buttons.hide();
		}
		else {
			buttons.show();
		}
	},
	
	validateInput: function (e) {
		
		var errorType = { noUploadFile: false, wrongFileType: false, mixedFile: false, noOutputFile: false };
		var errorMsg = '';
		var fileType = $('#name-suffix').val();
		var uploadCount = 0;
		
		$('#body input').each(function (i) {
			var el = $(this);
			var ext = el.val() || '';
			if (ext) uploadCount++;
			
			ext = ext.toLowerCase().split('.').pop();
			
			if (!(/js|css/.test(ext))) {
				errorType.wrongFileType = true;
			}
			
			if (fileType !== ext) errorType.mixedFile = true;
		});
		
		$('#name-suffix').val(fileType);
		
		if ($('#name').val() === '') errorType.noOutputFile = true;
		if (uploadCount === 0) errorType.noUploadFile = true;
		
		for (var key in errorType) {
			if (errorType[key]) errorMsg += multiUpload.errorMessages[key] + '\n\n';
		}
		
		if (errorMsg !== '') {
			multiUpload.showError(errorMsg);
			e.preventDefault();
			return false;
		}
		return true;
	},
	
	showError: function (msg) {
		alert(msg);
	}
	
};