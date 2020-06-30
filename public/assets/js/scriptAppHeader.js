document.addEventListener('DOMContentLoaded', function() {
  /*
  * 
  * Função global para fazer requisições ajax e enviar as informações para o 
  * servidor
  */
  function getAjax(date) {
    let url = window.location.pathname.split('/');
    const cauntUrl = url.length - 1;
    if(url[cauntUrl] == 'app') {
      url = '/app/dateApp';
    } 
    else if(url[cauntUrl] == 'receitas') {
      url = '/app/dateReceive';
    } 
    else if(url[cauntUrl] == 'despesas') {
      /*
      * Colocar a url como a rota para a requisição ajax
      * url = '/routeAjaxApp
      */
      console.log('despesas');
    } 
    else if(url[cauntUrl] == 'tarefas') {
      /*
      * Colocar a url como a rota para a requisição ajax
      * url = '/routeAjaxApp
      */
      console.log('tarefas');
    }
    else if(url[cauntUrl] == 'fixas') {
      /*
      * Colocar a url como a rota para a requisição ajax
      * url = '/routeAjaxApp
      */
      console.log('fixas');
    }
    else if(url[cauntUrl] == 'carteiras') {
      /*
      * Colocar a url como a rota para a requisição ajax
      * url = '/routeAjaxApp
      */
      console.log('carteiras');
    }

    
    $.ajax({
      type: 'post',
      url: url,
      data: `date=${date}`,
      //dataType: 'json',
      success: d => {
        $('.headerAppUser h4').html(d.name);
        if(url[cauntUrl] == 'app') {}
        else if(url[cauntUrl] == 'receitas') {
         
        } 
        else if(url[cauntUrl] == 'despesas') {}
        else if(url[cauntUrl] == 'tarefas') {}
        else if(url[cauntUrl] == 'fixas') {}
        else if(url[cauntUrl] == 'carteiras'){}
        console.log(d)
      },
      error: erro => {
        console.log('erro')
        console.log(erro)
      }
    })
    
  }

  /*
  * getDaysCalender(dateMonth, dateYear)
  * Seleciona o mês e o ano atual, converte esses valores para string e apresenta
  * esses valores ao usuário.
  */
  function getDaysCalender(dateMonth, dateYear) {
    monthSelect = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun','Jul', 
    'Ago', 'Set', 'Out', 'Nov', 'Dez'];

    document.querySelector('.monthSelectActive h4').innerHTML = monthSelect[dateMonth];
    document.querySelector('.year').innerHTML = dateYear;
    
    // percorrer as tds para selecionar o mes atual
    const table = document.querySelectorAll(".month td");
    for(let i=0; i < table.length; i++){
      if(table[i].innerText === monthSelect[dateMonth]) {
        table[i].classList.add("tdActive");
      }
    
    }

    return `${dateMonth}-${dateYear}`;
  }

  /*
  * Selecionando o mês atual e o ano atual para serem apresentados ao usuário
  */
  let dt = new Date();
  let month = dt.getMonth();
  let year = dt.getFullYear();
  getAjax(getDaysCalender(month, year));
  

  /*
  *  Selecionando o próximo ano ou o ano anterior
  */
  const btnPrev = document.querySelector(".ButtonPrevious");
  const btnNext = document.querySelector(".ButtonNext");

  btnPrev.onclick = () => {
    year--;
    document.querySelector('.year').innerHTML = year;
  }

  btnNext.onclick = () => {
    year++;
    document.querySelector('.year').innerHTML = year;
  }

  /*
  * selectDate(mes)
  * Seleciona a data que o usuário requisitou
  */

  function selectDate(dateMonth) {
    // Para converter o mes para numero
    monthSelect = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun','Jul', 
    'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    // Para trazer o ano que foi selecionado
    let yearSelect = document.querySelector('.year').innerHTML;
    // Seleciona o mes que foi selecionado junto com o ano para deixar a class active
    const table = document.querySelectorAll(".month td");
    for(let i=0; i < table.length; i++){
      if(table[i].innerText === dateMonth) {
        document.querySelector('.monthSelectActive h4').innerHTML = dateMonth;
        table[i].classList.add("tdActive");
      }
    }

    // Transformando o mês em numero
    let monthNum = null;
    for(let i=0; i<=monthSelect.length; i++) {
      if(monthSelect[i] == dateMonth) {
        monthNum = i+1;
      }
    }

    // Enviar essa informação para o php para fazer a filtragem do mês e ano
    return `${monthNum}-${yearSelect}`;
  }
  
  /*
  * Seleciona o mês e o ano no calendário e envia esses dados para serem tratados
  * na função selectDate
  */
  
  $('.monthSelect').click((e) => {
    $('.monthSelect').removeClass('tdActive');
    // Caputrando o mes escolhido e enviando para a function
    let monthSelect = e.target.innerHTML;
    // Recebendo os dados da function
    let date = selectDate(monthSelect);
    //Envia o ados para o getAjax para enviar via ajax para o php
    getAjax(date)
    // Fechando calendario após selecionar o mes e o ano
    $('.appCalendar').slideToggle();
  })


  /*
  * Abrindo o calendário quando for solicitado pelo usuário
  */

  $('.monthSelectActive').click((e) => {
    $('.appCalendar').slideToggle();
  })


  /*
  * Executando métodos quando a página for carregada
  */
  $(document).ready(() => {
    // Deixando o calendario com display none
    $('.appCalendar').hide();
  })

}, false);