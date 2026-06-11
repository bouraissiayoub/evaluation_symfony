# Betting Platform - Symfony Project

## Description
A fictive sports betting platform built with Symfony 6. 
Users can place bets on sports events, manage their wallet, and set responsible gaming limits.

## Requirements
- PHP 8.1+
- Composer
- MySQL / phpMyAdmin
- Symfony CLI

## Installation

### 1. Clone the project
git clone https://github.com/YOUR_USERNAME/betting-platform.git
cd betting-platform

### 2. Install dependencies
composer install

### 3. Configure the database
Copy .env.example to .env and edit the DATABASE_URL:
DATABASE_URL="mysql://root:@127.0.0.1:3306/betting_platform?serverVersion=8.0"

### 4. Create the database
php bin/console doctrine:database:create

### 5. Run migrations
php bin/console doctrine:migrations:migrate

### 6. Load fixtures (demo data)
php bin/console doctrine:fixtures:load

### 7. Start the server
symfony serve

### 8. Open the browser
http://localhost:8000

---

## Demo accounts (after fixtures)

| Email | Password | Role |
|-------|----------|------|
| admin@bet.com | password | ROLE_ADMIN |
| manager@bet.com | password | ROLE_MANAGER |
| (random emails) | password | ROLE_USER |

---

## Features by role

### ROLE_USER
- Register (must be 18+)
- Login / Logout
- View published events and place bets
- Deposit money to wallet
- View bet history (GAGNE / PERDU / EN_ATTENTE)
- Set daily/weekly bet and deposit limits
- Self-exclude for a defined period

### ROLE_MANAGER
- Create sports events with outcomes
- Publish / Close events
- Enter results and trigger payout calculation
- Odds recalculate automatically after each bet

### ROLE_ADMIN
- View all users
- Assign ROLE_MANAGER to a user
- Suspend / Reactivate accounts

---

## REST API

| Method | URL | Description |
|--------|-----|-------------|
| GET | /api/events | List all published events |
| GET | /api/events/{id} | Get one event with its outcomes |

---

## Architecture

src/
  Controller/
    Admin/        — admin routes
    Api/          — REST API
  Entity/         — User, SportEvent, Issue, Bet, Transaction
  Repository/     — database queries
  Service/        — business logic (WalletService, BettingService, OddsCalculatorService)
  Security/       — AppAuthenticator, UserChecker
  EventSubscriber/ — BetSubscriber (logs every bet)
  DataFixtures/   — demo data
templates/        — Twig pages