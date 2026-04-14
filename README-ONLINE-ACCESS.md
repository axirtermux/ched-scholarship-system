# CHED Scholarship System - Online Access Setup

## Quick Start Guide

Your system is now configured for online access without hosting. Choose one of the two methods below:

## Method 1: Port Forwarding (Recommended for Permanent Access)

### Step 1: Ensure XAMPP is Running
- Start XAMPP Control Panel
- Start Apache server (should be on port 80)
- Verify by visiting http://localhost/ched in your browser

### Step 2: Configure Router Port Forwarding
1. Access your router's admin panel (usually 192.168.1.1 or 192.168.0.1)
2. Find "Port Forwarding" or "Virtual Server" settings
3. Add a new rule:
   - **External Port**: 80 (or choose a different port like 8080)
   - **Internal Port**: 80
   - **Internal IP**: Your computer's local IP (e.g., 192.168.168.1.100)
   - **Protocol**: TCP
4. Save and apply changes

### Step 3: Get Your Public IP
- Visit https://whatismyipaddress.com to get your public IP
- Your system will be accessible at: `http://YOUR_PUBLIC_IP/ched/`

### Step 4: Set Up Dynamic DNS (Optional but Recommended)
Since your public IP may change, set up dynamic DNS:
1. Create a free account at https://www.noip.com/ or https://duckdns.org/
2. Create a hostname (e.g., your-system.ddns.net)
3. Install their dynamic DNS update client on your computer
4. Your system is now accessible at: `http://your-system.ddns.net/ched/`

### Step 5: Test Connection
- Visit: `http://your-public-ip/ched/test-connection.php`
- Or: `http://your-dynamic-dns/ched/test-connection.php`

## Method 2: ngrok Tunneling (Quick Setup)

### Step 1: Ensure XAMPP is Running
- Start XAMPP Control Panel
- Start Apache server (should be on port 80)
- Verify by visiting http://localhost/ched in your browser

### Step 2: Install ngrok
1. Download ngrok from https://ngrok.com/download
2. Extract to a folder (e.g., `C:\ngrok\`)
3. Sign up at https://ngrok.com/signup (free)
4. Get your authtoken from the dashboard
5. Run: `ngrok config add-authtoken YOUR_AUTH_TOKEN`

### Step 3: Start Online Access
**Option A: Using the provided script**
- Double-click `start-ngrok-with-path.bat`
- Or edit the script to set your ngrok path if not in system PATH

**Option B: Manual command**
- Open Command Prompt
- Run: `ngrok http 80`

### Step 4: Get Your Online URL
- ngrok will display a forwarding URL like: `https://abc123.ngrok-free.app`
- Your system is now accessible at: `https://abc123.ngrok-free.app/ched/`
- Share this URL with anyone who needs access

### Step 5: Test Connection
- Visit: `https://your-ngrok-url.ngrok-free.app/ched/test-connection.php`
- You should see a JSON response confirming the connection

## Files Created

### Port Forwarding Method
1. **port-forwarding-guide.md** - Detailed router configuration guide
2. **check-network-config.bat** - Network configuration helper script
3. **test-connection.php** - Connection test endpoint

### ngrok Method
1. **ngrok-setup-guide.md** - Detailed ngrok setup instructions
2. **start-ngrok.bat** - Simple ngrok starter (ngrok must be in PATH)
3. **start-ngrok-with-path.bat** - Ngrok starter with path configuration
4. **test-connection.php** - Connection test endpoint

## Important Notes

### Port Forwarding Method Notes
- **Permanent access** - No need to keep any software running
- **Public IP changes** - Your ISP may change your public IP periodically
- **Router access required** - You need router admin credentials
- **Dynamic DNS recommended** - Use No-IP or DuckDNS for consistent hostname
- **Static local IP recommended** - Set a static IP for your computer

### ngrok Method Notes
- **Easy setup** - No router configuration needed
- **URL changes** - The URL changes each time you restart ngrok (unless paid plan)
- **Must keep running** - Keep ngrok terminal open while you need access
- **Free tier limitations** - URL changes, session limits
- **Paid benefits** - Custom domains, persistent URLs, better performance

### Security Considerations ⚠️
- **Your localhost is now publicly accessible** - anyone with the URL can access it
- Consider adding authentication for production use
- Don't expose sensitive data
- Monitor system access logs
- Use HTTPS if possible (requires SSL certificate setup)
- Keep your system and router firmware updated
- Consider using a non-standard port (e.g., 8080) to reduce automated attacks

### Keeping Access Active

**Port Forwarding:**
- Access is always available (no software needed)
- Set up dynamic DNS to handle IP changes
- Ensure your computer and router stay on

**ngrok:**
- Keep the ngrok terminal window open while you need access
- Closing the terminal closes the tunnel
- For persistent access, consider ngrok paid plans ($8/month+)

### Troubleshooting

**Port Forwarding Issues:**

**Port 80 already in use:**
- Stop Skype, IIS, or other apps using port 80
- Or use different external port (e.g., 8080) in router config

**Router access denied:**
- Check router credentials (often on router label)
- Try default passwords: admin, password, (blank)
- Reset router if needed (will reset all settings)

**Public IP not accessible:**
- Check port forwarding rules in router
- Verify Windows Firewall allows port 80
- Ensure Apache is running
- Try accessing from mobile data (not WiFi)

**Public IP keeps changing:**
- Set up dynamic DNS (see port-forwarding-guide.md)
- Or request static IP from ISP (may cost extra)

**ngrok Issues:**

**Port 80 already in use:**
- Stop Skype, IIS, or other apps using port 80
- Or use different port: `ngrok http 8080`

**ngrok command not found:**
- Add ngrok to system PATH
- Or use `start-ngrok-with-path.bat` and edit the NGROK_PATH variable

**Connection refused:**
- Ensure XAMPP Apache is running
- Check Apache is on port 80

**URL not accessible:**
- Check firewall settings
- Verify ngrok is running
- Try HTTP URL if HTTPS has certificate issues

### Alternative: LocalTunnel (No Account Required)

If you prefer not to create an ngrok account and don't want to configure port forwarding:

1. Install Node.js
2. Run: `npm install -g localtunnel`
3. Start: `lt --port 80 --subdomain your-custom-name`

Note: Similar to ngrok, this requires keeping the terminal open.

## System Configuration

Your system is already configured for external access:
- ✅ CORS enabled in config.php
- ✅ No hardcoded localhost URLs in application
- ✅ Database uses localhost (correct for local database)
- ✅ API routes work with external domains

No code changes required for basic online access.

## Support

For ngrok issues: https://ngrok.com/docs
For system issues: Check the main documentation

## Next Steps

1. Test the setup using the instructions above
2. Share the ngrok URL with intended users
3. Monitor system performance and security
4. Consider upgrading to ngrok paid plans for:
   - Custom subdomains
   - Persistent URLs
   - Better performance
   - Additional security features
