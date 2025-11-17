FROM alpine

RUN apk add --no-cache \
    bash \
    gawk \
    coreutils # uniq, sort \
# coreutils - uniq, sort
# gawk - awk

WORKDIR /app
VOLUME /app
CMD ["/bin/bash"]