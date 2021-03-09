#! /bin/sh

LIBZOOKEEPER_VERSION=$1
LIBZOOKEEPER_MAJOR_VERSION=`echo ${LIBZOOKEEPER_VERSION} | awk -F'.' '{print $1}'`
LIBZOOKEEPER_MINOR_VERSION=`echo ${LIBZOOKEEPER_VERSION} | awk -F'.' '{print $2}'`
LIBZOOKEEPER_PATCH_VERSION=`echo ${LIBZOOKEEPER_VERSION} | awk -F'.' '{print $3}'`

if [ ${LIBZOOKEEPER_MAJOR_VERSION} -ge 3 -a ${LIBZOOKEEPER_MINOR_VERSION} -ge 5 -a ${LIBZOOKEEPER_PATCH_VERSION} -ge 9 ]; then
    PACKAGE_NAME=apache-zookeeper-${LIBZOOKEEPER_VERSION}
    URL_PREFIX=http://apache.mirrors.lucidnetworks.net/zookeeper
else
    PACKAGE_NAME=zookeeper-${LIBZOOKEEPER_VERSION}
    URL_PREFIX=http://archive.apache.org/dist/zookeeper
fi
URL_DIR_NAME=zookeeper-${LIBZOOKEEPER_VERSION}
LIBZOOKEEPER_PREFIX=${HOME}/lib${URL_DIR_NAME}

TRAVIS_SCRIPT_DIR=$(cd $(dirname $0); pwd)

wget ${URL_PREFIX}/${URL_DIR_NAME}/${PACKAGE_NAME}.tar.gz || exit 1
tar xvf ${PACKAGE_NAME}.tar.gz || exit 1

if [ ${LIBZOOKEEPER_MAJOR_VERSION} -ge 3 -a ${LIBZOOKEEPER_MINOR_VERSION} -ge 5 ]; then
	if [ ${LIBZOOKEEPER_PATCH_VERSION} -ge 5 ]; then
		wget ${URL_PREFIX}/${URL_DIR_NAME}/${PACKAGE_NAME}-bin.tar.gz || exit 1
		tar xvf ${PACKAGE_NAME}-bin.tar.gz || exit 1
		cd ${PACKAGE_NAME}-bin
	else
        cd ${PACKAGE_NAME}
	fi
        ${TRAVIS_SCRIPT_DIR}/init_zk_instances.sh || exit 1
        ${TRAVIS_SCRIPT_DIR}/launch_zk_instances.sh || exit 1
    cd ..
else
    mv ${PACKAGE_NAME}/conf/zoo_sample.cfg ${PACKAGE_NAME}/conf/zoo.cfg
    ${PACKAGE_NAME}/bin/zkServer.sh start
fi

if [ ${LIBZOOKEEPER_MAJOR_VERSION} -ge 3 -a ${LIBZOOKEEPER_MINOR_VERSION} -ge 5 -a ${LIBZOOKEEPER_PATCH_VERSION} -ge 9 ]; then
    cd ${PACKAGE_NAME}
    ant compile_jute
    cd zookeeper-client/zookeeper-client-c
    autoreconf -if
else
    cd ${PACKAGE_NAME}/zookeeper-client/zookeeper-client-c
fi
    ./configure --prefix=${LIBZOOKEEPER_PREFIX} || exit 1
    make || exit 1
    make install || exit 1
cd ../../..

exit 0
