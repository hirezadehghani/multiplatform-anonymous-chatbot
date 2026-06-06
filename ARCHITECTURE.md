# Multi Platform Anonymous Chat Platform

## Tech Stack

* Laravel 13.12.0
* PostgreSQL
* Filament v5.6.6
* Redis
* Horizon

## Goal

Anonymous chat platform supporting:

* Bale
* Telegram (later not in this scope)
* Rubika (later not in this scope)
* Future platforms (later)

All platforms share:

* User profile
* Wallet
* Referral system
* Chat history

## Database

### users

Primary user identity.

### user_accounts

Maps platform accounts to users.

### bots

Stores bot instances.

### wallets

One wallet per user.

### transactions

Wallet transactions.

### chat_rooms

Anonymous chat rooms.

### messages

Messages inside rooms.

### referrals

Referral system.

### link_codes

Cross-platform account linking.

## Architecture

Controllers
→ Services
→ Actions
→ Jobs
→ Connectors

## Connectors

PlatformConnector interface

Implementations:

* BaleConnector
* TelegramConnector
* RubikaConnector

## Services

* MatchService
* ChatService
* WalletService
* ReferralService
* PlatformResolver

## Requirements

* Multi platform
* Redis matchmaking queue
* Queue-based message delivery
* Filament admin panel
* Scalable for high traffic

## Rules

Never store platform-specific IDs in users table.

Always use:

users
→ user_accounts

relationship.

Files must be stored in object storage and not inside database.


******

Rules:
- Multi platform architecture
- Never store platform ids in users table
- Use UserAccount model for platform mapping
- Use services instead of fat controllers
- Use enums where possible
- Use dependency injection
- Follow SOLID principles