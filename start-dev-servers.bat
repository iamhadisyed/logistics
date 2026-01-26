@echo off
echo ==========================================
echo Starting Daakia Development Servers
echo ==========================================
echo.

REM Start Laravel Backend
echo [1/2] Starting Laravel Backend (Port 8000)...
start "Daakia Backend" cmd /k "cd /d c:\daakia\dakia_backend01 && php artisan serve"

REM Wait a moment
timeout /t 3 /nobreak >nul

REM Start Next.js Frontend
echo [2/2] Starting Next.js Frontend (Port 3000)...
start "Daakia Frontend" cmd /k "cd /d c:\daakia\dakia_app01 && npm run dev"

echo.
echo ==========================================
echo Servers Starting!
echo ==========================================
echo Backend:  http://localhost:8000
echo Frontend: http://localhost:3000
echo.
echo Press any key to close this window...
pause >nul
