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


function invalidLogin() {
  $('.loadingArea').hide();
  $('.sectionLoginOneInfo p').show();
  
  $('.sectionLoginOneForm').addClass('formInvalid');
  setTimeout(() => {  
    $('.sectionLoginOneForm').removeClass('formInvalid');
  }, 1000);
}
/**
 * Evento para autenticar o login do usuário.
 * Evento responsável por ver se os dados enviados pelo usuário estão corretos
 para garantir a segurança da aplicação e não executar códigos indevidos passado
 pelos inputs.
 */
$('.sectionLoginOneForm form').on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show()
  const form = $(this).serializeArray();
  //const token = {'name': 'token'}
  console.log(token);
  console.log(form);
  const email = form[0];
  const password = form[1];
  // Validar email
  const valid = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
  let validate = true;

  if(email.value == '' || !valid.test(email.value)) {
    invalidLogin();
    $('.sectionLoginOneInfo p').html('Ops… Usuário invalido!');
    validate = false;
  }
  if(password.value == '' || password.value.length < 8) {
    invalidLogin();
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
          invalidLogin();
          $('.sectionLoginOneInfo p').html(d.messege);
        }
      },
      error: erro => {
        invalidLogin();
        $('.sectionLoginOneInfo p').html('Algum erro inesperado aconteceu, tente novamente mais tarde.');
      }
    })
  }
})


/**
 * Função para email inválido recuperar senha.
 */
function emailMessege() {
  $('.loadingArea').hide();
  $('.sectionLoginOneForm').addClass('formInvalid');
  setTimeout(() => {  
    $('.sectionLoginOneForm').removeClass('formInvalid');
  }, 1000);
}
/**
 * Evento responsável por verifica se o email do esqueci senha está correto.
 * Verifica a procedência do email e envia os dados para a rota do php por 
 requisição ajax, para ser enviado o email de verificação para efetuar a troca
 da sennha.
 */
$('.ModalPassword form').on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show()
  const form = $(this).serializeArray()
  const email = form[0];
  const valid = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
  if(email.value == '' || !valid.test(email.value)) {
    emailMessege();
    $('.ModalPassword p').addClass('error');
    $('.ModalPassword p').html('E-mail inválido!');
  }
  else {
    $.ajax({
      type: 'post',
      url: '/changeTokenPassword',
      data: form,
      dataType: 'json',
      success: d => {
        if(d.message == 'success') {
          emailMessege();
          $('.ModalPassword p').addClass('success');
          $('.ModalPassword p').html('Foi enviado um e-mail para você com instruções para mudar a senha.')
        } else {
          emailMessege();
          $('.ModalPassword p').addClass('error');
          $('.ModalPassword p').html(d.message);
        }
      },
      error: erro => {
        emailMessege();
        $('.ModalPassword p').addClass('error');
        $('.ModalPassword p').html('Algum erro inesperado aconteceu, tente novamente mais tarde.');
      }
    })
  }
})



/**
 * Executando o modal.
 * Função responsável por executar o modal de esqueci senha para fazer a troca
 da senha do usuário.
 */
function executeModal() {
  $('.bgModalPassword').show();
  $('.bgModalPassword').addClass('bgModalPasswordAnimation');

  $('.bgModalExit').click(() => {
    $('.bgModalPassword').removeClass('bgModalPasswordAnimation');
    setTimeout(() => {  
      $('.bgModalPassword').hide();
    }, 1000);
  })
  $('.ModalPassword span').click(() => {
    $('.bgModalPassword').removeClass('bgModalPasswordAnimation');
    setTimeout(() => {  
      $('.bgModalPassword').hide();
    }, 1000);
  })
}
$('.inputBlock span').click(() => {
  executeModal();
})




$(document).ready(() => {
  $('.sectionLoginOneInfo p').hide();
  $('.bgModalPassword').hide();
  
  url()
})