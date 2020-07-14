/*
* Constantes globais
*/
const gender = $('.appProfileGenderSelect');
const genderNav = $('.appProfileGenderNav');
/*
* Evento de click
* Atua na abertura da nav de gênero com as opções a escolher
*/
gender.click(() => {
  genderNav.slideToggle('fast');
})

/*
* Evento de click
* Atua na seleção das opções da nav gênero, ou seja, ao selecionar ele altera
* o value do input gênero para a que foi selecionada.
*/
genderNav.click((e) => {
  const select = e.target.innerHTML;
  $('.appProfileGenderSelect input').val(select);
  genderNav.slideToggle('fast');
})

/**
 * Evento de click que atua na abertuda do modal de alteração da senha.
 */
$('.appProfileButtonUpdatePassword').click(() => {
  $('.updatePasswordArea').show();
  $('.updatePasswordArea').addClass('passwordAreaAnimation');

  $('.updatePassowrdExit').click(() => {
    $('.updatePasswordArea').removeClass('passwordAreaAnimation');
    setTimeout(() => {  
      $('.updatePasswordArea').hide();
    }, 1000);
  })
})

/**
 * Evento de submição.
 * Atua na captura dos dados da submição dos dados do perfil do usuário.
 */
$(".sectionAppAreaRight form").on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show();
  const form = $(this).serializeArray();
  const valid = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
  const name = form[0]
  const surname = form[1]
  const nasciment = form[2]
  const gender = form[3]
  const password = form[4]
  const confirmPassword = form[5]
  let validate = true
  let validGender = false

  if(name.value == '' || name.value.length < 3) {
    $('.loadingArea').hide();
    $('#messegeProfile').addClass('error');
    $('#messegeProfile').html('Por favor, informe um nome válido.');
    validate = false;
  }

  if(surname.value == '' || surname.value.length < 3) {
    $('.loadingArea').hide();
    $('#messegeProfile').addClass('error');
    $('#messegeProfile').html('Por favor, informe um sobrenome válido.');
    validate = false;
  }

  if(gender.value == '') {
    $('.loadingArea').hide();
    $('#messegeProfile').addClass('error');
    $('#messegeProfile').html('Por favor, informe um gênero válido.');
    validate = false;
  }
  if(gender.value == 'Masculino' || gender.value == 'Feminino') {
    validGender = true;
  }

  if(nasciment.value == '' || nasciment.value.length > 10 || nasciment.value.length < 10 ) {
    $('.loadingArea').hide();
    $('#messegeProfile').addClass('error');
    $('#messegeProfile').html('Por favor, informe uma data de nascimento válido.');
    validate = false;
  }

  if(password.value == '' || password.value.length < 8) {
    $('.loadingArea').hide();
    $('#messegeProfile').addClass('error');
    $('#messegeProfile').html('Por favor, informe uma senha válida com no mínimo 8 caracteres.');
    validate = false;
  }
  if(confirmPassword.value == '' || confirmPassword.value.length < 8) {
    $('.loadingArea').hide();
    $('#messegeProfile').addClass('error');
    $('#messegeProfile').html('Por favor, confirme uma senha válida com no mínimo 8 caracteres.');
    validate = false;
  }
  if(password.value != confirmPassword.value) {
    $('.loadingArea').hide();
    $('#messegeProfile').addClass('error');
    $('#messegeProfile').html('Suas senhas estão diferentes, por favor, verifique sua senha.');
    validate = false;
  }

  if(validate && validGender) {
    $.ajax({
      type: 'post',
      url: '/app/userProfile',
      data: form,
      dataType: 'json',
      success: d => {
        if(d.messege == 'success') {
          location.reload();
        }
        else {
          $('.loadingArea').hide();
          $('.sectionSingupOneAreaForm p').addClass('error');
          $('.sectionSingupOneAreaForm p').html(d.messege);
        }
      }
    })
  } 
  else {
    $('.loadingArea').hide();
    $('#messegeProfile').addClass('error');
    $('#messegeProfile').html('Ops... Seus dados são invalidos verifique seus dados.');
  }
})

/**
 * Evento de submição para alterar a senha do usuário.
 */
$('.updatePasswordForm form').on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show();
  const form = $(this).serializeArray();
  const password = form[0];
  const newPassword = form[1];
  const confirmPassword = form[2];
  let validate = true;

  if(password.value == '' || newPassword.value == '' || 
  password.value.length < 8 || newPassword.value.length < 8) {
    $('.loadingArea').hide();
    $('.updatePasswordForm p').addClass('error');
    $('.updatePasswordForm p').html('Por favor, informe uma senha válida com no mínimo 8 caracteres.');
    validate = false;
  }
  if(confirmPassword.value == '' || confirmPassword.value.length < 8) {
    $('.loadingArea').hide();
    $('.updatePasswordForm p').addClass('error');
    $('.updatePasswordForm p').html('Por favor, confirme uma senha válida com no mínimo 8 caracteres.');
    validate = false;
  }
  if(newPassword.value != confirmPassword.value) {
    $('.loadingArea').hide();
    $('.updatePasswordForm p').addClass('error');
    $('.updatePasswordForm p').html('Suas senhas estão diferentes, por favor, verifique sua senha.');
    validate = false;
  }

  if(validate) {
    $.ajax({
      type: 'post',
      url: '/app/updatePassword',
      data: form,
      dataType: 'json',
      success: d => {
        if(d.messege == 'success') {
          location.reload();
        }
        else {
          $('.loadingArea').hide();
          $('.sectionSingupOneAreaForm p').addClass('error');
          $('.sectionSingupOneAreaForm p').html(d.messege);
        }
      }
    })
  }
})


/*
* Executar quando carregar a página phtml
*/
$(document).ready(() => {
  genderNav.hide();
  $('.updatePasswordArea').hide();
})