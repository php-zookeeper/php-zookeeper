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

#if defined(__GNUC__) || defined(__clang__)
# define PHP_ZK_REF_ADD(ref) __atomic_add_fetch(&(ref), 1, __ATOMIC_ACQ_REL)
# define PHP_ZK_REF_SUB(ref) __atomic_sub_fetch(&(ref), 1, __ATOMIC_ACQ_REL)
#else
# define PHP_ZK_REF_ADD(ref) (++(ref))
# define PHP_ZK_REF_SUB(ref) (--(ref))
#endif

php_cb_data_t* php_cb_data_new(HashTable *ht, zend_fcall_info *fci, zend_fcall_info_cache *fcc, zend_bool oneshot)
{
    php_cb_data_t *cbd = ecalloc(1, sizeof(php_cb_data_t));
    cbd->fci = *fci;
    cbd->fcc = *fcc;
    cbd->oneshot = oneshot;
    cbd->refcount = 1; /* the callbacks table owns one reference */
    /* PHP 8 initializes the next index with a sentinel; append uses key 0. */
    cbd->h = ht->nNextFreeElement == ZEND_LONG_MIN ? 0 : ht->nNextFreeElement;
    if (!zend_hash_index_add_ptr(ht, cbd->h, cbd)) {
        efree(cbd);
        zend_throw_error(NULL, "Could not register ZooKeeper callback");
        return NULL;
    }
    cbd->ht = ht;
    Z_TRY_ADDREF(cbd->fci.function_name);
#ifdef ZTS
	// Save pointer of globals' struct
	cbd->ctx = ZK_G_P();
#if PHP_VERSION_ID >= 70100
	cbd->vm_interrupt = &EG(vm_interrupt);
#endif
#endif
    return cbd;
}

static void php_cb_data_free(php_cb_data_t *cbd)
{
    Z_TRY_DELREF(cbd->fci.function_name);
    efree(cbd);
}

php_cb_data_t* php_cb_data_addref(php_cb_data_t *cbd)
{
    if (cbd) {
        PHP_ZK_REF_ADD(cbd->refcount);
    }
    return cbd;
}

void php_cb_data_release(php_cb_data_t *cbd)
{
    if (!cbd) {
        return;
    }
    ZEND_ASSERT(cbd->refcount > 0);
    if (PHP_ZK_REF_SUB(cbd->refcount) == 0) {
        php_cb_data_free(cbd);
    }
}

void php_cb_data_remove(php_cb_data_t *cb_data)
{
	HashTable *ht;
	zend_long h;

	if (!cb_data || !cb_data->ht) {
		/* The table reference is already gone (closed client or callback that
		 * was already removed); pending queue entries keep it alive. */
		return;
	}

	ht = cb_data->ht;
	h = cb_data->h;
	cb_data->ht = NULL;
	cb_data->canceled = 1;

	/* The hashtable destructor drops the table reference; it may free the
	 * callback data when no queue entry or active dispatch frame holds one. */
	if (zend_hash_index_del(ht, h) == FAILURE) {
		php_cb_data_release(cb_data);
	}
}
