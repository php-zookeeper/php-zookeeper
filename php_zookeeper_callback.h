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

#ifndef PHP_ZOOKEEPER_CALLBACK
#define PHP_ZOOKEEPER_CALLBACK

#include <zookeeper.h>
#include <php.h>
#include <php_zookeeper.h>

typedef struct _php_cb_data_t {
    zend_fcall_info fci;
    zend_fcall_info_cache fcc;
    zend_bool oneshot;
    zend_long h;
    HashTable *ht;
#ifdef ZTS
	zend_zookeeper_globals *ctx;
#if PHP_VERSION_ID >= 70100
	zend_bool *vm_interrupt;
#endif
#endif
} php_cb_data_t;

php_cb_data_t* php_cb_data_new(HashTable *ht, zend_fcall_info *fci, zend_fcall_info_cache *fcc, zend_bool oneshot);
void php_cb_data_destroy(php_cb_data_t *cbd);
void php_cb_data_remove(php_cb_data_t *cb_data);

#endif  /* PHP_ZOOKEEPER_CALLBACK */
