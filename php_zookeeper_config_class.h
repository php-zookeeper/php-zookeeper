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

#ifndef PHP_ZOOKEEPER_CONFIG_CLASS
#define PHP_ZOOKEEPER_CONFIG_CLASS

extern zend_class_entry *php_zk_config_ce;

void php_zk_config_register(TSRMLS_D);
zend_object* php_zk_config_new_from_zk(zend_class_entry *ce, php_zk_t *php_zk TSRMLS_DC);

#endif  /* PHP_ZOOKEEPER_CONFIG_CLASS */
