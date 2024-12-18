#! /bin/sh

for idx in `seq 1 3`; do
    bin/zkServer.sh start conf/zoo${idx}.cfg
done

exit 0