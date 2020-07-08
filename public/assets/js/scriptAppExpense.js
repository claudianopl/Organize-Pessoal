/*
* Constantes globais
*/
const expense = $(".sectionAppExpenseFilterAllActive");
const expenseSelect = $('.sectionAppExpenseFilterAllActive h4');
const expenseNav = $('.sectionAppExpenseFilterAllNav');

const category = $('.sectionAppExpenseFilterCategoryActive');
const categorySelect = $('.sectionAppExpenseFilterCategoryActive h4');
const categoryNav = $('.sectionAppExpenseFilterCategoryNav');

// ----> Despesas <----
/*
* Evento de click
* Atua na abertura da nav despesas com as opções de filtro
*/
expense.click(() => {
  expenseNav.slideToggle('slow');
})
/*
* Evento de click
* Atua na seleção das opções da nav despesas, ou seja, ao selecionar ele altera
* o h4 da despesas para a que foi selecionada.
*/
expenseNav.click((e) => {
  const select = e.target.innerHTML;
  expenseSelect.html(select);
  expenseNav.slideToggle('slow');
})

// ----> Category <----
/*
* Evento de click
* Atua na abertura da nav categoria com as opções de filtro
*/
category.click(() => {
  categoryNav.slideToggle('slow');
})

/*
* Evento de click
* Atua na seleção das opções da nav categoria, ou seja, ao selecionar ele altera
* o h4 da categoria para a que foi selecionada.
*/
categoryNav.click((e) => {
  const select = e.target.innerHTML;
  categorySelect.html(select);
  categoryNav.slideToggle('slow');
})


function selectFilterExpense() {
  let filterExpense = expenseSelect.html();
  let filterCategory = categorySelect.html();
  if(filterExpense == 'Todas despesas') {
    filterExpense = '';
  }
  if(filterCategory == 'Todas categorias') {
    filterCategory = '';
  }
  if(filterExpense != '' || filterCategory != '') {
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
 * Evento click.
 * Evento ativa a função de abertuda do modal de inserção.
 */
$('.sectionAppExpenseThrow').click(() => {
  $('.newExpensesArea').show();
  $('.newExpensesArea').addClass('ExpensesAreaAnimation');

  $('.newExpensesExit').click(() => {
    $('.newExpensesArea').removeClass('ExpensesAreaAnimation');
    setTimeout(() => {  
      $('.newExpensesArea').hide();
    }, 1000);
  })
})

/**
 * Evento change.
 * O evento abre as opções que estão dentro do select de repetição, que são as 
 * opções de fixas e parceladas.
 */
$('.enrollment').on('change', function(e) {
  const value = $(this).val()
  if(value == 'Única' || value == '') {
    $('.fixed').hide();
    $('.parcel').hide();
  }
  if(value == 'Fixa') {
    $('.parcel').hide();
    $('.fixed').slideToggle('slow');
  } else if(value == 'Parcelada') {
    $('.fixed').hide();
    $('.parcel').slideToggle('slow');
  }
})


/**
 * Evento de submit.
 * Criando uma nova despesa o evento verificar se todos os dados obrigatórios 
 * da despesas foram preenchido, caso estiver tudo correto enviamos os dados via 
 * ajax para a rota de back-end com o ajax.
 */
$('.newExpensesForm form').on('submit', function(e) {
  e.preventDefault();
})




function executeModalUpdateExpenses() {
  $('.updateExpensesArea').show();
  $('.updateExpensesArea').addClass('ExpensesAreaAnimation');

  $('.updateExpensesExit').click(() => {
    $('.updateExpensesArea').removeClass('ExpensesAreaAnimation');
    setTimeout(() => {  
      $('.updateExpensesArea').hide();
    }, 1000);
  })
}
/**
 * Função para atualizar uma despesa.
 * Vai abrir o modal com os dados daquela despesa nesse modal, para o usuário 
 * atualizar os dados.
 * @param {String} id 
 */
function updateExpenses(id) {

}

/*
* Executar quando carregar a página phtml
*/
$(document).ready(() => {
  expenseNav.hide();
  categoryNav.hide();
  $('.newExpensesArea').hide();
  $('.fixed').hide();
  $('.parcel').hide();
  $('.updateExpensesArea').hide();
  $('#messege').hide();
})