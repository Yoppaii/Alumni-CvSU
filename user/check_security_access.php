<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function isSecurityVerified() {
    $currentSection = isset($_GET['section']) ? $_GET['section'] : 'home';
    
    if (!isset($_SESSION['security_verified']) || 
        !isset($_SESSION['security_verified_time']) || 
        !isset($_SESSION['verified_section']) ||
        $_SESSION['verified_section'] !== $currentSection) {
        return false;
    }
    
    $timeout = 30 * 60;
    if (time() - $_SESSION['security_verified_time'] > $timeout) {
        unset($_SESSION['security_verified']);
        unset($_SESSION['security_verified_time']);
        unset($_SESSION['verified_section']);
        return false;
    }
    
    return true;
}

function requiresSecurityVerification($section) {
    $securedSections = [
        '2-step-verification',
        'change-password',
        'recovery-email',
        'backup-codes'
    ];
    
    return in_array($section, $securedSections);
}
?>