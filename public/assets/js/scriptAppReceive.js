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
 * Função para filtrar as receitas.
 * A função captura os valores informados pelo usuário e envia para uma rota do 
 * back-end responsável por filtrar e retornar os valores filtrados que por sua 
 * vez ele adicionar o html da layout.
 */
function sectionAppFilter() {
  let filterStatus = receiveSelect.html();
  let filterCategory = categorySelect.html();
  let filterDate = $('.sectionAppTwoFilterDadosDate input').val();

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
  if (filterStatus == 'Receitas Recebidas'){
    filterStatus = 1;
  }
  else if(filterStatus == 'Receitas Não Recebidas') {
    filterStatus = 0;
  } 
  const dataObj = {
    'status':filterStatus, 
    'category':filterCategory, 
    'date':filterDate
  };
  
  
  /**
   * Enviando dados via ajax para a rota do back-end.
   */
  $.ajax({
    type: 'post',
    url: '/app/filterReceive',
    data: dataObj,
    dataType: 'json',
    success: (d) => {
      if(d.data.length > 0) {
        $('.sectionAppTable').show('slow');
        $('.sectionAppThreeMessage').hide();

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


          $('.payReceived h4').html(`Recebido: R$${d.sum.paymentReceived}`);
          $('.payReceivable h4').html(`Recebido: R$${d.sum.paymentNotReceived}`);
        });
        
      } 
      else {
        $('.payReceived h4').html(`Recebido: R$${d.sum.paymentReceived}`);
        $('.payReceivable h4').html(`Recebido: R$${d.sum.paymentNotReceived}`);
        $('.sectionAppTable').hide();
        $('.sectionAppThreeMessage h3').html('Você não possui contas neste filtro.')
        $('.sectionAppThreeMessage').show('slow');
      }
    }
  })
}


/**
 * Evento de click.
 * Atua na execução o modal de inserção da receita.
 */
$('.sectionAppTwoRecive').click(() => {
  $('.newReceiveArea').show();
  $('.newReceiveArea').addClass('ReceiveAreaAnimation');

  $('.newReceiveFormExit').click(() => {
    $('.newReceiveArea').removeClass('ReceiveAreaAnimation');
    setTimeout(() => {  
      $('.newReceiveArea').hide();
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
 * Criando uma nova receita.
 * o evento verificar se todos os dados obrigatórios da receita foram preenchido,
 * caso estiver tudo correto enviamos os dados via ajax para a rota de back-end
 * com o ajax.
 */
$('.newReceiveArea form').on('submit', function (e) {
  e.preventDefault();
  //$('.loadingArea').show();
  let form = $(this).serializeArray();
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
    $('.newReceiveForm p').addClass('error');
    $('.newReceiveForm p').html('Informação inválido, por favor, verifique as informações.');
    validate = false;
  }
  if(parseInt(parcel.value) > 420) {
    $('.loadingArea').hide();
    $('.newReceiveForm p').addClass('error');
    $('.newReceiveForm p').html('Número de parcelas máximas são de 420, por favor, altere as parcelas.');
    validate = false;
  }
  if(validate) {
    $.ajax({
      type: 'post',
      url: '/app/insertReceive',
      data: form,
      dataType: 'json',
      success: (d) => {
        if(d.messege == 'success') {
          location.reload();
        } else {
          $('.loadingArea').hide();
          $('.newReceiveForm p').addClass('error');
          $('.newReceiveForm p').html(d.messege);
        }
      }
    })
  }
})


/**
 * Função para remover uma receita.
 * Após remover, vamos apresentar uma mensagem ao usuário informando que foi 
 * removida com sucesso.
 * @param {String} id 
 */
function removeReceived(id) {
  $.ajax({
    type: 'post',
    url: '/app/removeReceived',
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
function executeModalUpdateReceived(){
  $('.updateReceiveArea').show();
  $('.updateReceiveArea').addClass('ReceiveAreaAnimation');

  $('.updateReceiveFormExit').click(() => {
    $('.updateReceiveArea').removeClass('ReceiveAreaAnimation');
    setTimeout(() => {  
      $('.updateReceiveArea').hide();
    }, 1000);
  })
}

/**
 * Função para atualizar uma receita.
 * Vai abrir o modal com os dados daquela receita nesse modal, para o usuário 
 * atualizar os dados.
 * @param {String} id 
 */
function updateReceived(id) {
  executeModalUpdateReceived();
  /**
   * Ajax para retornar e preencher os dados da receita a ser atualizada
   */
  $.ajax({
    type: 'post',
    url: '/app/updateReceived',
    data: {'type':'filter','id':id},
    dataType: 'json',
    success: d => {
      $('#updateReceiveDesc').val(d.description);
      $('#updateReceiveValue').val(d.value);
      $('#updateReceiveDate').val(d.date);
      $('#updateReceiveWallet').val(d.id_wallet);
      $('#updateReceiveCategory').val(d.category);
    }
  })

  /**
   * Evento de submit.
   * O evento verificar se todos os dados obrigatórios da receita foram preenchido,
   * caso estiver tudo correto enviamos os dados via ajax para a rota de back-end
   * para efeturamos o update.
   */
  $('.updateReceiveForm form').on('submit', function (e) {
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
        url: '/app/updateReceived',
        data: {'type':'update','id':id, 'form':form},
        dataType: 'json',
        success: d => {
          if(d.messege == 'success'){
            location.reload();
          }
          else {
            $('.loadingArea').hide();
            $('.updateReceiveForm p').addClass('error');
            $('.updateReceiveForm p').html(d.messege);
          }
        }
      })
    }
  })

  
}

/**
 * Função para concluir uma receita.
 * @param {String} id 
 */
function concludeReceived(id) {
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
        $('#messege').show('slow');
        $('#messege').html('Ops... Um error inesperado aconteceu.');
        $('#messege').addClass('error');
      }
    }
  });
}


/**
 * Evento para executar as funções quando carregar a página phtml.
 */
$(document).ready(() => {
  $('.sectionAppTwoFilterNavReceive').hide();
  $('.sectionAppTwoFilterNavCategory').hide();
  $('.newReceiveArea').hide();
  $('.fixed').hide();
  $('.parcel').hide();
  $('.updateReceiveArea').hide();
  $('#messege').hide();
})