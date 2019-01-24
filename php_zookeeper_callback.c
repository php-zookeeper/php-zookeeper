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
  | Authors: Andrei Zmievski <andrei@php.net>                            |
  |          Timandes White <timands@gmail.com>                          |
  +----------------------------------------------------------------------+
*/

#include <php.h>

#include "php_zookeeper_callback.h"

php_cb_data_t* php_cb_data_new(HashTable *ht, zend_fcall_info *fci, zend_fcall_info_cache *fcc, zend_bool oneshot TSRMLS_DC)
{
    php_cb_data_t *cbd = ecalloc(1, sizeof(php_cb_data_t));
    cbd->fci = *fci;
    cbd->fcc = *fcc;
    cbd->oneshot = oneshot;
    cbd->h = ht->nNextFreeElement;
    Z_TRY_ADDREF(cbd->fci.function_name);
    zend_hash_next_index_insert_mem(ht, (void*)&cbd, sizeof(php_cb_data_t *));
    cbd->ht = ht;
#if ZTS
    TSRMLS_SET_CTX(cbd->ctx);
#endif
    return cbd;
}

void php_cb_data_destroy(php_cb_data_t **entry)
{
    php_cb_data_t *cbd = *(php_cb_data_t **)entry;
    if (cbd) {
        Z_TRY_DELREF(cbd->fci.function_name);
        efree(cbd);
    }
}
