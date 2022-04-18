

# Get Started


* `system` - The directory containing all configuration files and sockets.
  * `system/config` - The directory containing configuration files for MariaDB, Redis, etc.
  * `system/(package name)` - The directory containing the sockets and data for each package. This shouldn't be modified.

# Creating a Database
> Even if the repl is private, it is not recommended to store sensitive data. For more production-like tasks, a hosted database being provided elsewhere is likely a good idea.

Next up is creating the MySQL database. Open the "Shell" tab in Replit, and execute mysql.sh using ``./mysql.sh``. You may or may not have to grant executable permissions via chmod first (``chmod +x mysql.sh``).

You should then be dropped into the MariaDB shell. To create a database, execute the following commands:
```sql
CREATE DATABASE yourdbname;
USE yourdbname;
```

It should be as simple as that to create a database, but it's no good if you don't have a user! Let's go ahead and do that now. Make sure you choose a strong password (ideally random). Next, you'll need to grant privileges to this user on the database.
```sql
CREATE USER 'username'@'%' IDENTIFIED BY 'password';
GRANT ALL PRIVILEGES ON yourdbname.* TO 'username'@'%';
```

You should be done here! Type exit in the command line to exit MariaDB.





