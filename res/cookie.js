// http://techpatterns.com/downloads/javascript_cookies.php
"use strict";
function Set_Cookie( name, value, expires, path, domain, secure ){
	if(!path) path=window.location.pathname.substr(0,window.location.pathname.lastIndexOf('/')+1);// Added this line
	var today = new Date();
	today.setTime( today.getTime() );
	if ( expires ){
		expires = expires * 1000 * 60 * 60 * 24;
	}
	var expires_date = new Date( today.getTime() + (expires) );
	document.cookie = name + "=" +escape( value ) +
		( ( expires ) ? ";expires=" + expires_date.toGMTString() : "" ) +
		( ( path ) ? ";path=" + path : "" ) +
		( ( domain ) ? ";domain=" + domain : "" ) +
		( ( secure ) ? ";secure" : ";samesite=strict" );
}
function Delete_Cookie( name, path ) {// edited, don't delete non-existent cookies
	if(!path) path=window.location.pathname.substr(0,window.location.pathname.lastIndexOf('/')+1);// Added this line
	document.cookie = name + "=" +
		( ( path ) ? ";path=" + path : "") +
		";domain=" + document.domain +
		";expires=Thu, 01-Jan-1970 00:00:01 GMT";
}
