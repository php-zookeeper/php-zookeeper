/*
  +----------------------------------------------------------------------+
  | Copyright (c) 2010 The PHP Group                                     |
  +----------------------------------------------------------------------+
  | This source file is subject to version 3.01 of the PHP license,      |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.php.net/license/3_01.txt.                                 |
  | If you did not receive a copy of the PHP license and are unable to   |
  | obtain it through the world-wide-web, please send a note to          |
  | license@php.net so we can mail you a copy immediately.               |
  +----------------------------------------------------------------------+
  | Authors: Timandes White <timands@gmail.com>                          |
  +----------------------------------------------------------------------+
*/
#include <php.h>

#include "php5to7.h"
#include "php_zookeeper_stat.h"

void php_stat_to_array(const struct Stat *stat, zval *array)
{
    if( !array ) {
        return;
    }
    if( Z_TYPE_P(array) != IS_ARRAY ) {
#ifdef ZEND_ENGINE_3
        zval_ptr_dtor(array);
#endif
        array_init(array);
    }

    add_assoc_double_ex(array, PHP5TO7_STRS("czxid"), stat->czxid);
    add_assoc_double_ex(array, PHP5TO7_STRS("mzxid"), stat->mzxid);
    add_assoc_double_ex(array, PHP5TO7_STRS("ctime"), stat->ctime);
    add_assoc_double_ex(array, PHP5TO7_STRS("mtime"), stat->mtime);
    add_assoc_long_ex(array, PHP5TO7_STRS("version"), stat->version);
    add_assoc_long_ex(array, PHP5TO7_STRS("cversion"), stat->cversion);
    add_assoc_long_ex(array, PHP5TO7_STRS("aversion"), stat->aversion);
    add_assoc_double_ex(array, PHP5TO7_STRS("ephemeralOwner"), stat->ephemeralOwner);
    add_assoc_long_ex(array, PHP5TO7_STRS("dataLength"), stat->dataLength);
    add_assoc_long_ex(array, PHP5TO7_STRS("numChildren"), stat->numChildren);
    add_assoc_double_ex(array, PHP5TO7_STRS("pzxid"), stat->pzxid);
}