#! /bin/sh

php bin/console access:import
google-chrome-stable --disable-gpu --headless --window-size=1920,1080 --no-sandbox --remote-debugging-address=0.0.0.0 --remote-debugging-port=9222 &

sudo /usr/sbin/apache2ctl -D FOREGROUND
