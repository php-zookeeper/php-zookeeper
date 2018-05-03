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

#include <php.h>

#include "php5to7.h"
#include "php_zookeeper_class.h"
#include "php_zookeeper_exceptions.h"
#include "php_zookeeper_config_class.h"
#include "php_zookeeper_stat.h"

typedef struct {
    php_zk_t     *php_zk;
    zend_object    zo;
} php_zk_conf_t;

static zend_object_handlers php_zk_conf_obj_handlers;

static inline php_zk_conf_t* php_zk_conf_fetch_object(zend_object *obj);
static zend_object* php_zk_config_new(zend_class_entry *ce TSRMLS_DC);

#define Z_ZK_CONF_P(zv) php_zk_conf_fetch_object(Z_OBJ_P((zv)))

#define PHP_ZK_CONF_METHOD_INIT_VARS                \
    zval*             object  = getThis(); \
    php_zk_conf_t*         i_obj   = NULL;      \

#define PHP_ZK_CONF_METHOD_FETCH_OBJECT                                                 \
    i_obj = Z_ZK_CONF_P(object);   \
    if (!i_obj->php_zk) {   \
        php_zk_throw_exception(PHPZK_CONNECT_NOT_CALLED TSRMLS_CC); \
        return; \
    } \


/* {{{ ZookeeperConfig::get( .. )
   */
static PHP_METHOD(ZookeeperConfig, get)
{
    zend_fcall_info fci = empty_fcall_info;
    zend_fcall_info_cache fcc = empty_fcall_info_cache;
    zval *stat_info = NULL;
    php_cb_data_t *cb_data = NULL;
    char *buffer;
    struct Stat stat;
    int status = ZOK;
    int length = 512;
    PHP_ZK_CONF_METHOD_INIT_VARS;

    if (zend_parse_parameters(ZEND_NUM_ARGS() TSRMLS_CC, "|f!z", &fci,
                              &fcc, &stat_info) == FAILURE) {
        return;
    }

    PHP_ZK_CONF_METHOD_FETCH_OBJECT;

    if (stat_info) {
        ZVAL_DEREF(stat_info);
    }

    if (fci.size != 0) {
        cb_data = php_cb_data_new(&i_obj->php_zk->callbacks, &fci, &fcc, 1 TSRMLS_CC);
    }

    buffer = emalloc (length+1);
    status = zoo_wgetconfig(i_obj->php_zk->zk, (fci.size != 0) ? php_zk_watcher_marshal : NULL,
                      cb_data, buffer, &length, &stat);
    buffer[length] = 0;

    if (status != ZOK) {
        efree (buffer);
        php_cb_data_destroy(&cb_data);
        php_zk_throw_exception(status TSRMLS_CC);

        /* Indicate data marshalling failure with boolean false so that user can retry */
        if (status == ZMARSHALLINGERROR) {
            RETURN_FALSE;
        }
        return;
    }

    if (stat_info) {
        php_stat_to_array(&stat, stat_info);
    }

    /* Length will be returned as -1 if the configuration data is NULL */
    if (length == -1) {
        RETURN_NULL();
    }

    PHP5TO7_RETVAL_STRINGL(buffer, length);
    efree(buffer);
}
/* }}} */

/* {{{ methods arginfo */
ZEND_BEGIN_ARG_INFO(arginfo_conf_get, 0)
    ZEND_ARG_INFO(0, watcher_cb)
    ZEND_ARG_INFO(1, stat_info)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_conf_add, 0, 0, 1)
    ZEND_ARG_INFO(0, servers)
    ZEND_ARG_INFO(1, stat_info)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_conf_remove, 0, 0, 1)
    ZEND_ARG_INFO(0, servers)
    ZEND_ARG_INFO(1, stat_info)
ZEND_END_ARG_INFO()

ZEND_BEGIN_ARG_INFO_EX(arginfo_conf_set, 0, 0, 1)
    ZEND_ARG_INFO(0, servers)
    ZEND_ARG_INFO(1, stat_info)
ZEND_END_ARG_INFO()
/* }}} */

/* {{{ zookeeper_config_class_methods */
#define ZK_CONF_ME(name, args) PHP_ME(ZookeeperConfig, name, args, ZEND_ACC_PUBLIC)
static zend_function_entry zookeeper_config_class_methods[] = {
    ZK_CONF_ME(get,             arginfo_conf_get)
    //ZK_CONF_ME(add,             arginfo_conf_add)
    //ZK_CONF_ME(remove,          arginfo_conf_remove)
    //ZK_CONF_ME(set,             arginfo_conf_set)
    PHP_FE_END
};
#undef ZK_CONF_ME
/* }}} */

static void php_zk_conf_destroy(php_zk_conf_t *i_obj TSRMLS_DC)
{
    efree(i_obj);
}

static inline php_zk_conf_t* php_zk_conf_fetch_object(zend_object *obj)
{
    return (php_zk_conf_t *)((char*)(obj) - XtOffsetOf(php_zk_conf_t, zo));
}

static void php_zk_conf_free_storage(zend_object *obj TSRMLS_DC)
{
    php_zk_conf_t *i_obj;

    i_obj = php_zk_conf_fetch_object(obj);
    zend_object_std_dtor(&i_obj->zo TSRMLS_CC);
    php_zk_conf_destroy(i_obj TSRMLS_CC);
}

void php_zk_config_register(TSRMLS_D)
{
    zend_class_entry ce;

    INIT_CLASS_ENTRY(ce, "ZookeeperConfig", zookeeper_config_class_methods);
    php_zk_config_ce = php5to7_register_internal_class_ex(&ce, NULL);
    php_zk_config_ce->create_object = php_zk_config_new;

    memcpy(&php_zk_conf_obj_handlers, zend_get_std_object_handlers(), sizeof(zend_object_handlers));
    php_zk_conf_obj_handlers.offset = XtOffsetOf(php_zk_conf_t, zo);
    php_zk_conf_obj_handlers.free_obj = php_zk_conf_free_storage;
}

static zend_object* php_zk_config_new(zend_class_entry *ce TSRMLS_DC)
{
    return php_zk_config_new_from_zk(ce, NULL TSRMLS_CC);
}

zend_object* php_zk_config_new_from_zk(zend_class_entry *ce, php_zk_t *php_zk TSRMLS_DC)
{
    php_zk_conf_t *i_obj;

    i_obj = ecalloc(1, sizeof(*i_obj));
    zend_object_std_init(&i_obj->zo, ce TSRMLS_CC);
    object_properties_init(&i_obj->zo, ce);
    i_obj->zo.handlers = &php_zk_conf_obj_handlers;

    if (php_zk)
        i_obj->php_zk = php_zk;

    return &i_obj->zo;
}
