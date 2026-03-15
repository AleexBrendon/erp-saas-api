📦 ERP-SASS-API

API backend para [descreva sua plataforma, ex: ERP SaaS leve], construída com Laravel, para gerenciar usuários, clientes, produtos, vendas e finanças.

🛠 Tecnologias

PHP 8.x

Laravel 10.x

MySQL / PostgreSQL

Laravel Sanctum (autenticação via API)

Composer (dependências)

⚡ Funcionalidades

CRUD de usuários, clientes, produtos e serviços

Registro de vendas e agendamentos

Controle financeiro e transações

Autenticação via token (Sanctum)

Middleware de permissão por empresa/usuário

🔧 Instalação
git clone <link-do-repositorio>
cd <pasta-do-projeto>
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve

A API estará disponível em: http://127.0.0.1:8000

🔑 Rotas Principais

/api/usuarios – CRUD de usuários

/api/clientes – CRUD de clientes

/api/produtos – CRUD de produtos

/api/servicos – CRUD de serviços

/api/vendas – Gestão de vendas

/api/financeiro – Controle financeiro

Todas as rotas seguem RESTful e algumas requerem Bearer Token.

🧪 Testando com Postman

Abra o Postman ou outra ferramenta de testes de API.

Crie uma nova Request e selecione o método HTTP correspondente (GET, POST, PUT, DELETE).

Insira a URL da rota, por exemplo:

http://127.0.0.1:8000/api/usuarios

Para rotas protegidas, adicione no Header:

Authorization: Bearer <seu_token_aqui>

Envie a requisição e verifique a resposta JSON da API.

Dica: você pode exportar uma coleção de rotas no Postman para compartilhar facilmente com outros desenvolvedores.


📄 Licença

MIT License © Alex Brendon