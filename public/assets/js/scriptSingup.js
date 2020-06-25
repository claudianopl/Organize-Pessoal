function invalidRegister() {
  $('.loadingArea').hide();
  $('.sectionSingupOneAreaForm p').addClass('error');
  
  $('.sectionSingupOneForm').addClass('formInvalid');
  setTimeout(() => {  
    $('.sectionSingupOneForm').removeClass('formInvalid');
  }, 1000);
}


$('.sectionSingupOneForm form').on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show();
  const form = $(this).serializeArray();
  const valid = new RegExp(/^[A-Za-z0-9_\-\.]+@[A-Za-z0-9_\-\.]{2,}\.[A-Za-z0-9]{2,}(\.[A-Za-z0-9])?/);
  const name = form[0];
  const surname = form[1];
  const email = form[2];
  const password = form[3];
  let validate = true;

  if(name.value == '' || name.value.length < 3) {
    invalidRegister();
    $('.sectionSingupOneAreaForm p').html('Por favor, informe um nome válido.');
    validate = false;
  }
  if(surname.value == '' || surname.value.length < 3) {
    invalidRegister();
    $('.sectionSingupOneAreaForm p').html('Por favor, informe um sobrenome válido.');
    validate = false;
  }
  if(email.value == '' || !valid.test(email.value) || email.value.length < 3) {
    invalidRegister();
    $('.sectionSingupOneAreaForm p').html('Por favor, informe um email válido.');
    validate = false;
  }
  if(password.value == '' || password.value.length < 8) {
    invalidRegister();
    $('.sectionSingupOneAreaForm p').html('Por favor, informe uma senha válida com no mínimo 8 caracteres.');
    validate = false;
  }

  if(validate) {
    $.ajax({
      type: 'post',
      url: '/newUser',
      data: form,
      dataType: 'json',
      success: d => {
        if(d.messege == 'success') {
          location.href='/confirmar-cadastro';
        }
        else {
          $('.loadingArea').hide();
          $('.sectionSingupOneAreaForm p').addClass('error');
          $('.sectionSingupOneAreaForm p').html(d.messege);
        }
      },
      error: erro => {
       // Futuro log de erro no banco de dados
      }
    })
    
  }


})
