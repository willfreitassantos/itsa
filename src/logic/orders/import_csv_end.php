<?php
  session_start();
  $files_csv = unserialize($_SESSION['files_csv']);
  unset($_SESSION['files_csv']);

  $i = 0;
  foreach($files_csv as $k => $val) {
    unlink($files_csv[$i]);
    $i++;
  }
  header('Location: ../orders/new');
?>
