#!/usr/bin/env bash
# Run phpt tests
#

set -u

BASENAME=${0}
TOOLS_DIR=$(dirname ${BASENAME})
WORK_DIR="${TOOLS_DIR}/.."

cd "${WORK_DIR}"

TEST=${1:-tests/}

USE_PHP=${TEST_PHP_EXECUTABLE:-$(which php)}

TEST_PHP_EXECUTABLE=${USE_PHP} \
REPORT_EXIT_STATUS=1 \
${USE_PHP} \
  -n -d open_basedir= -d output_buffering=0 -d memory_limit=-1 \
  run-tests.php -n -d extension_dir=modules -d extension=zookeeper.so ${TEST}

if [ $? -ne 0 ]; then
    echo
    echo "====================================================================="
    echo "Dumping diff files ..." >&2
    for f in `find . -name *.diff`; do
        ${TOOLS_DIR}/dump.sh $f
    done
    echo "====================================================================="
    exit 1
fi
