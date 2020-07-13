/**
 * função para gerar o gráfico anual das despesas e receitas.
 */
function graphic() {
  /**
   * Ajax para retornar os dados para alimentar o graphic.
   */
  $.ajax({
    type: 'post',
    url: '/app/graphicWallet',
    dataType: 'json',
    success: (d) => {
      let dataExpenses = Array();
      let dataReceived = Array();
      d.expenses.forEach(element => {    
        dataExpenses.push(element.amount);
      });
      d.received.forEach(element => {
        dataReceived.push(element.amount);
      })

      let ctx = document.getElementById('GraphicWallet').getContext("2d");
      var graphic = new Chart(ctx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun','Jul', 
          'Ago', 'Set', 'Out', 'Nov', 'Dez'],
          datasets: [{
            label: 'Receita',
            backgroundColor: "#34F06F",
            borderColor: "#34F06F",
            data: dataReceived,
            borderWidth: 2,
            fill: false
          }, {
            label: 'Despesa',
            backgroundColor: "#E34E4E",
            borderColor: "#E34E4E",
            data: dataExpenses,
            borderWidth: 2,
            fill: false
          }] // datasets
        }, // data
        options: {
          title: {
            display: true,
            fontSize: 16,
            text: 'Relatório anual: Receitas x Despesas'
          }
        }
      });
    }
  })
}

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
          $('#messegeWallet').show('slow');
          $('#messegeWallet').addClass('error');
          $('#messegeWallet').html(d.messege);
        }
      }
    })
  }
})

function removeWallet(id) {
  $.ajax({
    type: 'post',
    url: '/app/removeWallet',
    data: {'id':id},
    dataType: 'json',
    success: (d) => {
      if(d.messege == 'success') {
        location.reload();
      } else {
        $('.loadingArea').hide();
        $('#messegeWallet').show('slow');
        $('#messegeWallet').addClass('error');
        $('#messegeWallet').html(d.messege);
      }
    }
  })
}


$(document).ready(() => {
  $('#messegeWallet').hide()
})


