<?php
header("Content-Type:text/html; charset=UTF-8");
  ////////////////////////////////////////////////////////////////////
set_time_limit(0);
ini_set('max_execution_time', '90000');
ini_set('memory_limit', '256M');
ini_set('realpath_cache_size', '16M');
ini_set('realpath_cache_ttl', '1200');


  // 1 - использовать функцию popen, 0 - использовать функцию system 
  define('USE_POPEN', 1);

  ////////////////////////////////////////////////////////////////////

  if (!USE_POPEN) { 
     if (!function_exists('system') || !is_callable('system')) {
        echo '<font color=red>system() не доступна. Скрипт работать не будет.</font><br/>';

     echo '<br/>Данная функция должна быть разрешена в файле ' . php_ini_loaded_file() . ' согласно инструкции из отчета.</br><br/>';

        die();
     }
  } else {
     if (!function_exists('popen') || !is_callable('popen')) {
        echo '<font color=red>popen() не доступна. Скрипт работать не будет.</font><br/>';

     echo '<br/>Данная функция должна быть разрешена в файле ' . php_ini_loaded_file() . ' согласно инструкции из отчета.</br><br/>';

        die();
     }
  }

function system2($cmd) {
  $f = popen($cmd, 'r');
  if (!$f) {
     echo '<font color=red>Ошибка. Команда не выполнена.</font><br/>';
     return;
  }

  while (!feof($f)) {
     echo fgets($f);
  }

  pclose($f);
}

$cmd_list = file('protect.sh');
foreach ($cmd_list as $cmd) {
  $cmd = trim($cmd);
  echo "<br><font color=green><b>$cmd</b></font><br/>";
  if (USE_POPEN) {
     system2($cmd);
  } else {
     $res = '';  
     system($cmd, $res);
     echo $res . "<br/>";
     if ($res < 0) {
        echo '<font color=red>Ошибка. Команда не выполнена.</font><br/>';
     }
  }
}