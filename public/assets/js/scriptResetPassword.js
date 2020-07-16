/*
  * Pegando o paramentro da url para ser usada caso necessário
  */
 function url() {
  // Capturando o parametro completo da url
  let params = window.location.search.substring(1).split('&');
  // Criando uma array para receber os dados
  let paramObject = {};
  
  for(let i=0; i<params.length; i++) {
    //Extraindo os valares da params
    let param = params[i].split('=');
    // Adicionando os dados dentro de paramObject
    paramObject[param[0]] = param[1];
  }
  return({'name': 'hash', 'value':paramObject.verification})
}
const hashUrl = url();

function invalidPassword() {
  $('.loadingArea').hide();
  $('.sectionResetPasswordArea p').addClass('error');
  $('.ResetPassword form').addClass('formInvalid');
  setTimeout(() => {  
    $('.ResetPassword form').removeClass('formInvalid');
  }, 1000);
}

$('.ResetPassword form').on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show();
  let form = $(this).serializeArray();
  form.push(hashUrl);

  const password = form[0];
  const confirmpassword = form[1];

  if(password.value == '' || password.value.length < 8) {
    invalidPassword();
    $('.sectionResetPasswordArea p').html('Senha inválida, por favor informe uma senha válida.');
  }
  else if(password.value != confirmpassword.value) {
    invalidPassword();
    $('.sectionResetPasswordArea p').html('Sua senha está diferente da confirmação, por favor verifique sua senha.');
  }
  else if(hashUrl.value.length != 32) {
    invalidPassword();
    $('.sectionResetPasswordArea p').html('Error: Informações inválidas, usuário não existe.');
  }
  else {
    $.ajax({
      type: 'post',
      url: '/changePassword',
      data: form,
      dataType: 'json',
      success: d => {
        if(d.message == 'success') {
          $('.loadingArea').hide();
          $('.sectionResetPasswordArea p').addClass('success');
          $('.sectionResetPasswordArea p').html('Senha alterada com sucesso!');
        } else {
          invalidPassword();
          $('.sectionResetPasswordArea p').html(d.message);
        }
      },
      error: e => {
        console.log(e)
        invalidPassword();
        $('.sectionResetPasswordArea p').html('Algum erro inesperado aconteceu, tente novamente mais tarde.');
      }
    })
  }
})