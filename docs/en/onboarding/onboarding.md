# Onboarding

## Requirements

Ensure you have installed:

* PHP 8.3
* Composer

---

## Setup

Clone the repository and install dependencies:

```bash
git clone <repo-url>
cd project
composer install
```

---

## Configuration

Copy the environment file:

```bash
cp .env.example .env
```

Generate the application key:

```bash
php artisan key:generate
```

---

## Running the Environment

Start the containers:

```bash
docker-compose up -d
```

---

## Syncing Documentation

Run the command to import and process the documentation:

```bash
php artisan docs:sync <project>
```

---

## Accessing the Application

Open in your browser:

```text
http://localhost/docs/<project>
```

---

## Notes

* Make sure the `docs/<project>` directory exists
* The documentation repository must be accessible locally
* If using queues, execute:

```bash
php artisan queue:work
```
