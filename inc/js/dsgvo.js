/* 
	Simple DSGVO javascript function
*/

// Show only on this pages (empty = all)
//let pages = [];
let pages = [];

let dsgvostyles = `
/*Cookie Consent Begin*/
#cookieConsent {
    background-color: rgba(20,20,20,0.8);
    min-height: 26px;
    font-size: 14px;
    color: #ccc;
    line-height: 26px;
    padding: 8px 0 8px 30px;
    font-family: "Trebuchet MS",Helvetica,sans-serif;
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    display: none;
    z-index: 9999;
}
#cookieConsent a {
    color: #4B8EE7;
    text-decoration: none;
}
#closeCookieConsent {
    float: right;
    display: inline-block;
    cursor: pointer;
    height: 20px;
    width: 20px;
    margin: -10px 0 0 0;
    font-weight: bold;
}
#closeCookieConsent:hover {
    color: #FFF;
}
a.cookieConsentOK {
    background-color: #F1D600;
    color: #000;
    display: inline-block;
    border-radius: 5px;
    padding: 0 20px;
    cursor: pointer;
    float: right;
    margin: 0 60px 0 10px;
}
.cookieConsentContact a.cookieConsentOK { padding: 15px 20px; }
a.cookieConsentOK:hover,
.cookieConsentContact a.cookieConsentOK:hover {
    background-color: #E0C91F;
}
/*Cookie Consent End*/
`;

function renderMessage() {
  // add styles to head
  let styleSheet = document.createElement( "style" );
  styleSheet.type = "text/css";
  styleSheet.innerText = dsgvostyles;
  document.head.appendChild( styleSheet );
  // create html element and append after 
  element  = '<div id="cookieConsent">';
  element += '<div id="closeCookieConsent">x</div>';
  element += 'This website uses cookies to verify the captcha in the contact form. <a href="?page=imprint#dataprotection">More About</a>. <a class="cookieConsentOK">I have understood</a>';
  element += '</div>';
  document.getElementById( 'content' ).insertAdjacentHTML('afterend', element );
}

document.addEventListener("DOMContentLoaded", function() {
  let cookieValue = document.cookie;
  if( cookieValue != undefined ) {
    cookieValue = cookieValue.split('; ')
    .find(row => row.startsWith('commitcookie'));
    if( cookieValue != undefined ) {
      cookieValue = cookieValue.split('=')[1];
    }
  }
  
  if( localStorage.getItem( "commitcookie" ) != "true" || cookieValue != "true" ) {
    let element = document.getElementById( "cookieConsent" );
    renderMessage();
    // show / add display block to cookieConsent
    document.getElementById( 'cookieConsent' ).setAttribute( 'style','display: block;' );
  }
  // Close Btn
  let cookieConsentOK = document.querySelectorAll( '.cookieConsentOK' );
  if( cookieConsentOK.length > 0 ) {
    document.getElementById( "closeCookieConsent" ).addEventListener( "click", function( event ) {
      // hide cookieConsent element
      document.getElementById( 'cookieConsent' ).setAttribute( 'style','display: none;' );
    });
  }

  // Commit Btn
  if( cookieConsentOK.length > 0 ) {
    
    if( cookieConsentOK[0] != null ) {
      cookieConsentOK[0].addEventListener( "click", function( event ) {
        localStorage.setItem( "commitcookie", "true" );
        const expirationDate = new Date();
        expirationDate.setFullYear(expirationDate.getFullYear() + 1 ); // expires in one year
        let expires = "; expires=" + expirationDate.toUTCString();
        let maxAge = "; max-age=" + 365*24*60*60 + "; " + expires; // 1 year in seconds
        document.cookie = "commitcookie=true" + maxAge;
    
        let queryString = location.search;
        let params = new URLSearchParams(queryString);
        let page = params.get("page");
        if( page === null || page === "" ) {
          page = 'home';
        }
    
        // hide the cookieConsent element
        document.getElementById( 'cookieConsent' ).setAttribute( 'style','display: none;' );
      
        // if currentpage == contact -> reload the page
        if( page == 'kontakt' || page == 'contact' ) window.location.reload(); // reload if clicked on contact page
      });
    }
    
    if( cookieConsentOK[1] != null ) {  
      cookieConsentOK[1].addEventListener( "click", function( event ) {
        localStorage.setItem( "commitcookie", "true" );
        const expirationDate = new Date();
        expirationDate.setFullYear(expirationDate.getFullYear() + 1 ); // expires in one year
        let expires = "; expires=" + expirationDate.toUTCString();
        let maxAge = "; max-age=" + 365*24*60*60 + "; " + expires; // 1 year in seconds
        document.cookie = "commitcookie=true" + maxAge;
  
        let queryString = location.search;
        let params = new URLSearchParams(queryString);
        let page = params.get("page");
        if( page === null || page === "" ) {
          page = 'home';
        }
  
        // hide the cookieConsent element
        document.getElementById( 'cookieConsent' ).setAttribute( 'style','display: none;' );
    
        // if currentpage == contact -> reload the page
        if( page == 'kontakt' || page == 'contact' ) window.location.reload(); // reload if clicked on contact page
      });
    }
  }

});