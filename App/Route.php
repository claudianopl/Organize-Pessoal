<?php

namespace App;

use MF\Init\Bootstrap;

class Route extends Bootstrap {

  protected function initRoutes() {

    /*
    * Routes do controller principal Home
    */
    $routes['home'] = array(
      'route' => '/',
      'controller' => 'HomeController',
      'action' => 'index'
    );

    $routes['about'] = array(
      'route' => '/sobre',
      'controller' => 'HomeController',
      'action' => 'about'
    );

    $routes['login'] = array(
      'route' => "/entrar",
      'controller' => 'HomeController',
      'action' => 'login',
    );

    $routes['resetPassword'] = array(
      'route' => "/redefinir",
      'controller' => 'HomeController',
      'action' => 'resetPassword',
    );

    $routes['singup'] = array(
      'route' => "/cadastre-se",
      'controller' => 'HomeController',
      'action' => 'singup',
    );

    $routes['confirm-register'] = array(
      'route' => '/confirmar-cadastro',
      'controller' => 'HomeController',
      'action' => 'confirmRegister'
    );
    
    $routes['register-confirmed'] = array(
      'route' => "/cadastro-confirmado",
      'controller' => 'HomeController',
      'action' => 'registerConfirmed',
    );

    /*
    * Routes user do controller App
    */

    $routes['app'] = array(
      'route' => "/app",
      'controller' => 'AppController',
      'action' => 'index',
    );

    $routes['appReceive'] = array(
      'route' => "/app/receitas",
      'controller' => 'AppController',
      'action' => 'receive',
    );

    $routes['appExpense'] = array(
      'route' => "/app/despesas",
      'controller' => 'AppController',
      'action' => 'expense',
    );

    $routes['appTasks'] = array(
      'route' => "/app/tarefas",
      'controller' => 'AppController',
      'action' => 'tasks',
    );

    $routes['appWallet'] = array(
      'route' => "/app/carteiras",
      'controller' => 'AppController',
      'action' => 'wallet',
    );

    $routes['appProfile'] = array(
      'route' => "/app/perfil",
      'controller' => 'AppController',
      'action' => 'profile',
    );

    /**
     * Rota do back-end.
     * Essas rotas são executadas no HomeController.
     */

    /**
     * Rota para criar novos usuários
     */
    $routes['newUser'] = array(
      'route' => "/newUser",
      'controller' => 'HomeController',
      'action' => 'newUser',
    );

    
    /**
     * Rota para efetuar login
     */
    $routes['authenticateUser'] = array(
      'route' => "/authenticateUser",
      'controller' => 'HomeController',
      'action' => 'authenticateUser',
    );

    /**
     * Rota para enviar e-email para modificar a senha
     */
    $routes['changeTokenPassword'] = array(
      'route' => "/changeTokenPassword",
      'controller' => 'HomeController',
      'action' => 'changeTokenPassword',
    );

    /**
     * Rota para modificar senha do usuário.
     */
    $routes['changePassword'] = array(
      'route' => "/changePassword",
      'controller' => 'HomeController',
      'action' => 'changePassword',
    );



    /**
     * Rota do back-end.
     * Essas rotas são executadas no AppController.
     */

    /**
     * Rota para comunicação ajax da página /app
     */
    $routes['headerUserName'] = array(
      'route' => "/app/headerUserName",
      'controller' => 'AppController',
      'action' => 'headerUserName',
    );

    /**
     * Rota para alimentar os gráficos da dashboard.
     */
    $routes['dashboardCalendar'] = array(
      'route' => "/app/dashboardCalendar",
      'controller' => 'AppController',
      'action' => 'dashboardCalendar',
    );

    /**
     * Rota para a seleção e apresentação das carteiras do usuário.
     */
    $routes['userGetWallet'] = array(
      'route' => "/app/userSelectWallet",
      'controller' => 'AppController',
      'action' => 'userSelectWallet',
    );
    
    /**
     * Rota para inserção de receitas.
     */
    $routes['insertReceive'] = array(
      'route' => "/app/insertReceive",
      'controller' => 'AppController',
      'action' => 'insertReceive',
    );

    /**
     * Rota para a filtrar as receitas do usuário.
     */
    $routes['filterReceive'] = array(
      'route' => "/app/filterReceive",
      'controller' => 'AppController',
      'action' => 'filterReceive',
    );

    /**
     * Rota para remover as receitas do usuário.
     */
    $routes['removeReceived'] = array(
      'route' => "/app/removeReceived",
      'controller' => 'AppController',
      'action' => 'removeReceived',
    );

    /**
     * Rota para atualizar as receitas do usuário.
     */
    $routes['updateReceived'] = array(
      'route' => "/app/updateReceived",
      'controller' => 'AppController',
      'action' => 'updateReceived',
    );

    /**
     * Rota para concluir as receitas do usuário.
     */
    $routes['concludeReceived'] = array(
      'route' => "/app/concludeReceived",
      'controller' => 'AppController',
      'action' => 'concludeReceived',
    );

    /**
     * Rota para inserir novas despesas do usuário.
     */
    $routes['insertExpenses'] = array(
      'route' => "/app/insertExpenses",
      'controller' => 'AppController',
      'action' => 'insertExpenses',
    );

    /**
     * Rota para filtrar as despesas do usuário.
     */
    $routes['filterExpenses'] = array(
      'route' => "/app/filterExpenses",
      'controller' => 'AppController',
      'action' => 'filterExpenses',
    );

    /**
     * Rota para editar as despesas do usuário.
     */
    $routes['updateExpenses'] = array(
      'route' => "/app/updateExpenses",
      'controller' => 'AppController',
      'action' => 'updateExpenses',
    );
    
    /**
     * Rota para remover despesas do usuário.
     */
    $routes['expensesRemove'] = array(
      'route' => "/app/expensesRemove",
      'controller' => 'AppController',
      'action' => 'expensesRemove',
    );

    /**
     * Rota para concluir despesas do usuário.
     */
    $routes['expensesConclude'] = array(
      'route' => "/app/expensesConclude",
      'controller' => 'AppController',
      'action' => 'expensesConclude',
    );

    /**
     * Rota para inserir as tarefas do usuário.
     */
    $routes['insertTasks'] = array(
      'route' => "/app/insertTasks",
      'controller' => 'AppController',
      'action' => 'insertTasks',
    );

    /**
     * Rota para filtrar as tarefas do usuário.
     */
    $routes['filterTasks'] = array(
      'route' => "/app/filterTasks",
      'controller' => 'AppController',
      'action' => 'filterTasks',
    );

    /**
    * Rota para atualizar as tarefas do usuário.
    */
    $routes['updateTasks'] = array(
      'route' => "/app/updateTasks",
      'controller' => 'AppController',
      'action' => 'updateTasks',
    );

    /**
    * Rota para remover tarefas do usuário.
    */
    $routes['removeTasks'] = array(
      'route' => "/app/removeTasks",
      'controller' => 'AppController',
      'action' => 'removeTasks',
    );

    /**
    * Rota para concluir tarefas do usuário.
    */
    $routes['concludeTasks'] = array(
      'route' => "/app/concludeTasks",
      'controller' => 'AppController',
      'action' => 'concludeTasks',
    );

    /**
    * Rota para criar carteiras do usuário.
    */
    $routes['insertWallet'] = array(
      'route' => "/app/insertWallet",
      'controller' => 'AppController',
      'action' => 'insertWallet',
    );

    /**
    * Rota para gerar o gráfico wallet.
    */
    $routes['graphicWallet'] = array(
      'route' => "/app/graphicWallet",
      'controller' => 'AppController',
      'action' => 'graphicWallet',
    );

    /**
    * Rota para remover uma wallet.
    */
    $routes['removeWallet'] = array(
      'route' => "/app/removeWallet",
      'controller' => 'AppController',
      'action' => 'removeWallet',
    );

    /**
    * Rota para atualizar o perfil do usuário.
    */
    $routes['userProfile'] = array(
      'route' => "/app/userProfile",
      'controller' => 'AppController',
      'action' => 'userProfile',
    );

    /**
    * Rota para trocar senha do usuário.
    */
    $routes['updatePassword'] = array(
      'route' => "/app/updatePassword",
      'controller' => 'AppController',
      'action' => 'updatePassword',
    );

    /**
    * Rota para efetuar o logoff.
    */
    $routes['userLogoff'] = array(
      'route' => "/logoff",
      'controller' => 'AppController',
      'action' => 'logoff',
    );

    // Execução das routes
    $this->setRoutes($routes);
  }
}

?>