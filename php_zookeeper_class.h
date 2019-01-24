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

#ifndef PHP_ZOOKEEPER_CLASS
#define PHP_ZOOKEEPER_CLASS

#include <zookeeper.h>
#include "php_zookeeper_callback.h"

typedef struct {
    zhandle_t     *zk;
    php_cb_data_t *cb_data;
    HashTable callbacks;
    zend_object    zo;
} php_zk_t;

void php_zk_watcher_marshal(zhandle_t *zk, int type, int state, const char *path, void *context);

#endif  /* PHP_ZOOKEEPER_CLASS */
