📦 ERP-SASS-API

API backend para [descreva sua plataforma, ex: ERP SaaS leve], construída com Laravel, para gerenciar usuários, clientes, produtos, vendas e finanças.

1. Pré-requisitos

Docker
 (Desktop)

Docker Compose

Postman
 ou outro cliente HTTP


2. Estrutura Docker

PHP-FPM (app) → roda Laravel

Nginx (nginx) → servidor web, serve public/

MySQL (mysql) → banco de dados

Redis (redis) → cache e filas

Volumes → persistência de dados do MySQL e arquivos Laravel


3. Configuração do .env

Exemplo de configuração de banco de dados para Docker:

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=erp_saas
DB_USERNAME=erp_user
DB_PASSWORD=erp_pass

O DB_HOST deve ser o nome do serviço MySQL definido no docker-compose.yml.


4. Comandos básicos do Docker

Subir containers
docker-compose up -d --build

Ver containers ativos
docker ps

Entrar no container Laravel
docker exec -it laravel_app bash

Parar e remover containers
docker-compose down

Iniciar servidor manualmente:
php artisan serve --host=0.0.0.0 --port=8000

Use -v para remover volumes se necessário.


5. Laravel - Comandos úteis

Dentro do container laravel_app:

Rodar migrations e seeders:
php artisan migrate --seed

Limpar cache e otimizações:
php artisan optimize:clear

Listar rotas:
php artisan route:list

Logs do Laravel:
tail -f storage/logs/laravel.log


6. Testando a API com Postman

URL base
http://localhost:8000/api

Endpoints principais

Método	Endpoint	Descrição
POST	/api/login	Autenticação do usuário
POST	/api/register	Cadastro de empresas/usuários
GET	/api/clientes	Listar clientes
POST	/api/clientes	Criar cliente
GET	/api/agendamentos	Listar agendamentos
POST	/api/agendamentos	Criar agendamento
GET	/api/financeiro	Listar lançamentos financeiros
POST	/api/financeiro	Criar lançamento financeiro
GET	/api/vendas	Listar vendas
POST	/api/vendas	Criar venda

Para endpoints autenticados, use o token retornado no login como Bearer Token.

Exemplo de requisição Login

POST http://localhost:8000/api/login
Body (JSON):
{
    "email": "admin@erp.com",
    "password": "123456"
}
Usando o token para autenticação

Copie o token retornado no login.

Nos headers da requisição autenticada:

Key: Authorization
Value: Bearer <seu_token_aqui>


7. Debug e logs

Laravel logs:
docker exec -it laravel_app tail -f storage/logs/laravel.log

Nginx logs:
docker logs -f laravel_nginx

MySQL logs:
docker logs -f mysql_db

Alterações no .env ou novas migrations:
php artisan config:clear
php artisan migrate


8. Dicas rápidas

Sempre verifique se containers estão ativos (docker ps).

Use o nome do serviço MySQL no .env (DB_HOST=mysql) e não 127.0.0.1.

Para testes rápidos, use Postman Collection com os endpoints definidos.

Para problemas de cache, rode php artisan optimize:clear dentro do container Laravel.


📄 Licença

MIT License © Alex Brendon