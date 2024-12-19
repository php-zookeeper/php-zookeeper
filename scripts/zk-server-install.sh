#!/bin/sh -e
__ERR__="zk-server-install: ERROR:"

__VERSION__="${1}"

# Check if there is only one argument
if [ $# -ne 1 ]; then
  >&2 echo "${__ERR__} usage: zk-server-install {version}"
  exit 255
fi

mkdir -p /usr/local/zookeeper

# Check that required command is installed before doing anything
command -v wget >/dev/null 2>&1 || { >&2 echo "${__ERR__} wget is not installed!"; return 255; }

wget -qqO - "https://www.apache.org/dyn/mirrors/mirrors.cgi?action=download&filename=zookeeper/zookeeper-${__VERSION__}/apache-zookeeper-${__VERSION__}-bin.tar.gz" \
  | tar -xzf - --directory /usr/local/zookeeper --strip-components=1

__NODE__="/var/zk/node"
__CONF__="/etc/zk/conf"
mkdir -p ${__CONF__}

# setup instances
for i in `seq 1 3`; do
  mkdir -p "${__NODE__}-${i}"
  echo "${i}" > "${__NODE__}-${i}/myid"
  cp /usr/local/zookeeper/conf/zoo_sample.cfg "${__CONF__}/node-${i}.cfg"
  sed -i "s|/tmp/zookeeper|${__NODE__}-${i}|g" "${__CONF__}/node-${i}.cfg"
  sed -i "s/=2181/=218${i}/g" "${__CONF__}/node-${i}.cfg"
  echo "standaloneEnabled=false" >> "${__CONF__}/node-${i}.cfg"
  echo "reconfigEnabled=true" >> "${__CONF__}/node-${i}.cfg"
  echo "DigestAuthenticationProvider.superDigest=php:8XMoy1dQwpDw4hrbBxH6R3JdEeI=" >> "${__CONF__}/node-${i}.cfg"
  echo "server.1=localhost:2888:3888;2181" >> "${__CONF__}/node-${i}.cfg"
done

echo "server.2=localhost:2889:3889;2182" >> "${__CONF__}/node-2.cfg"
echo "server.2=localhost:2889:3889;2182\nserver.3=localhost:2890:3890;2183" >> "${__CONF__}/node-3.cfg"
