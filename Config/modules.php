<?php

return [
    'paths' => [
        /*
        |--------------------------------------------------------------------------
        | Generator path
        |--------------------------------------------------------------------------
        | Customise the paths where the folders will be generated.
        | Set the generate key to false to not generate that folder
        */
        'generator' => [
            'entity-fields' => ['path' => 'Entities/Fields', 'generate' => false],
            'entity-validator' => ['path' => 'Entities/Validators', 'generate' => false],
        ]
    ],
];
