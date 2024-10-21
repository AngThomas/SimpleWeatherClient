ARG APACHE_VERSION=""
FROM httpd:${APACHE_VERSION:+${APACHE_VERSION}-}alpine

RUN apk update \
    && apk upgrade

# Copy apache vhost file to proxy php requests to php-fpm container
COPY project.conf conf/project.conf
RUN echo "Include conf/project.conf" >> conf/httpd.conf

# Remove defautl host configuration
RUN sed -i \
    -e 's~^\(DocumentRoot \).*$~\1/usr/local/apache2/htdocs/public~' \
    conf/httpd.conf
RUN sed -i \
    -e 's~^\(DocumentRoot \).*$~\1/usr/local/apache2/htdocs/public~' \
    conf/extra/httpd-ssl.conf

# Turn on Apache extension
RUN sed -i \
    -e 's/^#\(LoadModule .*mod_deflate.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_proxy.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_proxy_fcgi.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_rewrite.so\)/\1/' \
    conf/httpd.conf

# SSL configuration
COPY server.crt conf/server.crt
COPY server.key conf/server.key
RUN sed -i \
    -e 's/^#\(Include .*httpd-ssl.conf\)/\1/' \
    -e 's/^#\(LoadModule .*mod_ssl.so\)/\1/' \
    -e 's/^#\(LoadModule .*mod_socache_shmcb.so\)/\1/' \
    conf/httpd.conf
