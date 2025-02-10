FROM ubuntu:latest

RUN apt-get update && apt-get install -y \
    sudo \
    nano

COPY ./bash_scripts /home

WORKDIR /home/bash_scripts