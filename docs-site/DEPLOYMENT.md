# 🚀 YHM Production Deployment Guide

دليل نشر YHM على سيرفر VPS حقيقي.

## 1. متطلبات السيرفر

- **VPS**: Ubuntu 22.04 LTS
- **RAM**: 2 GB (الحد الأدنى)
- **CPU**: 1 vCPU
- **Disk**: 20 GB SSD
- **Domain**: (اختياري للـ HTTPS)

**مزودو الخدمة الموصى بهم:**
- DigitalOcean ($6/شهر)
- Linode ($5/شهر)
- Hetzner (€4/شهر)
- Oracle Cloud (مجاني)

## 2. تثبيت الحزم الأساسية

```bash
# تحديث النظام
sudo apt update && sudo apt upgrade -y

# تثبيت PHP 8.3
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mysql \
    php8.3-mbstring php8.3-xml php8.3-curl php8.3-zip php8.3-intl \
    php8.3-bcmath php8.3-redis

# تثبيت MySQL 8
sudo apt install -y mysql-server

# تثبيت Nginx
sudo apt install -y nginx

# تثبيت Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
