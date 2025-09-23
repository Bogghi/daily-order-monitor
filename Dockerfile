FROM nginx:alpine
WORKDIR /var
RUN /bin/mkdir -p /var/www/html/API/v1
COPY api /var/www/html/API/v1
COPY nginx/default.conf /etc/nginx/conf.d/default.conf