<?php
// Configure your Subject Prefix and Recipient here
$subjectPrefix = 'Relacionamento Comercial';
$emailTo       = ' lucas_rc15@live.com';

$errors = array(); // array to hold validation errors
$data   = array(); // array to pass back data

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = stripslashes(trim($_POST['name']));
    $email   = stripslashes(trim($_POST['email']));
    $message = stripslashes(trim($_POST['message']));
    $spam    = $_POST['textfield'];

    if (empty($name)) {
        $errors['name'] = 'Você precisa fornencer seu nome.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Formato de e-mail esta incorreto.';
    }

    if (empty($message)) {
        $errors['message'] = 'Mensagem é necessária.';
    }

    // if there are any errors in our errors array, return a success boolean or false
    if (!empty($errors)) {
        $data['success'] = false;
        $data['errors']  = $errors;
    } else {
        $subject = "Message from $subjectPrefix";
        $body    = '
            Nome: '.$name.'
            E-mail: '.$email.'
            Mensagem: '.nl2br($message).'
        ';

        $headers  = "MIME-Version: 1.1" . PHP_EOL;
        $headers .= "Content-type: text/html; charset=utf-8" . PHP_EOL;
        $headers .= "Content-Transfer-Encoding: 8bit" . PHP_EOL;
        $headers .= "Date: " . date('r', $_SERVER['REQUEST_TIME']) . PHP_EOL;
        $headers .= "Message-ID: <" . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>' . PHP_EOL;
        $headers .= "From: " . "=?UTF-8?B?".base64_encode($name)."?=" . " <$email> " . PHP_EOL;
        $headers .= "Return-Path: $emailTo" . PHP_EOL;
        $headers .= "Reply-To: $email" . PHP_EOL;
        $headers .= "X-Mailer: PHP/". phpversion() . PHP_EOL;
        $headers .= "X-Originating-IP: " . $_SERVER['SERVER_ADDR'] . PHP_EOL;


        if (empty($spam)) {
          mail($emailTo, "=?utf-8?B?" . base64_encode($subject) . "?=", $body, $headers);
        }

        $data['success'] = true;
        $data['confirmation'] = 'Obrigado por entrar em contato! Em breve retornaremos.';
    }

    // return all our data to an AJAX call
    echo json_encode($data);
}
