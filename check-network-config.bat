@echo off
echo ========================================
echo CHED Scholarship System - Network Check
echo ========================================
echo.
echo This script will help you configure
echo port forwarding by checking your network.
echo.

echo Checking your local IP configuration...
echo.
ipconfig | findstr "IPv4"
echo.

echo Checking for active Apache server...
echo.
netstat -an | findstr ":80"
echo.

echo Router IP (Default Gateway):
ipconfig | findstr "Default Gateway"
echo.

echo ========================================
echo Next Steps:
echo ========================================
echo.
echo 1. Note your IPv4 address (e.g., 192.168.1.100)
echo 2. Note your Default Gateway (router IP)
echo 3. Access your router admin panel using the gateway IP
echo 4. Configure port forwarding:
echo    - External Port: 80
echo    - Internal Port: 80
echo    - Internal IP: Your IPv4 address from step 1
echo    - Protocol: TCP
echo 5. Get your public IP from: https://whatismyipaddress.com
echo 6. Test access: http://YOUR_PUBLIC_IP/ched/
echo.
echo For detailed instructions, see: port-forwarding-guide.md
echo.
pause
