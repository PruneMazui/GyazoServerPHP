#!/bin/bash

###################################################################
# OS初期設定
###################################################################

# OSベース設定
\cp -f /source/setup/dev/etc/sysconfig/network /etc/sysconfig/network
\cp -f /source/setup/dev/etc/hosts /etc/hosts

hostname gyazo-php-dev

yum update -y
package-cleanup --oldkernels --count=1

\cp -f /usr/share/zoneinfo/Asia/Tokyo /etc/localtime
\cp -f /source/setup/dev/etc/sysconfig/clock /etc/sysconfig/clock
source /etc/sysconfig/clock

###################################################################
# プロビジョニング
###################################################################

# リポジトリ追加
yum install -y yum-plugin-priorities
rpm --import http://ftp.riken.jp/Linux/fedora/epel/RPM-GPG-KEY-EPEL-6
rpm --import http://rpms.famillecollet.com/RPM-GPG-KEY-remi
rpm -ivh http://ftp.riken.jp/Linux/fedora/epel/6/x86_64/epel-release-6-8.noarch.rpm
rpm -ivh http://rpms.famillecollet.com/enterprise/remi-release-6.rpm

\cp /source/setup/dev/etc/yum.repos.d/CentOS-Base.repo /etc/yum.repos.d/CentOS-Base.repo
\cp /source/setup/dev/etc/yum.repos.d/remi.repo /etc/yum.repos.d/remi.repo

# ミドルウェア追加
yum install -y httpd
yum install -y mysql mysql-server
yum install -y wget
yum install -y php php-pear php-mysqlnd php-mbstring php-xml php-gd
yum install -y php-pecl-xdebug

# apacheの設定反映
\cp -f /source/setup/dev/etc/httpd/conf/httpd.conf /etc/httpd/conf/httpd.conf
\cp -f /source/setup/dev/etc/httpd/conf/include.conf /etc/httpd/conf/include.conf
\cp -f /source/setup/dev/etc/httpd/conf.d/php.conf /etc/httpd/conf/php.conf
\cp -f /source/setup/dev/etc/httpd/conf.d/welcome.conf /etc/httpd/conf/welcome.conf
service httpd restart
chkconfig httpd on

# mysqlの設定反映

if [ -e /var/log/mysql ]; then
    echo "/var/log/mysql already exist"
else
    mkdir /var/log/mysql
    chmod 777 /var/log/mysql
fi

\cp -f /source/setup/dev/etc/my.cnf /etc/my.cnf
\cp -f /source/setup/dev/etc/logrotate.d/mysql /etc/logrotate.d/mysql
chkconfig mysqld on
service mysqld restart

mysql -u root -e "GRANT ALL ON *.* TO root@'localhost' WITH GRANT OPTION;"
mysql -u root -e "GRANT ALL ON *.* TO root@'%'         WITH GRANT OPTION;"
mysql -u root -e "FLUSH PRIVILEGES;"

# phpの設定反映
\cp -f /source/setup/dev/etc/php.ini /etc/php.ini
\cp -f /source/setup/dev/etc/php.d/misc.ini /etc/php.d/misc.ini
\cp -f /source/setup/dev/etc/php.d/xdebug.ini /etc/php.d/xdebug.ini

if [ -e /var/log/php ]; then
    echo "/var/log/php already exist"
else
    mkdir /var/log/php
    chmod 777 /var/log/php
    touch /var/log/php/php.log
    chmod 666 /var/log/php/php.log
fi

\cp -f /source/setup/dev/etc/logrotate.d/php /etc/logrotate.d/php
service httpd restart

# アプリケーションキャッシュディレクトリ作成
if [ -e /var/opt/gyazo-php ]; then
    echo "/var/opt/gyazo-php already exist"
else
    mkdir /var/opt/gyazo-php
fi

if [ -e /var/opt/gyazo-php/templates_c ]; then
    echo "/var/opt/gyazo-php/templates_c already exist"
else
    mkdir /var/opt/gyazo-php/templates_c
    chmod 777 /var/opt/gyazo-php/templates_c
fi


###################################################################
# composer更新
###################################################################

cd /source/
if [ -e /source/composer.phar ]; then
    echo "/source/composer.phar already exist"
else
    curl -s http://getcomposer.org/installer | php
fi

if [ -e /source/vendor ]; then
    php composer.phar update --dev
else
    php composer.phar install --dev
fi

###################################################################
# db系
###################################################################

echo setup database
cd /source/sql
/bin/bash initdb.sh
