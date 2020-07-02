/*
* Constantes globais
*/
const receive = $('.sectionAppTwoFilterActiveReceive');
const receiveSelect = $('.sectionAppTwoFilterActiveReceive h4');
const receiveNav = $('.sectionAppTwoFilterNavReceive');

const category = $('.sectionAppTwoFilterActiveCategory');
const categorySelect = $('.sectionAppTwoFilterActiveCategory h4')
const categoryNav = $('.sectionAppTwoFilterNavCategory');

// ----> Receitas <----
/*
* Evento de click
* Atua na abertura da nav receitas com as opções de filtro
*/
receive.click(() => {
  receiveNav.slideToggle('slow');
})

/*
* Evento de click
* Atua na seleção das opções da nav receita, ou seja, ao selecionar ele altera
* o h4 da receita para a que foi selecionada.
*/

receiveNav.click((e) => {
  const select = e.target.innerHTML;
  receiveSelect.html(select)
  receiveNav.slideToggle('slow');
})

// ----> Category <----
/*
* Evento de click
* Atua na abertura da nav categorias com as opções de filtro
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
  categorySelect.html(select)
  categoryNav.slideToggle('slow')
})


/**
 * Executando o modal quando solicitado.
 */
function executeModal(){
  $('.newReceiveArea').show();
  $('.newReceiveArea').addClass('newReceiveAreaAnimation');

  $('.newReceiveFormExit').click(() => {
    $('.newReceiveArea').removeClass('newReceiveAreaAnimation');
    setTimeout(() => {  
      $('.newReceiveArea').hide();
    }, 1000);
  })
}

$('.sectionAppTwoRecive').click(() => {
  executeModal();
})



/**
 * Função para filtrar as receitas.
 * A função captura os valores informados pelo usuário e envia para uma rota do 
 * back-end responsável por filtrar e retornar os valores filtrados que por sua 
 * vez ele adicionar o html da layout.
 * @param {String} receive
 * @param {String} category
 * @param {String} date
 */
function sectionAppFilter(receive, category, date) {
  let filterReceive = receiveSelect.html();
  let filterCategory = categorySelect.html();

  if(filterReceive == 'Receitas') {
    filterReceive = '';
  }
  if(filterCategory == 'Categoria') {
    filterCategory = '';
  }
  
  if(filterReceive != '' || filterCategory != '') {
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

function newReceiveInvalid(info) {
    $('.loadingArea').hide();
    $('.newReceiveForm p').addClass('error');
    $('.newReceiveForm p').html(`${info} inválido, por favor, verifique as informações.`);
}

$('.newReceiveArea form').on('submit', function (e) {
  e.preventDefault();
  $('.loadingArea').show();
  let form = $(this).serializeArray();
  const desc = form[0];
  const value = form[1];
  const date = form[2];
  const wallet = form[3];
  const category = form[4];
  const repetition = form[5];
  let validate = true
  if(desc.value.length < 3 || value.value == '' || date.value == '' || 
  wallet.value == '' || category.value == '' || repetition.value == '') {
    newReceiveInvalid('Informação');
    validate = false;
  }
  
  if(validate) {
    $.ajax({
      type: 'post',
      url: '/app/insertData?location=receive',
      data: form,
      dataType: 'json',
      success: (d) => {
        if(d.messege == 'success') {
          location.href='/app/receitas';
        } else {
          $('.loadingArea').hide();
          $('.newReceiveForm p').addClass('error');
          $('.newReceiveForm p').html(`Um erro inesperado aconteceu, tente novamente mais tarde!`);
        }
      }
    })
  }
  
})

/**
 * Abrindo as opções de fixas e parcelas.
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


/*
* Executar quando carregar a página phtml
*/
$(document).ready(() => {
  $('.sectionAppTwoFilterNavReceive').hide();
  $('.sectionAppTwoFilterNavCategory').hide();
  $('.newReceiveArea').hide();
  $('.fixed').hide();
  $('.parcel').hide();
})



/**
 * Função para remover uma receita.
 * Após remover, vamos apresentar uma mensagem ao usuário informando que foi 
 * removida com sucesso.
 * @param {String} id 
 */
function removeReceive(id) {
  console.log(id);
}

/**
 * Função para atualizar uma receita.
 * Vai abrir o modal com os dados daquela receita nesse modal, para o usuário 
 * atualizar os dados.
 * @param {String} id 
 */
function updateReceive(id) {
  console.log(id);
}

/**
 * Função para concluir uma receita.
 * Após concluir, vamos apresentar uma mensagem ao usuário informando que foi 
 * concluido com sucesso.
 * @param {String} id 
 */
function concludeReceive(id) {
  console.log(id);
}