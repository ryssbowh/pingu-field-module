<?php

return [
    'name' => 'Field',
    'useCache' => true,//!env('APP_DEBUG'),
    'cache-keys' => [
        'repositories' => 'field.repository',
        'validators' => 'field.validator',
        'fields' => 'field.fields',
        'values' => 'field.values',
        'revisions' => 'field.revisions'
    ],
    /**
     * Number of revisions to keep in database.
     * -1 for unlimited
     */
    'numberRevisionsToKeep' => 10
];
