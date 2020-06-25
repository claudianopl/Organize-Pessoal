/*
  * Pegando o paramentro da url para ser usada caso necessário
  */
 function url() {
  // Capturando o parametro completo da url
  let params = window.location.search.substring(1).split('&');
  // Criando um objeto para receber os dados
  let paramObject = {};
  for(let i=0; i<params.length; i++) {
    //Extraindo os valares da params
    let param = params[i].split('=');
    // Adicionando os dados dentro de paramObject
    paramObject[param[0]] = param[1];
  }

  if(Object.keys(paramObject).length > 0 && paramObject.e == 0) {
    $('.sectionLoginOneInfo p').show();
    $('.sectionLoginOneInfo p').html('Acesso negado, por favor efetuar o login.');
  }
}

function invalidRegister() {
  $('.loadingArea').hide();
  $('.sectionLoginOneInfo p').show();
  
  $('.sectionLoginOneForm').addClass('formInvalid');
  setTimeout(() => {  
    $('.sectionLoginOneForm').removeClass('formInvalid');
  }, 1000);
}

$('.sectionLoginOneForm form').on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show()
  const form = $(this).serializeArray();
  const email = form[0];
  const password = form[1];
  // Validar email
  const valid = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
  let validate = true;

  if(email.value == '' || !valid.test(email.value)) {
    invalidRegister();
    $('.sectionLoginOneInfo p').html('Ops… Usuário invalido!');
    validate = false;
  }
  if(password.value == '' || password.value.length < 8) {
    invalidRegister();
    $('.sectionLoginOneInfo p').html('Ops… Usuário invalido!');
    validate = false;
  }
  if(validate) {
    $.ajax({
      type: 'post',
      url: '/authenticateUser',
      data: form,
      dataType: 'json',
      success: d => {
        if(d.messege == 'success') {
          location.href='/app';
        } else {
          invalidRegister();
          $('.sectionLoginOneInfo p').html(d.messege);
        }
        
      },
      error: erro => {
       // Futuro log de erro no banco de dados
      }
    })
  }
}) 


$(document).ready(() => {
  $('.sectionLoginOneInfo p').hide()

  url()
})