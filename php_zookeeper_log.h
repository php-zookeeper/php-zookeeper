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

#ifndef PHP_ZOOKEEPER_LOG_H
#define PHP_ZOOKEEPER_LOG_H

void php_zk_log_error(zhandle_t *zh, ...);
void php_zk_log_warn(zhandle_t *zh, ...);
void php_zk_log_info(zhandle_t *zh, ...);
void php_zk_log_debug(zhandle_t *zh, ...);

#endif  /* PHP_ZOOKEEPER_LOG_H */