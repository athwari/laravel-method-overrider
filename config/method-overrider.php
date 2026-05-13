<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Final Method Policy
    |--------------------------------------------------------------------------
    | ignore | warn | strict
    |
    */

    'final_method_policy' => env(
        'METHOD_OVERRIDER_FINAL_POLICY',
        'warn'
    ),

];