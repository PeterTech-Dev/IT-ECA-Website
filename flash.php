<?php
// flash.php
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => htmlspecialchars($message)];
}

function displayFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $type = $_SESSION['flash']['type'];
        $message = $_SESSION['flash']['message'];
        echo "<div class='flash-message $type' role='alert'>$message</div>";
        unset($_SESSION['flash']);
    }
}
?>