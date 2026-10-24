#!/usr/bin/env bash
# Build test-only inspection helpers; no test hooks enter the production module.
set -euo pipefail
root=$(cd "$(dirname "$0")/.." && pwd)
test_build=$(mktemp -d)
trap 'rm -rf "$test_build"' EXIT
php_bin=${TEST_PHP_EXECUTABLE:-php}
php_config=${PHP_CONFIG:-php-config}
zk_include=${ZK_INCLUDE_DIR:-${LIBZOOKEEPER_DIR:-/usr}/include/zookeeper}
read -r -a includes <<< "$("$php_config" --includes)"
read -r -a compiler <<< "${CC:-cc}"
link_flags=(-shared -fPIC)
if [[ $(uname -s) == Darwin ]]; then
    link_flags=(-bundle -undefined dynamic_lookup)
fi
"${compiler[@]}" "${link_flags[@]}" -g -O0 "${includes[@]}" -I"$root" -I"$zk_include" \
    "$root/dev-tools/test-callback-registry.c" -o "$test_build/callback_registry_test.so"
"$php_bin" -n -d "extension=$root/modules/zookeeper.so" \
    -d "extension=$test_build/callback_registry_test.so" "$root/dev-tools/test-callback-registry.php"
