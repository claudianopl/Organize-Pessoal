$('.sectionAppWalletArea').hover(() => {
  $('.MinhasCarteiras').slideToggle()
})

$('.carteiraSelect').click((e) => {
  let carteira = e.target.innerHTML
  $(".sectionAppWalletInfo h4").html(carteira)
  console.log(carteira); // Enviar para a função ajax
})

$(document).ready(() => {
  $('.MinhasCarteiras').hide()
})