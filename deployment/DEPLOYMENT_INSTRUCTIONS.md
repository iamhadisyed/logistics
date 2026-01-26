# GoDaddy Deployment Instructions 
 
## Backend (Laravel) 
1. Upload all files from 'backend' folder to your GoDaddy hosting 
2. Rename .env.production to .env and update database credentials 
3. Run: composer install --optimize-autoloader --no-dev 
4. Run: php artisan key:generate 
5. Run: php artisan migrate --force 
6. Run: php artisan db:seed --class=LogisticsSeeder 
 
## Frontend (Next.js) 
1. Upload all files from 'frontend' folder to your GoDaddy hosting 
2. Run: npm install --production 
3. Configure your web server to serve the Next.js app 
