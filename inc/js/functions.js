// functions.js //

// read element from cookie
// https://www.w3schools.com/js/js_cookies.asp
function getCookie( cname ) {
  let name = cname + "=";
  let decodedCookie = decodeURIComponent( document.cookie );
  let ca = decodedCookie.split( ';' );
  for( let i = 0; i <ca.length; i++ ) {
    let c = ca[i];
    while( c.charAt(0) == ' ' ) {
      c = c.substring(1);
    }
    if( c.indexOf( name ) == 0 ) {
      return c.substring(name.length, c.length);
    }
  }
  return "";
}

// translate the timeformat
function getTimeFormat( timeStamp ) {
  let result = undefined;
  let cdate = new Date( timeStamp );
  if( timeStamp != undefined && timeStamp != "" ) {
    result = ( cdate.getHours() <= 9 ? '0' + cdate.getHours() : cdate.getHours() )
       + ":" + ( cdate.getMinutes() <= 9 ? '0' + cdate.getMinutes() : cdate.getMinutes() )
       + ":" + ( cdate.getSeconds() <= 9 ? '0' + cdate.getSeconds() : cdate.getSeconds());
  } else {
    cdate = new Date();
    result = ( cdate.getHours() <= 9 ? '0' + cdate.getHours() : cdate.getHours() )
    + ":" + ( cdate.getMinutes() <= 9 ? '0' + cdate.getMinutes() : cdate.getMinutes() )
    + ":" + ( cdate.getSeconds() <= 9 ? '0' + cdate.getSeconds() : cdate.getSeconds());
  }
  return result;
}

// update the Session time in Site header (id ctime)
function updateCtime( timerId ) {
	// get data from cookie
  let created = getCookie( 'created' );
	let lifetime = getCookie( 'lifetime' );
	// convert cookie time to unixtestamp
	let created_ut = created *1000;
	// converst created into date
  if( created != undefined && created != "" ) {
		// get elapsed time in seconds
    let elapsed = ( ( created *1000 ) - new Date().getTime() );
		//let elapsed = ( created *1000 ) - ( lifetime );
    let cdate = new Date( elapsed );
    created = getTimeFormat( cdate );
		// console.log( 'lifetime: ' + lifetime );
		// console.log( 'created: ' + created );
		// console.log( 'elapsed: ' + elapsed );
  }
	
//	console.log( window.navigator );
//	console.log( getLoc() );
	
	// get html timer element
  let timerEl = document.getElementById( timerId );
  if( timerEl === undefined && timerEl != "" ) {
		// create login element if not exists
    let loginEl = document.getElementsByClassName( 'login' );
    loginEl.innerHTML += 'username <span id="ctime"></span> | <a href="?page=login&logout=true">Loout</a>';
  }
	let uts_now = new Date().getTime();
	let elapsed = uts_now - created_ut;
	// console.log( 'created: ' + created );
	// console.log( 'elapsed: ' + elapsed );
	if( elapsed <= 72000 ) {
		// if created is lower than 20min colorize yellow
		timerEl.style.color = 'orange';
	} else if( elapsed <= 36000 ) {
		// if created is lower than 10min colorize red
		timerEl.style.color = 'red';
	} else {
		timerEl.style.color = 'black';
	}
	// console.log( 'created: ' + created );
	// console.log( 'elapsed: ' + elapsed );
  timerEl.innerHTML = created;
}

// initial update event
document.addEventListener("DOMContentLoaded", function() {
  let created = getCookie( 'created' );
  if( created != undefined && created != "" ) {
    setInterval( 'updateCtime( "ctime" )', 1000 );
  }
});