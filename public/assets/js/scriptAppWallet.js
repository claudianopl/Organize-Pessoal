/**
 * Evento de submit.
 * Cadastramos novas carteiras com esse evento.
 */
$('.sectionAppNewWallet form').on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show();
  const form = $(this).serializeArray();
  const wallet = form[0];
  let validate = true
  if(wallet.value == ''){
    $('.loadingArea').hide();
    $('#messege').addClass('error');
    $('#messege').html('Por favor, informe o nome da carteira.');
    validate = false
  }
  if(validate) {
    $.ajax({
      type: 'post',
      url: '/app/insertWallet',
      data: form,
      dataType: 'json',
      success: (d) => {
        if(d.messege == 'success') {
          location.reload();
        } else {
          $('.loadingArea').hide();
          $('.newExpensesForm p').addClass('error');
          $('.newExpensesForm p').html(d.messege);
        }
      }
    })
  }
})