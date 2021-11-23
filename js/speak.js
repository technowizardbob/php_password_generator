var voices = [];
var default_voice = {};
var voice_canceld = false;

const getVoices = () => {
  return new Promise((resolve) => {
    var get_voices = window.speechSynthesis.getVoices();
    if (get_voices.length) {
      resolve(get_voices);
      return;
    }
    window.speechSynthesis.onvoiceschanged = () => {
      get_voices = window.speechSynthesis.getVoices();
      resolve(get_voices);
    };
  });
};

function has_voice() {
    let my_voice = getVoices();
    my_voice.then(function(value) {
        value.forEach(function (item, index) {
            if (item["lang"] === "en-US") {
                default_voice = value[index];
                document.getElementById("allow-speak").style.display = "block";
            }
        });
    });
}

if(! (navigator.userAgent.toLowerCase().indexOf('firefox') > -1)){
    has_voice();
}

var speechUtteranceChunker = function (utt, settings, callback) {
    settings = settings || {};
    let chunkLength = settings && settings.chunkLength || 160;
    let pattRegex = new RegExp('^.{' + Math.floor(chunkLength / 2) + ',' + chunkLength + '}[\.\!\?\,]{1}|^.{1,' + chunkLength + '}$|^.{1,' + chunkLength + '} ');
    let txt = (settings && settings.offset !== undefined ? utt.text.substring(settings.offset) : utt.text);
    let chunkArr = txt.match(pattRegex);

    if (chunkArr[0] !== undefined && chunkArr[0].length > 2) {
        let chunk = chunkArr[0];
        let newUtt = new SpeechSynthesisUtterance(chunk);
        for (let myprop in utt) {
            switch (myprop) {
                case "voice": case "volume": case "rate": case "pitch": case "lang":
                newUtt[myprop] = utt[myprop];
                console.warn(myprop);
                break;
            }
        }
        newUtt.onend = function () {
            if (voice_canceld) {
                return;
            }
            settings.offset = settings.offset || 0;
            settings.offset += chunk.length - 1;
            speechUtteranceChunker(utt, settings, callback);
        };
        console.log(newUtt); //IMPORTANT!! Do not remove: Logging the object out fixes some onend firing issues.
        //placing the speak invocation inside a callback fixes ordering and onend issues.
        setTimeout(function () {
            window.speechSynthesis.speak(newUtt);
        }, 0);
    } else {
        //call once all text has been spoken...
        if (callback !== undefined) {
            callback();
        }
    }
};

var stop = function(me) {
    voice_canceld = true;
    window.speechSynthesis.cancel();
    voice_done();
};

var pause_voice = function(me) {
    window.speechSynthesis.pause();
    document.getElementById(me).innerHTML = `<button onclick="resume_voice('${me}');">Resume</button>`;
};

var resume_voice = function(me) {
    window.speechSynthesis.resume();
    document.getElementById(me).innerHTML = `<button onclick="pause_voice('${me}');">Pause</button>`;
};

/* end of voice */

function voice_done() {
	document.getElementById('stop').style.display='none'; /* Hide me */
}

function speak_all(mytext) {    
    let mymsg = new SpeechSynthesisUtterance();
    mymsg.voice = default_voice;
    mymsg.volume = 0.5; // From 0 to 1
    mymsg.rate = 0.8; // From 0.1 to 10
    mymsg.pitch = 1; // From 0 to 2
    mymsg.lang = 'en-US';
    mymsg.text = mytext;
    speechUtteranceChunker(mymsg, {}, voice_done);
}


function sethtml(id, text) {
    let el = document.getElementById(id);
    el.innerHTML = text;
}
      

/* This allows the computer to speak */
function speak(tts, talk) {
  stopping = false;
  document.getElementById('stop').style.display=''; /* Show me */
  speechSynthesis.cancel();
  
  tts = String(tts);
  let text = '';
  let see = '';
  let in_word = false;
  
    
  for (let i=0;i <= tts.length; i++) { 
    let c = tts.substr(i, 1);
    
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
    text += ",";
  }
  sethtml('speakme', see);
  if (talk === true) {
    speak_all(text);
  }
}
