$(document).ready(function() {
	
	// enable fileuploader plugin
	$('#upload').fileuploader({
        addMore: true,
        extensions: ['jpg', 'jpeg'],
		limit: 10,
		fileMaxSize: 12,
		allowDuplicates: false,
        changeInput: '<div class="fileuploader-input">' +
                        '<div class="fileuploader-input-inner">' +
                            '<i class="fa fa-cloud-upload" aria-hidden="true"></i>' +
                            '<h3 class="fileuploader-input-caption"><span>Clique ou arraste para adicionar uma foto</span></h3>' +
                            '<p>ou</p>' +
                            '<div class="fileuploader-input-button"><span>Selecionar arquivo</span></div>' +
                        '</div>' +
                    '</div>',
        theme: 'thumbnails',//'thumbnails','dragdrop',
        thumbnails: {
			box: '<div class="fileuploader-items">' +
                      '<ul class="fileuploader-items-list"></ul><span class="aviso">(clique e arraste nos arquivos para organizar a ordem <i class="fa fa-arrows" aria-hidden="true"></i> )</span>' +
                  '</div>',
			item: '<li class="fileuploader-item">' +
				       '<div class="fileuploader-item-inner">' +
                           '<div class="thumbnail-holder">${image}</div>' +
                           '<div class="actions-holder">' +
						   	   '<a class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="remove"></i></a>' +
							   '<span class="fileuploader-action-popup__"></span>' +
                           '</div>' +
                       	   '<div class="progress-holder">${progressBar}</div>' +
                       '</div>' +
                   '</li>',
			item2: '<li class="fileuploader-item">' +
				       '<div class="fileuploader-item-inner">' +
                           '<div class="thumbnail-holder">${image}</div>' +
                           '<div class="actions-holder">' +
                               '<a class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="remove"></i></a>' +
							   '<span class="fileuploader-action-popup__"></span>' +
                           '</div>' +
                       '</div>' +
                   '</li>',
			startImageRenderer: true,
			canvasImage: false,
			_selectors: {
                sorter: '.fileuploader-item'
			},
			onItemShow: function(item, listEl) {
				var plusInput = listEl.find('.fileuploader-thumbnails-input');
				
				plusInput.insertAfter(item.html);
				
				if(item.format == 'image') {
					item.html.find('.fileuploader-item-icon').hide();
				}
			}
		},
		afterRender: function(listEl, parentEl, newInputEl, inputEl) {
			var plusInput = listEl.find('.fileuploader-thumbnails-input'),
				api = $.fileuploader.getInstance(inputEl.get(0));
		
			plusInput.on('click', function() {
				api.open();
			});
		},
        editor: {
            cropper: {
                showGrid: true
            }	
        },
        sorter: {
            selectorExclude: ''
        },
        captions: {
			button: "Selecioar Files",
			feedback: "Choose files To Upload",
			feedback2: "Arquivos selecionados",
			drop: "Drop file here to Upload",
			removeConfirmation: "Tem certeza que quer excluir essa foto?",
			errors: {
				filesLimit: "Only {{fi-limit}} files are allowed to be uploaded.",
				filesType: "Only Images are allowed to be uploaded.",
				filesSize: "{{fi-name}} is too large! Please upload file up to {{fi-maxSize}} MB.",
				filesSizeAll: "Files you've choosed are too large! Please upload files up to {{fi-maxSize}} MB."
			}
        },        
        
        
    });
	
});