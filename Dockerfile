FROM alexcheng/magento

MAINTAINER Alistair Stead <alistair_stead@me.com>

COPY ./bin/modman /usr/local/bin/modman
COPY ./bin/link-module /usr/local/bin/link-module
