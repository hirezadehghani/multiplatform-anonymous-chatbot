# Multiplatform Anonymous Chatbot

<p align="center">
  <strong>A platform-independent anonymous messaging backend built with Laravel.</strong><br>
  Connect users across multiple messaging platforms through a unified, scalable backend.
</p>

<p align="center">

![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?logo=laravel\&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.4-777BB4?logo=php\&logoColor=white)
![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-4169E1?logo=postgresql\&logoColor=white)
![Redis](https://img.shields.io/badge/Redis-Latest-DC382D?logo=redis\&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Enabled-2496ED?logo=docker\&logoColor=white)
![License](https://img.shields.io/github/license/hirezadehghani/multiplatform-anonymous-chatbot)
![Last Commit](https://img.shields.io/github/last-commit/hirezadehghani/multiplatform-anonymous-chatbot)

</p>

---

## Overview

**Multiplatform Anonymous Chatbot** is a backend service that enables anonymous conversations between users across different messaging platforms.

Instead of creating separate bots for each platform, the application provides a unified backend that handles user registration, anonymous matching, message routing, and platform integrations.

This project is primarily a **portfolio project** focused on software architecture, backend engineering, and scalable application design.

---

## Motivation

The project also serves as a practical experiment in **AI-assisted (Vibe Coding)** development.

AI was used as a collaborative development tool—not as a replacement for software engineering. Every generated solution was reviewed, debugged, tested, and integrated manually. The objective was to explore how experienced developers can increase productivity while maintaining code quality and architectural ownership.

---

## Features

* Anonymous user registration
* Cross-platform messaging architecture
* Platform-independent backend
* Queue-based message processing
* Redis caching
* PostgreSQL database
* Dockerized development environment
* RESTful API
* Scalable Laravel architecture
* Modular service layer
* Extensible bot providers

### Planned Features

* Telegram Bot
* Bale Bot
* Discord Integration
* WhatsApp Integration
* User reports
* Moderation tools
* Rate limiting
* Conversation history
* Web dashboard
* Metrics & monitoring

---

# Architecture

```text
                +----------------+
                | Telegram Bot   |
                +-------+--------+
                        |
                +-------v--------+
                | Bale Bot       |
                +-------+--------+
                        |
                +-------v--------+
                | Future Bots    |
                +-------+--------+
                        |
              +---------v----------+
              | Laravel API        |
              |--------------------|
              | Authentication     |
              | Matching           |
              | Message Routing    |
              | Services           |
              +---------+----------+
                        |
          +-------------+-------------+
          |                           |
 +--------v--------+         +--------v--------+
 | PostgreSQL      |         | Redis           |
 | Persistent Data |         | Queue / Cache   |
 +-----------------+         +-----------------+
```

---

# Technology Stack

| Category         | Technology               |
| ---------------- | ------------------------ |
| Language         | PHP 8.4                  |
| Framework        | Laravel 13               |
| Database         | PostgreSQL               |
| Cache            | Redis                    |
| Containerization | Docker                   |
| Web Server       | Nginx                    |
| Queue            | Redis Queue              |
| Version Control  | Git                      |
| CI/CD            | GitHub Actions           |

---

# Project Structure

```text
app/
 ├── Services/
 ├── Models/
 ├── Actions/
 ├── Jobs/
 ├── Events/
 ├── Listeners/
 ├── Http/
 └── Providers/

docker/

database/

routes/

tests/
```

---

# Getting Started

## Clone

```bash
git clone https://github.com/hirezadehghani/multiplatform-anonymous-chatbot.git

cd multiplatform-anonymous-chatbot
```

## Environment

```bash
cp .env.example .env
```

Configure:

* Database
* Redis
* Bot Tokens

---

## Run with Docker

```bash
docker compose up -d --build
```

Install dependencies

```bash
docker compose exec app composer install
```

Generate application key

```bash
docker compose exec app php artisan key:generate
```

Run migrations

```bash
docker compose exec app php artisan migrate
```

Start queue worker

```bash
docker compose exec app php artisan queue:work
```

---

# Running Tests

```bash
php artisan test
```

or

```bash
vendor/bin/pest
```

---

# Screenshots

## Application

> Replace these placeholders with actual screenshots.

```
docs/images/dashboard.png

docs/images/chat.png

docs/images/docker.png

docs/images/tests.png
```

Example structure:

```text
docs/
└── images/
    ├── dashboard.png
    ├── architecture.png
    ├── docker.png
    ├── tests.png
```

---

# Roadmap

* [x] Docker environment
* [x] PostgreSQL support
* [x] Redis integration
* [x] Queue system
* [ ] Telegram integration
* [ ] Bale integration
* [ ] Discord integration
* [ ] Authentication improvements
* [ ] CI/CD
* [ ] Monitoring
* [ ] Production deployment

---

# Why Recruiters May Find This Project Interesting

This repository demonstrates practical experience with:

* Backend architecture
* Laravel ecosystem
* Containerized development
* Database design
* Redis queues
* Clean project organization
* API development
* AI-assisted software engineering
* Problem solving and debugging
* Continuous learning through real-world implementation

---

# Contributing

Contributions, suggestions, and bug reports are welcome.

Please open an Issue before submitting large changes.

---

# License

This project is licensed under the MIT License.

---

## Author

**Reza Dehghani**

Backend Developer • Laravel • PHP

GitHub:
https://github.com/hirezadehghani

LinkedIn:
https://linkedin.com/in/hirezadehghani

Website:
https://hireza.ir
