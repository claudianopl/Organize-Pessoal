/* ------ Animação de digitação do titulo principal ------*/
const title = document.getElementById('homeTitleWriter');

// Função para fazer animação de escritura 
function typeWriter(element) {
  const elementArray = element.innerHTML.split('');
  element.innerHTML = '';
  
  elementArray.forEach((letter, i) => {
    setTimeout(() => element.innerHTML += letter, 75*i);
  });
}
typeWriter(title);



/* ------ animação carregar um elementos em determinado tempo ------ */
function load(element) {
  element.addClass('animateStart');
}

/* ------ Ação jQuery para animar os botões mais perguntas ------ */
$('.sectionHomeFourHelp').click(function() {
  const hCollapse = $(this).find('p.hCollapse');
  const hCollapseIcon = $(this).find('h4.hCollapseIcon');

  hCollapse.slideToggle('slow');
  hCollapseIcon.toggleClass('iconPlus');
  hCollapseIcon.toggleClass('iconMinus');
});

/* ------ Executa quando após o carregamento dos documentos ------ */
$(document).ready(() => {
  // Mostrando a descrição principal e o botão inscreva-se após a escrita
  setTimeout(()=> {load($('.sectionHomeOneInfoDesc'))}, 6000);
  setTimeout(()=> {load($('.sectionHomeOneInfoDescButton'))}, 6500);

  // Deixando o mais pergunta com display none
  $('.hCollapse').hide();
})


