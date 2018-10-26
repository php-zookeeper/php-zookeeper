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
  | Authors: Ryan Uber <ru@ryanuber.com>                                 |
  |          Timandes White <timands@gmail.com>                          |
  +----------------------------------------------------------------------+
*/

#ifndef PHP_ZOOKEEPER_EXCEPTIONS
#define PHP_ZOOKEEPER_EXCEPTIONS

/**
 * register exceptions
 */
void php_zk_register_exceptions(TSRMLS_D);
zend_class_entry * php_zk_get_exception_with_message(zend_class_entry *ce, char *message TSRMLS_DC);
/**
 * throw exception according to status
 */
void php_zk_throw_exception(int zk_status TSRMLS_DC);

#define PHPZK_INITIALIZATION_FAILURE 5999
#define PHPZK_CONNECT_NOT_CALLED 5998

#endif  /* PHP_ZOOKEEPER_EXCEPTIONS */
