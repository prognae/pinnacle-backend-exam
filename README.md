<h2>Step by step process on how to run the laravel application</h2>

1. Clone the repository so you can get all the needed files.
2. After cloning, open the terminal and navigate to the cloned repository.
3. Run 'composer update' to initialize laravel in the application.
4. Run 'npm install' to get all the required npm libraries and packages.
5. Navigate and rename .env.example to .env so it can become your environtment variables.
6. In the terminal, run 'php artisan key:generate' to initialize your unique app key in the .env file.
7. In the .env file, update your database configuration so all migrations can be present in your desired database.
8. In the terminal, run 'php artisan migrate' to migrate all tables from the app to your local database.
9. Congratulations! You can now run the laravel application. Run 'php artisan serve' in the terminal and you are all set.

--

- Authentication used is Laravel Auth and Sanctum. Users need to create/login an existing account before they can run the CRUD operations for users and posts. 
- Only authenticated users can use the user and post routes.
- Posts can be filtered by either specifying a user ID in the request params (ex. ?user=1) or by searching them by their title or content (ex. ?search=test).
- Only the owner of the post can delete that post.


