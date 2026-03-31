#!/bin/bash

if [[ $# -ne 2 ]];
then
    echo 'error: required 2 arguments';
    exit 1
fi

if [[ -z $1 || !("$1" =~ ^-?[0-9]+([.][0-9]+)?$) ]];
then
    echo 'invalid first argument'
    exit 1
fi

if [[ -z $2 || !("$1" =~ ^-?[0-9]+([.][0-9]+)?$) ]];
then
    echo 'invalid second argument'
    exit 1
fi

echo "$1 $2" | awk '{print $1 + $2}'

