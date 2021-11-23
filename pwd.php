<?php

require "config.php";

function connect() {
  try {
    if (defined('DB_SOCKET')) {
      return new PDO(DB_TYPE . ':unix_socket=' . DB_SOCKET . ';dbname=' . DB_NAME . ';charset=utf8', 
        DB_USER, DB_PASS, array(PDO::ATTR_PERSISTENT => true));
    } else {
      return new PDO(DB_TYPE . ':host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8', 
        DB_USER, DB_PASS, array(PDO::ATTR_PERSISTENT => true));
    }
  } catch (PDOException $e) {
     exit;
  }
}

  function get_readable($length = 8) {
    $db = connect();
    $wsql = '';
    if (isset($_REQUEST['longer']) && $_REQUEST['longer'] == 'true') {
      $wsql = ' && lng >= 7';  
    }

    $stmt = $db->prepare("SELECT `word` FROM `pwds` WHERE lng<=? {$wsql} ORDER BY RAND() LIMIT 1");
    $stmt->execute(array($length));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row['word'];
  }

$r = "";
$first_char = "";
$res_pwd = "";

  function readable_random_string($length = 12) {
    $conso = array("b", "c", "d", "f", "g", "h", "j", "k", "l",
        "m", "n", "p", "r", "s", "t", "v", "w", "x", "y", "z");
    $vocal = array("a", "e", "i", "o", "u");
    $special = array("!","@","#","$","%","&","*",".");
    global $r, $first_char, $res_pwd;
    $password = "";
    $did_special_chr = false;
    $did_number = false;
    
    srand((double) microtime() * 1000000);
    
    if (rand(0, 100) > 50) {
      $first_char = $special[rand(0, 7)];
      $password.= $first_char;
      $did_special_chr = true;
      $length--;
    }  
    
    if (isset($_REQUEST['readable']) && $_REQUEST['readable'] == 'true') {
      $r = get_readable($length - 2);
      $password .= str_replace(' ', '_', $r);
      $length -= strlen($r);
    }

    while ($length > 0) { 
      if ($length >= 1) { 
        $odds = rand(0, 100);
        $ccc = ($odds > 50) ? strtoupper($conso[rand(0, 19)]) : $conso[rand(0, 19)]; 
        $res_pwd .= $ccc;
        $password .= $ccc;
        $length--; 
      }
      if ($length >= 1) { 
        $odds = rand(0, 100);
        $vvv = ($odds > 50) ? strtoupper($vocal[rand(0, 4)]) : $vocal[rand(0, 4)]; 
        $res_pwd .= $vvv;
        $password .= $vvv;
        $length --; 
      }
      if ($length >= 1) { 
        if ($did_special_chr === false || ( $did_number === true && (rand(0, 100) > 50) )) {
          $sss = $special[rand(0, 7)];
          $res_pwd .=$sss;
          $password.=$sss;
          $did_special_chr = true;
        } else {
          $nnn = rand(0,9); // Get a Number from 0 to 9.
          $res_pwd.=$nnn;
          $password.=$nnn;
          $did_number = true;
        }
        $length--;
      }
    }
    return $password;
  }
  
  $len = (isset($_REQUEST['password_length'])) ? $_REQUEST['password_length'] : 12;
  if ($len > 50 || $len < 3) { $len = 12; }
  
  $password = readable_random_string($len);
  
  $read_me = "{$first_char} < {$r} spelled > [{$r}] {$res_pwd}";
  $sayme = (! empty($r)) ? $read_me : $password;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="Password Generator">
    <meta name="author" content="Robert Strutts">
    <title>Password Generator</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="css/jquery.mobile-1.4.5.min.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="css/bootstrap.min.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
    <link rel="stylesheet" href="css/glyphish.css" media="screen" type="text/css" />
    <link rel="stylesheet" href="css/print.css" media="print" type="text/css" />
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
    <style>
    .passwords { font-family: Consolas,monaco,monospace; }
    </style>
    
    <script src="js/core.js" type="text/javascript"></script>
    <script src="js/speak.js" type="text/javascript"></script>
</head>
<body>
<br><br>
    Generated password:<br />
    <textarea class="passwords" readonly="readonly" rows="1" cols="90"><?= $password; ?></textarea>

	<form method="GET" action="pwd.php">

    <div class="well"><div id="speakme" class="passwords"></div></div>

    <div class="form-group">
      <label name="password_length">Password length:</label> 
      <select id="password_length" name="password_length" class="form-control selectpicker" onChange="checkAllowLonger();">
<?php for($i=3; $i <= 30; $i++) { ?>     
        <option <?= ($i == $len) ? "selected=\"selected\"" : "" ?>><?= $i ?></option>
<?php } ?>      
      </select>
    </div>
    
    <div class="form-group">
      <label for="readable">Make Readable</label>
      <input type="checkbox" name="readable" id="readable" value="true" onClick="checkAllowLonger();" <?= (isset($_REQUEST['readable']) && $_REQUEST['readable'] == 'true') ? "checked='checked'" : ""; ?> />
    </div>  

    <div class="form-group" id="clonger" style="display: none;">
      <label for="longer">Force use of longer words</label>
      <input type="checkbox" name="longer" id="longer" value="true" <?= (isset($_REQUEST['longer']) && $_REQUEST['longer'] == 'true') ? "checked='checked'" : ""; ?> />      
    </div>  
    
    <input class="btn btn-info btn-small" type="submit" name="submit" value="Generate another password" /><br /><br />
  </form>
  <div id="allow-speak" style="display: none;"><button class="btn btn-success btn-small" onClick="speak('<?= $sayme; ?>', true);">Speak</button></div>
  <span id="stop"><button class="btn btn-danager btn-small" onClick="stop();">Stop Speaking!</button></span>
<?php
if (isset($_REQUEST['readable']) && $_REQUEST['readable'] == 'true') {
?>
<script>  
  var lg = document.getElementById('clonger').style;  
  lg.display = '';
</script>  
<?php } ?>  
</body>
</html>
