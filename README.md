##Setting up Development server using git
1. Setup the db, get the username, password, dbname, hostname
2. Get to your server public_html or www
3. Open the folder in terminal
4. Git clone https://github.com/greatcollections/single.git it will ask for your GitHub username and password
5. A folder named single will be created and the code will be downloaded to the directory, rename the dir to your need
6. Open ./single/functions/conf.php
7. Change DATABASE,SERVER,SERVER_USERNAME,SERVER_PASSWORD to match you current database
8. Goto the server url
9. Modify .htaccess is the setup is working from a subfolder. php based url redirect
10. You will receive a Cache not writable error with a folder name next to it, change the folder to PHP writable one
11. If the code is hosted in a subfolder like localhost/single add single/ to SUB_FOLDER_ROOT in conf.php( make sure there is a forward slash at the end)
12. Create a crontab in your terminal like "* * * * * curl http://localhost/single/cron.php" which runs every minute.
