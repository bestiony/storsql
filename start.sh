
echo -e "\e[32mStarting application...\033[0m"

# Start MariaDB and Redis

if pidof -x "mysqld" -o $$ > /dev/null; then
echo "MariaDB already running. No need to run again."
else
mysqld --defaults-file=/home/runner/$REPL_SLUG/system/config/my.cnf --datadir="/home/runner/$REPL_SLUG/system/mariadb" --silent-startup &
fi


# # Serve application
# cd src
echo -e "\e[32mRunning index.php\033[0m"
php -S 0.0.0.0:8000 -t .
