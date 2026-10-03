/* Test-only module: inspect the real extension's callback ownership contract. */
#include <php.h>
#include "php_zookeeper_class.h"

static unsigned int destroyed;

static void count_destroy(zval *entry)
{
    destroyed++;
    php_cb_data_destroy(Z_PTR_P(entry));
}

#define CHECK(condition, label) do { \
    if (!(condition)) { php_printf("FAIL: %s\n", label); failures++; } \
} while (0)

PHP_FUNCTION(zookeeper_test_registry_checks)
{
    HashTable callbacks;
    zend_fcall_info fci = {0};
    zend_fcall_info_cache fcc = {0};
    php_cb_data_t *cbd, *failed;
    unsigned int failures = 0, oneshot, round;

    ZVAL_NULL(&fci.function_name);
    for (oneshot = 0; oneshot <= 1; oneshot++) {
        destroyed = 0;
        zend_hash_init(&callbacks, 5, NULL, count_destroy, 0);
        for (round = 0; round < 2; round++) {
            cbd = php_cb_data_new(&callbacks, &fci, &fcc, oneshot);
            CHECK(cbd != NULL, "first registration succeeds");
            if (cbd) {
                CHECK(cbd->h == 0, "first callback records key 0");
                CHECK(zend_hash_index_find_ptr(&callbacks, cbd->h) == cbd,
                    "saved key resolves to the registered pointer");
                php_cb_data_remove(cbd);
            }
            CHECK(zend_hash_num_elements(&callbacks) == 0, "remove deletes the entry immediately");
            CHECK(destroyed == round + 1, "remove destroys the wrapper immediately");
            zend_hash_clean(&callbacks);
            CHECK(destroyed == round + 1, "clean does not destroy the wrapper twice");
        }
        zend_hash_destroy(&callbacks);
    }

    zend_hash_init(&callbacks, 5, NULL, count_destroy, 0);
    callbacks.nNextFreeElement = ZEND_LONG_MAX;
    ZVAL_STRING(&fci.function_name, "callback_registry_unused");
    cbd = php_cb_data_new(&callbacks, &fci, &fcc, 1);
    CHECK(cbd && cbd->h == ZEND_LONG_MAX, "maximum key is recorded without subtraction");
    failed = php_cb_data_new(&callbacks, &fci, &fcc, 1);
    CHECK(failed == NULL, "occupied maximum key rejects registration");
    CHECK(zend_hash_num_elements(&callbacks) == 1, "failed registration preserves the existing entry");
    CHECK(zend_hash_index_find_ptr(&callbacks, ZEND_LONG_MAX) == cbd,
        "failed registration does not replace the existing callback");
    CHECK(Z_REFCOUNT(fci.function_name) == 2, "failed registration rolls back its callable reference");
    /* Allow the old implementation to fail cleanly without leaking its orphan. */
    if (failed && failed != cbd) {
        php_cb_data_destroy(failed);
    }
    zend_hash_destroy(&callbacks);
    zval_ptr_dtor(&fci.function_name);
    RETURN_BOOL(failures == 0);
}

PHP_FUNCTION(zookeeper_test_exhaust_registry)
{
    zval *object;
    php_zk_t *client;
    zend_fcall_info fci = {0};
    zend_fcall_info_cache fcc = {0};
    if (zend_parse_parameters(ZEND_NUM_ARGS(), "o", &object) == FAILURE) {
        return;
    }
    client = (php_zk_t *) ((char *) Z_OBJ_P(object) - XtOffsetOf(php_zk_t, zo));
    ZVAL_NULL(&fci.function_name);
    client->callbacks.nNextFreeElement = ZEND_LONG_MAX;
    if (!php_cb_data_new(&client->callbacks, &fci, &fcc, 0)) {
        zend_error(E_ERROR, "Could not prepare the exhausted registry fixture");
    }
}

PHP_FUNCTION(zookeeper_test_registry_state)
{
    zval *object;
    php_zk_t *client;
    if (zend_parse_parameters(ZEND_NUM_ARGS(), "o", &object) == FAILURE) {
        return;
    }
    client = (php_zk_t *) ((char *) Z_OBJ_P(object) - XtOffsetOf(php_zk_t, zo));
    array_init(return_value);
    add_assoc_long(return_value, "count", zend_hash_num_elements(&client->callbacks));
    add_assoc_bool(return_value, "default_registered", client->cb_data &&
        zend_hash_index_find_ptr(&client->callbacks, client->cb_data->h) == client->cb_data);
}

ZEND_BEGIN_ARG_INFO_EX(arginfo_checks, 0, 0, 0)
ZEND_END_ARG_INFO()
ZEND_BEGIN_ARG_INFO_EX(arginfo_object, 0, 0, 1)
    ZEND_ARG_INFO(0, object)
ZEND_END_ARG_INFO()

static const zend_function_entry test_functions[] = {
    PHP_FE(zookeeper_test_registry_checks, arginfo_checks)
    PHP_FE(zookeeper_test_exhaust_registry, arginfo_object)
    PHP_FE(zookeeper_test_registry_state, arginfo_object)
    PHP_FE_END
};

zend_module_entry callback_registry_test_module_entry = {
    STANDARD_MODULE_HEADER,
    "callback_registry_test", test_functions,
    NULL, NULL, NULL, NULL, NULL,
    "1.0", STANDARD_MODULE_PROPERTIES
};

ZEND_GET_MODULE(callback_registry_test)
