# KSG Live

Browser-based screen sharing for the KSG local network. No login. No installs.
Just open a URL.

## What it does

- Presenter shares their screen and microphone
- Viewers watch and hear in a browser at ~30ms latency
- Multiple rooms run at the same time
- Works entirely on the LAN, no internet needed

## Files

    index.html      The page users see
    signal.php      WebRTC signaling (no auth)
    cleanup.php     Removes old signaling data (run daily)
    logo.png        KSG logo
    start.sh        Pretty launcher (Linux/macOS)

## How to deploy

1. Copy these files to a web server with PHP 8.0+
2. Point the server at the folder
3. Open the URL in a browser

## How to use

    Presenter:  http://<server-ip>/?role=presenter&room=class1
    Viewer:     http://<server-ip>/?role=viewer&room=class1

Or open the bare URL and pick a role from the buttons.

## Requirements

- Web server with PHP 8.0+
- All users on the same network
- Chrome or Edge

## Does NOT need

- Internet access
- A database
- User accounts
- Any client install
