#! /bin/sh

LIBZOOKEEPER_VERSION=$1
LIBZOOKEEPER_MAJOR_VERSION=`echo ${LIBZOOKEEPER_VERSION} | awk -F'.' '{print $1}'`
LIBZOOKEEPER_MINOR_VERSION=`echo ${LIBZOOKEEPER_VERSION} | awk -F'.' '{print $2}'`

PACKAGE_NAME=zookeeper-${LIBZOOKEEPER_VERSION}
LIBZOOKEEPER_PREFIX=${HOME}/lib${PACKAGE_NAME}

TRAVIS_SCRIPT_DIR=$(cd $(dirname $0); pwd)

wget http://apache.mirrors.lucidnetworks.net/zookeeper/${PACKAGE_NAME}/${PACKAGE_NAME}.tar.gz || exit 1
tar xvf ${PACKAGE_NAME}.tar.gz || exit 1

if [ ${LIBZOOKEEPER_MAJOR_VERSION} -ge 3 -a ${LIBZOOKEEPER_MINOR_VERSION} -ge 5 ]; then
    ${TRAVIS_SCRIPT_DIR}/init_zk_instances.sh || exit 1
    ${TRAVIS_SCRIPT_DIR}/launch_zk_instances.sh || exit 1
else
    mv ${PACKAGE_NAME}/conf/zoo_sample.cfg ${PACKAGE_NAME}/conf/zoo.cfg
    ${PACKAGE_NAME}/bin/zkServer.sh start
fi

cd ${PACKAGE_NAME}/src/c
    ./configure --prefix=${LIBZOOKEEPER_PREFIX} || exit 1
    make || exit 1
    make install || exit 1
cd ../../..

exit 0
