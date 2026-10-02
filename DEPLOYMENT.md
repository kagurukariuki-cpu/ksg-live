DEPLOYMENT GUIDE
================

FOR THE ADMINISTRATOR
---------------------

WHAT YOU NEED FROM IT
---------------------

  1. A folder on an IIS server, for example:
         D:\WebStatic\ksg-live\

  2. IIS configured to serve that folder at a URL, for example:
         https://intranet.ksg.ac.ke/ksg-live/

  3. PHP 8.0 or higher enabled on IIS

  4. Write access to that folder so you can:
        - Drop the 11 files in
        - Edit users.json when needed
        - Use admin.php to manage accounts

  5. An IIS Request Filter on /ksg-live/ that denies HTTP access
     to *.json files. This protects users.json from being
     downloaded directly by a browser.

  6. Optional: a Windows Task Scheduler entry that runs:
        php cleanup.php
     once per day.


THE EMAIL TO SEND IT
--------------------

Subject: Small IIS virtual directory for a presentation

Hello,

I need to host a small web app on the internal network for an
upcoming presentation. Ten files total, about 100 KB.

Could you:

  1. Create a folder at D:\WebStatic\ksg-live\
  2. Point IIS at it as https://intranet.ksg.ac.ke/ksg-live/
  3. Confirm PHP 8.0+ is enabled (or tell me if only ASP.NET)
  4. Give me write access to the folder
  5. Add a Request Filter denying HTTP access to *.json

The app is static files plus a few small PHP scripts. No database,
no external calls, no server-side processes other than PHP.

I will remove everything after the presentation.

Thanks.


FIRST-TIME SETUP
----------------

Step 1  Copy all 11 files from this folder to the server folder

Step 2  On the server, open Command Prompt and run:

            cd D:\WebStatic\ksg-live
            php make_hash.php "KSG@2026"

        The output looks like:
            $2y$12$abcdefghijklmnopqrstuvwxyz0123456789...

Step 3  Open users.json in a text editor
        Replace the line that says:
            "password_hash": "$2y$12$REPLACE_WITH_HASH",
        With:
            "password_hash": "PASTE_THE_HASH_HERE",
        Save the file

Step 4  Delete make_hash.php from the server (no longer needed)

Step 5  Open the URL in a browser:
            https://intranet.ksg.ac.ke/ksg-live/

Step 6  Log in as:
            Email:    admin@ksg.ac.ke
            Password: KSG@2026

Step 7  When prompted, change the admin password to something strong
        (minimum 8 characters, letter + number)

Step 8  Go to:
            https://intranet.ksg.ac.ke/ksg-live/admin.php

Step 9  Add teachers and students:
        - Email: their KSG email
        - Role:  viewer / presenter / admin
        - Classes: comma-separated (e.g., Class 1, Class 2)
        - Temp password: leave as KSG@2026 or set a new one

Step 10 Tell each user their email and temporary password


MANAGING USERS AFTERWARD
------------------------

Add a user        admin.php → fill in the form → Add
Remove a user     admin.php → find their row → Delete
Force a reset     admin.php → find their row → Reset


PASSWORD RULES (enforced by the server)
---------------------------------------

  - At least 8 characters
  - At least one letter
  - At least one number
  - Must be different from the previous password


SECURITY NOTES
--------------

  - Passwords are stored as bcrypt hashes (cost 12)
  - Session cookies are HttpOnly and SameSite=Strict
  - Sessions expire after 8 hours
  - Users can only access their assigned classes
  - Only presenters can broadcast
  - Admin panel is a separate page requiring role=admin


REMOVING AFTER THE EVENT
------------------------

Delete the folder D:\WebStatic\ksg-live\ entirely.
No permanent changes are left on the server.