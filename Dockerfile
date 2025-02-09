FROM ubuntu:latest

RUN apt-get update && apt-get install -y \
    bc \
    gawk \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /scripts

COPY ./scripts /scripts
COPY ./cities.txt /data/cities.txt

ENTRYPOINT ["/bin/bash"]