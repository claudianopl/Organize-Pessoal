const btnSubmit = document.querySelector('.loginBtnSubmit');
const formLogin = document.querySelector('#formLogin');
const formMens = document.querySelector(".sectionLoginOneInfo p");
formMens.style.display = 'none';

btnSubmit.addEventListener('click', (event) => {
  // Pausa a função de click
  event.preventDefault();

  const fields = [...document.querySelectorAll('.inputBlock input')];

  // Adiciona a class com o evento invalid
  fields.forEach(field => {
    if(field.value === ''){
      formLogin.classList.add("validateError");
      formMens.style.display = 'block';
      formMens.innerHTML = 'Email ou senha invalidos.';
    }
  })

  /* Remove a class com o evento invalid e se caso não for invalido, 
  ele desaparece com o formulário */
  const formError = document.querySelector(".validateError");
  if(formError) {
    formError.addEventListener("animationend", event => {
      if(event.animationName === 'invalid') {
        formError.classList.remove('validateError');
      }
    })
  } else {
    formLogin.classList.add('loginFormHide')
    formMens.style.display = 'none';
  }

})

// Elimina a barra de rolagem enquanto o formulário desaparece
formLogin.addEventListener('animationstart', event => {
  if(event.animationName === 'down') {
    document.querySelector("body").style.overflow = 'hidden';
  }
})
// Retorna a barra de rolagem quanto o formulário desaparece por completo.
formLogin.addEventListener('animationend', event => {
  if(event.animationName === "down") {
    formLogin.style.display = "none";
    document.querySelector("body").style.overflow = 'none';
  }
})