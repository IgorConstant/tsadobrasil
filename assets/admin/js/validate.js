$('form#validate').submit(function () {

  $form = $(this);
  $e = 0;
  
  $('span.helper').remove();
  $('#formErro').html('');

  $("form input, form select, form textarea").each(function(){

  $(this).removeClass('helperShow');    
  
      var attr = $(this).attr("validate");

      if ($(this).val().trim() == '' && (typeof attr !== typeof undefined && attr !== false)){
          $(this).addClass('helperShow');
          $(this).parent().append('<span class="helper">** Campo obrigatório</span>');
          $e++;
      }

  })
  
  if($e!=0){
      $('#formErro').html('Erro ao enviar, verifique o(s) campo(S) obrigatório(s)!');
      return false;
  }    

});