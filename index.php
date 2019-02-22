<?php
require_once 'bootstrap.php';

session_start();

$user = $_SESSION['user_name'] ?? false;
if ($user){
    header('Location: pz2abook.php');
    exit();
}
function form_success($safe_input, $form) {
    $user = [
        'user_name' => $safe_input['user_name'],
        'full_name' => $safe_input['full_name'],
        'password' => $safe_input['password'],
        'user_id' => '',
        'score' => 0,
        'museika' => false
    ];
    if (file_exists(USERS_FILE)) {
        $users_array = file_to_array(USERS_FILE);
        $users_array[] = $user;
    } else {
        $users_array = [$user];
    }
    return array_to_file($users_array, USERS_FILE);
}

function validate_user_name($field_input, &$field, $form_input) {
    if (file_exists(USERS_FILE)) {
        $users_array = file_to_array(USERS_FILE);
        foreach ($users_array as $user) {
            if ($user['user_name'] == $field_input) {
                $field['error_msg'] = strtr('Pz2aballas tokiu pavadinimu '
                        . '"@user_name" jau egzistuoja!', [
                    '@user_name' => $field_input
                ]);
                return false;
            }
        }
    }
    return true;
}

function validate_full_name($field_input, &$field, $form_input) {
    if (!preg_match('/\s/', $field_input)) {
        $field['error_msg'] = strtr('Pz2aballas reikia tarpo '
                . '"@full_name" per xerovas putiaknygei!', [
            '@full_name' => $field_input
        ]);
        return false;
    }

    return true;
}

function validate_repeat_password($safe_input, &$form) {
    if ($safe_input['repeat_password'] !== $safe_input['password']) {
        $form['fields']['password']['error_msg'] = strtr('Rankos kiba dreba '
                . '"@repeat_password" neatitinka to ką rašei prieš tai!', [
            '@repeat_password' => $safe_input['repeat_password']
        ]);

        return false;
    }

    return true;
}

function validate_password($field_input, &$field, $safe_input) {
    if (strlen($safe_input['password']) < 6) {
        $field['error_msg'] = strtr('Per trumpas taviškis '
                . '"@password" mūsų PUTiAKNYGEI!', [
            '@password' => $safe_input['password']
        ]);

        return false;
    }

    return true;
}

$form = [
    'fields' => [
        'user_name' => [
            'label' => 'Create user',
            'type' => 'text',
            'placeholder' => 'User name',
            'validate' => [
                'validate_not_empty',
                'validate_user_name'
            ]
        ],
        'full_name' => [
            'label' => 'Full Name',
            'type' => 'text',
            'placeholder' => ' Full Name',
            'validate' => [
                'validate_not_empty',
                'validate_full_name'
            ]
        ],
        'password' => [
            'label' => 'Create password',
            'type' => 'text',
            'placeholder' => 'Password',
            'validate' => [
                'validate_not_empty',
                'validate_password'
            ]
        ],
        'repeat_password' => [
            'label' => 'Repeat password',
            'type' => 'text',
            'placeholder' => 'Password',
            'validate' => [
                'validate_not_empty',
            ]
        ],
    ],
    'validate' => [
        'validate_repeat_password'
    ],
    'buttons' => [
        'submit' => [
            'text' => 'Create!'
        ]
    ],
    'callbacks' => [
        'success' => [
            'form_success'
        ],
        'fail' => []
    ]
];
$show_form = true;
if (!empty($_POST)) {
    $safe_input = get_safe_input($form);
    $form_success = validate_form($safe_input, $form);
    if ($form_success) {
        $success_msg = strtr('User`is "@user_name" sėkmingai sukurtas!', [
            '@user_name' => $safe_input['user_name']
        ]);
        $show_form = false;
    }
}
?>
<html>
    <head>
        <link rel="stylesheet" href="css/style.css">
        <title>PZ2ABALL | Register</title>
    </head>
    <body>
        <!-- Navigation -->
        <?php require 'objects/navigation.php'; ?>
        <!-- Content -->
        <h1>JOIN OUR PUTIAKNYGĘ</h1>
        <?php if ($show_form): ?>
            <!-- Form -->
            <?php require 'objects/form.php'; ?>
        <?php else: ?>
            <h2>Zašibys!</h2>
            <h3><?php print $success_msg; ?></h3>
        <?php endif; ?>
    </body>
</html>
