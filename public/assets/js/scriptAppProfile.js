/*
* Constantes globais
*/
const gender = $('.appProfileGenderSelect');
const genderNav = $('.appProfileGenderNav');
/*
* Evento de click
* Atua na abertura da nav de gênero com as opções a escolher
*/
gender.click(() => {
  genderNav.slideToggle('fast');
})

/*
* Evento de click
* Atua na seleção das opções da nav gênero, ou seja, ao selecionar ele altera
* o value do input gênero para a que foi selecionada.
*/
genderNav.click((e) => {
  const select = e.target.innerHTML;
  $('.appProfileGenderSelect input').val(select);
  genderNav.slideToggle('fast');
})


/*
* Executar quando carregar a página phtml
*/
$(document).ready(() => {
  genderNav.hide();
})