FROM ubuntu:18.04

ARG DEBIAN_FRONTEND=noninteractive
ENV TZ=Asia/Chongqing
RUN apt update \
	&& apt -y install software-properties-common \
	&& LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php \
	&& apt update \
	&& apt -y install php7.4-cli php7.4-dev \
	&& apt -y install php8.0-cli php8.0-dev

RUN apt -y install vim wget ant libcppunit-dev

RUN wget https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.5.9/apache-zookeeper-3.5.9.tar.gz \
	&& tar xvf apache-zookeeper-3.5.9.tar.gz \
	&& wget https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.6.3/apache-zookeeper-3.6.3.tar.gz \
	&& tar xvf apache-zookeeper-3.6.3.tar.gz \
	&& cd apache-zookeeper-3.5.9 \
		&& cp ivy* build.xml ../apache-zookeeper-3.6.3 \
	&& cd .. \
	&& rm -rf apache-zookeeper-3.5.9* \
	&& cd ../apache-zookeeper-3.6.3 \
		&& ant compile_jute \
		&& cd zookeeper-client/zookeeper-client-c \
			&& autoreconf -if \
			&& ./configure --prefix=/opt/libzookeeper-3.6.3 \
			&& make && make install \
		&& cd ../.. \
	&& cd .. \
	&& rm -rf apache-zookeeper-3.6.3*

RUN wget https://mirrors.tuna.tsinghua.edu.cn/apache/zookeeper/zookeeper-3.6.3/apache-zookeeper-3.6.3-bin.tar.gz \
	&& tar xvf apache-zookeeper-3.6.3-bin.tar.gz \
	&& mv apache-zookeeper-3.6.3-bin /opt/ \
	&& rm -rf apache-zookeeper-3.6.3-bin.tar.gz

RUN wget https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.5.9/apache-zookeeper-3.5.9.tar.gz \
	&& tar xvf apache-zookeeper-3.5.9.tar.gz \
	&& wget https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.6.3/apache-zookeeper-3.6.3.tar.gz \
	&& tar xvf apache-zookeeper-3.6.3.tar.gz \
	&& cd apache-zookeeper-3.5.9 \
		&& ant compile_jute \
		&& cd zookeeper-client/zookeeper-client-c \
			&& autoreconf -if \
			&& ./configure --prefix=/opt/libzookeeper-3.5.9 \
			&& make && make install \
		&& cd ../.. \
		&& cp ivy* build.xml ../apache-zookeeper-3.6.3 \
	&& cd .. \
	&& rm -rf apache-zookeeper-3.5.9* \
	&& cd ../apache-zookeeper-3.6.3 \
		&& ant compile_jute \
		&& cd zookeeper-client/zookeeper-client-c \
			&& autoreconf -if \
			&& ./configure --prefix=/opt/libzookeeper-3.6.3 \
			&& make && make install \
		&& cd ../.. \
	&& cd .. \
	&& rm -rf apache-zookeeper-3.6.3*

# PHP dependencies
RUN apt update \
	&& apt -y install libc-client-dev libxml2-dev libkrb5-dev \
		libsqlite3-dev libz-dev libzip-dev libbz2-dev libcurl4-openssl-dev \
		libgmp-dev libldap2-dev libsasl2-dev libonig-dev libpq-dev \
		libreadline-dev libtidy-dev libxslt-dev

# Before compiling
RUN wget https://www.php.net/distributions/php-8.0.7.tar.bz2
COPY default_configure_options.erb default_configure_options.erb

# PHP 8.0 ZTS(Debug)
RUN tar xvf php-8.0.7.tar.bz2 \
	&& cd php-8.0.7 \
		&& ./configure --prefix=/opt/php-8.0-zts-debug \
			--enable-zts --enable-debug \
			`cat default_configure_options.erb | xargs echo -n` \
		&& make && make install \
	&& cd .. \
	&& rm -rf php-8.0.7

# PHP 8.0 ZTS
RUN tar xvf php-8.0.7.tar.bz2 \
	&& cd php-8.0.7 \
		&& ./configure --prefix=/opt/php-8.0-zts \
			--enable-zts \
			`cat default_configure_options.erb | xargs echo -n` \
		&& make && make install \
	&& cd .. \
	&& rm -rf php-8.0.7

RUN apt update \
	&& apt -y install gdb

# PHP 7.0 ZTS
RUN wget https://www.php.net/distributions/php-7.0.33.tar.bz2 \
	&& tar xvf php-7.0.33.tar.bz2 \
	&& cd php-7.0.33 \
		&& ./configure --prefix=/opt/php-7.0-zts \
			--enable-maintainer-zts \
			`cat default_configure_options.erb | xargs echo -n` \
		&& make && make install \
	&& cd .. \
	&& rm -rf php-7.0.33*


