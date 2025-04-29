<?php
$hashed_password = password_hash('123', PASSWORD_BCRYPT);
echo $hashed_password;
?>
