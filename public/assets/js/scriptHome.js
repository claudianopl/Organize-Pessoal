/* ------ Debounce do Lodash ------ */
const debounce = function(func, wait, immediate) {
  let timeout;
  return function(...args) {
    const context = this;
    const later = function () {
      timeout = null;
      if (!immediate) func.apply(context, args);
    };
    const callNow = immediate && !timeout;
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
    if (callNow) func.apply(context, args);
  };
};

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
$('.sectionFourHelp').click(function() {
  const hCollapse = $(this).find('div.hCollapse');
  const hCollapseIcon = $(this).find('h4.hCollapseIcon');

  hCollapse.slideToggle('slow');
  hCollapseIcon.toggleClass('iconPlus');
  hCollapseIcon.toggleClass('iconMinus');
});


/* ------ Ação para animar os scroll ------ */
const target = document.querySelectorAll('[data-anime]');
const animationClass = 'animateStart';

function animeScroll() {
 const windowTop = window.pageYOffset + (window.innerHeight * 0.8);
 target.forEach((e) => {
  if(windowTop > e.offsetTop) {
    e.classList.add(animationClass);
  }
 })
}

animeScroll();

if(target.length) {
  window.addEventListener('scroll', debounce(() => {
    animeScroll();
  }, 200));
}


/* ------ Executa quando após o carregamento dos documentos ------ */
$(document).ready(() => {
  // Mostrando a descrição principal e o botão inscreva-se após a escrita
  setTimeout(()=> {load($('.sectionHomeDesc'))}, 6000);
  setTimeout(()=> {load($('.sectionHomeButtonCadastrar'))}, 6500);

  // Deixando o mais pergunta com display none
  $('.hCollapse').hide();
})


