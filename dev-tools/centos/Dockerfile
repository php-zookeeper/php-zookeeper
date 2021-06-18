FROM centos:7

RUN yum install -y https://mirrors.tuna.tsinghua.edu.cn/remi/enterprise/remi-release-7.rpm \
	&& yum install -y php74-php-cli php74-php-devel

RUN yum install -y vim wget ant
RUN yum groupinstall -y "Development Tools"

RUN yum install -y cppunit-devel

RUN wget https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.5.9/apache-zookeeper-3.5.9.tar.gz \
	&& tar xvf apache-zookeeper-3.5.9.tar.gz \
	&& wget https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.6.2/apache-zookeeper-3.6.2.tar.gz \
	&& tar xvf apache-zookeeper-3.6.2.tar.gz \
	&& cd apache-zookeeper-3.5.9 \
		&& cp ivy* build.xml ../apache-zookeeper-3.6.2 \
	&& cd .. \
	&& rm -rf apache-zookeeper-3.5.9* \
	&& cd ../apache-zookeeper-3.6.2 \
		&& ant compile_jute \
		&& cd zookeeper-client/zookeeper-client-c \
			&& autoreconf -if \
			&& ./configure --prefix=/opt/libzookeeper-3.6.2 \
			&& make && make install \
		&& cd ../.. \
	&& cd .. \
	&& rm -rf apache-zookeeper-3.6.2*

RUN wget https://mirrors.tuna.tsinghua.edu.cn/apache/zookeeper/zookeeper-3.6.2/apache-zookeeper-3.6.2-bin.tar.gz \
	&& tar xvf apache-zookeeper-3.6.2-bin.tar.gz \
	&& mv apache-zookeeper-3.6.2-bin /opt/ \
	&& rm -rf apache-zookeeper-3.6.2-bin.tar.gz
	
RUN yum install -y php80-php-cli php80-php-devel

RUN wget https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.5.9/apache-zookeeper-3.5.9.tar.gz \
	&& tar xvf apache-zookeeper-3.5.9.tar.gz \
	&& wget https://mirrors.bfsu.edu.cn/apache/zookeeper/zookeeper-3.6.2/apache-zookeeper-3.6.2.tar.gz \
	&& tar xvf apache-zookeeper-3.6.2.tar.gz \
	&& cd apache-zookeeper-3.5.9 \
		&& ant compile_jute \
		&& cd zookeeper-client/zookeeper-client-c \
			&& autoreconf -if \
			&& ./configure --prefix=/opt/libzookeeper-3.5.9 \
			&& make && make install \
		&& cd ../.. \
		&& cp ivy* build.xml ../apache-zookeeper-3.6.2 \
	&& cd .. \
	&& rm -rf apache-zookeeper-3.5.9* \
	&& cd ../apache-zookeeper-3.6.2 \
		&& ant compile_jute \
		&& cd zookeeper-client/zookeeper-client-c \
			&& autoreconf -if \
			&& ./configure --prefix=/opt/libzookeeper-3.6.2 \
			&& make && make install \
		&& cd ../.. \
	&& cd .. \
	&& rm -rf apache-zookeeper-3.6.2*


