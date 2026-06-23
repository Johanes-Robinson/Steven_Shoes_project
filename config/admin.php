<?php

$adminEmails = explode(',', env('ADMIN_EMAILS', ''));

return [
    'emails' => array_values(array_unique(array_filter(array_map(
        fn (string $email) => strtolower(trim($email)),
        $adminEmails
    )))),
];
