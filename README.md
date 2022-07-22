<p align="center"><a href="https://yampi.com.br" target="_blank"><img src="https://icons.yampi.me/svg/brand-yampi.svg" width="200"></a></p>

# Teste prático para Back-End Developer
***

Bem-vinda, pessoa desenvolvedora.

Este é o teste que nós, aqui da Yampi, usamos para avaliar tecnicamente todas as pessoas que estão participando do nosso processo seletivo para a vaga de desenvolvimento Back-End.

## TL;DR

- Você deverá criar um CRUD através de uma API REST com Laravel;
- Você deverá criar um comando artisan que se comunicará com uma outra API para importar em seu banco de dados;

## Começando

**Faça um fork desse projeto para iniciar o desenvolvimento. PRs não serão aceitos.**

### Configuração do ambiente
***

**Para configuração do ambiente é necessário ter o [Docker](https://docs.docker.com/desktop/) instalado em sua máquina.**

Dentro da pasta do projeto, rode o seguinte comando: `docker-compose up -d`.

Copie o arquivo `.env.example` a renomeie para `.env` dentro da pasta raíz da aplicação.

```bash
cp .env.example .env
```

Após criar o arquivo `.env`, será necessário acessar o container da aplicação para rodar alguns comandos de configuração do Laravel.

Para acessar o container use o comando `docker exec -it yampi_test_app sh`.

Digite os seguintes comandos dentro do container:

```bash
composer install
php artisan key:generate
php artisan migrate
```

Após rodar esses comandos, seu ambiente estará pronto para começar o teste.

Para acessar a aplicação, basta acessar `localhost:8000`

### Funcionalidades a serem implementadas

**Essa aplicação deverá se comportar como uma API REST, onde será consumida por outros sistemas. Nesse teste você deverá se preocupar em constriuir somente a API**. 

##### CRUD produtos

Aqui você deverá desenvolver as principais operações para o gerenciamento de um catálogo de produtos, sendo elas:

- Criação
- Atualização
- Exclusão

O produto deve ter a seguinte estrutura:

Campo       | Tipo      | Obrigatório   | Pode se repetir
----------- | :------:  | :------:      | :------:
id          | int       | true          | false
name        | string    | true          | false        
price       | float     | true          | true
decription  | text      | true          | true
category    | string    | true          | true
image_url   | url       | false         | true

Os endpoints de criação e atualização devem seguir o seguinte formato de payload:

```json
{
    "name": "product name",
    "price": 109.95,
    "description": "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit...",
    "category": "test",
    "image": "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
}
```

**Importante:** Tanto os endpoints de criação é atualização, deverão ter uma camada de validação dos campos.

##### Buscas de produtos

Para realizar a manutenção de um catálogo de produtos é necessário que o sistema tenha algumas buscas, sendo elas:

- Busca pelos campos `name` e `category` (trazer resultados que batem com ambos os campos).
- Busca por uma categoria específica.
- Busca de produtos com e sem imagem.
- Buscar um produto pelo seu ID único.

##### Importação de produtos de uma API externa

É necessário que o sistema seja capaz de importar produtos que estão em um outro serviço. Deverá ser criado um comando que buscará produtos nessa API e armazenará os resultados para a sua base de dados. 

Sugestão: `php artisan products:import`

Esse comando deverá ter uma opção de importar um único produto da API externa, que será encontrado através de um ID externo.

Sugestão: `php artisan products:import --id=123`

Utilize a seguinte API para importar os produtos: [https://fakestoreapi.com/docs](https://fakestoreapi.com/docs)

---

Se houver dúvidas, por favor, abra uma issue nesse repositório. Ficaremos felizes em ajudá-lo ou até mesmo melhorar essa documentação.

---
Feito por [Kênia](https://www.linkedin.com/in/kenia-alves-pereira-araujo/)

## Como usar a API

```sh
[Base URL: localhost:8000/api]
```
Através dessa api é possível recuperar um ou vários produtos. Também é possível cadastrar e atualizar um produto.

### Cadastrar um produto
Método: *POST*
Caminho: ```/product```
**Retorno** (exemplo): "Produto criado com sucesso! ID: 1"

### Atualizar um produto
Método: *PUT*
Caminhho: ```/product/{ID}```
Parâmetro: Id do produto (integer)
**Retorno** (exemplo): "Produto atualizado com sucesso! ID: 1"

### Deletar um produto
Método: *DELETE*
Caminhho: ```/product/{ID}```
Parâmetro: Id do produto (integer)
**Retorno** (exemplo): "Produto removido com sucesso! ID: 1"

### Buscar produto por ID específico
Método: *GET*
Caminho: ```product/{ID}```
Parâmetro: ID do produto {integer}
**Retorno**: Dados do produto no formato json conforme especificado no payload acima

### Buscar produtos por uma categoria específica
Método: *GET*
Caminho: ```products/category/{CATEGORIA}```
Parâmetro: Categoria do produto {string}
**Retorno**: Uma lista de produtos no formato json conforme especificado no payload acima

### Buscar todos os produtos
Método: *GET*
Caminho: ```products/```
**Retorno**: Uma lista de todos os produtos no formato json conforme especificado no payload acima

### Buscar produtos por nome e categoria
Método: *GET*
Caminho: ```products/```
Request: É necessário informar nome e categoria
```json
{
    "name": "product name",
    "category": "test"
}
```
**Retorno**: Uma lista de produtos de acordo com a categoria e o nome do produto informado, no formato json conforme especificado no payload acima

---

## Como importar produtos da API externa

Esse recurso permite cadastrar ou atualizar um ou vários produtos de uma vez.

**Opções**:
1. Trazer vários produtos. Após inserir o comando, haverá uma opçao de informar um limite máximo de produtos a serem importados. O sistema irá perguntar se deseja sincronizar[^1] os produtos.
```php artisan products:import```
2. Trazer somente um produto. Caso o produto a ser importado já exista na base, o sistema irá perguntar se deseja sincronizar os produtos.
```php artisan products:import --id=123```
[^1]: A sincronização consiste em atualizar os produtos do seu catálogo, que sejam de origem externa e correspondam aos produtos trazidos na busca atual, com as informações externas.