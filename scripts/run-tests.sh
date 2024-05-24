#!/bin/sh -e
__CURRENT__=$(cd "$(dirname "$0")";pwd)
__DIR__=$(cd "$(dirname "${__CURRENT__}")";pwd)

if [ -z "${TEST_PHP_EXECUTABLE}" ]; then
  export TEST_PHP_EXECUTABLE=`which php`
fi

if [ -z "${REPORT_EXIT_STATUS}" ]; then
  export REPORT_EXIT_STATUS=1
fi

${TEST_PHP_EXECUTABLE} \
  -n \
  -d "open_basedir=" \
  -d "output_buffering=0" \
  -d "memory_limit=1024m" \
  ${__DIR__}/run-tests.php ${__DIR__}/tests \
    -n \
    -d "extension_dir=modules" \
    -d "extension=zookeeper.so" \
    .

if [ $? -ne 0 ]; then
  echo
  echo "====================================================================="
  echo "Dumping diff files ..." >&2
  for f in `find . -name *.diff`; do
    echo "---------------------------------------------------------------------"
    echo "Dumping file ${f} ..."
    cat ${f}
    echo
  done
  echo "====================================================================="
  exit 1
fi
