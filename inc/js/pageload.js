/***********************************************************
                         Pageload-Skript
************************************************************/

// inspired by: https://stackoverflow.com/a/14343144/5208166
let timerStart = Date.now();

// DOM is ready for manipulation (DOM ready, like jQuery document.ready)
document.addEventListener("DOMContentLoaded", function() {
	let readytime = ( Date.now() - timerStart ) / 1000;
	//console.log( "Time until DOMready: ", readytime );
	document.getElementById("pageReadyTime").innerHTML = readytime;
});

// DOM and all Content is fully loaded (DOM loaded, like jQuery window.load)
window.onload=function(){
	let loadtime = ( Date.now() - timerStart ) / 1000;
	//console.log( "Time until everything loaded: ", loadtime );
	document.getElementById("pageLoadTime").innerHTML = loadtime;
}
