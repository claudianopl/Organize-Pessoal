/*
* Constantes globais
*/
const tasks = $(".sectionAppTasksFilterAllActive");
const tasksSelect = $('.sectionAppTasksFilterAllActive h4');
const tasksNav = $('.sectionAppTasksFilterAllNav');
/*
* Evento de click
* Atua na abertura da nav status de tarefas com as opções de filtro
*/

tasks.click(() => {
  tasksNav.slideToggle('slow');
})

/*
* Evento de click
* Atua na seleção das opções da nav status de tarefas, ou seja, ao selecionar
* ele altera o h4 da status de tarefas para a que foi selecionada.
*/
tasksNav.click((e) => {
  const select = e.target.innerHTML;
  tasksSelect.html(select);
  tasksNav.slideToggle('slow');
})

/**
 * Função para filtrar as tarefas.
 * A função captura os valores informados pelo usuário e envia para uma rota do 
 * back-end responsável por filtrar e retornar os valores filtrados que por sua 
 * vez ele adicionar o html da layout.
 */
function selectFilterTasks() {
  let filterStatus = tasksSelect.html();
  let filterDate = $('.sectionAppTwoFilterDadosDate input').val();
  if(filterStatus == 'Todas tarefas') {
    filterStatus = '';
  }
  if(filterDate == undefined) {
    filterDate=''
  }
  if (filterStatus == 'Tarefas concluídas'){
    filterStatus = 1;
  }
  else if(filterStatus == 'Tarefas pendentes') {
    filterStatus = 0;
  }
  const dataObj = {
    'status':filterStatus,
    'date':filterDate
  }

  /**
   * Enviando dados via ajax para a rota do back-end.
   */
  $.ajax({
    type: 'post',
    url: '/app/filterTasks',
    data: dataObj,
    dataType: 'json',
    success: (d) => {
      if(d.data.length > 0) {
        $('.sectionAppTableTasks').show('slow');
        $('.sectionAppMessageTasks').hide();

        $('.sectionAppTableItemTasks').remove();
        let fatherTable = $('.sectionAppTableTasks');

        d.data.forEach(element => {
          let sectionAppTableItem = document.createElement('article');
          sectionAppTableItem.className = 'sectionAppTableItemTasks';
          sectionAppTableItem.id = element.id;
          
          let desc = document.createElement('p');
          desc.className = 'desc';
          desc.innerHTML = element.description;

          let date = document.createElement('p');
          date.className = 'date';
          const dateSplit = element.date.split(' ');
          const dayMonthYearSplit = dateSplit[0].split('-');
          let dayMonthYear = `${dayMonthYearSplit[2]}/${dayMonthYearSplit[1]}/${dayMonthYearSplit[0]}`;
          const timeSplit = dateSplit[1].split(':');
          let time = `${timeSplit[0]}:${timeSplit[1]}`;
          
          date.innerHTML = `${dayMonthYear} ${time}
          <img src="/assets/images/app/appGlobal/remove.svg"
          onclick="removeTasks('${element.id}')">
          <img src="/assets/images/app/appGlobal/update.svg"
          onclick="updateTasks('${element.id}')">
          <img src="/assets/images/app/appGlobal/conclude.svg"
          onclick="concludeTasks('${element.id}')">`;
          
          sectionAppTableItem.append(desc);
          sectionAppTableItem.append(date);
          
          fatherTable.append(sectionAppTableItem);
        })
      }
      else {
        $('.sectionAppTableTasks').hide();
        $('.sectionAppMessageTasks h3').html('Você não possui tarefas neste filtro.')
        $('.sectionAppMessageTasks').show('slow');
      }
    }
  })
}


/**
 * Evento de click.
 * Atua na execução do modal de inserção da tarefa.
 */
$('.sectionAppTasksThrow').click(() => {
  $('.newTasksArea').show();
  $('.newTasksArea').addClass('TasksAreaAnimation');

  $('.newTasksExit').click(() => {
    $('.newTasksArea').removeClass('TasksAreaAnimation');
    setTimeout(() => {  
      $('.newTasksArea').hide();
    }, 1000);
  })
});


/**
 * Evento de submit.
 */
$('.newTasksForm form').on('submit', function(e) {
  e.preventDefault();
  $('.loadingArea').show();
  const form = $(this).serializeArray();
  const desc = form[0];
  const date = form[1];
  const wallet = form[2];
  let validate = true
  if(date.value.length > 16) {
    $('.loadingArea').hide();
    $('.newTasksForm p').addClass('error');
    $('.newTasksForm p').html('Data contém mais caracteres que o necessário, por favor, verifique a data.');
    validate = false;
  }
  if(desc.value.length < 3 || date.value == '' || wallet.value == '') {
    $('.loadingArea').hide();
    $('.newTasksForm p').addClass('error');
    $('.newTasksForm p').html('Informação inválido, por favor, verifique as informações.');
    validate = false;
  }
  if(validate) {
    $.ajax({
      type: 'post',
      url: '/app/insertTasks',
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


/**
 * Função para remover uma tarefa.
 * @param {String} id 
 */
function removeTasks(id) {
  $.ajax({
    type: 'post',
    url: '/app/removeTasks',
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

/**
 * Função de execução do modal de atualização quando solicitado.
 */
function executeModalUpdateTasks(){
  $('.updateTasksArea').show();
  $('.updateTasksArea').addClass('TasksAreaAnimation');

  $('.updateTasksExit').click(() => {
    $('.updateTasksArea').removeClass('TasksAreaAnimation');
    setTimeout(() => {  
      $('.updateTasksArea').hide();
    }, 1000);
  })
}

/**
 * Função para atualizar uma tarefa.
 * Vai abrir o modal com os dados daquela tarefa nesse modal, para o usuário 
 * atualizar os dados.
 * @param {String} id 
 */
function updateTasks(id) {
  executeModalUpdateTasks();

  /**
   * Ajax para retornar e preencher os dados da tarefa a ser atualizada
   */
  $.ajax({
    type: 'post',
    url: '/app/updateTasks',
    data: {'type':'filter','id':id},
    dataType: 'json',
    success: d => {
      $('#updateTasksDec').val(d.description);
      $('#updateTasksDate').val(d.date);
      $('#updateTasksWallet').val(d.id_wallet);
      $('#updateTasksStatus').val(d.status);
    }
  })

  /**
   * Evento de submit.
   * O evento verificar se todos os dados obrigatórios da tarefa foram preenchido,
   * caso estiver tudo correto enviamos os dados via ajax para a rota de back-end
   * para efeturamos o update.
   */
  $('.updateTasksForm form').on('submit', function (e) {
    e.preventDefault();
    $('.loadingArea').show();
    const form = $(this).serializeArray();
    const desc = form[0];
    const date = form[1];
    const wallet = form[2];
    const status = form[3];
    let validate = true
    if(date.value.length > 16) {
      $('.loadingArea').hide();
      $('.newTasksForm p').addClass('error');
      $('.newTasksForm p').html('Data contém mais caracteres que o necessário, por favor, verifique a data.');
      validate = false;
    }
    if(desc.value.length < 3 || date.value == '' || wallet.value == '' || status.value == '') {
      $('.loadingArea').hide();
      $('.updateTasksForm p').addClass('error');
      $('.updateTasksForm p').html('Informação inválido, por favor, verifique as informações.');
      validate = false;
    }
    if(validate) {
      $.ajax({
        type: 'post',
        url: '/app/updateTasks',
        data: {'type':'update','id':id, 'form':form},
        dataType: 'json',
        success: d => {
          if(d.messege == 'success'){
            location.reload();
          }
          else {
            $('.loadingArea').hide();
            $('.updateTasksForm p').addClass('error');
            $('.updateTasksForm p').html(d.messege);
          }
        }
      })
    }
  })
}

/**
 * Função para concluir uma tarefa.
 * @param {String} id 
 */
function concludeTasks(id) {
  $('.loadingArea').show();
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
        $('.loadingArea').hide();
        $('#messege').show('slow');
        $('#messege').html('Ops... Um error inesperado aconteceu.');
        $('#messege').addClass('error');
      }
    }
  });
}


/*
* Executar quando carregar a página phtml
*/
$(document).ready(() => {
  tasksNav.hide();
  $('.newTasksArea').hide();
  $('.updateTasksArea').hide();
})