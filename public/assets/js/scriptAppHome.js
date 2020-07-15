/**
 * Função para fazer a confirmação das receitas, despesas ou tarefas.
 * @param {String} type informa se a confirmação é da receita, despesa ou tarefas
 * @param {String} id
 */
function confirmed(type, id) {
  $('.loadingArea').show();
  if(type == 'received') {
    $.ajax({
      type: 'post',
      url: '/app/concludeReceived',
      data: {'id':id},
      dataType: 'json',
      success: d => {
        if(d.messege == 'success') {
          location.reload();
        }
        else {
          $('.loadingArea').hide();
          $('#messege').show('slow');
          $('#messege').html('Ops... Um error inesperado aconteceu.');
          $('#messege').addClass('error');
        }
      }
    });
  }
  if(type == 'expenses') {
    $.ajax({
      type: 'post',
      url: '/app/expensesConclude',
      data: {'id':id},
      dataType: 'json',
      success: d => {
        if(d.messege == 'success') {
          location.reload();
        }
        else {
          $('.loadingArea').hide();
          $('#messege').show('slow');
          $('#messege').html('Ops... Um error inesperado aconteceu.');
          $('#messege').addClass('error');
        }
      }
    });
  }
  if(type == 'tasks') {
    $.ajax({
      type: 'post',
      url: '/app/concludeTasks',
      data: {'id':id},
      dataType: 'json',
      success: d => {
        if(d.messege == 'success') {
          location.reload();
        }
        else {
          $('#messege').show('slow');
          $('#messege').html('Ops... Um error inesperado aconteceu.');
          $('#messege').addClass('error');
        }
      }
    });
  }
}