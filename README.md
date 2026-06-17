# Beach Apartment Booking System

Sistema de gerenciamento de reservas para aluguel de apartamento na praia, desenvolvido em PHP e MySQL.

O projeto tem como objetivo facilitar o processo de reserva, permitindo que clientes consultem disponibilidade, solicitem hospedagens e acompanhem suas reservas, com uma área administrativa em desenvolvimento para gerenciamento do imóvel.

## Objetivo

Criar uma plataforma de reservas completa para aluguel de temporada, centralizando:

- Disponibilidade do imóvel
- Solicitações de reserva
- Controle de clientes
- Gerenciamento administrativo

## Aprendizados

Durante o desenvolvimento deste projeto, pratiquei:

- Desenvolvimento de aplicações web com PHP
- Organização de código em PHP
- Integração com MySQL
- Organização de arquivos e camadas do sistema
- Autenticação de usuários
- Controle de sessões
- Manipulação de formulários
- Validação de dados
- Segurança no armazenamento de senhas
- Estruturação de sistemas web

## Tecnologias

- PHP
- MySQL
- PDO
- HTML5
- CSS3
- JavaScript

## Funcionalidades Implementadas

### Área Pública

- Página inicial
- Galeria de fotos
- Consulta de disponibilidade
- Calendário com navegação entre meses
- Visualização de datas ocupadas
- Regras do imóvel
- Página de contato

### Área do Cliente

- Cadastro de usuário
- Login e autenticação
- Controle de sessão
- Hash de senhas utilizando password_hash()
- Solicitação de reserva
- Visualização das próprias reservas
- Cancelamento de solicitações

### Sistema de Reservas

- Registro de reservas no banco de dados
- Validação de datas
- Cálculo do valor total da estadia
- Controle de status da reserva

Status utilizados:

- Solicitado
- Confirmado
- Cancelado

### Segurança

- Senhas armazenadas com password_hash()
- Verificação de senha com password_verify()
- Uso de PDO com prepared statements
- Controle de acesso através de sessões


## Funcionalidades Futuras

### Área Administrativa

- Dashboard administrativo
- Aprovação e gerenciamento de reservas
- Gerenciamento de usuários
- Controle de despesas
- Relatórios financeiros
- Estatísticas de reservas

### Melhorias Futuras

- Sistema de preços por temporada
- Dashboard com gráficos e métricas
- Integração com notificações
- Chatbot de atendimento
- Melhorias de experiência do usuário


## Estrutura do Projeto

```text
beach-apartment-booking-system/
│
├── admin/              # Área administrativa
├── assets/             # Arquivos estáticos (CSS, JS, imagens)
├── client/             # Área do cliente
├── config/             # Configurações do sistema e banco de dados
├── dao/                # Classes de acesso ao banco de dados
├── database/           # Arquivos SQL e estrutura do banco
├── helpers/            # Funções auxiliares
├── models/             # Modelos das entidades
├── public/             # Arquivos públicos de acesso
├── templates/          # Componentes reutilizáveis
└── uploads/            # Arquivos enviados pelo usuário
```

## Status

Projeto em desenvolvimento.