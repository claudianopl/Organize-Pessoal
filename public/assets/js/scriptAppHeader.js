document.addEventListener('DOMContentLoaded', function() {
  /**
   * Se comunica com o back-end através do ajax.
   * A função envia os dados, para o back-end do laytou app, foi criada porque 
   * é executada duas vezes, uma quando o usuário entra na página app e outra
   * quando o usuário solicita uma nova data.
   * @param {String} date 
   */
  function getAjax(date) {
    /*
    let url = window.location.pathname.split('/');
    const cauntUrl = url.length - 1;
    if(url[cauntUrl] == 'app') {
      url = '/app/dateApp';
    } 
    o calendário só sera usado na página app,
    então a url vai ser o back-end da página app
    
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
    */
  }

  /**
  * Formata o calendário para de int para string.
  * A função seleciona o mês e o ano atual e formata os valores para string e 
  * apresenta no calendário para o usuário.
  * @param {Number} dateMonth 
  * @param {Number} dateYear 
  * @return {String}
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
    return `${dateMonth}-${dateYear}`
  }

  /**
   * Selecionando o mês atual e o ano atual para serem apresentados ao usuário.
   */
  const dt = new Date();
  const month = dt.getMonth();
  const year = dt.getFullYear();
  /**
   * Executa a função para formatar o calendário e retorna a data formata em string.
   */
  getAjax(getDaysCalender(month, year));

  


  /**
  * Seleciona o próximo ano, ou o ano anterior.
  */
  const btnPrev = document.querySelector(".ButtonPrevious");
  const btnNext = document.querySelector(".ButtonNext");
  // Ano anterior.
  btnPrev.onclick = () => {
    year--;
    document.querySelector('.year').innerHTML = year;
  }
  // Proximo ano.
  btnNext.onclick = () => {
    year++;
    document.querySelector('.year').innerHTML = year;
  }

  /**
   * Seleciona a data que o usuário requisitou.
   * Após selecionar a data que o usuário requisitou, a função faz com que essa
   * data fique ativado no front-end.
   * @param {String} dateMonth 
   */
  function selectDate(dateMonth) {
    // Para converter o mes para numero.
    monthSelect = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun','Jul', 
    'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    // Para trazer o ano que foi selecionado.
    let yearSelect = document.querySelector('.year').innerHTML;
    // Seleciona o mes que foi selecionado junto com o ano para deixar a class active.
    const table = document.querySelectorAll(".month td");
    for(let i=0; i < table.length; i++){
      if(table[i].innerText === dateMonth) {
        document.querySelector('.monthSelectActive h4').innerHTML = dateMonth;
        table[i].classList.add("tdActive");
      }
    }

    // Transformando o mês em numero.
    let monthNum = null;
    for(let i=0; i<=monthSelect.length; i++) {
      if(monthSelect[i] == dateMonth) {
        monthNum = i+1;
      }
    }

    // Enviar essa informação para o php para fazer a filtragem do mês e ano.
    return `${monthNum}-${yearSelect}`;
  }
  
  /**
   * Seleciona o mês e o ano no calendário e envia esses dados para serem tratados
   * na função selectDate.
   */
  $('.monthSelect').click((e) => {
    $('.monthSelect').removeClass('tdActive');
    // Caputrando o mes escolhido e enviando para a function.
    let monthSelect = e.target.innerHTML;
    // Recebendo os dados da function.
    let date = selectDate(monthSelect);
    //Envia o ados para o getAjax para enviar via ajax para o php.
    getAjax(date)
    // Fechando calendario após selecionar o mes e o ano.
    $('.appCalendar').slideToggle();
  })


  /**
   * Abrindo o calendário quando for solicitado pelo usuário.
   */
  $('.monthSelectActive').click((e) => {
    $('.appCalendar').slideToggle();
  })

  /**
  * Executando métodos quando a página for carregada.
  */
  $(document).ready(() => {
    // Deixando o calendario com display none.
    $('.appCalendar').hide();
    /**
     * Escondendo o calendário do header nas demais layouts.
     */
    let url = window.location.pathname.split('/');
    const cauntUrl = url.length - 1;
    if(url[cauntUrl] != 'app') {
      $('.headerAppCalendarArea').hide();
    } 
  })

}, false);