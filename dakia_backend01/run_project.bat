@echo off

REM Daakia Backend starter script
REM Ensure MySQL (XAMPP) is running before using this script.

cd /d "%~dp0"

echo Installing PHP dependencies...
composer install
if %errorlevel% neq 0 (
    echo Composer install failed. Check composer.json.
    pause
    exit /b %errorlevel%
)

echo Running migrations and seeding database...
php artisan migrate:fresh --seed
if %errorlevel% neq 0 (
    echo Migration failed.
    pause
    exit /b %errorlevel%
)

echo Starting Laravel development server...
php artisan serve

pause
