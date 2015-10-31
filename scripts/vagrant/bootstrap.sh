#!/usr/bin/env bash

wget -qO - https://packages.elastic.co/GPG-KEY-elasticsearch | apt-key add -
echo "deb http://packages.elastic.co/elasticsearch/1.7/debian stable main" | tee -a /etc/apt/sources.list.d/elasticsearch-1.7.list

apt-get update

apt-get install -y curl git php5-cli php5-curl php5-pgsql postgresql sqlite3 openjdk-7-jre elasticsearch

# Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Start Elasticsearch
service elasticsearch restart
update-rc.d elasticsearch defaults 95 10
