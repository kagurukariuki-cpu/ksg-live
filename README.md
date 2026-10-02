KSG LIVE — LOCAL NETWORK DEPLOYMENT
====================================

WHAT IT IS
----------

A browser-based screen-sharing system for the KSG internal network.
The presenter's screen and microphone stream directly to each viewer
over WebRTC. No installs on user devices. No internet needed.

WHAT IT DOES
------------

  - Presenter shares their screen and microphone
  - Viewers watch and hear in a browser at ~30ms latency
  - Multiple classes run at the same time
  - Users log in with a KSG email and password
  - Passwords are bcrypt-hashed
  - Only presenters can broadcast


FILES IN THIS FOLDER
--------------------

  index.html            The page users see (login, class picker, sender, viewer)
  login.php             Verifies email + password, starts a session
  change_password.php   Forces a new password on first login
  signal.php            Introduces the two peers (WebRTC signaling)
  logout.php            Clears the session
  me.php                Checks if the user is already logged in
  admin.php             Add or remove users from a browser
  cleanup.php           Removes old signaling data (run daily)
  make_hash.php         One-time utility to generate password hashes
  users.json            Account list (bcrypt hashes, not plain passwords)
  logo.png              KSG logo shown at the top of every page


HOW TO DEPLOY
-------------

1. Copy all 11 files to the web server folder
   Example: D:\WebStatic\ksg-live\

2. Point IIS (or Apache) at that folder so it is reachable at a URL
   Example: https://intranet.ksg.ac.ke/ksg-live/

3. Confirm PHP 8.0+ is enabled

4. On the server, run:
       php make_hash.php "KSG@2026"

   Copy the hash that prints (starts with $2y$12$...)

5. Open users.json in a text editor
   Replace REPLACE_WITH_HASH with the hash from step 4
   Save

6. Block direct HTTP access to *.json in this folder
   (adds protection for users.json)

7. Delete make_hash.php from the server

8. Open the URL in a browser
   Log in as admin@ksg.ac.ke / KSG@2026
   Change the password when prompted
   Add teachers and students at /admin.php


HOW USERS USE IT
----------------

PRESENTER:
  1. Open the URL
  2. Sign in with KSG email + password
  3. Pick a class
  4. Click Start Sharing
  5. In Chrome: choose "Entire Screen", tick "Share system audio"
  6. Click Share, allow microphone

VIEWER:
  1. Open the URL
  2. Sign in
  3. Pick their class
  4. Watch


REQUIREMENTS
------------

  - Web server with PHP 8.0 or higher
  - All users on the same internal network
  - Chrome or Edge on each device


WHAT IT DOES NOT NEED
---------------------

  - Internet access
  - A database
  - Installation on any client device


SUPPORT
-------

Administrator: Kaguru Kariuki (kaguru.kariuki@ksg.ac.ke)