TROUBLESHOOTING
===============

FOR USERS
---------

"Invalid login"
    Check spelling of email and password.
    If still failing, ask the administrator to reset.

"Waiting for presenter..."
    The teacher has not started sharing yet. Wait.

No sound
    Click once on the video.
    Check the volume slider in the top-right.
    Check the device volume (laptop volume keys).

Video freezes
    Wait 10 seconds. If it stays frozen, click Exit
    and reload the page.

"Access Denied"
    You are trying to enter a class you are not assigned to.
    Contact the administrator.

Forgot password
    Contact the administrator. They can reset it.


FOR THE ADMINISTRATOR
---------------------

Login always fails
    - The hash in users.json is wrong
    - Regenerate: php make_hash.php "newpassword"
    - Paste the new hash into users.json

Change-password loops forever
    - users.json is not writable by PHP
    - Check file permissions on the server

"Permission denied" when saving a password
    - Same as above. Fix write permissions.

Viewer stuck on "Waiting for presenter..."
    - Presenter is not sharing yet
    - Or presenter and viewer are on different networks
    - Or the presenter's firewall is blocking outbound

Video works but no audio
    - Presenter did not tick "Share system audio" in the
      Chrome picker
    - Presenter did not allow microphone when prompted
    - Check the presenter's "Sources:" line on the sender
      page: it should say "screen + system audio + mic"

Blank page at the URL
    - Check the PHP error log
    - Confirm PHP is running
    - Confirm index.html is in the folder

Chrome does not show "Share system audio"
    - The checkbox only appears on the "Entire Screen" tab
    - Not on "Window"
    - If still missing, use Microsoft Edge for the presenter


FOR THE PRESENTER (during a session)
------------------------------------

Keep the tab visible
    Chrome slows down background tabs. If you switch away,
    the stream will pause. Keep the sender tab in front.

Do not minimize the window
    Same reason.

Changing display mode
    Press Win+P BEFORE clicking Start Sharing.
    If you change it during a session, the capture freezes.

Recording
    Click the Record button before or during sharing.
    When you stop, a .webm file downloads to your Downloads
    folder. Play it with VLC or Chrome.


FOR IT SUPPORT
--------------

Endpoints
    /login.php              POST email + password
    /change_password.php    POST current + new
    /signal.php             GET/POST signaling messages
    /me.php                 GET session status
    /logout.php             GET clears session
    /admin.php              Browser UI for user management
    /cleanup.php            GET deletes old temp files

Session
    PHP session cookie
    HttpOnly, SameSite=Strict
    8-hour expiry

Storage
    users.json — accounts (bcrypt hashes)
    PHP temp dir — signaling queues (deleted daily)

Network
    All traffic is HTTPS on port 443
    No external calls
    No DNS lookups outside the intranet
    No WebSocket — polling over HTTP

Logs
    Standard PHP error log
    No custom logging