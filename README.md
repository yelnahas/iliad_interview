# Project Setup and API Usage Guide

This guide provides step-by-step instructions to configure the Docker environment, run tests, and interact with the API using Swagger documentation.

## Prerequisites

Ensure you have the following installed:
- Docker

## Environment Configuration

1. Build and start the Docker containers:
   ```bash
   docker compose build
   docker compose up -d
   ```

2. Access the PHP container shell:
   ```bash
   docker exec -it php sh
   ```

3. Run the following command to set up the JWT secret key:
   ```bash
   php artisan jwt:secret --force
   ```

## Running Tests

To execute the feature tests:
```bash
php artisan test --testsuite=Feature
```

## Preparing Test Data

To test the APIs effectively, you need to generate some fake data.

1. Generate 20 fake products:
   ```bash
   php artisan db:seed ProductSeeder
   ```

2. Generate 10 fake orders:
   ```bash
   php artisan db:seed OrderSeeder
   ```

## API Documentation

The API documentation is available via Swagger.

1. Open your browser and navigate to:
   [http://localhost:8080/api/documentation](http://localhost:8080/api/documentation)

2. Generate the JWT secret key (if not already done):
   ```bash
   php artisan jwt:secret --force
   ```

3. Authorize Swagger to access the APIs:
   - Click the **"Authorize"** button in the Swagger UI.
   - Paste the generated token into the input field and confirm.

## Available APIs

Below is a list of available endpoints with their functionalities:

1. **GET /api/orders/search**
   - Search for an order using the Algolia Engine.

2. **PUT /api/orders**
   - Update a specific order.

3. **POST /api/order**
   - Create a new order.

4. **DELETE /api/orders/{id}**
   - Delete a specific order.

---
