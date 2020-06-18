<?php // Chrome's and IE's css columns break things
$expires=86400;//24 hrs
header('Content-type: text/css; charset=UTF-8');
header("Pragma: public");
header("Cache-Control: ".(!isset($_GET['theme'])||isset($_GET['save'])?"no-cache, must-revalidate":"maxage=$expires"));
header('Expires: '.gmdate('D, d M Y H:i:s',time()+$expires).' GMT');
if(isset($_GET['save'])&&isset($_GET["theme"]))// 10 years long enough for a cookie to stick arround?
	setcookie("theme",$_GET['theme'],time()+315576000,substr($_SERVER['PHP_SELF'],0,strlen(end(explode('/',$_SERVER['PHP_SELF'])))*-1-4),$_SERVER['SERVER_NAME']);
if(isset($_GET["theme"]))
	$C=explode('.',$_GET["theme"]);
else if(isset($_COOKIE["theme"]))
	$C=explode('.',$_COOKIE["theme"]);
else
	$C=array('000',111,222,333,444,555,666,777,888,999,'AAA');
$BG_COLOR=$C[0]; // Page Background
$LK_COLOR=$C[1]; // Link Color (hover)
$LC_COLOR=$C[2]; // Link Color
$PB_COLOR=$C[3]; // Page Content Background
$HB_COLOR=$C[4]; // Header Background
$HT_COLOR=$C[5]; // Header Text
$PT_COLOR=$C[6]; // Page Text
$BB_COLOR=$C[7]; // Debug Console Background
$BT_COLOR=$C[8]; // Debug Console Text
$AH_COLOR=$C[9]; // Alert Header Background
$AT_COLOR=$C[10]; // Alert Header Text
$transitionTime='0.8s'; // The rotateChange, setClipboard, and printMsg functions in main.js needs to be adjusted based on this value
function hex2rgb($h){
	$c=array();
	$x=strlen($h)==3?1:2;
	for($i=0,$s=$x*3;$i<$s;$i+=$x)
		array_push($c,substr($h,$i,$x));
	foreach($c as $k => $v)
		$c[$k]=hexdec($x==1?$v.$v:$v);
	return $c;
}
function getShadow($b,$t){
	$b=hex2rgb($b);
	$t=hex2rgb($t);
	$c=array();
	foreach($b as $k => $v){
		$a=abs($t[$k]-$v);
		array_push($c,abs($a-($a<16||$a>239?round($a/2):0)));
	}
	return 'rgb('.implode(',',$c).')';
}
?>
@keyframes fadein {
	from {
		opacity: 0;
	}
	to {
		opacity: 1;
	}
}
body, #container, #header, #message table, .side_box, .side_box h2, #preview, #preview_links img, #preview_img p, #preview h2,
  .box, .box img, .box pre.border, .box h2, #footer, #debug pre, .tab, .tab div, .dualForm .footer, .colorPicker, .message h2 {
	transition-property: background-color, border, color;
	transition-duration: <?php echo $transitionTime; ?>;
}
body {
	margin: 0;
	padding: 1em;
	font: 12px verdana, arial, helvetica, sans-serif;
	font-size: 12px;
	background: url("images/powered_by_linux.png") bottom right no-repeat fixed;
	background-color: #<?php echo $BG_COLOR; ?>;
}

button, input[type="button"], input[type="submit"], input[type="reset"], select, a, #scans .box h2, .colorPicker {
	cursor: pointer;
}
input[disabled], select[disabled]{
	cursor: auto;
}

input[type="text"][size="3"]{
	width: 37px;
}

a {
	color: #<?php echo $LC_COLOR; ?>;
	transition-property: color;
	transition-duration: <?php echo $transitionTime; ?>;
}

a:hover {
	color: #<?php echo $LK_COLOR; ?>;
}

img{
	display: block;
}

.i{
	font-style: italic;
}

.center{
	text-align: center;
}

.tool {
	position: relative;
	display: inline-block;
}

.tool:hover .tip {
	display: block;
	text-decoration: none !important;
	font-variant: normal;
	font-weight: normal;
	animation-name: fadein;
}

.tool .tip:hover {
	display: none;
}

.tool .tip {
	background-color: rgba(0, 0, 0, 0.75);
	border-radius: 5px;
	color: #ffffff;
	display: none;
	padding: 5px;
	font-family: sans-serif;
	font-size: 12px;
	position: absolute;
	left: 101%;
	left: calc(100% + 1px);
	bottom: 101%;
	bottom: calc(100% + 1px);
	z-index: 3;
	white-space: nowrap;
	text-decoration: none;
	animation-duration: <?php echo $transitionTime; ?>;
	text-shadow: none;
	box-shadow: 0 0 3px rgba(255, 255, 255, 0.65);
}

#container {
	width: 735px;
	margin: 10px auto;
	padding: 0.5em;
	text-align: left;
	background-color: #<?php echo $PB_COLOR; ?>;
	color: #<?php echo $PT_COLOR; ?>;
	border-radius: 5px;
	box-shadow: 0 0 10px #<?php echo $PB_COLOR; ?>;
}

#header {
	height: 75px;
	margin: 0 0 0.5em 0;
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	background: url("images/logo.png") no-repeat scroll 8px center;
	background-color: #<?php echo $HB_COLOR; ?>;
	border-radius: 5px 5px 0 0;
	position: relative;
}
#header > span {
	display: block;
	position: absolute;
	left: 71px;
	height: 75px;
	overflow: hidden;
	width: 152px;
	color: #<?php echo $HT_COLOR; ?>;
	color: rgba(<?php echo implode(',',hex2rgb($HT_COLOR)); ?>,.85);
	line-height: 29.5px;
	font-size: 25px;
}
#header > span > span {
	text-shadow: <?php $S=getShadow($HB_COLOR,$HT_COLOR);echo "0 0 1px $S, 0 0 2px $S, 0 0 3px $S, 0 0 4px $S" ?>;
	display: block;
	width: 138px;
	transition-property: color, text-shadow;
	transition-duration: <?php echo $transitionTime; ?>;
	transform: rotate(-29deg);
	margin-top: 12px;
}
#header span span span{
	float: right;
}

.tab {
	height: 25px;
	float: right;
	background-color: #<?php echo $PB_COLOR; ?>;
	padding: 2px 2px 0px 2px;
	margin: 22px 10px 0 0;
	font-size: 16px;
	border-radius: 5px;
	text-transform: capitalize;
	position: relative;
}
.tab.active {
	padding-bottom: 27px;
	border-radius: 5px 5px 0 0;
}
.tab a {
	display: inline-block;
	max-width: 135px;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
	margin: 0 3px;
}
.tab div {
	display: none;
}
.tab.active div {
	border-radius: 5px;
	height: 10px;
	width: 10px;
	position: absolute;
	display: block;
}
.tab.active div.top {
	z-index: 2;
	background-color: #<?php echo $HB_COLOR; ?>;
}
.tab.active div.bottom {
	background-color: #<?php echo $PB_COLOR; ?>;
}
.tab.active div.topleft {
	left: -10px;
	bottom: 0;
}
.tab.active div.bottomleft {
	left: -5px;
	bottom: -5px;
}
.tab.active div.topright {
	bottom: 0;
	right: -10px;
}
.tab.active div.bottomright {
	right: -5px;
	bottom: -5px;
}

.column {
	float: left;
	width: 235px;
	margin: 0 8px 0 0;
	padding: 0;
}

#nojs {
	text-align: center;
}
#nojs .message {
	opacity: 1;
}

h2 > .del {
	float: right;
	border: 1px #<?php echo $HT_COLOR; ?> solid;
	border-radius: 3px;
	text-indent: 0;
}

.message {
	border: 1px solid #<?php echo $AH_COLOR; ?>;
	padding: 0;
	margin-bottom: 0.5em;
	border-radius: 5px 5px 0 0;
	width: 720px;
	margin: 0px 0px 0.5em 0.5em;
	transition-property: height, opacity, margin-bottom;
	transition-duration: <?php echo $transitionTime; ?>;
	height: 0;
	opacity: 0;
	overflow: hidden;
}
.message.ie {
	background: url("images/best_viewed_in_firefox.png") bottom right no-repeat scroll;
	background-color: #<?php echo $PB_COLOR; ?>;
	padding-bottom: 20px;
}
.message h2 {
	border: 1px solid #<?php echo $AH_COLOR; ?>;
	border-radius: 2px 2px 0 0;
	text-indent: 0.5em;
	font-size: 12px;
	font-variant: small-caps;
	color: #<?php echo $AT_COLOR; ?>;
	margin: 0;
	padding: 0.5em;
	background-color: #<?php echo $AH_COLOR; ?>;
}
.message h2 .del {
	border-color: #<?php echo $AT_COLOR; ?>;
	margin-top: -3px;
}
.message div {
	text-align: center;
	margin: 1em;
}
.message table {
	background-color: #<?php echo $HB_COLOR; ?>;
	border-radius: 5px;
	margin: 0;
	width: 100%;
}
.message td,th {
	background-color: #<?php echo $PB_COLOR; ?>;
}
.message ul {
	margin:0;
}

#debug {
	display: none;
}

#debug pre {
	background-color: #<?php echo $BB_COLOR; ?>;
	color: #<?php echo $BT_COLOR; ?>;
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

.icon {
	padding: 16px 16px 0 0;
	background-repeat: no-repeat;
	background-image: url("images/buttons.png");
	border: 1px solid #<?php echo $HB_COLOR; ?>;
}
.icon.right{
	float: right;
	margin-right: 3px;
	border-radius: 3px;
}
p span .icon{
	margin-bottom: 3px;
}
p .icon {
	margin-left: 7px;
}
p .icon:first-child {
	margin-left: 0;
}
.zip-off {
	background-position: 0 0;
}
.zip {
	background-position: -16px 0;
}
.download-off {
	background-position: -32px 0;
}
.download {
	background-position: -48px 0;
}
.pdf-off {
	background-position: 0 -16px;
}
.pdf {
	background-position: -16px -16px;
}
.print-off {
	background-position: -32px -16px;
}
.print {
	background-position: -48px -16px;
}
.view-off {
	background-position: 0 -32px;
}
.view {
	background-position: -16px -32px;
}
.edit-off {
	background-position: -32px -32px;
}
.edit {
	background-position: -48px -32px;
}
.del-off {
	background-position: -64px 0;
}
.del {
	background-position: -64px -16px;
}
.upload {
	background-position: -48px -48px;
}
.upload-off {
	background-position: -32px -48px;
}
.recent {
	background-position: -64px -48px;
}
.recent-off {
	background-position: -64px -32px;
}
.email {
	background-position: -16px -48px;
}
.email-off {
	background-position: 0 -48px;
}

form[name="theme"] p > span{
	width: 180px;
	display: inline-block;
	text-align: left;
}
input.colorPicker{
	width: 30px;
	height: 12px;
	color: transparent;
	border-radius: 5px;
	border: 2px inset #<?php echo $HB_COLOR; ?>;
}

.side_box {
	width: 250px;
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	float: left;
	padding: 0;
	margin: 0 0 0.5em 0.5em;
	border-radius: 5px 5px 0 0;
}

.side_box h2 {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	text-indent: 0.5em;
	font-size: 12px;
	font-variant: small-caps;
	color: #<?php echo $HT_COLOR; ?>;
	margin: 0 0 5px;
	padding: 0.5em;
	background-color: #<?php echo $HB_COLOR; ?>;
	text-align: center;
}

.side_box input {
	font-size: 12px;
}

.side_box select, #popUpDiv #p_config select {
	font-size: 12px;
	width: 157px;
}
#popUpDiv button, #popUpDiv input{
	padding:0;
	margin-top:3px;
}

.side_box select[name="scanner"] option[disabled] {
	background-color: yellow;
}

select.title, select.title option {
	text-transform: capitalize;
}

select.upper, select.upper option {
	text-transform: uppercase;
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
	min-height: 19px;
}

#preview {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	float: left;
	padding: 0;
	margin: 0 0 0.5em 0.5em;
	border-radius: 5px 5px 0 0;
}

#preview p {
	margin: 5px;
}

#preview_links img, #preview_img p {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	text-align: center;
}
#preview_img.tool{
	margin-top: -5px;
}
#preview_img .rule{
	top: -70px;
	left: 0;
}
#preview_img .rule:last-child{
	top: 0;
	left: -70px;
	height: 471px;
}
#preview_img img {
	height: 471px;
	width: 450px;
	position: relative;
	transition-property: transform, filter;
	transition-duration: <?php echo $transitionTime; ?>;
	transform: rotate(0) scale(1);
	filter: brightness(100%) contrast(100%);
}
#preview_img #select{/* For imgAreaSelect when using the HTML5 ruler*/
	position:relative;
}
#preview_img p {
	position: relative;
	background-color: #FFF;
	overflow: hidden;
}

img[src="res/images/blank.gif"] {
	background: url("images/preview.png") no-repeat scroll center center transparent;
}

#preview_img img[title="Scanning"], #preview_img img[title="Processing"] {
	background: url("images/loading.gif") no-repeat scroll center center transparent;
	position: absolute;
	left: 0;
	top: 0;
}

#preview h2 {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	text-indent: 0.5em;
	font-size: 12px;
	font-variant: small-caps;
	color: #<?php echo $HT_COLOR; ?>;
	margin: 0;
	padding: 0.5em;
	background-color: #<?php echo $HB_COLOR; ?>;
	text-align: center;
}

#scans {
	float: left;
	margin: 0;
	padding: 0;
	width: 100%;
}

#scans .box h2, .colorPicker { /* Doubleclick tends to highlight text and it does not look right */
	user-select: none;
	-ms-user-select: none;
	-webkit-user-select: none; /* Safari */
}

#scans .box h2.included {
	background-color: #<?php echo $LK_COLOR; ?>;
	border-color: #<?php echo $LK_COLOR; ?>;
	border-radius: 5px 5px 0 0;
}

#scans.columns {
	column-count: 3;
	column-gap: 0;
	margin-left: 4px;
	overflow: visible;
}

#scans.columns .box {
	display: inline-block;
	float: none;
	margin-left: 0;
}

.box, .box-full {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	float: left;
	padding: 0;
	margin: 0 0 0.5em 0.5em;
	border-radius: 5px 5px 0 0;
}

.box {
	width: 235px;
}

.box-wide {
	width: 462px;
}

.box-full {
	width: 720px;
	float: none;
	display: inline-block;
}

.box-full img {
	max-width: 708px;
}

.box img {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
}

.box pre.border{
	margin: 5px;
	border: 1px solid #<?php echo $HB_COLOR; ?>;
}

.box h2 {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	border-radius: 2px 2px 0 0;
	text-align: center;
	font-size: 12px;
	font-variant: small-caps;
	color: #<?php echo $HT_COLOR; ?>;
	margin: 0;
	padding: 0.5em;
	background-color: #<?php echo $HB_COLOR; ?>;
}

.box h3 {
	text-align: center;
}

.box p {
	margin: 5px;/*5px 10px 5px 5px*/
}
.box .footer {
	width: 100%;
	border-top: 1px solid #<?php echo $HB_COLOR; ?>;
	display: inline-block;
	text-align: center;
}

pre {
	overflow: auto;
}

code {
	font-family: "Courier New", Courier, monospace;
	font-size: 13px;
}

.simplelist {
	list-style: none;
	padding: 0;
	margin: 0 5px;
}

#paper-list ul {
	list-style: none;
	padding-right: 40px;
	overflow: visible;
}

#paper-list.columns ul {
	column-count: 3;
	column-gap: 50px;
}

#paper-list li, .boxlist {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	margin-top: -1px;
	position: relative;
	height: 20px;
	width: 100%;
	display: inline-block;
}
#paper-list .code {
	float: right;
	font-family: monospace;
	position: absolute;
	right: 1px;
	top: 0;
	height: 100%;
	padding-top: 3px;
}

#paper-list a, .simplelist a {
	margin: 1px 0 -2px 1px;
}

#imgur-uploads {
	padding-bottom: 5px;
}

#imgur-uploads .box {
	margin: 5px 0 0 5px;
	text-align: center;
	width: 172px;
}

#imgur-uploads .box h2 > span {
	width: 140px;
	width: calc(100% - 20px);
	display: inline-block;
	word-wrap: break-word;
}

#imgur-uploads .box .tool > img {
	width: 160px;
	height: 160px;
	margin: 3px 3px 0;
	cursor: pointer;
}

#imgur-uploads .box .album {
	width: 160px;
	height: 160px;
	margin-top: 3px;
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	cursor: pointer;
	overflow: hidden;
	display: block; /* Chrome */
	display: -webkit-box;
	-webkit-box-pack: center;
	-webkit-box-align: center;
	-webkit-flex-wrap: wrap;
	display: -ms-flexbox; /* IE */
	-ms-flex-pack: center;
	-ms-flex-align: center;
	-ms-flex-wrap: wrap; 
	display: -moz-box; /* Firefox has issues as of writing this as of 2020 firefox uses the webkit name instead?*/
	-moz-box-pack: center;
	-moz-box-align: center;
	-moz-flex-wrap: wrap;
}
#imgur-uploads .box .album img {
	display: inline-block;
	margin: 0;
	border: none;
	width: 80px;
	height: 80px;
}

#imgur-codes, #imgur-scroller{
	width: 100%;
}

#imgur-codes{
	overflow: hidden;
	white-space: nowrap;
}

#imgur-scroller{
	height: 15px;
	overflow-x: scroll;
	overflow-y: hidden;
}

#imgur-scroller div{
	display: inline-block
}

.dualForm form {
	float: left;
	width: 50%;
}
.dualForm form.m {
	border-left: 1px solid #<?php echo $HB_COLOR; ?>;
	margin-left: -1px;
}
.dualForm form p > span {
	width: 100px;
	display: inline-block;
}
.dualForm form input[type="text"]:not([size]), .dualForm form input[type="password"] {
	width: 125px;
}
.dualForm .largeButton{
	float: right;
	width: 200px;
	height: 125px;
	overflow: hidden;
}
.dualForm .largeButton span{
	display: inline-block;
	transform: rotate(-20deg) scale(5);
}

#text-editor {
	text-align: center;
}

#text-editor #preview_links {
	text-align: left;
}

#text-editor textarea {
	width: 714px;
	height: 400px;

}

#text-editor input {
	width: 350px;
}

#rulegen > div, #rulegen input{
	margin:5px;
}

#rulegen ul{
	column-count: 2;
	column-gap: 0;
}

#rulegen li{
	list-style:none;
}

#footer {
	clear: both;
	text-align: center;
	height: 20px;
	margin: 0;
	padding: 0;
	border: 5px solid #<?php echo $HB_COLOR; ?>;
	border-radius: 0 0 5px 5px;
}

#footer p {
	margin: 0;
	padding: 0;
}

/* popup div css */

#popUpDiv #imgur-data {
	text-align: left;
}
#popUpDiv #imgur-data ul {
	list-style: none;
}
#popUpDiv #imgur-data li li {
	margin-left: 40px;
}
#popUpDiv #imgur-data .codes {
	display: inline-block;
	width: 100%;
	border-right: 1px solid black;
}
#popUpDiv #imgur-data .codes h2 {
	font-size:12px;
	text-align:center;
}

#popUpDiv #email {
	border: 1px solid #<?php echo $HB_COLOR; ?>;
	border-radius: 5px 5px 0 0;
	overflow: hidden;
	padding: 5px;
}
#popUpDiv #email > h2 {
	background-color: #000;
	color: #FFF;
	margin: -5px -5px 5px;
	font-size: 15px;
	padding: 0.5em;
}
#popUpDiv #email .security {
	color: #ff0000;
	border: 1px solid #ff0000;
	border-radius: 5px 5px 0 0;
	margin-bottom: 5px;
}
#popUpDiv #email .security h2 {
	background-color: #ff0000;
	color: #ffffff;
	text-align: center;
	margin: 0;
	padding-bottom: 5px;
}
#popUpDiv #email .security ul {
	margin-top: 0;
	padding-right: 5px;
	text-align: left;
}
#popUpDiv #email form {
	width: 265px;
	text-align: left;
	float: left;
}
#popUpDiv #email form .label {
	width: 95px;
}
#popUpDiv #email .control input:not([type="checkbox"]), #popUpDiv #email .control select, #popUpDiv #email .control textarea {
	width: 150px;
}
#popUpDiv #email .help {
	border: 1px solid #000;
	border-radius: 5px 5px 0 0;
	margin-bottom: 5px;
	float: right;
	width: 138px;
	text-align: left;
}
#popUpDiv #email .help h2 {
	background-color: #000;
	color: #FFF;
	margin: 0;
	font-size: 15px;
	text-align: center;
	padding: 0.5em;
	font-size: 12px;
}
#popUpDiv #email .help p {
	margin: 0;
	padding: 5px;
}

/* http://www.pat-burt.com/web-development/how-to-do-a-css-popup-without-opening-a-new-window/ */
#blanket {
	background-color: rgba(17,17,17,0.65);
	position: fixed;
	z-index: 9001;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	transition-property: background-color;
	transition-duration: <?php echo $transitionTime; ?>;
}
#popUpDiv {
	position: fixed;
	background-color: #eeeeee;
	text-align: center;
	z-index: 9002;
	border-radius: 5px;
	transition-property: opacity;
	transition-duration: <?php echo $transitionTime; ?>;
	padding: 5px;
	box-shadow: 0 0 7px #FFF;
}
