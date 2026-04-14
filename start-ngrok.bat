@echo off
echo ========================================
echo CHED Scholarship System - Ngrok Setup
echo ========================================
echo.
echo This script will start ngrok to make your
echo localhost accessible online.
echo.
echo Prerequisites:
echo 1. ngrok must be installed and in your PATH
echo 2. XAMPP Apache must be running on port 80
echo 3. You must have authenticated ngrok with your authtoken
echo.
echo If ngrok is not in your PATH, edit this script
echo and provide the full path to ngrok.exe
echo.
echo Press any key to start ngrok...
pause > nul

echo.
echo Starting ngrok tunnel on port 80...
echo.

ngrok http 80

echo.
echo Ngrok has stopped. Press any key to exit...
pause > nul
