# manages job listing System

## Introduction

This is a fleet management system for manages job listings with complex filtering , built using Laravel web framework and a relational database. The system is designed to manage  manages job listings with complex filtering .


## Technology Used

- Laravel.
- mysql
  
## Installation and Usage

### Running the Project

1. Clone the repository to your local machine using `git clone`.
2. Run composer install.
3. Create and configure .env file.
4. Run migrations: php artisan migrate.
5. Seed data: php artisan db:seed.
6. Start the server: php artisan serve.

# API Documentation

## Jobs Endpoint

### GET ```/api/jobs```

Get all jobs of the system with complex filter paramter to get the jobs with it's attributes , languages , locations and categories
you can use this operatores ( =, != , >, <, >=, <= , IN , LIKE)
be carful when using IN give me value in () with comma seperated and don't but space between IN and () 
when using attribute you can put the name or type or options of attribute after : then you can get multi value for this by using IN with ().

**Request Params:**

```json
{
    "filter":(job_type IN(full-time) AND (languages HAS_ANY (php))) AND (locations IS_ANY (NY)) OR attribute:years_experience<5,
}
```

## Design Decisions and Trade-offs

1.EAV Implementation:
-** Used a traditional EAV pattern with separate tables for attributes and values
-** Trade-off: More complex queries but provides maximum flexibility
-** Alternative: JSON columns in the jobs table for simpler queries but less filtering capability
2.Filter Parsing:
-** Implemented a custom parser for the filter string
-**Trade-off: Could have used a package like Doctrine's Lexer for more robust parsing
-** Decision: Kept it simple for the assessment but noted it would need improvement
3.Query Optimization:
-** Added indexes on frequently filtered fields
-** Used eager loading to prevent N+1 queries
-** Trade-off: More complex queries for EAV filtering
4.API Design:
-** Chose a single filter parameter with a custom syntax    
-** Alternative: Could have used multiple query parameters for different filter types
-** Decision: Single parameter allows for more complex, nested filters
