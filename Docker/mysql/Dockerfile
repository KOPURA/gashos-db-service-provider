FROM mysql:8.0.13

# Setup the credentials for the DB
ENV MYSQL_ROOT_PASSWORD 'Abcd1234'
ENV MYSQL_DATABASE 'Database'
ENV MYSQL_USER 'Admin'
ENV MYSQL_PASSWORD 'Admin'

# We want persistent database
VOLUME ['/var/lib/mysql']