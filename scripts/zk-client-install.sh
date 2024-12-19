#!/bin/sh -e
__ERR__="zk-client-install: ERROR:"

# Check that there are at least two arguments
if [ $# -gt 2 ]; then
  >&2 echo "${__ERR__} usage: zk-client-install {version} {prefix: optional}"
  exit 255
elif [ $# -lt 1 ]; then
  >&2 echo "${__ERR__} usage: zk-client-install {version} {prefix: optional}"
  exit 255
fi

__VERSION__="${1}"
__PREFIX__=""

if [ ! -z ${2} ]; then
  __PREFIX__="--prefix=${2}"
fi

# Check that required commands are installed before doing anything
command -v autoreconf >/dev/null 2>&1 || { >&2 echo "${__ERR__} autoreconf is not installed!"; return 255; }
command -v make >/dev/null 2>&1 || { >&2 echo "${__ERR__} make is not installed!"; return 255; }
command -v wget >/dev/null 2>&1 || { >&2 echo "${__ERR__} wget is not installed!"; return 255; }

wget -qqO - "https://www.apache.org/dyn/mirrors/mirrors.cgi?action=download&filename=zookeeper/zookeeper-${__VERSION__}/apache-zookeeper-${__VERSION__}.tar.gz" \
  | tar -xzf - --directory /tmp

(cd /tmp/apache-zookeeper-${__VERSION__}; mvn -pl zookeeper-jute compile)

(cd /tmp/apache-zookeeper-${__VERSION__}/zookeeper-client/zookeeper-client-c; \
  autoreconf -if; \
  ./configure ${__PREFIX__}; \
  make; \
  make install;)
