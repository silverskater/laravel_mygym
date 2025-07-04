# Developer Notes

## Project Setup

[Composer](https://getcomposer.org/) is required to install dependencies:
`composer install`

[Laravel Sail](https://laravel.com/docs/master/sail) is a light-weight command-line interface for interacting with Laravel's default Docker development environment.

-   **Start:**  
    `sail up -d`
-   **Stop:**  
    `sail down`
-   **Logs:**  
    `sail logs app`
-   **Shell:**  
    `sail shell`
-   **Run artisan:**  
    `sail artisan <command>`

## API

-   The API Endpoints are documented in [api.md](./api.md).
-   All API endpoints except `/register` and `/login` require authentication via Laravel Sanctum.
-   Pass the token as a Bearer token in the `Authorization` header.
-   Tokens are returned in the format `<id>|<token>`. Use only the part after the `|` for API requests if needed. See [Login and extract the token](./api.md#login-jq) for an example.

## Laravel Sanctum

[Laravel Sanctum](https://laravel.com/docs/master/sanctum) provides token management and token abilities for API endpoint authentication.  
Sanctum routes and logic are in `routes/api.php` and the `User` model must use the `HasApiTokens` trait.

## Database

-   **Migrate:**  
    `sail artisan migrate`
-   **Rollback:**  
    `sail artisan migrate:rollback`
-   **Seed:**  
    `sail artisan db:seed`
-   **Refresh (reset and seed):**  
    `sail artisan migrate:fresh --seed`

-   Seeders are in `database/seeders/`.
-   Factories for test data are in `database/factories/`.

## Automated Tests

-   Run all tests:  
    `sail artisan test`
-   Run with coverage:  
    `sail artisan test --coverage`
-   API tests are in `tests/Feature/Api/`.
-   Use factories for all models in tests to ensure database integrity and isolation.

### [Mutation testing](https://en.wikipedia.org/wiki/Mutation_testing)

Copy `infection.json5` from `infection.json5.dist` and customize as needed for local runs. See [Infection Docs: Configuration](https://infection.github.io/guide/usage.html#configuration) for more details.

**Run the [Infection Mutation Testing Framework](https://infection.github.io/):**

```sh
sail exec app vendor/bin/infection
```

Note: Running inside Sail (the docker container) ensures the database hostname resolves correctly. Alternatively, configure your `.env.testing` or `phpunit.xml` to use `127.0.0.1` as the DB host when running from the host, and ensure the MySQL port is published.

Check the test log file `infection.log` and address the escaped mutants to improve test coverage.

## Utility Commands

### Increment Scheduled Class Dates

The custom Artisan command `app:increment-date` increments the `scheduled_at` date for all scheduled classes by one or more days.
This is useful for testing or rolling over class schedules in bulk.

**Usage:**

```sh
sail artisan app:increment-date --days=3
```

Each scheduled class will have its date incremented by one day, and the command will output the updated date for each class.

## Troubleshooting

-   If API endpoints return HTML instead of JSON, ensure you set the `Accept: application/json` header.
-   Foreign key errors in tests usually mean a related model was not created; always create required related records in tests.
-   For Sanctum, ensure `HasApiTokens` is used in the `User` model.
-   To clear caches:
    ```
    sail artisan config:clear
    sail artisan cache:clear
    sail artisan route:clear
    ```
