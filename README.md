## About Appointment
Build the backend of an api to manage users and appointments. 

During covid, my dentist office specifically kept calling me to change appointments- It made me wonder how things are done from their end and how to possibly make the experience better overall. 

This api is specifically for a dentist’s office to help manage their database.

If you would like to follow along in the series - The playlist link for this video can be found <a href="https://www.youtube.com/playlist?list=PLxyLoVOL_gY5SzhYeWqDkKHxxl8hETqQb" target="_blank">here</a>
<br>

## Tech stack
    - Laravel/PHP
    - Postgres

## Project setup
- Create .env file
    - This should be a replica of the .env.example, add the following:
    - `DB_CONNECTION=pgsql`
    - `DB_HOST=127.0.0.1`
    - `DB_PORT=`
    - `DB_DATABASE=appointment`
    - `DB_USERNAME=root`
    - `DB_PASSWORD=`

    NOTE: Password and Username is whatever you set it to. 

- Postgres
    - Install Postgres online
    - Apps I use (Mac user so you may have to find alternatives): 
        - Postgres App - Postgres server
        - Postbird - To see the database

- Install dependencies 
    - Clone project locally, navigate to where you placed it and then run `composer install`

- View API in Browser
    - In your terminal, type `php artisan serve` 
        - It should respond with the server port:

            ```INFO  Server running on [http://127.0.0.1:8000].```

- View Emails
    - I'm using Mailtrap, use your preferred mail service
        - Update the Mail attributes in your .env file based on the Settings in the Mail service you choose
        ```
        MAIL_MAILER=
        MAIL_HOST=
        MAIL_PORT=
        MAIL_USERNAME=
        MAIL_PASSWORD=
        MAIL_ENCRYPTION=
        MAIL_FROM_ADDRESS=
        ```

## How to view api (Non browser)
Use Postman [install here](https://www.postman.com/downloads/)
    - Example route to visit Get all users: http://127.0.0.1:8000/api/users

## How to run Test
- Initialise the database seeder by running the command: `php artisan db:seed`
- Remove all tables and add new data: `php artisan migrate:fresh --seed`
- Use this command to run a current test `php artisan test --filter AppointmentTest`
    - NOTE: `--filter` allows you to run one test class

**If you need to make changes to the database:**
- Run the command: `php artisan migrate:fresh --seed`
- Make your changes
- Then run the command `php artisan db:seed` and confirm your changes in the database.
