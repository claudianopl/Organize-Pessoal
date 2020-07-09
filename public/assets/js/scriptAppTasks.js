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

function selectFilterTasks() {
  let filterTasks = tasksSelect.html();
  if(filterTasks == 'Todas tarefas') {
    filterTasks = '';
  }
  if(filterTasks != '') {
    console.log('ok')
    /*
    $.ajax({
      type: 'post',
      url: window.location.pathname,
      data: `receive=${filterReceive}&category=${filterCategory}`,
      dataType: 'html',
      success: (d) => {
        console.log(d.receitas)
      },
      error: (e) => {
        console.log('error')
      }
    })
    */
    /*
    * CRIAR A LÓGICA PARA SE COMUNICAR COM A ROTA DO BACKEND E NÃO COM A 
    * ROTA DO FRONT-END
    */
  }
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
})


/**
 * Função para remover uma tarefa.
 * @param {String} id 
 */
function removeTasks() {

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
function updateTasks() {
  executeModalUpdateTasks();
}

/**
 * Função para concluir uma tarefa.
 * @param {String} id 
 */
function concludeTasks() {

}


/*
* Executar quando carregar a página phtml
*/
$(document).ready(() => {
  tasksNav.hide();
  $('.newTasksArea').hide();
  $('.updateTasksArea').hide();
})