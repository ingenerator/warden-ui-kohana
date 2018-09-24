<?php
/**
 * Messages shown by warden during auth flows
 */
return [
    'authentication' => [
        'incorrect_password'          => [
            'title'   => 'That was not the correct password',
            'message' => 'We\'ve emailed a link to %email% for you to reset it',
        ],
        'invalid_password_reset_link' => [
            'title'   => 'Password reset failed',
            'message' => 'The link you clicked was invalid or expired. Please try to log in again and we\'ll send a new email if required',
        ],
        'logout_success'              => [
            'title'   => 'You have logged out',
            'message' => 'Come back soon!',
        ],
        'password_reset_success'      => [
            'title'   => 'Password reset successful',
            'message' => 'The password for %email% was successfully changed, you are now logged in',
        ],
        'unregistered_user'           => [
            'title'   => 'Please register to continue',
            'message' => 'The email address %email% is not registered',
        ],
    ],
    'register'       => [
        'email_verification_sent'    => [
            'title'   => 'Verification Email Sent',
            'message' => 'Please check your inbox at %email% and click the link to continue',
        ],
        'existing_user_registration' => [
            'title'   => 'You\'re already registered',
            'message' => '%email% is already registered - please login below.',
        ],
        'registration_success'       => [
            'title'   => 'Registration Successful',
            'message' => 'You have successfully created an account.',
        ],
    ]
];
