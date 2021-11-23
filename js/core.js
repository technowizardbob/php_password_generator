function checkAllowLonger() {
  let ps = document.getElementById('password_length').value;
  let lg = document.getElementById('clonger').style;
  let isReadable = document.getElementById('readable').checked;
  
  if (isReadable === true && ps > 8) {
    lg.display = '';
  } else {
    lg.display = 'none';
  }
}
