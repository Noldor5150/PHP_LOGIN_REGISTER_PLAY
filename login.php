<?php
require_once 'bootstrap.php';

session_start();
$user = $_SESSION['user_name'] ?? false;
if ($user) {
    header("Location: pz2abook.php");
    exit();
}

function form_success($safe_input, $form) {
    $user = [
        'user_name' => $safe_input['user_name'],
        'password' => $safe_input['password']
    ];
    $_SESSION['user_name'] = $safe_input['user_name'];
}

function validate_login($safe_input, &$form) {
    $user_name = $safe_input['user_name'];
    $password = $safe_input['password'];

    if (file_exists(USERS_FILE)) {
        $users_array = file_to_array(USERS_FILE);
        foreach ($users_array as $user) {
            if ($user['user_name'] == $user_name &&
                    $user['password'] == $password) {

                return true;
            }
        }
    }

    $form['fields']['user_name']['error_msg'] = strtr('"Nepavyko prijungti @user"!', [
        '@user' => $user_name
    ]);
    $form['fields']['password']['error_msg'] = strtr('"Nepavyko prijungti @user"!', [
        '@user' => $user_name
    ]);

    return false;
}

$name_placeholder_array = [
    'zirgas69',
    'pimpaklynis69',
    'Rembo69',
    'Maryte69'
];
$password_placeholder_array = [
    'manopasswordas69',
    'neatsimenu',
    'bbzn',
    'pisiulka'
];

$form = [
    'fields' => [
        'user_name' => [
            'label' => 'User Name',
            'type' => 'text',
            'placeholder' => $name_placeholder_array[array_rand($name_placeholder_array, 1)],
            'validate' => [
                'validate_not_empty',
            ]
        ],
        'password' => [
            'label' => 'Password',
            'type' => 'password',
            'placeholder' => $password_placeholder_array[array_rand($password_placeholder_array, 1)],
            'validate' => [
                'validate_not_empty',
//                'validate_user_password'
            ]
        ],
    ],
    'buttons' => [
        'submit' => [
            'text' => 'Login to Putiaknygė!'
        ]
    ],
    'validate' => [
        'validate_login'
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
        $success_msg = strtr('User`is "@user_name" sėkmingai įvestas!', [
            '@user_name' => $safe_input['user_name']
        ]);
        $show_form = false;
    }
}
?>
<html>
    <head>
        <link rel="stylesheet" href="css/style.css">
        <title>Putiaknygė | Register</title>
    </head>
    <body>
        <!-- Navigation -->    
<?php require 'objects/navigation.php'; ?>        

        <!-- Content -->    
        <h1>Login to Putiaknygė!</h1>

<?php if ($show_form): ?>
            <!-- Form -->        
            <?php require 'objects/form.php'; ?>
        <?php else: ?>
            <h2>Zašibys!</h2>
            <h3><?php print $success_msg; ?></h3>
        <?php endif; ?>
    </body>
</html>