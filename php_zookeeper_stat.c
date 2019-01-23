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

    add_assoc_double_ex(array, ZEND_STRL("czxid"), stat->czxid);
    add_assoc_double_ex(array, ZEND_STRL("mzxid"), stat->mzxid);
    add_assoc_double_ex(array, ZEND_STRL("ctime"), stat->ctime);
    add_assoc_double_ex(array, ZEND_STRL("mtime"), stat->mtime);
    add_assoc_long_ex(array, ZEND_STRL("version"), stat->version);
    add_assoc_long_ex(array, ZEND_STRL("cversion"), stat->cversion);
    add_assoc_long_ex(array, ZEND_STRL("aversion"), stat->aversion);
    add_assoc_double_ex(array, ZEND_STRL("ephemeralOwner"), stat->ephemeralOwner);
    add_assoc_long_ex(array, ZEND_STRL("dataLength"), stat->dataLength);
    add_assoc_long_ex(array, ZEND_STRL("numChildren"), stat->numChildren);
    add_assoc_double_ex(array, ZEND_STRL("pzxid"), stat->pzxid);
}
