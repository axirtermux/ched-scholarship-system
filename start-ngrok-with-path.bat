@echo off
echo ========================================
echo CHED Scholarship System - Ngrok Setup
echo ========================================
echo.
echo This script will start ngrok to make your
echo localhost accessible online.
echo.
echo IMPORTANT: Edit this script and update the NGROK_PATH
echo variable to point to your ngrok.exe location if it's
echo not in your system PATH.
echo.
echo Example: set NGROK_PATH=C:\ngrok\ngrok.exe
echo.

REM Update this path to your ngrok.exe location if needed
set NGROK_PATH=ngrok

REM Check if ngrok exists
where ngrok >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo ERROR: ngrok not found in PATH.
    echo.
    echo Please either:
    echo 1. Add ngrok to your system PATH, or
    echo 2. Edit this script and set NGROK_PATH to full path
    echo    Example: set NGROK_PATH=C:\Users\YourName\Downloads\ngrok.exe
    echo.
    pause
    exit /b 1
)

echo Prerequisites check:
echo - ngrok: Found
echo.
echo IMPORTANT: Make sure XAMPP Apache is running on port 80
echo.
echo Press any key to start ngrok tunnel...
pause > nul

echo.
echo Starting ngrok tunnel on port 80...
echo Your system will be accessible at the HTTPS URL shown below
echo.

%NGROK_PATH% http 80

echo.
echo Ngrok has stopped. Press any key to exit...
pause > nul
