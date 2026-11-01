# Instituto Dr Anderson Gois

Sistema web de gerenciamento de alunos, cursos e agendamentos do Instituto Dr Anderson Gois, feito em Laravel 12 como Trabalho de Conclusão da Turma 28 (PHP Full Stack) da Coude Escola de Programação.

O site divulga os cursos de ortodontia, DTM e bruxismo, estética e harmonização. A equipe entra com login para cadastrar alunos e controlar os agendamentos.

> Os cursos, alunos, datas, cidades e valores gerados pelo seeder são apenas exemplos.

## Como o tema atende ao trabalho

| Pedido no trabalho | Neste sistema | Tabela |
| --- | --- | --- |
| Clientes | Alunos | `clientes` |
| Serviços | Cursos | `eventos` |
| Agendamentos | Agendamentos de alunos em cursos | `agendamentos` |

`agendamentos` se liga a `clientes`, `eventos` e `users` por chaves estrangeiras.

## Perfis de acesso

| Ação | Visitante | Atendente | Administrador |
| --- | --- | --- | --- |
| Ver e buscar cursos públicos | Sim | Sim | Sim |
| Ver cursos privados | Não | Sim | Sim |
| Listar, criar e editar alunos e agendamentos | Não | Sim | Sim |
| Excluir alunos e agendamentos | Não | Não | Sim |
| Criar, editar e excluir cursos | Não | Não | Sim |

## Requisitos

- PHP 8.2 ou superior, com as extensões `pdo_mysql` (ou `pdo_sqlite`), `mbstring` e `fileinfo`
- Composer
- MySQL ou SQLite

## Instalação

```bash
git clone <url-do-repositorio>
cd coude-eventos
composer install
cp .env.example .env
php artisan key:generate
```

### Banco de dados

Com **MySQL**, crie um banco vazio e ajuste o `.env`:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=instituto
DB_USERNAME=root
DB_PASSWORD=
```

Com **SQLite**, mantenha `DB_CONNECTION=sqlite` e crie o arquivo `database/database.sqlite`.

Depois, crie as tabelas e os dados de exemplo:

```bash
php artisan migrate --seed
```

### Executar

```bash
php artisan serve
```

Acesse http://127.0.0.1:8000.

## Usuários de demonstração

| Perfil | Usuário | Senha |
| --- | --- | --- |
| Administrador | `anderson` | `12345678` |
| Atendente | `atendente` | `12345678` |

Essas contas servem só para demonstração. Não use essas senhas em produção.

## Testes

```bash
php artisan test
```

Os testes usam SQLite em memória e cobrem login, rotas protegidas, permissões por perfil, CRUD e validação de dados.
