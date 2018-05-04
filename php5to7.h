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
  | Authors: John Boehr <jbboehr@gmail.com>                              |
  +----------------------------------------------------------------------+
*/

#include <php.h>

#ifdef ZEND_ENGINE_3
typedef size_t strsize_t;
#define php5to7_add_assoc_string_ex add_assoc_string_ex
#define php5to7_add_next_index_string add_next_index_string
#define php5to7_register_internal_class_ex(class, parent) zend_register_internal_class_ex(class, parent)
#define PHP5TO7_ZVAL_STRING(z, s) ZVAL_STRING(z, s)
#define PHP5TO7_RETVAL_STRING(s) RETVAL_STRING(s)
#define PHP5TO7_RETVAL_STRINGL(s, l) RETVAL_STRINGL(s, l)
#define PHP5TO7_STRS(s) ZEND_STRL(s)

#define Z_ZK_P(zv) php_zk_fetch_object(Z_OBJ_P((zv)))
#else
typedef int strsize_t;
#define php5to7_add_assoc_string_ex(...) add_assoc_string_ex(__VA_ARGS__, 1)
#define php5to7_add_next_index_string(...) add_next_index_string(__VA_ARGS__, 1)
#define php5to7_register_internal_class_ex(class, parent) zend_register_internal_class_ex(class, parent, NULL TSRMLS_CC)
#define PHP5TO7_ZVAL_STRING(z, s) ZVAL_STRING(z, s, 1)
#define PHP5TO7_RETVAL_STRING(s) RETVAL_STRING(s, 1)
#define PHP5TO7_RETVAL_STRINGL(s, l) RETVAL_STRINGL(s, l, 1)
#define PHP5TO7_STRS(s) ZEND_STRS(s)

#define Z_ZK_P(zv) zend_object_store_get_object(zv TSRMLS_CC)
#endif
