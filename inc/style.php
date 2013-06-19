<?php
// Chrome's css columns break things
header('Content-type: text/css');
$expires=86400;//24 hrs
header("Pragma: public");
header("Cache-Control: maxage=".$expires);
header('Expires: '.gmdate('D, d M Y H:i:s',time()+$expires).' GMT');
if(isset($_GET['colors'])){//10 years long enough for a cookie to stick arround?
	setcookie("colors",$_GET['colors'],time()+(60*60*24*365*10),"/",$_SERVER['SERVER_NAME']);
	$COLORS=explode('x',$_GET['colors']);
}
else{
	if(!isset($_COOKIE["colors"])){
		$COLORS=array('3c9642','3c7796');
	}
	else{
		$COLORS=explode('x',$_COOKIE["colors"]);
	}
}
$BG_COLOR=$COLORS[0];
$LK_COLOR=$COLORS[1];
?>
@-moz-keyframes fadein { /* Firefox */
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}
@-webkit-keyframes fadein { /* Chrome and Safari */
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}
@-o-keyframes fadein { /* Opera (if they ever add this) */
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}
@-ms-keyframes fadein { /* IE 10 */
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}
@keyframes fadein { /* Standard */
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}

body, #header, #message table, .side_box, .side_box h2, #preview, #preview_links img, #preview_img p, #preview h2, .box, .box img, .box pre.border, .box h2, #footer, #debug pre{
	-moz-transition-property: background, border, color;
	-moz-transition-duration: 0.8s;
	-o-transition-property: background, border, color;
	-o-transition-duration: 0.8s;
	-webkit-transition-property: background, border, color;
	-webkit-transition-duration: 0.8s;
}
body {
	margin: 0;
	padding: 1em;
	font: 12px verdana, arial, helvetica, sans-serif;
	font-size: 12px;
	background: url("images/powered_by_linux.png") bottom right no-repeat fixed #<?php echo $BG_COLOR; ?>;
}

a {
	color: #<?php echo $BG_COLOR; ?>;
	-moz-transition-property: color;
	-moz-transition-duration: 0.8s;
	-o-transition-property: color;
	-o-transition-duration: 0.8s;
	-webkit-transition-property: color;
	-webkit-transition-duration: 0.8s;
	transition-property: color;
	transition-duration: 0.8s;
}

a:hover {
	color: #<?php echo $LK_COLOR; ?>;
}

.tool{
	position: relative;
	display: inline-block;
}

.tool:hover .tip{
	display: block;
	text-decoration: none !important;
	font-variant: normal;
	font-weight: normal;
	-moz-animation-name: fadein; /* Firefox */
	-webkit-animation-name: fadein; /* Chrome and Safari */
	-o-animation-name: fadein; /* Opera (if they ever add this) */
	-ms-animation-name: fadein; /* IE 10 */
	animation-name: fadein; /* Standard */
}

.tool .tip:hover{
	display: none;
}

.tool .tip{
	background-color: rgba(0, 0, 0, 0.75);
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-o-border-radius: 5px;
	color: #ffffff;
	display: none;
	left: 105%;
	padding: 5px;
	font-family: sans-serif;
	font-size: 12px;
	position: absolute;
	bottom: 50%;
	z-index: 1;
	white-space: nowrap;
	text-decoration: none;
	-moz-animation-duration: 0.8s; /* Firefox */
	-webkit-animation-duration: 0.8s; /* Chrome and Safari */
	-o-animation-duration: 0.8s; /* Opera (if they ever add this) */
	-ms-animation-duration: 0.8s; /* IE 10 */
	animation-duration: 0.8s; /* Standard */
}

#container {
	width: 735px;
	height: 100%;
	margin: auto;
	padding: 0.5em;
	text-align: left;
	background: #FFFFFF;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-o-border-radius: 5px;
}

#header {
	height: 75px;
	margin: 0 0 0.5em 0;
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	background: url("images/logo.png") no-repeat scroll left center #<?php echo $BG_COLOR; ?>;
	-moz-border-radius: 5px 5px 0 0;
	border-radius: 5px 5px 0 0;
	-webkit-border-radius: 5px 5px 0 0;
	-o-border-radius: 5px 5px 0 0;
}

.tab {
	height: 25px;
	float: right;
	background: #ffffff;
	padding: 2px 2px 0px 2px;
	margin: 1.4em 0.5em 0.5em 0;
	font-size: 16px;
	-moz-border-radius: 5px;
	border-radius: 5px;
	-webkit-border-radius: 5px;
	-o-border-radius: 5px;
	text-transform: capitalize;
}

.column{
	float: left;
	width: 235px;
	margin: 0 8px 0 0;
	paddin: 0;
}

#nojs{
	text-align: center;
}

.message {
	border: 1px solid #aa0000;
	padding: 0;
	margin-bottom: 0.5em;
	border-radius: 5px 5px 0 0;
	-o-border-radius: 5px 5px 0 0;
	-moz-border-radius: 5px 5px 0 0;
	-webkit-border-radius: 5px 5px 0 0;
	height: 0;
	/*overflow: hidden; what was this even her for?*/
	-moz-transition-property: height;
	-moz-transition-duration: 0.8s;
	-o-transition-property: height;
	-o-transition-duration: 0.8s;
	-webkit-transition-property: height;
	-webkit-transition-duration: 0.8s;
}

.message.ie{
	background: url("images/best_viewed_in_firefox.png") bottom right no-repeat scroll #ffffff;
	padding-bottom: 20px;
}

.message h2 {
	border: 1px solid #ff0000;
	text-indent: 0.5em;
	font-size: 12px;
	font-variant: small-caps;
	color: #ffffff;
	margin: 0;
	padding: 0.5em;
	background: #ff0000;
}

.message h2 .close{
	float: right;
	border: 1px #ffffff solid;
	border-radius: 3px;
	text-indent: 0;
}

.message div {
	text-align: center;
	margin: 1em;
}

.message table{
	background-color: #<?php echo $BG_COLOR; ?>;
	border-radius: 5px;
	-o-border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	margin: 0;
	width: 100%;
}

.message td,th{
	background-color: #ffffff;
}

.message ul{
	margin:0;
}

#debug{
	display: none;
}

#debug pre{
	background-color: #<?php echo $BG_COLOR==383838?'000000':'383838'?>;
	color: #ffffff;
	margin: 0;
	max-height: 500px;
	padding: 10px;
	width: auto;
}

#sidebar {
	width: 258px;
	float: left;
	margin: 0 0 0 0;
}

.icon{
	padding: 16px 16px 0 0;
	background-repeat: no-repeat;
	background-image: url("images/buttons.png");
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	
}

.icon1{
	padding: 50px 151px 0 0;
	background-repeat: no-repeat;
	background-image: url("images/buttons1.jpg");
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	}
	
.pdf-off1{
	background-position: -150px 0;
}

.pdf1{
	background-position: 0 0;
}

.zip-off{
	background-position: 0 0;
}

.zip{
	background-position: -16px 0;
}

.download-off{
	background-position: -32px 0;
}

.download{
	background-position: -48px 0;
}

.pdf-off{
	background-position: 0 -16px;
}

.pdf{
	background-position: -16px -16px;
}

.print-off{
	background-position: -32px -16px;
}

.print{
	background-position: -48px -16px;
}

.view-off{
	background-position: 0 -32px;
}

.view{
	background-position: -16px -32px;
}

.edit-off{
	background-position: -32px -32px;
}

.edit{
	background-position: -48px -32px;
}

.del-off{
	background-position: -64px 0;
}

.del{
	background-position: -64px -16px;
}

.upload{
	background-position: -48px -48px;
}

.upload-off{
	background-position: -32px -48px;
}

.recent{
	background-position: -64px -48px;
}

.recent-off{
	background-position: -64px -32px;
}

.email{
	background-position: -16px -48px;
}

.email-off{
	background-position: 0 -48px;
}

.side_box {
	width: 250px;
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	float: left;
	padding: 0;
	margin: 0 0 0.5em 0.5em;
	-moz-border-radius: 5px 5px 0 0;
	border-radius: 5px 5px 0 0;
	-webkit-border-radius: 5px 5px 0 0;
	-o-border-radius: 5px 5px 0 0;
}

.side_box h2 {
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	text-indent: 0.5em;
	font-size: 12px;
	font-variant: small-caps;
	color: #ffffff;
	margin: 0;
	padding: 0.5em;
	background: #<?php echo $BG_COLOR; ?>;
} 

.side_box input {
	font-size: 12px;
}

div.ie_276228{/* http://support.microsoft.com/kb/276228 */
	margin: 0;
	padding: 0;
}

.side_box select {
	font-size: 12px;
	width: 155px;
}

.side_box select[name="scanner"] option[disabled="disabled"]{
	background-color: yellow;
}

select.title, select.title option{
	text-transform: capitalize;
}

.side_box p {
	margin: 0.5em;
}

.label {
	width: 80px;
	float: left;
	margin: 2px 2px 2px 5px;
}

.control {
	width: 145px;
	float: left;
	margin: 2px;
}

#preview {
	width: 462px;
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	float: left;
	padding: 0;
	margin: 0 0 0.5em 0.5em;
	-moz-border-radius: 5px 5px 0 0;
	border-radius: 5px 5px 0 0;
	-webkit-border-radius: 5px 5px 0 0;
	-o-border-radius: 5px 5px 0 0;
}

#preview p{
	margin:5px;
}

#preview_links img, #preview_img p {
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	text-align:center;
}

#preview_img img{
	width: 450px;
	position: relative;
}

#preview_img p {
	position: relative;
}

img[src="inc/images/blank.png"]{
	background: url("images/preview.png") no-repeat scroll center center transparent;
}

#preview_img img[title="Scanning"], #preview_img img[title="Processing"]{
	background: url("images/loading.gif") no-repeat scroll center center transparent;
	position: absolute;
	left: 0;
	top: 0;
}

#preview h2 {
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	text-indent: 0.5em;
	font-size: 12px;
	font-variant: small-caps;
	color: #ffffff;
	margin: 0;
	padding: 0.5em;
	background: #<?php echo $BG_COLOR; ?>;
}

#scans{
	float: left;
	margin: 0;
	padding: 0;
	width: 100%;
}

#scans.enable{
	-moz-column-count: 3;
	-moz-column-gap: 0;
	-webkit-column-count: 3; 
	-webkit-column-gap: 0;
	column-count: 3;
	column-gap: 0;
	padding-right: 6px;
}

#scans.enable .box{
	display: inline-block;
	float: none;
}

.box, .box-full{
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	float: left;
	padding: 0;
	margin: 0 0 0.5em 0.5em;
	-moz-border-radius: 5px 5px 0 0;
	border-radius: 5px 5px 0 0;
	-webkit-border-radius: 5px 5px 0 0;
	-o-border-radius: 5px 5px 0 0;
}

.box {
	width: 235px;
}

.box-full{
	width: 720px;
}

.box-full img{
	max-width: 708px;
}

.box img {
	border: 1px solid #<?php echo $BG_COLOR; ?>;
}

.box pre.border{
	margin: 5px;
	border: 1px solid #<?php echo $BG_COLOR; ?>;
}

.box h2 {
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	text-align: center;
	font-size: 12px;
	font-variant: small-caps;
	color: #ffffff;
	margin: 0;
	padding: 0.5em;
	background: #<?php echo $BG_COLOR; ?>;
}

.box p {
	margin: 5px;/*5px 10px 5px 5px*/
}

pre{
	overflow: auto;
}

code{
	font-family: monospace;
}

.simplelist{
	list-style: none;
	padding: 0;
	margin: 0 5px;
}

#paper-list ul{
	-moz-column-count: 3;
	-moz-column-gap: 50px;
	/* broken tooltips in opera, messed up borders and broken tooltips in chrome and safari
	-webkit-column-count: 3;
	-webkit-column-gap: 50px;
	column-count: 3;
	column-gap: 50px;*/
	list-style: none;
	padding-right: 40px;
	overflow: visible;
}

#paper-list li, .boxlist{
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	margin-top: -1px;
	position: relative;
	height:20px;
	width:100%;
	display: inline-block;
}

#paper-list .code{
	float: right;
	font-family: monospace;
	position: absolute;
	right: 1px;
	top: 0;
	height: 100%;
	padding-top: 3px;
}

#paper-list a,.simplelist a{
	margin: 1px 0 -2px 1px;
}

#paperForm form{
	float: left;
	width: 50%;
}

#paperForm form.m{
	border-left: 1px solid #<?php echo $BG_COLOR; ?>;
	margin-left: -1px;
}

#text-editor{
	text-align:center;
}

#text-editor #preview_links{
	text-align:left;
}

#text-editor textarea{
	width:714px;
	height:400px;

}

#text-editor input{
	width:350px;
}

#footer{
	clear: both;
	text-align: center;
	height: 20px;
	margin: 0;
	padding: 0;
	border: 5px solid #<?php echo $BG_COLOR; ?>;
	-moz-border-radius: 0 0 5px 5px;
	border-radius: 0 0 5px 5px;
	-webkit-border-radius: 0 0 5px 5px;
	-o-border-radius: 0 05px 5px;
}

#footer p{
	margin: 0;
	padding: 0;
}

/* popup div css */

#popUpDiv #imgur-data{
	text-align:left;
}
#popUpDiv #imgur-data ul{
	list-style: none;
}
#popUpDiv #imgur-data li li{
	margin-left: 40px;
}
#popUpDiv #imgur-data .codes{
	display: inline-block;
	width: 100%;
	border-right: 1px solid black;
}
#popUpDiv #imgur-data .codes h2{
	font-size:12px;
	text-align:center;
}

#popUpDiv #email{
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	border-radius: 5px 5px 0 0;
	overflow: hidden;
	padding: 5px;
}
#popUpDiv #email > h2{
	background-color: #<?php echo $BG_COLOR; ?>;
	color: #ffffff;
	margin: -5px -5px 5px;
	font-size: 15px;
	padding: 0.5em;
}
#popUpDiv #email .security{
	color: #ff0000;
	border: 1px solid #ff0000;
	border-radius: 5px 5px 0 0;
	margin-bottom: 5px;
}
#popUpDiv #email .security h2{
	background-color: #ff0000;
	color: #ffffff;
	text-align: center;
	margin: 0;
	padding-bottom: 5px;
}
#popUpDiv #email .security ul{
	margin-top:0;
	padding-right: 5px;
	text-align:left;
}
#popUpDiv #email form{
	width:265px;
	text-align:left;
	float:left;
}
#popUpDiv #email form .label{
	width:95px;
}
#popUpDiv #email .control input:not([type="checkbox"]){
	width:150px;
}
#popUpDiv #email .help{
	border: 1px solid #<?php echo $BG_COLOR; ?>;
	border-radius: 5px 5px 0 0;
	margin-bottom: 5px;
	float: right;
	width: 138px;
	text-align: left;
}
#popUpDiv #email .help h2{
	background-color: #<?php echo $BG_COLOR; ?>;
	color: #ffffff;
	margin: 0;
	font-size: 15px;
	text-align: center;
	padding: 0.5em;
	font-size: 12px;
}
#popUpDiv #email .help p{
	margin:0;
	padding:5px;
}

/* http://www.pat-burt.com/web-development/how-to-do-a-css-popup-without-opening-a-new-window/ */
#blanket{
	background-color:rgba(17,17,17,0.65);
	position: fixed;
	z-index: 9001;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	-moz-transition-property: background-color;
	-moz-transition-duration: 0.8s;
	-o-transition-property: background-color;
	-o-transition-duration: 0.8s;
	-webkit-transition-property: background-color;
	-webkit-transition-duration: 0.8s;
}
#popUpDiv{
	position: fixed;
	background-color: #eeeeee;
	text-align: center;
	z-index: 9002;
	border-radius: 5px;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	-moz-transition-property: opacity;
	-moz-transition-duration: 0.8s;
	-o-transition-property: opacity;
	-o-transition-duration: 0.8s;
	-webkit-transition-property: opacity;
	-webkit-transition-duration: 0.8s;
	padding: 5px;
}
