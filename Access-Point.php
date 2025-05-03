<?php
$section = isset($_GET['Cavite-State-University']) ? strtolower($_GET['Cavite-State-University']) : 'home';

switch ($section) {
    case 'login':
        include 'admin/portal/login.php';
        break;

    case 'verify':
        include 'admin/portal/verify-code.php';
        break;

    case 'register':
        include 'admin/portal/new-register/register.php';
        break;
    case 'register-co-admin':
        include 'admin/portal/it-support-register.php';
        break;

    case 'reset-password':
        include 'admin/portal/reset-password.php';
        break;
    case 'terms-and-conditions':
        include 'admin/terms/terms-and-conditions.php';
        break;
    case 'privacy-policy':
        include 'admin/terms/privacy-policy.php';
        break;
    case 'reset-verify':
        include 'admin/portal/reset-password-verify.php';
        break;

    case 'new-password':
        include 'admin/portal/new-password.php';
        break;

    case 'verify-step':
        include 'admin/portal/2-factor-authentication-verify.php';
        break;

    case 'verify-step-another-options':
        include 'admin/portal/2-factor-authentication.php';
        break;


    case 'reservations':
        include 'pages/account/reservations.php';
        break;

    case 'settings':
        include 'pages/account/settings.php';
        break;

    case 'messages':
        include 'pages/account/messages.php';
        break;

    case 'reset-password':
        include 'pages/auth/reset-password.php';
        break;

    case '2fa-verify':
        include 'pages/auth/2-factor-authentication-verify.php';
        break;

    case 'logout':
        include 'pages/auth/logout.php';
        break;

    default:
        include 'asset/error/404-error.php';
        break;
}
