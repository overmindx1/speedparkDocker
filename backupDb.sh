#!/bin/sh
docker exec speedParkMariaDb  mysqldump ebay_solution -uroot --password=34erDFcv | gzip -c > /root/docker/databaseBackup/ebay_solution_`date +%Y-%m-%d`.gz 