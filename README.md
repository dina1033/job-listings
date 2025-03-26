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

## Design Decisions and Trade-offs  

### **1. EAV Implementation**  
- Used a traditional Entity-Attribute-Value (EAV) pattern with separate tables for attributes and values.  
- **Trade-off:** More complex queries but provides maximum flexibility.  
- **Alternative:** JSON columns in the jobs table for simpler queries but less filtering capability.  

### **2. Filter Parsing**  
- Implemented a custom parser for the filter string.  
- **Trade-off:** Could have used a package like Doctrine's Lexer for more robust parsing.  
- **Decision:** Kept it simple for the initial implementation but noted the need for improvement.  

### **3. Query Optimization**  
- Added indexes on frequently filtered fields.  
- Used eager loading to prevent N+1 queries.  
- **Trade-off:** More complex queries for EAV filtering.  

### **4. API Design**  
- Chose a single filter parameter with a custom syntax.  
- **Alternative:** Could have used multiple query parameters for different filter types.  
- **Decision:** A single parameter allows for more complex, nested filters.  
