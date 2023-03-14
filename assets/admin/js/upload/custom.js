$(document).ready(function() {
	
	// enable fileuploader plugin
	$('#upload').fileuploader({
        addMore: true,
        extensions: ['jpg', 'jpeg', 'png'],
		limit: 50,
		fileMaxSize: 20,
		enableApi: true,
		allowDuplicates: false,
        changeInput: '<div class="fileuploader-input">' +
                        '<div class="fileuploader-input-inner">' +
                            '<p><i class="fa fa-cloud-upload" aria-hidden="true"></i></p>' +
                            //'<h3 class="fileuploader-input-caption"><span>Clique ou arraste para adicionar uma foto</span></h3>' +
                            '<p>Clique aqui para selecionar as fotos</p>' +
                            //'<div class="fileuploader-input-button"><span>Selecionar foto</span></div>' +
                        '</div>' +
                    '</div>',
        theme: 'thumbnails',//'thumbnails','dragdrop',
        thumbnails: {
			box: '<div class="fileuploader-items">' +
                      '<ul class="fileuploader-items-list"></ul><span class="aviso">(<i class="fa fa-star" aria-hidden="true"></i> A primeira foto será usada no destaque | <i class="fa fa-arrows" aria-hidden="true"></i> clique e arraste a foto para organizar a ordem)</span>' +
                  '</div>',
			item: '<li class="fileuploader-item">' +
				       '<div class="fileuploader-item-inner">' +
                           '<div class="thumbnail-holder">${image}</div>' +
                           '<div class="actions-holder">' +
						   	   '<span class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="fa fa-trash-o" aria-hidden="true"></i></span>' +
							   //'<span class="fileuploader-action-popup"></span>' +
                           '</div>' +
                       	   //'<div class="progress-holder">${progressBar}</div>' +
                       '</div>' +
                   '</li>',
			item2: '<li class="fileuploader-item">' +
				       '<div class="fileuploader-item-inner">' +
                           '<div class="thumbnail-holder">${image}</div>' +
                           '<div class="actions-holder">' +
                               '<span class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="fa fa-trash-o" aria-hidden="true"></i></span>' +
							   //'<span class="fileuploader-action-popup"></span>' +
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
            selectorExclude: '.fileuploader-action-remove'
		},
		dragDrop: {
            container: '.fileuploader-input'
        },
		//upload: true,
        captions: {
			button: "Selecioar Files",
			feedback: "Clique ou arraste para adicionar uma foto.",
			feedback2: "Clique ou arraste para adicionar uma foto.",
			drop: "Solte os arquivos aqui para fazer o upload!",
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