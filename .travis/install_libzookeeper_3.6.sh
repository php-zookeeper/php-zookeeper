#! /bin/sh

TRAVIS_SCRIPT_DIR=$(cd $(dirname $0); pwd)
LIBZOOKEEPER_VERSION=$1
PACKAGE_NAME=apache-zookeeper-${LIBZOOKEEPER_VERSION}
URL_PREFIX=http://apache.mirrors.lucidnetworks.net/zookeeper
URL_DIR_NAME=zookeeper-${LIBZOOKEEPER_VERSION}
LIBZOOKEEPER_PREFIX=${HOME}/lib${URL_DIR_NAME}
SRC_PKG_NAME=${PACKAGE_NAME}
SRC_PKG_FILE=${SRC_PKG_NAME}.tar.gz
BIN_PKG_NAME=${PACKAGE_NAME}-bin
BIN_PKG_FILE=${BIN_PKG_NAME}.tar.gz

# Download binary package, then init and launch it
wget ${URL_PREFIX}/${URL_DIR_NAME}/${BIN_PKG_FILE} || exit 1
tar xvf ${BIN_PKG_FILE} || exit 1
cd ${BIN_PKG_NAME}
    ${TRAVIS_SCRIPT_DIR}/init_zk_instances.sh || exit 1
    ${TRAVIS_SCRIPT_DIR}/launch_zk_instances.sh || exit 1
cd ..

# Download source package, then build it
wget ${URL_PREFIX}/zookeeper-3.5.9/apache-zookeeper-3.5.9.tar.gz \
    && tar xvf apache-zookeeper-3.5.9.tar.gz \
    && wget ${URL_PREFIX}/${URL_DIR_NAME}/${SRC_PKG_FILE}\
    && tar xvf ${SRC_PKG_FILE}\
    && cd apache-zookeeper-3.5.9 \
        && ant compile_jute \
        && cd zookeeper-client/zookeeper-client-c \
            && autoreconf -if \
            && ./configure --prefix=/${HOME}/libzookeeper-3.5.9 \
            && make && make install \
        && cd ../.. \
        && cp ivy* build.xml ../${SRC_PKG_NAME} \
    && cd .. \
    && rm -rf apache-zookeeper-3.5.9* \
    && cd ${SRC_PKG_NAME} \
        && ant compile_jute \
        && cd zookeeper-client/zookeeper-client-c \
            && autoreconf -if \
            && ./configure --prefix=${LIBZOOKEEPER_PREFIX} \
            && make && make install \
        && cd ../.. \
    && cd .. \
    && rm -rf ${SRC_PKG_NAME}*

exit 0
