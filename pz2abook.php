<?php
require_once 'bootstrap.php';
session_start();

$user = $_SESSION['user_name'] ?? false;
if (!$user) {
    header('Location: login.php');
    exit();
}

function form_success($safe_input, $form) {
    $target_partner_index = $safe_input['action'];

    if (file_exists(USERS_FILE)) {
        $partners_array = file_to_array(USERS_FILE);
        $museika = $partners_array[$target_partner_index]['museika'];

        if (!$museika) {
            $partners_array[$target_partner_index]['score'] ++;
        }

        return array_to_file($partners_array, USERS_FILE);
    }
}

function generateForms($existing_partners) {
    $forms = [];
    foreach ($existing_partners as $partner_index => $partner_data) {
        $forms[] = [
            'fields' => [
                'user_name' => [
                    'label' => $partner_data['user_name'],
                    'type' => 'hidden',
                    'value' => $partner_data['user_name'],
                    'validate' => [
                        'validate_not_empty'
                    ]
                ],
                'user_score' => [
                    'label' => $partner_data['score'],
                    'type' => 'hidden',
                    'value' => $partner_data['score'],
                    'validate' => [
                        'validate_not_empty'
                    ]
                ]
            ],
            'buttons' => [
                $partner_index => [
                    'text' => 'Tau pz2a!'
                ]
            ],
            'callbacks' => [
                'success' => [
                    'form_success'
                ],
                'fail' => []
            ]
        ];
    }

    return $forms;
}

$existing_partners = file_to_array(USERS_FILE);
$forms = generateForms($existing_partners);
$show_form = true;

if (isset($_POST)) {
    $form_idx = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_NUMBER_INT);
    $form = $forms[$form_idx] ?? false;
    $form_success = false;

    if ($form) {
        $safe_input = get_safe_input($form);
        $form_success = validate_form($safe_input, $form);
    }
    if ($form_success) {
        $existing_partners = file_to_array(USERS_FILE);
        $forms = generateForms($existing_partners);
    }
}
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'logout') {
        $_SESSION = [];
    }
}
?>
<html>
    <head>
        <link rel="stylesheet" href="css/style.css">
        <title>This is your PZ2ABOOK!</title>
    </head>
    <body>   
        <!-- Navigation -->    
        <?php require 'objects/navigation.php'; ?>

        <!-- Content -->       
        <h1>This is your PZ2ABOOK!</h1>
        <h2>Kiek kartų GAVAI PZ2I?</h2>
        <?php if ($show_form): ?>
            <!-- Form -->        
            <?php require 'objects/partnersform.php'; ?>
        <?php else: ?>
            <h2>Zašibys!</h2>
            <h3><?php print $message; ?></h3>
        <?php endif; ?>
        <?php if (isset($_SESSION['user_name'])): ?>
            <button name="action" value="logout">Logout!</button>
        <?php endif; ?>
    </body>
</html>