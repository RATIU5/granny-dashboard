#!/bin/sh

# Exit on non defined variables and on non zero exit codes
set -eu

SERVER_ADMIN="${SERVER_ADMIN:-you@example.com}"
HTTP_SERVER_NAME="${HTTP_SERVER_NAME:-www.example.com}"
HTTPS_SERVER_NAME="${HTTPS_SERVER_NAME:-www.example.com}"
LOG_LEVEL="${LOG_LEVEL:-info}"
TZ="${TZ:-UTC}"
PHP_MEMORY_LIMIT="${PHP_MEMORY_LIMIT:-256M}"

echo 'Updating configurations'

# Change Server Admin, Name, Document Root
sed -i "s/ServerAdmin\ you@example.com/ServerAdmin\ ${SERVER_ADMIN}/" /etc/apache2/httpd.conf
sed -i "s/#ServerName\ www.example.com:80/ServerName\ ${HTTP_SERVER_NAME}/" /etc/apache2/httpd.conf
sed -i 's#^DocumentRoot ".*#DocumentRoot "/htdocs"#g' /etc/apache2/httpd.conf
sed -i 's#Directory "/var/www/localhost/htdocs"#Directory "/htdocs"#g' /etc/apache2/httpd.conf
sed -i 's#AllowOverride None#AllowOverride All#' /etc/apache2/httpd.conf

# Change TransferLog after ErrorLog
sed -i 's#^ErrorLog .*#ErrorLog "/dev/stderr"\nTransferLog "/dev/stdout"#g' /etc/apache2/httpd.conf
sed -i 's#CustomLog .* combined#CustomLog "/dev/stdout" combined#g' /etc/apache2/httpd.conf

# SSL DocumentRoot and Log locations
sed -i 's#^ErrorLog .*#ErrorLog "/dev/stderr"#g' /etc/apache2/conf.d/ssl.conf
sed -i 's#^TransferLog .*#TransferLog "/dev/stdout"#g' /etc/apache2/conf.d/ssl.conf
sed -i 's#^DocumentRoot ".*#DocumentRoot "/htdocs"#g' /etc/apache2/conf.d/ssl.conf
sed -i "s/ServerAdmin\ you@example.com/ServerAdmin\ ${SERVER_ADMIN}/" /etc/apache2/conf.d/ssl.conf
sed -i "s/ServerName\ www.example.com:443/ServerName\ ${HTTPS_SERVER_NAME}/" /etc/apache2/conf.d/ssl.conf

# Re-define LogLevel
sed -i "s#^LogLevel .*#LogLevel ${LOG_LEVEL}#g" /etc/apache2/httpd.conf

# Enable commonly used apache modules
sed -i 's/#LoadModule\ rewrite_module/LoadModule\ rewrite_module/' /etc/apache2/httpd.conf
sed -i 's/#LoadModule\ deflate_module/LoadModule\ deflate_module/' /etc/apache2/httpd.conf
sed -i 's/#LoadModule\ expires_module/LoadModule\ expires_module/' /etc/apache2/httpd.conf

# Modify php memory limit and timezone
sed -i "s/memory_limit = .*/memory_limit = ${PHP_MEMORY_LIMIT}/" /etc/php81/php.ini
sed -i "s#^;date.timezone =\$#date.timezone = \"${TZ}\"#" /etc/php81/php.ini
sed -i 's/^display_errors =.*/display_errors = On/' /etc/php81/php.ini
sed -i 's/^error_reporting =.*/error_reporting = E_ALL/' /etc/php81/php.ini

# Modify xdebug.ini to enable it
sed -i 's/^;xdebug.mode =.*/xdebug.mode = debug/' /etc/php81/conf.d/50_xdebug.ini
echo "zend_extension=xdebug.so" >> /etc/php81/conf.d/50_xdebug.ini \
	&& echo "xdebug.mode=debug" >> /etc/php81/conf.d/50_xdebug.ini \
	&& echo "xdebug.log=/var/www/html/xdebug/xdebug.log" >> /etc/php81/conf.d/50_xdebug.ini \
	&& echo "xdebug.discover_client_host=1" >> /etc/php81/conf.d/50_xdebug.ini \
	&& echo "xdebug.remote_enable=on" >> /etc/php81/conf.d/50_xdebug.ini \
	&& echo "xdebug.client_host=host.docker.internal" >> /etc/php81/conf.d/50_xdebug.ini \
	&& echo "xdebug.remote_host=host.docker.internal" >> /etc/php81/conf.d/50_xdebug.ini \
	&& echo "xdebug.client_port=9000" >> /etc/php81/conf.d/50_xdebug.ini

echo 'Running Apache'

httpd -D FOREGROUND
