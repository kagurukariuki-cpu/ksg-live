FROM php:8.2-cli

WORKDIR /app
COPY . .

ENV PHP_CLI_SERVER_WORKERS=10

EXPOSE 10000

CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-10000} -t ."]