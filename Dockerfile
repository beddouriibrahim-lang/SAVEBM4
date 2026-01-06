FROM php:8.1-apache

RUN apt-get update && apt-get install -y \
    ffmpeg \
    python3 \
    python3-pip

RUN pip3 install yt-dlp

COPY . /var/www/html/

RUN chmod -R 755 /var/www/html

EXPOSE 80
