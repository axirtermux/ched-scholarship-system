# Port Forwarding Setup Guide for CHED Scholarship System

## Overview
This guide provides detailed instructions for setting up port forwarding to make your CHED Scholarship System accessible online without using tunneling services like ngrok.

## Prerequisites
- XAMPP Apache server running
- Router access (admin credentials)
- Static local IP for your computer (recommended)
- Public IP address

## Step 1: Find Your Computer's Local IP

### Windows
1. Open Command Prompt
2. Run: `ipconfig`
3. Look for "IPv4 Address" under your network adapter
4. Example: `192.168.1.100`

### Set Static IP (Recommended)
1. Open Network Settings
2. Go to Change adapter options
3. Right-click your network adapter → Properties
4. Select "Internet Protocol Version 4 (TCP/IPv4)" → Properties
5. Choose "Use the following IP address":
   - IP address: Your current local IP (e.g., 192.168.1.100)
   - Subnet mask: 255.255.255.0
   - Default gateway: Your router IP (usually 192.168.1.1)
   - Preferred DNS server: 8.8.8.8 (Google DNS)
6. Save changes

## Step 2: Access Router Admin Panel

### Common Router Addresses
- 192.168.1.1 (most common)
- 192.168.0.1
- 192.168.1.254
- 10.0.0.1

### Finding Router IP
1. Open Command Prompt
2. Run: `ipconfig`
3. Look for "Default Gateway" under your active adapter
4. Enter this address in your browser

### Login Credentials
- Check your router label/sticker
- Default usernames: admin, administrator, root
- Default passwords: admin, password, (blank)
- If changed, use your custom credentials

## Step 3: Configure Port Forwarding

### Common Router Interfaces

#### TP-Link
1. Go to Forwarding → Virtual Servers
2. Click "Add New"
3. Configure:
   - Service Port: 80 (or external port of choice)
   - Internal Port: 80
   - IP Address: Your computer's local IP
   - Protocol: All
   - Status: Enabled
4. Save

#### D-Link
1. Go to Advanced → Port Forwarding
2. Click "Add"
3. Configure:
   - Name: CHED-System
   - External Port: 80
   - Internal Port: 80
   - Protocol: TCP
   - Internal IP: Your computer's local IP
4. Apply

#### Netgear
1. Go to Advanced → Advanced Setup → Port Forwarding/Port Triggering
2. Click "Add Custom Service"
3. Configure:
   - Service Name: CHED-System
   - External Starting Port: 80
   - External Ending Port: 80
   - Internal Starting Port: 80
   - Internal Ending Port: 80
   - Internal IP Address: Your computer's local IP
4. Apply

#### ASUS
1. Go to WAN → Virtual Server/Port Forwarding
2. Click "Yes" for Enable Port Forwarding
3. Add new rule:
   - Service Name: CHED-System
   - Port Range: 80
   - Local IP: Your computer's local IP
   - Local Port: 80
   - Protocol: TCP
4. Apply

#### Generic Steps (if brand not listed)
1. Look for "Port Forwarding", "Virtual Server", "NAT", or "Applications & Gaming"
2. Add a new forwarding rule
3. Configure:
   - External Port: 80 (or your choice)
   - Internal Port: 80
   - Internal IP: Your computer's local IP
   - Protocol: TCP (or Both)
4. Save/Apply

## Step 4: Configure Windows Firewall

### Allow Apache Through Firewall
1. Open Windows Defender Firewall
2. Go to "Allow an app or feature through Windows Defender Firewall"
3. Click "Change settings"
4. Find "Apache HTTP Server" or add it
5. Check both "Private" and "Public" networks
6. Click OK

### Alternative: Open Port 80
1. Open Windows Defender Firewall with Advanced Security
2. Go to Inbound Rules → New Rule
3. Select "Port"
4. TCP, Specific local ports: 80
5. Allow the connection
6. Apply to all profiles
7. Name: "Apache HTTP Server"
8. Finish

## Step 5: Get Your Public IP

1. Visit: https://whatismyipaddress.com
2. Note your public IP address
3. Test access: `http://YOUR_PUBLIC_IP/ched/`

## Step 6: Set Up Dynamic DNS (Optional but Recommended)

### Why Dynamic DNS?
- Your public IP may change when router restarts
- Dynamic DNS gives you a permanent hostname
- No need to share changing IP addresses

### Using No-IP (Free)
1. Sign up at https://www.noip.com/
2. Create a hostname (e.g., your-ched-system.ddns.net)
3. Download and install the DUC (Dynamic Update Client)
4. Login with your No-IP credentials
5. Select your hostname
6. The client will automatically update your IP

### Using DuckDNS (Free)
1. Go to https://www.duckdns.org/
2. Login with your preferred account (Google, etc.)
3. Create a subdomain (e.g., your-ched-system.duckdns.org)
4. Install the DuckDNS update client or use their cron script
5. Your IP will be automatically updated

## Step 7: Test Your Setup

### Local Test
1. Visit: `http://localhost/ched/test-connection.php`
2. Should return JSON success response

### Network Test
1. Use your phone on mobile data (not WiFi)
2. Visit: `http://YOUR_PUBLIC_IP/ched/test-connection.php`
3. Or: `http://your-dynamic-dns/ched/test-connection.php`

### Troubleshooting
- If not accessible: Check firewall settings
- If timeout: Verify port forwarding rules
- If connection refused: Ensure Apache is running
- If 403 error: Check Apache configuration

## Security Considerations

⚠️ **Important Security Notes:**
- Your system is now publicly accessible
- Consider adding authentication
- Use HTTPS if possible (requires SSL certificate)
- Monitor access logs
- Keep your system updated
- Consider using a non-standard port (e.g., 8080) to reduce automated attacks

## Advanced: Using a Different External Port

If port 80 is blocked by your ISP or you want to use a different port:

1. Change external port in router forwarding (e.g., 8080)
2. Access via: `http://YOUR_PUBLIC_IP:8080/ched/`
3. No changes needed to Apache (still uses internal port 80)

## Maintenance

- Keep your router firmware updated
- Monitor your public IP changes
- Keep dynamic DNS client running
- Regularly check system accessibility
- Review router logs for suspicious activity

## Troubleshooting Common Issues

### Port Already in Use
- Check if other services use port 80
- Use a different external port (e.g., 8080)

### ISP Blocks Port 80
- Use port 8080 or 8443 instead
- Some ISPs block port 80 for security

### Router Restart Changes IP
- Set up dynamic DNS (see Step 6)
- Or configure static public IP (requires ISP plan)

### Firewall Blocking Access
- Check Windows Firewall
- Check router firewall settings
- Temporarily disable for testing

## Support Resources

- Router manufacturer support website
- PortForward.com (detailed router guides)
- No-IP support (dynamic DNS)
- DuckDNS documentation
