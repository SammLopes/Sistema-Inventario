# Sistema de Estoque

Esse é o manual de como rodar o projeto usando o laradock. 

## Passo a passo para rodar com Laradock

### 1) Clonar o projeto. 

- Primeiro clonamos o projeto passando a flag `--recurse-submodules`, para poder clonar também o projeto do laradock. 

```bash
git clone --recurse-submodules <git@github.com:SammLopes/Sistema-Inventario.git || https://github.com/SammLopes/Sistema-Inventario.git>
```
- Caso execute sem a flag `--recurse-submodules`, pode executar esse comando abaixo.
```bash
git submodule update --init --recursive
``` 

### 2) Preparar variáveis de ambiente
Crie os arquivos de ambiente a partir dos exemplos:

```bash
cp laradock/.env.example laradock/.env
cp .env.example .env
```

Edite **`laradock/.env`** (serviços/portas/credenciais) :
```

### Workspace ###############################
WORKSPACE_SSH_PORT=2223

### NGINX #################################################

NGINX_HOST_HTTP_PORT=85
NGINX_HOST_HTTPS_PORT=443
NGINX_HOST_LOG_PATH=./logs/nginx/
NGINX_SITES_PATH=./nginx/sites/
NGINX_PHP_UPSTREAM_CONTAINER=php-fpm
NGINX_PHP_UPSTREAM_PORT=9000
NGINX_SSL_PATH=./nginx/ssl/

### MySQL ##################

MYSQL_VERSION=8.4
MYSQL_DATABASE=inventory_db
MYSQL_USER=app
MYSQL_PASSWORD=secret
MYSQL_PORT=3307
MYSQL_ROOT_PASSWORD=root
MYSQL_ENTRYPOINT_INITDB=./mysql/docker-entrypoint-initdb.d

```
- Tive que troca os valores de algumas variáveis devido á algusn conflitos no meu computador. 

Edite **`.env`** do **Laravel** (aplicação):
```
APP_URL=http://localhost
DB_CONNECTION=mysql
DB_HOST=mysql          # nome do serviço no docker-compose do Laradock
DB_PORT=3306
DB_DATABASE=inventory_db
DB_USERNAME=app
DB_PASSWORD=secret

SESSION_DRIVER=database
```

> **Importante:** O **DB_HOST** deve ser o **nome do serviço** no `docker-compose` do Laradock (geralmente `mysql`), e **não** `127.0.0.1`.

### 3) Subir os serviços (Nginx, MySQL, NgInx, Php-Fpm)
- Para subir esses serviçoes entre na pasta do laradock dentro da raiz do projeto.
```bash
sudo docker compose -f ./docker-compose.yml up -d mysql nginx php-fpm  
```

### 4) Executar os comandos do Laravel 

Os comandos a abixo são executados dentro do container, ou seja, dentro do workspace. 

- Instala as dependencias. 
```bash
docker compose exec workspace composer install
```

- Esse comando executa as migratins e em seguida os seeders. 
```bash
docker compose exec workspace php artisan migrate:fresh --seed 
```

- Gerar o APP_KEY do projeto. 
```bash
docker compose exec workspace php artisan key:generate
```
- Instala  as depencias do projeto javascript. 
```bash
npm install
```

- Isso pode ocorre, ele pode tentar escrever no diretório bootstrap e não conseguir devido a falta de permissão, caso ocorre entre container do workspace e de as permissões necessárias com os comandos abaixo.

#### Permissões (se necessário)
```bash
chown -R www-data:www-data storage bootstrap/cache || true
chmod -R ug+rwX storage bootstrap/cache
```

### 5) Acessar a aplicação
- Navegador: **http://localhost** (porta configurada no `laradock/.env`)
- Logs úteis:
  ```bash
  docker compose -f laradock/docker-compose.yml logs -f nginx
  docker compose -f laradock/docker-compose.yml logs -f mysql
  ```

