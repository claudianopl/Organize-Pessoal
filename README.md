<h1 align="center">
    <img alt="Organize Pessoal" title="#OrganizePessoal" src="https://organizepessoa.000webhostapp.com/assets/images/Banner1280.png" style="width:100%;"/>
</h1>

<p align="center">
<img alt="Repository size" src='https://img.shields.io/github/repo-size/claudianopl/Organize-Pessoal'> <img alt="License" src='https://img.shields.io/github/license/claudianopl/Organize-Pessoal'> <img alt="Website" src='https://img.shields.io/website?url=https%3A%2F%2Forganizepessoa.000webhostapp.com%2F'>
</p>

<h3 align="center"><a href='organizepessoa.000webhostapp.com'>Acessar a demonstração</a></h3>

<h4 align="center"> 
	🚧  Organize Pessoal ✅ Concluído  🚧
</h4>

## Índice

<!--ts-->
   * [Sobre](#%EF%B8%8F-sobre)
   * [Funcionalidade](#%EF%B8%8F-funcionalidade)
   * [Tecnologias Usadas](#-Tecnologias-Usadas)
   * [Packagist Usadas](#-Packagist-Usadas)
   * [Como executar o projeto](#-Como-executar-o-projeto)
<!--te-->

## ✏️ Sobre

<p>📈 O Organize Pessoal é a forma de ajudar as pessoas com um gerenciador de finanças e tarefas simples, muito poderoso e totalmente gratuito. 

Projeto foi desenvolvido para praticar os conhecimentos adquiridos em PHP, feito totalmente com o intuito de treinar e ajudar de alguma forma as pessoas.</p>

## ⚙️ Funcionalidade

- [x] Cadastro de usuário
- [x] Confirmação do e-mail do usuário
- [x] Solicitação de modificação da senha com confirmação por e-mail
- [x] Deashboard do usuário
	- Possuí o saldo do usuário.
	- Valor total de todas as receitas recebidas no mês selecionado no calendário
	- Valor total de todas as despesas recebidas no mês selecionado no calendário
	- Notificação das receitas, despesas e tarefas pendentes até o mês atual, para lembrar o usuário de finalizá-las
	- Gráfico com todas as receitas e despesas do mês selecionado
	- Gráfico com as despesas detalhadas pelas categorias
- [x] Criação, edição e remoção das receitas a receber ou recebidas
	- Pode filtrar as receitas desejadas por status, categoria e/ou data
	- As receitas podem ser únicas, fixas (mensal ou anual) ou parcelada
- [x] Criação, edição e remoção das despesas a pagar ou pagas
	- Pode filtrar as despesas desejadas por status, categoria e/ou data
	- As despesas podem ser únicas, fixas (mensal ou anual) ou parcelada
- [x] Criação, edição e remoção das tarefas a fazer
	- Pode filtrar as tarefas desejadas por data
- [x] Criação, edição e remoção das carteiras
	- Cada carteira possui um gráfico anual de todas as receitas e despesas que o usuário gerou
- [x] Atualização do perfil do usuário e troca da senha

## 🚀 Tecnologias Usadas

As seguintes ferramentas foram usadas na construção do projeto:
- **[Javascript](https://www.javascript.com/)**
- **[Jquery](https://jquery.com/)**
- **[PHP](https://www.php.net/)**
- **[MySQL](https://www.mysql.com)**

## 🔖 Packagist Usadas

- **[PHPMailer](https://github.com/PHPMailer/PHPMailer)**
- **[PHP-JWT](https://github.com/firebase/php-jwt)**

## ⚡ Como executar o projeto

### Pré-requisitos
Antes de começar, você vai precisar ter instalado em sua máquina as seguintes ferramentas: [Git](https://git-scm.com/), [PHP](https://www.php.net/) (igual ou superior ao 7.2.0) e o [Composer](https://getcomposer.org/). Além disto é bom ter um editor para trabalhar com o código como [VSCode](https://code.visualstudio.com/)

### 🎲 Rodando aplicação
```bash 
# clone do repositório
$ git clone https://github.com/claudianopl/Organize-Pessoal

# Entrar no diretório
$ cd Organize-Pessoal

# Instalando as dependências do composer
$ composer install

# Entrando no local público para executar o projeto
$ cd public

# Executando o projeto no localhost usando a porta 8080
$ php -S localhost:8080
```

### ❌ OBS
É necessário a configuração dos arquivos:
- App/Connection.php (Configure o arquivo para usar a sua conexão com o banco de dados)
- App/Controllers/HomeController.php (Configure o $serverMail e $mailData adaptando para o seu uso)

## 📝 Licença

Este projeto esta sobe a licença MIT.

Feito com ❤️ por Claudiano Lima 👋🏽 [Entre em contato!](linkedin.com/in/claudianopl)
