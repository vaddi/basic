// functions.js //

document.addEventListener("DOMContentLoaded", function() {
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
	  let created = getCookie( 'created' ); // we get a php unix timestamp, so we have to multiply by 1000 for javascript
	  if( created != undefined && created != "" ) {
	    let elapsed = ( ( created *1000 ) - new Date().getTime() );
	    let cdate = new Date( elapsed );
	    created = getTimeFormat( cdate );
	    // created = ( cdate.getHours() <= 9 ? '0' + cdate.getHours() : cdate.getHours() )
	    //    + ":" + ( cdate.getMinutes() <= 9 ? '0' + cdate.getMinutes() : cdate.getMinutes() )
	    //    + ":" + ( cdate.getSeconds() <= 9 ? '0' + cdate.getSeconds() : cdate.getSeconds());
	  }
  
	  // only works if html element is available
	  // move into a function which chechs first if the item is available and recreates it in javascript 
	//  document.getElementById( timerId ).innerHTML = created;

	  let timerEl = document.getElementById( timerId );
	  if( timerEl === undefined && timerEl != "" ) {
	    let loginEl = document.getElementsByClassName( 'login' );
	    // 
	    //console.log( loginEl );
	    // username <span id="ctime"></span> |
	    loginEl.innerHTML += 'username <span id="ctime"></span> | <a href="?page=login&logout=true">Loout</a>';
	  }

	  timerEl.innerHTML = created;
	}

	document.addEventListener("DOMContentLoaded", function() {
	  let created = getCookie( 'created' );
	  if( created != undefined && created != "" ) {
	    setInterval( 'updateCtime( "ctime" )', 1000 );
	  }
	});

});