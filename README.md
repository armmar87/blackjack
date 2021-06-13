

# Instruction for installing blackjack

## PHP version ^7.3
## Laravel version ^8.40

- Clonning or download project
- Run `composer install` command
- Create `.env` dile in main repository
- Copy `.env.example` content and past to `.env`
- Create DB for application
- Run `php artisan migrate --seed` command
- Run `php artisan serve` command
- And call this endpoint `http://127.0.0.1:8000/api/play-blackjack`
