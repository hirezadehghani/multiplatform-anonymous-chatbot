FROM nginx:alpine

# Copy the configuration template
COPY docker/nginx/default.conf.template /etc/nginx/templates/default.conf.template

# Copy only the public folder from your host/build directory into Nginx
COPY public /var/www/public