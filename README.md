# Job Listing Management System

## Introduction

This is a job listing management system with complex filtering capabilities, built using the Laravel web framework and a relational database. The system allows users to manage job listings with advanced filtering options.

## Technology Used

- Laravel
- MySQL

## Installation and Usage

### Running the Project

1. Clone the repository to your local machine using `git clone`.
2. Install dependencies: `composer install`.
3. Create and configure the `.env` file.
4. Run database migrations: `php artisan migrate`.
5. Seed the database: `php artisan db:seed`.
6. Start the server: `php artisan serve`.

## API Documentation

### Jobs Endpoint

#### GET `/api/jobs`

Retrieves all job listings with complex filtering options. The filtering system allows querying jobs based on attributes, languages, locations, and categories using various operators (`=`, `!=`, `>`, `<`, `>=`, `<=`, `IN`, `LIKE`).

**Important Notes on Filters:**
- When using `IN`, provide values in parentheses `()` separated by commas, with no spaces between `IN` and `()`.
- To filter by attributes, use the format `attribute_name:condition`. You can use `IN` with multiple values inside `()`.

**Example Request Parameters:**

```json
{
    "filter": "(job_type IN(full-time) AND (languages HAS_ANY(php))) AND (locations IS_ANY(NY)) OR attribute:years_experience<5"
}
```

This README provides an overview of the system and its design choices to ensure flexibility, performance, and maintainability.

