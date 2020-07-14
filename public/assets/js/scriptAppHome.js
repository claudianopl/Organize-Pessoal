/**
 * Função para fazer a confirmação das receitas, despesas ou tarefas.
 * @param {String} type informa se a confirmação é da receita, despesa ou tarefas
 * @param {String} id
 */
function confirmed(type, id) {
  if(type == 'received') {
    console.log(id);
  }
  if(type == 'expenses') {
    console.log(id);
  }
  if(type == 'tasks') {
    console.log(id);
  }
}