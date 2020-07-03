/**
 * Constantes globais para fazer a filtragem.
 */
const receive = $('.sectionAppTwoFilterActiveReceive');
const receiveSelect = $('.sectionAppTwoFilterActiveReceive h4');
const receiveNav = $('.sectionAppTwoFilterNavReceive');

const category = $('.sectionAppTwoFilterActiveCategory');
const categorySelect = $('.sectionAppTwoFilterActiveCategory h4')
const categoryNav = $('.sectionAppTwoFilterNavCategory');


/**
 * Evento de click.
 * Atua na abertura da nav receitas com as opções de filtro.
 */
receive.click(() => {
  receiveNav.slideToggle('slow');
})


/**
 * Evento de click.
 * Atua na seleção das opções da nav receita, ou seja, ao selecionar ele altera 
 * o h4 da receita para a que foi selecionada.
 */
receiveNav.click((e) => {
  const select = e.target.innerHTML;
  receiveSelect.html(select)
  receiveNav.slideToggle('slow');
})


/**
 * Evento de click.
 * Atua na abertura da nav categorias com as opções de filtro.
 */
category.click(() => {
  categoryNav.slideToggle('slow');
})


/**
 * Evento de click.
 * Atua na seleção das opções da nav categoria, ou seja, ao selecionar ele altera 
 * o h4 da categoria para a que foi selecionada.
 */
categoryNav.click((e) => {
  const select = e.target.innerHTML;
  categorySelect.html(select)
  categoryNav.slideToggle('slow')
})


/**
 * Função de execução do modal quando solicitado.
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

/**
 * Evento de click.
 * Atua na execução o modal.
 */
$('.sectionAppTwoRecive').click(() => {
  executeModal();
})


/**
 * Função para filtrar as receitas.
 * A função captura os valores informados pelo usuário e envia para uma rota do 
 * back-end responsável por filtrar e retornar os valores filtrados que por sua 
 * vez ele adicionar o html da layout.
 */
function sectionAppFilter() {
  let filterStatus = receiveSelect.html();
  let filterCategory = categorySelect.html();
  let filterDate = $('.sectionAppTwoFilterDadosDate input').val();
  let data = null

  if(filterStatus == 'Receitas') {
    filterStatus = '';
  }
  if(filterCategory == 'Categoria') {
    filterCategory = '';
  }
  if(filterDate == undefined) {
    filterDate='';
  }

  /**
   * Criando a data do ajax.
   * Formatando e criando um objeto denominado dataObj que recebe os dados da 
   * filtragem, se algum dado não for vazio, formatamos o status da receita e 
   * criamos o dataObj com os dados da filtragem do usuário para enviarmos 
   * ao ajax. Caso contrário, criamos o dataObj com todos os dados vazios.
   */
  if(filterStatus != '' || filterCategory != '' || filterDate != '') {
    if(filterStatus == 'Receitas Não Recebidas') {
      filterStatus = 0;
    } else if (filterStatus == 'Receitas Recebidas'){
      filterStatus = 1;
    }
    const dataObj = {
      'status':filterStatus, 
      'category':filterCategory, 
      'date':filterDate
    };
    data = dataObj;
  } 
  else {
    const dataObj = {
      'status':filterStatus, 
      'category':filterCategory, 
      'date':filterDate
    };
    data = dataObj;
  }
  
  /**
   * Enviando dados via ajax para a rota do back-end.
   */
  $.ajax({
    type: 'post',
    url: '/app/filterReceive',
    data: data,
    dataType: 'json',
    success: (d) => {
      if(d.length > 0) {
        $('.sectionAppTable').show('slow');
        $('.sectionAppThreeMessage').hide();

        $('.sectionAppTableItem').remove()

        let fatherTable = $('.sectionAppTable');

        d.forEach(element => {
          let sectionAppTableItem = document.createElement('article');
          sectionAppTableItem.className = 'sectionAppTableItem';

          let desc = document.createElement('p');
          desc.className = 'desc';
          desc.innerHTML = element.description;

          let date = document.createElement('p');
          date.className = 'date';
          date.innerHTML = element.date;

          let category = document.createElement('p');
          category.className = 'category';
          category.innerHTML = (element.category);

          let enrollment = document.createElement('p');
          enrollment.className = 'enrollment';
          if(element.enrollment == 'Parcelada') {
            if(element._n_parcel_pay == null) {
              enrollment.innerHTML = '0/'+element.n_parcel;
            }
            else {
              enrollment.innerHTML = element._n_parcel_pay+'/'+element.n_parcel;
            }
          }
          else if(element.enrollment == 'Fixa') {
            enrollment.innerHTML = element.status_parcel_fixed;
          }
          else {
            enrollment.innerHTML = element.enrollment;
          }

          let price = document.createElement('p');
          price.className = 'price';
          price.innerHTML = `R$${element.value} 
          <img src="/assets/images/app/appGlobal/remove.svg"
          onclick="removeReceive('${element.id}')">
          <img src="/assets/images/app/appGlobal/update.svg"
          onclick="updateReceive('${element.id}')">
          <img src="/assets/images/app/appGlobal/conclude.svg"
          onclick="concludeReceive('${element.id}')">
          `;

          sectionAppTableItem.append(desc);
          sectionAppTableItem.append(date);
          sectionAppTableItem.append(category);
          sectionAppTableItem.append(enrollment);
          sectionAppTableItem.append(price);

          fatherTable.append(sectionAppTableItem);
        });
      } 
      else {
        $('.sectionAppTable').hide();
        $('.sectionAppThreeMessage').show('slow');
      }
    }
  })
}


/**
 * A função informa ao usuário que alguma informação está inválida.
 */
function newReceiveInvalid() {
    $('.loadingArea').hide();
    $('.newReceiveForm p').addClass('error');
    $('.newReceiveForm p').html(`Informação inválido, por favor, verifique as informações.`);
}


/**
 * Evento de submit.
 * Criando uma nova receita.
 * o evento verificar se todos os dados obrigatórios da receita foram preenchido,
 * caso estiver tudo correto enviamos os dados via ajax para a rota de back-end
 * com o ajax.
 */
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
    newReceiveInvalid();
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
 * Evento change.
 * O evento abre as opções que estão dentro do select de recepção, que são as 
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
 * Evento para executar as funções quando carregar a página phtml.
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
 * concluído com sucesso.
 * @param {String} id 
 */
function concludeReceive(id) {
  console.log(id);
}