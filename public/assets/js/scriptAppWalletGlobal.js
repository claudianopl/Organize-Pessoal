/**
 * Comunicação com a rota do back-end.
 * A função envia para o back-end da aplicação qual carteira o usuário selecionou
 * fazendo com que essa carteira fique selecionada em toda aplicação.
 */
function walletSelect(id) {
  $('.loadingArea').show();
  $.ajax({
    type: 'post',
    url: '/app/userSelectWallet',
    data: `wallet=${id}`,
    success: (d) => {
      location.reload();
    }
  })
}

$('.sectionAppWalletArea').hover(() => {
  $('.MinhasCarteiras').slideToggle()
})



$(document).ready(() => {
  $('.MinhasCarteiras').hide()
})
