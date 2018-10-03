<?php
/**
 * Messages shown by warden during auth flows
 */
return [
    'authentication' => [
        'account_not_active' => [
            'title'   => 'Account not active',
            'message' => 'You have not verified your email address - please check your inbox at %email% and click the link to complete your registration',
        ],
        'email_verification_failed' => [
            'title'   => 'Email server not available',
            'message' => 'That was not the right password, but we can\'t contact your email server at %email% to send a password reset at the moment. Please try again later.',
        ],
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
    'change_email'   => [
        'email_already_registered' => [
            'title'   => 'Email address in use',
            'message' => 'You cannot change your email address to %email% because there is already an account registered to that address',
        ],
        'email_changed'            => [
            'title'   => 'Email updated',
            'message' => 'You have successfully updated your email to <strong>%email%</strong>',
        ],
        'invalid_verify_link'      => [
            'title'   => 'Invalid link',
            'message' => 'The link you clicked was invalid or expired - your email address has not been updated',
        ],
        'verification_email_sent'  => [
            'title'   => 'Confirm your new email',
            'message' => 'Please check your inbox at %email% and click the link to verify and save your new address',
        ],
    ],
    'profile'        => [
        'password_changed' => [
            'title'   => 'Password updated',
            'message' => 'Your password has been changed.',
        ],
    ],
    'register'       => [
        'account_activated' => [
            'title'   => 'Account verified',
            'message' => 'Thank you - you have now verified your email and your account is active',
        ],
        'email_verification_sent'    => [
            'title'   => 'Verification Email Sent',
            'message' => 'Please check your inbox at %email% and click the link to continue',
        ],
        'existing_user_registration' => [
            'title'   => 'You\'re already registered',
            'message' => '%email% is already registered - please login below.',
        ],
        'invalid_activation_link' => [
            'title'   => 'Invalid link',
            'message' => 'The link you clicked was invalid or expired. Please try to log in again and we\'ll send a new email if required',
        ],
        'invalid_register_link' => [
            'title'   => 'Invalid link',
            'message' => 'The link you clicked was invalid or expired. Please try to register again and we\'ll send you a new email',
        ],
        'registration_success'       => [
            'title'   => 'Registration Successful',
            'message' => 'You have successfully created an account.',
        ],
    ]
];
