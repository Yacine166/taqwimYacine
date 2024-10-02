#env file
cp .env.example .env

# Install Composer dependencies
composer install
npm install

#mysql dbs
mysql -u root
CREATE DATABASE taqwim

# Run the first time configuration commands
php artisan migrate:fresh --seed
php artisan key:generate
