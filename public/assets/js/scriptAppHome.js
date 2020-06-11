$('.sectionAppHomeOneInfoHeaderWallet').hover(() => {
  $('.MinhasCarteiras').slideToggle()
})

$('.carteiraSelect').click((e) => {
  let carteira = e.target.innerHTML
  console.log(carteira); // Enviar para a função ajax
})

$(document).ready(() => {
  $('.MinhasCarteiras').hide()
})