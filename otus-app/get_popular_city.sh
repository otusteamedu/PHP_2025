#!/bin/bash

if [ $# -ne 1 ]
then
  echo "invalid argument count"
  exit -1
fi

if [ ! -f $1 ]
then
  echo "invalid file"
  exit -1
fi

awk 'NR > 1 {print $3}' $1 | sort | uniq -c | sort -nr | head -3 | awk '{print $2}'