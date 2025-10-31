FROM rabbitmq:3.13.7-management-alpine
LABEL authors="Artem"

# Установка curl в Alpine
RUN apk add --no-cache curl

EXPOSE 5672 15672

HEALTHCHECK --interval=30s --timeout=10s --start-period=60s --retries=3 \
    CMD curl -f http://localhost:15672 || exit 1