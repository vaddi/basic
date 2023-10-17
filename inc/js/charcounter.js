/* Textarea Counter
 * A tiny function to add a character counter under textareas. Call by CSS-Class or ID.
 * Usage: textareaCounter( '#msg', 160 ); // simple
 *
 * @param int			id		The id of the textbox (id or class with "#" or "." notation)
 * @param int length		The maximum length of textarea content
 * @param int 	warn		Wen reach this, the Counternumber will turn color to yellow
 */
//Loaded in /inc/form/formular.php
function textareaCounter( id, length, warn ) {
	if( length === undefined ) length = 80;
	if( warn === undefined ) warn = length / 100 * 15;

	let idPrefix = '_feedback';
	let amountClass = 'amount';
  
  let element = document.getElementById( id );
  
  if( element === null ) {
    return false;
  }

	// on reload, get old data and subtract from amount
  if( element !== null && element.value !== undefined && element.value.length > 0 ) temp_length = ( length - element.value.length );
  	else temp_length = length;

  // create div if not exist
  if( document.getElementById( id + idPrefix ) == null ) {
  	element.insertAdjacentHTML('afterend', '<div id="' + id.substring(0, id.length) + idPrefix + '" class="pull-right" style="color:#ccc;"><span class="' + amountClass + '">' + temp_length + '</span> Zeichen verbleibend</div>' );
    element.setAttribute( 'maxlength', length )
  }

  element.addEventListener( "keyup", function( event ) {

  	var temp_length = element.getAttribute( 'maxlength' );
    var text_length = element.value.length;
    var text_remaining = temp_length - text_length;

    document.getElementById( id + idPrefix ).innerHTML = '<span class="' + amountClass + '">' + text_remaining + '</span>' + ' Zeichen verbleibend';

		if( text_remaining <= 0 ) {
      document.querySelectorAll( '.' + amountClass )[0].setAttribute( 'style','color: #F00;' );
		} else if( text_remaining < warn ) {
      document.querySelectorAll( '.' + amountClass )[0].setAttribute( 'style','color: #aa0;' );
		} else {
      document.querySelectorAll( '.' + amountClass )[0].setAttribute( 'style','color: #aaa;' );
		}
  });
}
