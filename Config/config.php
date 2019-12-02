<?php

return [
    'name' => 'Field',
    'useCache' => !env('APP_DEBUG'),
    /**
     * Number of revisions to keep in database.
     * -1 for unlimited
     */
    'numberRevisionsToKeep' => 10
];
