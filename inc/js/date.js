function uhrzeit(anzeige) {

Now = new Date();

var TagMonat = Now.getDate();
var TagNum = Now.getDay();
var Tag = new Array("So","Mo","Di","Mi","Do","Fr","Sa");
var MonthNum = Now.getMonth();
var Monat = new Array("Jan","Feb","M&auml;r","Apr","Mai","Jun","Jul","Aug","Sep","Okt","Nov","Dez");
//var Monat = new Array("1","2","3","4","5","6","7","8","9","10","11","12");
var Jahr = Now.getYear();
if (Jahr<2000) Jahr=Jahr+1900;

var Stunde  = Now.getHours();
var Minute  = Now.getMinutes();
var Sekunde = Now.getSeconds();

document.getElementById("uhr").innerHTML = 
	
	// Dayname
	Tag[TagNum] + " " +
	
	// Date MM.DD.YYYY (check n > 9 = 0n)
	((TagMonat<=9)?"0" + TagMonat:TagMonat) + "." +
	( (Monat[MonthNum]<=9) ? "0" + Monat[MonthNum] : Monat[MonthNum] ) + "." +
	Jahr + " " +
	
	// Time HH:MM:SS (check n > 9 = 0n)
	//  ((Sekunde % 2 == 0) ? "." : "·")
	Stunde + ":" + 
	((Minute<=9)?"0" + Minute:Minute) + ":" + 
	((Sekunde<=9)?"0" + Sekunde:Sekunde);

}

document.addEventListener("DOMContentLoaded", function(){
  uhrzeit( 'jetzt' ); 
  setInterval( 'uhrzeit()', 1000 );
});
