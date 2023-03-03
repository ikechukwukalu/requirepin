<?php

return [
    /**
     * int - Default pin
     */
    'default' => '0000',
    /**
     * bool - Allow a user to authenticate using the default pin
     */
    'allow_default_pin' => false,
    /**
     * int - Uses seconds. Make sure to update the 'expires_at'
     * column if you changed this value after migration
     */
    'duration' => 300,
    /**
     * boolean
     */
    'verify_sender' => true,
    /**
     * string - Name of form input
     */
    'input' => '_pin',
    /**
     * string - Name of URL param
     */
    'param' => '_uuid',
    /**
     * int - Max chars for pin
     */
    'max' => 4,
    /**
     * int - Min chars for pin
     */
    'min' => 4,
    /**
     * int|boolean - Check all or a specified number of
     * previous pins
     */
    'check_all' => true,
    /**
     * int - Number of previous pins to check
     */
    'number' => 4,
    /**
     * int - Number of times a user is allowed to authenticate
     * using his pin
     */
    'max_attempts' => 3,
    /**
     * int - Number of times a user is allowed to authenticate
     * using his pin
     */
    'delay_minutes' => 1,
    /**
     * int - Number of times a user is allowed to call
     * arrested route
     */
    'max_trial' => 3,
    /**
     * string - Route that will be displayed in the notification
     * that is sent when a user's pin has been changed
     */
    'change_pin_route' => 'change/pin',

    /**
     * Pin notification configurations
     */
    'notify' => [
        /**
         * boolean - Send a notification whenever pin is changed
         */
        'change' => true,
    ]
];
