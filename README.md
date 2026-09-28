# Backend Developer Practical Interview

Laravel 12 REST API for transaction management and callback verification.

## Tech Stack

* Laravel 12
* PHP 8.3+
* MySQL
* Postman

## Features

* Create transaction
* List transactions
* View transaction
* Request validation
* Signature generation (HMAC-SHA256)
* Callback signature verification
* Transaction status update

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

Configure your database and signature secret in `.env`:

```env
DB_DATABASE=interview_api
DB_USERNAME=root
DB_PASSWORD=

SIGNATURE_SECRET=interview-secret-key
```

## API

```text
POST  /api/transactions
GET   /api/transactions
GET   /api/transactions/{id}

POST  /api/callbacks
POST  /api/callbacks/generate-signature
```

Use **Postman** to test the APIs.

### Callback Example

```json
{
    "reference": "TXN-001",
    "event": "payment.completed",
    "status": "success",
    "signature": "your-generated-signature"
}
```

A valid signature updates the transaction status. An invalid signature is rejected.

> Signature generation and callback verification were added as bonus features for the interview.
