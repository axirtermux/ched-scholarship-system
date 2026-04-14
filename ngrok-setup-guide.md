# CHED Scholarship System - Online Access Setup Guide

## Overview
This guide will help you make your localhost CHED scholarship system accessible online without hosting, using ngrok tunneling service.

## Prerequisites
- XAMPP Apache server running
- Active internet connection
- ngrok account (free tier available)

## Step 1: Install ngrok

1. Download ngrok from https://ngrok.com/download
2. Extract the downloaded file
3. Place `ngrok.exe` in a convenient location (e.g., `C:\ngrok\`)
4. Add ngrok to your system PATH or remember the full path

## Step 2: Authenticate ngrok

1. Sign up at https://ngrok.com/signup
2. Get your authtoken from the dashboard
3. Run in command prompt:
   ```cmd
   ngrok config add-authtoken YOUR_AUTH_TOKEN
   ```

## Step 3: Start ngrok Tunnel

Run the following command in your terminal/command prompt:
```cmd
ngrok http 80
```

This will:
- Start a secure tunnel to your localhost port 80
- Provide you with a public URL (e.g., https://abc123.ngrok-free.app)
- Display the forwarding URL in your terminal

## Step 4: Access Your System Online

1. Copy the HTTPS forwarding URL from ngrok (e.g., https://abc123.ngrok-free.app)
2. Access your system using: `https://abc123.ngrok-free.app/ched/`
3. Share this URL with others for online access

## Step 5: Keep ngrok Running

- Keep the ngrok terminal window open while you need online access
- The tunnel will close when you close the terminal
- For persistent access, consider ngrok's paid plans or use a startup script

## Alternative: LocalTunnel (No Account Required)

If you prefer not to create an ngrok account:

1. Install Node.js if not already installed
2. Install localtunnel:
   ```cmd
   npm install -g localtunnel
   ```
3. Start the tunnel:
   ```cmd
   lt --port 80 --subdomain your-custom-name
   ```

## Security Considerations

⚠️ **Important Security Notes:**
- Your localhost is now publicly accessible
- Anyone with the URL can access your system
- Consider adding authentication in production
- Don't expose sensitive data
- Use the HTTPS URL provided by ngrok
- Monitor ngrok activity in your dashboard

## Troubleshooting

**Port 80 already in use:**
- Stop Skype or other applications using port 80
- Or use a different port: `ngrok http 8080`

**Connection refused:**
- Ensure XAMPP Apache is running
- Check if Apache is listening on the correct port

**URL not accessible:**
- Check your firewall settings
- Verify ngrok is running properly
- Try the HTTP URL if HTTPS has issues

## Advanced Configuration

For custom domains and persistent tunnels, consider ngrok's paid plans starting at $8/month.

## System Configuration Updates

Your current system is already configured to work with external URLs:
- CORS is enabled in config.php
- No hardcoded localhost URLs in the application
- Database uses localhost (correct for local access)

No code changes are required for basic ngrok setup.
