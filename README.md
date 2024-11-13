# Hiring Symfony Project

A Symfony-based project for managing the hiring process, containerized with Docker Compose, and includes data fixtures for easy setup.

## Table of Contents
- [Project Setup](#project-setup)
- [Docker Compose Setup](#docker-compose-setup)
- [Loading Fixtures](#loading-fixtures)
- [Running the Application](#running-the-application)

## Project Setup

1. **Clone the repository:**
    ```bash
    git clone https://github.com/momen573/hiring.git
    cd hiring
    ```

## Docker Compose Setup

1. **Build and start containers:**
    ```bash
    docker-compose up --build
    ```

2. **Access the Symfony container:**
    ```bash
    docker-compose exec app bash
    ```

3. **Install Composer dependencies:**
    ```bash
    composer install
    ```

4. **Install NPM dependencies:**
    ```bash
    npm install
    ```



5. **Load Fixtures:**
    Inside the Symfony container, run the following command to load your fixtures:
    ```bash
    docker-compose exec app bin/console doctrine:fixtures:load
    ```
6. **Visit the application:**
    The Symfony app will be accessible at `http://127.0.0.1:8000`.

