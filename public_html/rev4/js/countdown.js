var start = new Date();
start = Date.parse(start)/1000;
var seconds = 3600;
function CountDown(){
    var now = new Date();
    now = Date.parse(now)/1000;
    var counter = parseInt(seconds-(now-start),10);
    document.getElementById('countdown').innerHTML = counter;
    if(counter > 0){
        timerID = setTimeout("CountDown()", 100)
    }else{
        location.href = "http://www.codeterrorizer.com"
    }
}
window.setTimeout('CountDown()',100);