<?php

return [
    /*
     |--------------------------------------------------------------------------
     | DocCheck Configuration
     |--------------------------------------------------------------------------
     |
     | Set the login ID
     |
     */
    'client_key'=>env('DOCCHECK_LOGIN_ID'),

    // Optional secret to be used
    'client_secret'=>env('DOCCHECK_SECRET'),
];