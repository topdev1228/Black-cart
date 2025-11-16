#!/bin/bash
echo "using start up script"
nohup socat TCP-LISTEN:8084,fork TCP:localhost:8080 &
echo $! > /tmp/socat.pid
export APP_ENV=local
php artisan config:cache
export APP_ENV=development
PID=$(netstat -tulpn | grep 5174 | awk '$4~":'"$port"'"{ gsub(/\/.*/,"",$7); print $7 }')
[ ! -z "$PID" ] && kill -9 ${PID}
npx vite --host --port 5174
