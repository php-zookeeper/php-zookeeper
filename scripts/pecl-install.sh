#!/bin/sh -e
__ERR__="pecl-install: ERROR:"

# Check that required commands are installed before doing anything
command -v getopt >/dev/null 2>&1 || { >&2 echo "${__ERR__} getopt is not installed!"; return 255; }
command -v make >/dev/null 2>&1 || { >&2 echo "${__ERR__} make is not installed!"; return 255; }
command -v phpize >/dev/null 2>&1 || { >&2 echo "${__ERR__} phpize is not installed!"; return 255; }
command -v php >/dev/null 2>&1 || { >&2 echo "${__ERR__} php is not installed!"; return 255; }

ARGS=$(getopt -o cl: --long with-coverage,with-libzookeeper-dir: -n 'pecl-install.sh' -- "$@")

eval set -- "$ARGS"

COVERAGE=false
LIBZOOKEEPER_DIR=""

while true; do
  case "$1" in
    -c | --with-coverage) COVERAGE=true; shift ;;
    -l | --with-libzookeeper-dir) LIBZOOKEEPER_DIR="${2}"; shift;;
    * ) break;;
  esac
done

if ${COVERAGE}; then
  export CFLAGS="--coverage -fprofile-arcs -ftest-coverage"
  export LDFLAGS="--coverage"
fi

phpize

if [ ! -z ${LIBZOOKEEPER_DIR} ]; then
  ./configure --with-libzookeeper-dir=${LIBZOOKEEPER_DIR}
else
  ./configure
fi

make
make install

echo "extension=zookeeper.so" >> $(php -i | grep /.+/php.ini -oE)
php --ri zookeeper
