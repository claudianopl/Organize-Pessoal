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

/* ------ Menu responsivo ------*/

function toggleMenu() {
  let menu = document.getElementById('menu')

  if(menu.style.opacity == '1') {
    //menu.slideToggle(slow)
    menu.style.transform = 'translateX(100%)'
    menu.style.opacity = '0';
  } else {
    menu.style.transform = 'translateX(0)'
    menu.style.opacity = '1';
  }
}

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


