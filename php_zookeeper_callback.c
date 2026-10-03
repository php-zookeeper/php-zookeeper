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

php_cb_data_t* php_cb_data_new(HashTable *ht, zend_fcall_info *fci, zend_fcall_info_cache *fcc, zend_bool oneshot)
{
    php_cb_data_t *cbd = ecalloc(1, sizeof(php_cb_data_t));
    zend_long h = ht->nNextFreeElement;
    cbd->fci = *fci;
    cbd->fcc = *fcc;
    cbd->oneshot = oneshot;
    /* PHP 8 uses this sentinel until the first numeric key is inserted. */
    if (h == ZEND_LONG_MIN) {
        h = 0;
    }
    Z_TRY_ADDREF(cbd->fci.function_name);
    if (!zend_hash_index_add_ptr(ht, (zend_ulong)h, cbd)) {
        php_cb_data_destroy(cbd);
        return NULL;
    }
    cbd->h = h;
    cbd->ht = ht;
    ZEND_ASSERT(zend_hash_index_find_ptr(ht, (zend_ulong)cbd->h) == cbd);
#ifdef ZTS
	// Save pointer of globals' struct
	cbd->ctx = ZK_G_P();
#if PHP_VERSION_ID >= 70100
	cbd->vm_interrupt = &EG(vm_interrupt);
#endif
#endif
    return cbd;
}

void php_cb_data_destroy(php_cb_data_t *cbd)
{
    if (cbd) {
        Z_TRY_DELREF(cbd->fci.function_name);
        efree(cbd);
    }
}

void php_cb_data_remove(php_cb_data_t *cb_data)
{
	if (cb_data && cb_data->ht) {
		zend_hash_index_del(cb_data->ht, cb_data->h);
		return;
	}
	php_cb_data_destroy(cb_data);
}
