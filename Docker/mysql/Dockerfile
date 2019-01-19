FROM mysql:8.0.3

# Setup the credentials for the DB
ENV MYSQL_ROOT_PASSWORD 'Abcd1234'
ENV MYSQL_DATABASE 'Database'
ENV MYSQL_USER 'Admin'
ENV MYSQL_PASSWORD 'Admin'

COPY CreateTables.sql /docker-entrypoint-initdb.d

# We want persistent database
VOLUME ['/var/lib/mysql']