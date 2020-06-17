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


/*
* Executar quando carregar a página phtml
*/

$(document).ready(() => {
  $('.sectionAppTwoFilterNavReceive').hide();
  $('.sectionAppTwoFilterNavCategory').hide();
})


/*
* Função para capturar os valores adicionados pelo usuário, para enviar com o 
* ajax para a rota do back-end para fazer a manipulação de dados e retornar os 
* dados filtrados pelo php e fazer o carregamento dos dados ajax.
*/
function sectionAppFilter() {
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