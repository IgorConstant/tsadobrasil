jQuery(function($) {
      $.mask.definitions['~']='[+-]';     
	  $('.data').mask('99/99/9999');
	  $('.telefone')  
        .mask("(99) 9999-9999?9")  
        .live('focusout', function (event) {  
            var target, phone, element;  
            target = (event.currentTarget) ? event.currentTarget : event.srcElement;  
            phone = target.value.replace(/\D/g, '');  
            element = $(target);  
            element.unmask();  
            if(phone.length > 10) {  
                element.mask("(99) 99999-999?9");  
            } else {  
                element.mask("(99) 9999-9999?9");  
            }  
        });     
	  $('.hora').mask('99:99');
      $('.cpf').mask('999.999.999-99');	   
	  $('.cnpj').mask('99.999.999/9999-99');
	  $('.cep').mask('99999-999');
	  $('.codigo').mask('999?9');
	  $('.porcentagem').mask('99.00');
	  $('.codigo').mask('999?9');
      $('.mes').mask('99/99');
      $('.money2').mask("#.##0,00", {reverse: true});
});


