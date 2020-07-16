document.addEventListener('DOMContentLoaded', function() {
  /**
   * Ajax para sempre retornar o nome do usuário.
   */
  $.ajax({
    type: 'post',
    url: '/app/headerUserName',
    dataType: 'json',
    success: d => {
      $('.headerAppUser h4').html(d.userName);
    }
  })
  /**
   * Se comunica com o back-end através do ajax.
   * A função envia os dados, para o back-end do laytou app, foi criada porque 
   * é executada duas vezes, uma quando o usuário entra na página app e outra
   * quando o usuário solicita uma nova data.
   * @param {String} date 
   */
  function getAjax(date) {
    const url = window.location.href.split('/');
    if(url[url.length - 1] == 'app') {
      $.ajax({
        type: 'post',
        url: '/app/dashboardCalendar',
        data: `date=${date}`,
        dataType: 'json',
        success: d => {
          $(".sectionAppHomeOneInfoHeaderReceiveInfo p").html(`R$${d.sum.sumReceived}`);
          $(".sectionAppHomeOneInfoHeaderExpensesInfo p").html(`R$${d.sum.sumExpenses}`);

          /**
           * Removendo o gráfico geral e criando um novo.
           */
          $('.GraphicGeneral').remove();
          let fatherGraphic = $('.sectionAppHomeOneInfoGraphicGeneral');
          
          let GraphicGeneral = document.createElement('div');
          GraphicGeneral.className = 'GraphicGeneral';

          let canvasGraficGeneral = document.createElement('canvas');
          canvasGraficGeneral.id = 'GraphicGeneral';

          GraphicGeneral.append(canvasGraficGeneral);
          fatherGraphic.append(GraphicGeneral);

          /**
           * Removendo o gráfico detalhado e criando um novo.
           */
          $('.GraphicDetailed').remove();
          let fatherGraphicDetailed = $('.sectionAppHomeOneInfoGraphicDetailed');
          
          let createGraphicDetailed = document.createElement('div');
          createGraphicDetailed.className = 'GraphicDetailed';

          let createCanvasGraphicDetailed = document.createElement('canvas');
          createCanvasGraphicDetailed.id = 'GraphicDetailed';

          createGraphicDetailed.append(createCanvasGraphicDetailed);
          fatherGraphicDetailed.append(createGraphicDetailed);

          
          /**
           * Gráfico de receitas x despesas
           */
          let days = Array();
          let sumReceived = Array();
          let sumExpenses = Array();
          d.graphic.received.forEach(element => {
            const date = element.day.split('-');
            days.push(`${date[2]}`);
            sumReceived.push(element.amount);
          });
          d.graphic.expenses.forEach(element => {
            sumExpenses.push(element.amount);
          })

          let ctx = document.getElementById('GraphicGeneral').getContext("2d");
          var graphic = new Chart(ctx, {
            type: 'line',
            data: {
              labels: days,
              datasets: [{
                label: 'Receita',
                backgroundColor: "#34F06F",
                borderColor: "#34F06F",
                data: sumReceived,
                borderWidth: 2,
                fill: false
              }, {
                label: 'Despesa',
                backgroundColor: "#E34E4E",
                borderColor: "#E34E4E",
                data: sumExpenses,
                borderWidth: 2,
                fill: false
              }] // datasets
            }, // data
            options: {
              title: {
                display: true,
                fontSize: 16,
                text: 'Relatório mensal: Receitas x Despesas'
              }
            }
          });

          /**
           * Gráfico detalhado.
           */
          let labelGraphic = Array();
          let graphicValue = Array();
          d.graphic.detailed.forEach(element => {
            labelGraphic.push(element.category);
            graphicValue.push(element.sumGraphic);
          })
          let GraphicDetailed = document.getElementById('GraphicDetailed').getContext("2d");
          var GraphicBar = new Chart(GraphicDetailed, {
            type: 'horizontalBar',
            data: {
              labels: labelGraphic,
              datasets: [{
                label: '',
                backgroundColor: ["#D874A8","#C683E4","#8193E1","#F67CFF",
                "#C657CE","#FF6C6C","#6470BA","#626491","#74E88B",
                "#FF977C","#FF70A1","#FF8A72","#FFBE57","#72B4E1",
                "#446FF5","#44BCE1","#E16B6B"],
                data: graphicValue,
              }] // datasets
            }, // data
            options: {
              title: {
                display: true,
                fontSize: 16,
                text: 'Relatório despesas detalhadas'
              },
              layout: {
                padding: {
                    left: 0,
                    right: 10,
                    top: 0,
                    bottom: 0
                }
              }
            }
          })
        }
      })
    }
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
    return `${dateMonth+1}-${dateYear}`
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