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
    <!-- <script src="analytics.js" type="text/javascript"></script> -->
    <script>
var stopping = true;      
function stop() {
  document.getElementById('stop').style.display='none'; /* Hide me */
  stopping = true;      
  setTimeout(function(){ speechSynthesis.cancel(); },600); 
}
      
function sethtml(id, text) {
    var el = document.getElementById(id);
    el.innerHTML = text;
}
      

/* This allows the computer to speak */
function speak(tts, talk) {
  stopping = false;
  document.getElementById('stop').style.display=''; /* Show me */
  speechSynthesis.cancel();
  
  tts = String(tts);
  var text = '';
  var see = '';
  var in_word = false;
  
    
  for (i=0;i <= tts.length; i++) { 
    var c = tts.substr(i, 1);
    
    if (c == '<') { in_word = true; text += 'the word ,.'; see += ' '; continue; }
    if (c == '>') { in_word = false; text += ' '; see += '  '; continue; }
    if (in_word === true) {
       text += c;
       see += c;
       continue;
    }
    
    if (c == '[') { see += ' ['; }    
    if (c == ']') { text+= '.'; see += '] '; }
    
    if (c == '1') { text += ' the number ,one '; see += 'the number one '; }
    if (c == '2') { text += ' the number ,two '; see += 'the number two '; }
    if (c == '3') { text += ' the number ,three '; see += 'the number three '; }
    if (c == '4') { text += ' the number ,four '; see += 'the number four '; }
    if (c == '5') { text += ' the number ,five '; see += 'the number five '; }
    if (c == '6') { text += ' the number ,six '; see += 'the number six '; }
    if (c == '7') { text += ' the number ,seven '; see += 'the number seven '; }
    if (c == '8') { text += ' the number ,eight '; see += 'the number eight '; }
    if (c == '9') { text += ' the number ,nine '; see += 'the number nine '; }
    if (c == '0') { text += ' the number ,zero '; see += 'the number zero '; }
    if (c == '!') { text += ' exclamation ,mark '; see += 'exclamation mark '; }
    if (c == '@') { text += ' at ,sign '; see += ' at sign '; }
    if (c == '#') { text += ' pound ,sign, '; see += ' pound sign '; }
    if (c == '$') { text += ' dollar ,sign, '; see += 'dollar sign '; }
    if (c == '%') { text += ' percentage ,sign '; see += 'percentage sign '; }
    if (c == '&') { text += ' amper ,sign '; see += 'amper sign '; }
    if (c == '*') { text += ' star ,sign '; see += 'star sign '; }
    if (c == '.') { text += ' period ,mark '; see += 'period mark '; }  
    if (c == 'a') { text += ' letter ,a '; see += ' a '; }
    if (c == 'b') { text += ' letter ,b '; see += ' b '; }
    if (c == 'c') { text += ' letter ,c '; see += ' c '; }
    if (c == 'd') { text += ' letter ,d '; see += ' d '; }
    if (c == 'e') { text += ' letter ,e '; see += ' e '; }
    if (c == 'f') { text += ' letter ,f '; see += ' f '; }
    if (c == 'g') { text += ' letter ,g '; see += ' g '; }
    if (c == 'h') { text += ' letter ,h '; see += ' h '; }
    if (c == 'i') { text += ' letter ,i '; see += ' i '; }
    if (c == 'j') { text += ' letter ,j '; see += ' j '; }
    if (c == 'k') { text += ' letter ,k '; see += ' k '; }
    if (c == 'l') { text += ' letter ,l '; see += ' l '; }
    if (c == 'm') { text += ' letter ,m '; see += ' m '; }
    if (c == 'n') { text += ' letter ,n '; see += ' n '; }
    if (c == 'o') { text += ' letter ,o '; see += ' o '; }
    if (c == 'p') { text += ' letter ,p '; see += ' p '; }
    if (c == 'q') { text += ' letter ,q '; see += ' q '; }
    if (c == 'r') { text += ' letter ,r '; see += ' r '; }
    if (c == 's') { text += ' letter ,s '; see += ' s '; }  
    if (c == 't') { text += ' letter ,t '; see += ' t '; }
    if (c == 'u') { text += ' letter ,u '; see += ' u '; }
    if (c == 'v') { text += ' letter ,v '; see += ' v '; }
    if (c == 'w') { text += ' letter ,w '; see += ' w '; }
    if (c == 'x') { text += ' letter ,x '; see += ' x '; }
    if (c == 'y') { text += ' letter ,y '; see += ' y '; }
    if (c == 'z') { text += ' letter ,z '; see += ' z '; }
    if (c == 'A') { text += ' uppercase letter ,A '; see += 'uppercase A '; }
    if (c == 'B') { text += ' uppercase letter ,B '; see += 'uppercase B '; }
    if (c == 'C') { text += ' uppercase letter ,C '; see += 'uppercase C '; }
    if (c == 'D') { text += ' uppercase letter ,D '; see += 'uppercase D '; }
    if (c == 'E') { text += ' uppercase letter ,E '; see += 'uppercase E '; }
    if (c == 'F') { text += ' uppercase letter ,F '; see += 'uppercase F '; }
    if (c == 'G') { text += ' uppercase letter ,G '; see += 'uppercase G '; }
    if (c == 'H') { text += ' uppercase letter ,H '; see += 'uppercase H '; }
    if (c == 'I') { text += ' uppercase letter ,I '; see += 'uppercase I '; }
    if (c == 'J') { text += ' uppercase letter ,J '; see += 'uppercase J '; }
    if (c == 'K') { text += ' uppercase letter ,K '; see += 'uppercase K '; }
    if (c == 'L') { text += ' uppercase letter ,L '; see += 'uppercase L '; }
    if (c == 'M') { text += ' uppercase letter ,M '; see += 'uppercase M '; }
    if (c == 'N') { text += ' uppercase letter ,N '; see += 'uppercase N '; }
    if (c == 'O') { text += ' uppercase letter ,O '; see += 'uppercase O '; }
    if (c == 'P') { text += ' uppercase letter ,P '; see += 'uppercase P '; }
    if (c == 'Q') { text += ' uppercase letter ,Q '; see += 'uppercase Q '; }
    if (c == 'R') { text += ' uppercase letter ,R '; see += 'uppercase R '; }
    if (c == 'S') { text += ' uppercase letter ,S '; see += 'uppercase S '; }  
    if (c == 'T') { text += ' uppercase letter ,T '; see += 'uppercase T '; }
    if (c == 'U') { text += ' uppercase letter ,U '; see += 'uppercase U '; }
    if (c == 'V') { text += ' uppercase letter ,V '; see += 'uppercase V '; }
    if (c == 'W') { text += ' uppercase letter ,W '; see += 'uppercase W '; }
    if (c == 'X') { text += ' uppercase letter ,X '; see += 'uppercase X '; }
    if (c == 'Y') { text += ' uppercase letter ,Y '; see += 'uppercase Y '; }
    if (c == 'Z') { text += ' uppercase letter ,Z '; see += 'uppercase Z '; }
    text += ' .';
  }
  sethtml('speakme', see);
  if (talk === true) {
    speekResponse(text);
  }
}

var voices = window.speechSynthesis.getVoices();

var sayit = function (ending) {
    if (stopping === true) { return false; }
    
    /* create msg speech object for speak function to use */
    var msg = new SpeechSynthesisUtterance();
    var voices = window.speechSynthesis.getVoices();
    msg.voice = voices[1]; // see: http://html5-examples.craic.com/google_chrome_text_to_speech.html
    msg.voiceURI = 'native';
    msg.volume = 1; // 0 to 1
    msg.rate = .7; // 0.1 to 10
    msg.pitch = 1; //0 to 2
    msg.lang = 'en-US';
    return msg;
}
var mmm;

var speekResponse = function (text) {
    speechSynthesis.cancel(); // if it errors, this clears out the error.
    if (stopping === true) { return false; }

    var sentences = text.split(".");
    for (var i=0; i < sentences.length; i++) {
        var toSay = sayit();
        toSay.text = sentences[i];
        speechSynthesis.speak(toSay);
    }
    mmm = toSay;
    mmm.addEventListener("end", hideStop, false);
}

function hideStop() {
  mmm.removeEventListener("end", hideStop, false);
  document.getElementById('stop').style.display='none'; /* Hide me */
}

function checkAllowLonger() {
  var ps = document.getElementById('password_length').value;
  var lg = document.getElementById('clonger').style;
  var isReadable = document.getElementById('readable').checked;
  
  if (isReadable === true && ps > 8) {
    lg.display = '';
  } else {
    lg.display = 'none';
  }
}
    </script>
    
</head>
<body onLoad="speak('<?= $sayme; ?>', false); stop();">
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
  <button class="btn btn-success btn-small" onClick="speak('<?= $sayme; ?>', true);">Speak</button>
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
