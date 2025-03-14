<?php
session_start();
session_destroy();
header("Location: /jobportal/index.html");
exit();
?>
