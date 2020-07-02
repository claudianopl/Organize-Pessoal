/**
 * Comunicação com a rota do back-end.
 * A função envia para o back-end da aplicação qual carteira o usuário selecionou
 * fazendo com que essa carteira fique selecionada em toda aplicação.
 */
function walletSelect(id) {
  console.log(id, name);
  $.ajax({
    type: 'post',
    url: '/app/userSelectWallet',
    data: `wallet=${id}`,
    dataType: 'json'
  })
}

$('.sectionAppWalletArea').hover(() => {
  $('.MinhasCarteiras').slideToggle()
})

$('.carteiraSelect').click((e) => {
  let carteira = e.target.innerHTML
  $(".sectionAppWalletInfo h4").html(carteira)
})

$(document).ready(() => {
  $('.MinhasCarteiras').hide()
})
