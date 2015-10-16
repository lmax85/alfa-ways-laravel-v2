<?php
return array(
    'plugins' => array(
        'adldap' => array(
            'account_suffix'=>  '@alfa.local',
            'domain_controllers'=>  array(
                '192.168.1.225',
                'dc1.alfa.local'
            ), // Load balancing domain controllers
            'base_dn'   =>  'DC=ALFA,DC=LOCAL',
            // 'admin_username' => 'Administrator', // This is required for session persistance in the application
            // 'admin_password' => 'Qwer1234',
        ),
    ),
);
