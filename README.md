# TCC Repositório
**Site TCC - ARQ - UFSC**

## Descrição
Lista os trabalhos registrados no site com seus respectivos links para o repositório institucional. Também verifica se há novos registros no respositório para completar os links da relação de trabalhos.

## Pré Requisitos
Sistema desenvolvido usando HTML, CSS, Javascript, PHP e MySQL. Sem o uso de nenhum framework.

## Instalação
1. **Banco de Dados** 
:    É necessário ter um banco de dados MySQL com as seguintes tabelas:

| tcc_trb |      |
| ------- | ---- |
| id | int |
| titulo | varchar(255)  utf8_unicode_ci |
| ano | year |
| semestre | int |
| tema_id | int |

| tcc_trb_rep |      |
| ----------- | ---- |
| id | int |
| trb_id | int |
| link | varchar(255)  utf8_unicode_ci|

| tcc_trb_usr |      |
| ----------- | ---- |
| id | int |
| trb_id | int |
| usr_id | int |
| status | int |

| tcc_usr |      |
| ------- | ---- |
| id | int |
| nome | varchar(255)  utf8_unicode_ci |
| matricula | varchar(20)  utf8_unicode_ci |
| senha | varchar(20)  utf8_unicode_ci |
| email | varchar(255)  utf8_unicode_ci |
| wtsap | varchar(20)  utf8_unicode_ci|
| lattes | varchar(255)  utf8_unicode_ci |
| status | int |
| ativo | tinyint |

2. **PHP**
:    É necessário que o servidor tenha instalado o PHP 7 ou superior.

3. **Configurações**
:    Ver no README.md da pasta config.

## Autoria
Sistema idealizado e codificado por [José Hélio Verissimo Júnior](https://github.com/joselio105/)