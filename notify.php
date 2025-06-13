<?php
// notify.php
// Payfast will send POST data here – use it to verify the payment (optional for testing)
file_put_contents("payfast_log.txt", print_r($_POST, true));

?>
