#!/bin/sh -e
__ERR__="coverage: ERROR:"

__CURRENT__=$(cd "$(dirname "$0")";pwd)
__DIR__=$(cd "$(dirname "${__CURRENT__}")";pwd)

# Check that required commands is installed before doing anything
command -v lcov >/dev/null 2>&1 || { >&2 echo "${__ERR__} lcov is not installed!"; return 255; }

lcov --directory . --zerocounters
lcov --directory . --capture --initial --output-file "${__DIR__}/coverage.info"
sh "${__CURRENT__}/run-tests.sh"
lcov --no-checksum --directory . --capture --output-file "${__DIR__}/coverage.info"
