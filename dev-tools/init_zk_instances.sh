#! /bin/sh

# Clean
for idx in `seq 1 3`; do
    rm conf/zoo${idx}.cfg*
    rm -rf /tmp/zookeeper${idx}
done

# Instance One
ZOOKEEPER_HOME=/tmp/zookeeper1
mkdir -p ${ZOOKEEPER_HOME}
echo "1" >${ZOOKEEPER_HOME}/myid
cp conf/zoo_sample.cfg conf/zoo1.cfg
sed -i 's/\/tmp\/zookeeper/\/tmp\/zookeeper1/g' conf/zoo1.cfg
echo "standaloneEnabled=false" >>conf/zoo1.cfg
echo "reconfigEnabled=true" >>conf/zoo1.cfg
echo "DigestAuthenticationProvider.superDigest=timandes:8dxIww9kuQFupwX/wdccu2gU4w8=" >>conf/zoo1.cfg
echo "server.1=localhost:2888:3888;2181" >>conf/zoo1.cfg

# Instance Two
ZOOKEEPER_HOME=/tmp/zookeeper2
mkdir -p ${ZOOKEEPER_HOME}
echo "2" >${ZOOKEEPER_HOME}/myid
cp conf/zoo1.cfg conf/zoo2.cfg
sed -i 's/=2181/=2182/g' conf/zoo2.cfg
sed -i 's/\/tmp\/zookeeper1/\/tmp\/zookeeper2/g' conf/zoo2.cfg
echo "server.2=localhost:2889:3889;2182" >>conf/zoo2.cfg

# Instance Three
ZOOKEEPER_HOME=/tmp/zookeeper3
mkdir -p ${ZOOKEEPER_HOME}
echo "3" >${ZOOKEEPER_HOME}/myid
cp conf/zoo2.cfg conf/zoo3.cfg
sed -i 's/=2182/=2183/g' conf/zoo3.cfg
sed -i 's/\/tmp\/zookeeper2/\/tmp\/zookeeper3/g' conf/zoo3.cfg
echo "server.3=localhost:2890:3890;2183" >>conf/zoo3.cfg

exit 0
