<?php

namespace Matecat\Dqf;

class Constants {
    /**
     * **********************************************
     * API
     * **********************************************
     */
    const API_PRODUCTION_URI = 'https://dqf-api.taus.net';
    const API_STAGING_URI    = 'https://dqf-api.stag.taus.net';
    const API_VERSION        = 'v3';

    /**
     * **********************************************
     * ANONYMOUS_SESSION
     * **********************************************
     */
    const ANONYMOUS_SESSION_ID = -999;

    /**
     * **********************************************
     * DATA_TYPE
     * **********************************************
     */
    const DATA_TYPE_BOOLEAN  = 'boolean';
    const DATA_TYPE_INTEGER  = 'integer';
    const DATA_TYPE_DOUBLE   = 'double';
    const DATA_TYPE_STRING   = 'string';
    const DATA_TYPE_ARRAY    = 'array';
    const DATA_TYPE_OBJECT   = 'object';
    const DATA_TYPE_RESOURCE = 'resource';
    const DATA_TYPE_NULL     = 'NULL';

    /**
     * **********************************************
     * HTTP_VERBS
     * **********************************************
     */
    const HTTP_VERBS_GET    = 'GET';
    const HTTP_VERBS_CREATE = 'POST';
    const HTTP_VERBS_UPDATE = 'PUT';
    const HTTP_VERBS_DELETE = 'DELETE';

    /**
     * **********************************************
     * LOGICAL OPERATORS
     * **********************************************
     */

    const LOGICAL_OPERATOR_EQUALS = '===';

    /**
     * **********************************************
     * PROJECT_TYPE
     * **********************************************
     */
    const PROJECT_TYPE_REVIEW      = 'review';
    const PROJECT_TYPE_TRANSLATION = 'translation';

    /**
     * **********************************************
     * REVIEW_TYPE
     * **********************************************
     */
    const REVIEW_TYPE_CORRECTION = 'correction';
    const PROJECT_TYPE_ERROR     = 'error_typology';
    const PROJECT_TYPE_COMBINED  = 'combined';
}
