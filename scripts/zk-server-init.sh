#!/bin/sh -e
__ERR__="zk-server-init: ERROR:"
#
# Check that required command is installed before doing anything
test -f /usr/local/zookeeper/bin/zkServer.sh >/dev/null 2>&1 || { >&2 echo "${__ERR__} zookeeper is not installed!"; return 255; }

# start instances
for i in $(ls /etc/zk/conf/node*.cfg); do
    /usr/local/zookeeper/bin/zkServer.sh start ${i}
done

exit 0
