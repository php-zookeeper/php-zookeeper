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

#ifndef PHP_ZOOKEEPER34to35
#define PHP_ZOOKEEPER34to35

#if ZOO_MAJOR_VERSION>=3 && ZOO_MINOR_VERSION>=5
#define PHP_ZK_LOG_ERROR(_zh, ...) LOG_ERROR(LOGCALLBACK(_zh), __VA_ARGS__)
#define PHP_ZK_LOG_WARN(_zh, ...) LOG_WARN(LOGCALLBACK(_zh), __VA_ARGS__)
#define PHP_ZK_LOG_INFO(_zh, ...) LOG_INFO(LOGCALLBACK(_zh), __VA_ARGS__)
#define PHP_ZK_LOG_DEBUG(_zh, ...) LOG_DEBUG(LOGCALLBACK(_zh), __VA_ARGS__)
#else
#define PHP_ZK_LOG_ERROR(_zh, ...) LOG_ERROR(__VA_ARGS__)
#define PHP_ZK_LOG_WARN(_zh, ...) LOG_WARN(__VA_ARGS__)
#define PHP_ZK_LOG_INFO(_zh, ...) LOG_INFO(__VA_ARGS__)
#define PHP_ZK_LOG_DEBUG(_zh, ...) LOG_DEBUG(__VA_ARGS__)
#endif

#endif   /* PHP_ZOOKEEPER34to35 */