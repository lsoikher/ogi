#!/bin/sh

cd /home/lidorsoikher012/ogi/wp-admin/config/test/
python scan.py -rf range.txt 80,8080,8090,9090,8081,8082,8083,8180,8181,8182,8183,8280,8281,8282,8283,8380,8381,8382,8383 500 10

cd /home/lidorsoikher012/ogi/wp-admin/config/test/
python check.py result.txt 200