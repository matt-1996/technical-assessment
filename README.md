<h1 align="center">
    Technical Assessment
</h1>

## About Project

the Project was about Inventory management
- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

<h1>Note</h1>
the timing was very limit and i had to skip some features like Writing Tests,
some part of the project could be cleaner with better decisions like When a stock movement is recorded, update stock_levels accordingly inside a
database transaction with row-level locking ( SELECT ... FOR UPDATE ). I could use Event/Listener
Or put Create records in a Queue For Better Performance but i decided to keep it simple because of lack of time


# Installation
clone the Project
<br>
`git clone https://github.com/matt-1996/technical-assessment.git`

## Install Dependencies
`composer i`

## Set Environments Variable

## Run Migrations and Seeder

`php artisan migrate --seed`

## Reconcile Command

`php artisan inventory:reconcile {tenant : Tenant ID}`
