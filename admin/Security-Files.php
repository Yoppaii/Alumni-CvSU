<?php
function isDirectAccess() {
    $directAccess = false;
    
    $callingScript = basename($_SERVER['PHP_SELF']);
    
    $protectedFiles = [
        'main_db.php',
    ];
    
    if (in_array($callingScript, $protectedFiles)) {
        $directAccess = true;
    }
    
    return $directAccess;
}

if (isDirectAccess()) {
    header('Location: index.php');
    exit();
}
?>