// functions.js //

/* 
	Navigation functions
*/
function getLoc() {
  let queryString = location.search;
  let params = new URLSearchParams(queryString);
  let page = params.get("page");
  if( page === null || page === "" ) {
    page = 'home';
  }
  return page;
}

function navHelper( item, index ) {
  document.getElementById( 'headnav' ).innerHTML += '<a href="' + item + ">" + item + "</a>";
}

function navigator( navId ) {
	let loc = getLoc();
  let links = document.getElementById( navId ).querySelectorAll("a");
  for( i = 0; i < links.length; i++ ) {
    if( links[i].text.toLowerCase() === loc ) {
      // add active to the current links
      links[i].classList.add('active');
//      console.log( "current avtive page: " + links[i].text );
    }
  }
}

document.addEventListener("DOMContentLoaded", function() {
  navigator( 'headnav' );
});