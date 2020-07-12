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
  let filterStatus = expenseSelect.html();
  let filterCategory = categorySelect.html();
  let filterDate = $('.sectionAppTwoFilterDadosDate input').val();
  if(filterStatus == 'Todas despesas') {
    filterStatus = '';
  }
  if(filterCategory == 'Todas categorias') {
    filterCategory = '';
  }
  if(filterDate == undefined) {
    filterDate='';
  }

  /**
   * Criando a data do ajax.
   * Formatando e criando um objeto denominado dataObj que recebe os dados da 
   * filtragem, se algum dado não for vazio, formatamos o status da daspesa e 
   * criamos o dataObj com os dados da filtragem do usuário para enviarmos 
   * ao ajax. Caso contrário, criamos o dataObj com todos os dados vazios.
   */
  if (filterStatus == 'Despesas Pagas'){
    filterStatus = 1;
  }
  else if(filterStatus == 'Despesas Não Pagas') {
    filterStatus = 0;
  } 
  const dataObj = {
    'status':filterStatus, 
    'category':filterCategory, 
    'date':filterDate
  };

  $.ajax({
    type: 'post',
    url: '/app/filterExpenses',
    data: dataObj,
    dataType: 'json',
    success: d => {
      if(d.data.length > 0) {
        $('.sectionAppTable').show('slow');
        $('.sectionAppMessageExpense').hide();

        $('.sectionAppTableItem').remove();
        let fatherTable = $('.sectionAppTable');

        d.data.forEach(element => {
          let sectionAppTableItem = document.createElement('article');
          sectionAppTableItem.className = 'sectionAppTableItem';
          sectionAppTableItem.id = element.id;

          let desc = document.createElement('p');
          desc.className = 'desc';
          desc.innerHTML = element.description;

          let date = document.createElement('p');
          date.className = 'date';
          let extractDate = element.date.split('-');
          date.innerHTML = `${extractDate[2]}/${extractDate[1]}/${extractDate[0]}`;
        
          let category = document.createElement('p');
          category.className = 'category';
          category.innerHTML = (element.category);

          let enrollment = document.createElement('p');
          enrollment.className = 'enrollment';
          if(element.enrollment == 'Parcelada') { 
            enrollment.innerHTML = element.n_parcel_pay+'/'+element.n_parcel;
          }
          else if(element.enrollment == 'Fixa') {
            enrollment.innerHTML = element.status_parcel_fixed;
          }
          else {
            enrollment.innerHTML = element.enrollment;
          }

          let price = document.createElement('p');
          price.className = 'price';
          if(element.status == 0) {
            price.innerHTML = `R$${element.value} 
            <img src="/assets/images/app/appGlobal/remove.svg"
            onclick="removeReceived('${element.id}')">
            <img src="/assets/images/app/appGlobal/update.svg"
            onclick="updateReceived('${element.id}')">
            <img src="/assets/images/app/appGlobal/conclude.svg"
            onclick="concludeReceived('${element.id}')">`;
          }
          else {
            price.innerHTML = `R$${element.value} 
            <img src="/assets/images/app/appGlobal/remove.svg"
            onclick="removeReceived('${element.id}')">
            <img src="/assets/images/app/appGlobal/update.svg"
            onclick="updateReceived('${element.id}')">`;
          }

          sectionAppTableItem.append(desc);
          sectionAppTableItem.append(date);
          sectionAppTableItem.append(category);
          sectionAppTableItem.append(enrollment);
          sectionAppTableItem.append(price);

          fatherTable.append(sectionAppTableItem);

          $('.paidExpenses h4').html(`Recebido: R$${d.sum.ExpensesPayme}`);
          $('.payExpenses h4').html(`Recebido: R$${d.sum.ExpensesNotPayme}`);
        })
      }
      else {
        $('.paidExpenses h4').html(`Recebido: R$${d.sum.ExpensesPayme}`);
        $('.payExpenses h4').html(`Recebido: R$${d.sum.ExpensesNotPayme}`);
        $('.sectionAppTable').hide();
        $('.sectionAppMessageExpense h3').html('Você não possui contas neste filtro.')
        $('.sectionAppMessageExpense').show('slow');
      }
    }
  })
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
  $('.loadingArea').show();
  const form = $(this).serializeArray();
  const desc = form[0];
  const value = form[1];
  const date = form[2];
  const wallet = form[3];
  const category = form[4];
  const repetition = form[5];
  const parcel = form[7];
  let validate = true
  if(desc.value.length < 3 || value.value == '' || date.value == '' || 
  wallet.value == '' || category.value == '' || repetition.value == '') {
    $('.loadingArea').hide();
    $('.newExpensesForm p').addClass('error');
    $('.newExpensesForm p').html('Informação inválido, por favor, verifique as informações.');
    validate = false;
  }
  if(parseInt(parcel.value) > 420) {
    $('.loadingArea').hide();
    $('.newExpensesForm p').addClass('error');
    $('.newExpensesForm p').html('Número de parcelas máximas são de 420, por favor, altere as parcelas.');
    validate = false;
  }
  if(validate) {
    $.ajax({
      type: 'post',
      url: '/app/insertExpenses',
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
 * Função para remover uma despesa.
 * @param {String} id 
 */
function removeExpense(id) {
  $('.loadingArea').show();
  $.ajax({
    type: 'post',
    url: '/app/expensesRemove',
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

/**
 * Função de execução do modal de atualização quando solicitado.
 */
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
function updateExpense(id) {
  executeModalUpdateExpenses();
   /**
   * Ajax para retornar e preencher os dados da despesa a ser atualizada.
   */
  $.ajax({
    type: 'post',
    url: '/app/updateExpenses',
    data: {'type':'filter','id':id},
    dataType: 'json',
    success: d => {
      $('#updateExpensesDesc').val(d.description);
      $('#updateExpensesValue').val(d.value);
      $('#updateExpensesDate').val(d.date);
      $('#updateExpensesWallet').val(d.id_wallet);
      $('#updateExpensesCategory').val(d.category);
      $('#updateExpensesStatus').val(d.status);
    }
  })

  /**
   * Evento de submit.
   * O evento verificar se todos os dados obrigatórios da despesa foram 
   * preenchida, caso estiver tudo correto enviamos os dados via ajax para a 
   * rota de back-end para efeturamos o update.
   */
  $('.updateExpensesForm form').on('submit', function (e) {
    e.preventDefault();
    $('.loadingArea').show();
    let form = $(this).serializeArray();
    const desc = form[0];
    const value = form[1];
    const date = form[2];
    const wallet = form[3];
    const category = form[4];
    let validate = true;
    if(desc.value.length < 3 || value.value == '' || date.value == '' || 
    wallet.value == '' || category.value == '') {
      $('.loadingArea').hide();
      $('.updateReceiveForm p').addClass('error');
      $('.updateReceiveForm p').html('Informação inválido, por favor, verifique as informações.');
      validate = false;
    }
    if(validate) {
      $.ajax({
        type: 'post',
        url: '/app/updateExpenses',
        data: {'type':'update','id':id, 'form':form},
        dataType: 'json',
        success: d => {
          if(d.messege == 'success'){
            location.reload();
          }
          else {
            $('.loadingArea').hide();
            $('.updateExpensesForm p').addClass('error');
            $('.updateExpensesForm p').html(d.messege);
          }
        }
      })
    }
  })
}

/**
 * Função para concluir uma despesa.
 * @param {String} id 
 */
function concludeExpense(id) {
  $('.loadingArea').show();
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