<head>
<meta http-equiv="Content-type" content="text/html; charset=UTF-8"/>
<!--[if lt IE 10]><meta http-equiv="X-UA-Compatible" content="chrome=1"><![endif]-->
<title><?php echo $GLOBALS['NAME']; ?> ~ <?php echo $GLOBALS['PAGE']; ?></title>
<link id="style" rel="stylesheet" href="inc/style.php<?php
if(isset($_COOKIE["colors"])){
	echo "?colors=".$_COOKIE["colors"];
}
?>" type="text/css"/>
<?php
if($GLOBALS['PAGE']=='Config')
	echo '<link id="style_new" rel="stylesheet" href="inc/style.php'.(isset($_COOKIE["colors"])?'?colors='.$_COOKIE["colors"]:'').'" type="text/css"/>';
?>
<link rel="shortcut icon" href="inc/images/favicon.png"/>
<link rel="stylesheet" type="text/css" href="jquery.imgareaselect-0.9.10/css/imgareaselect-animated.css"/>
<script type="text/javascript" src="jquery.imgareaselect-0.9.10/scripts/jquery.min.js"></script>
<script type="text/javascript" src="jquery.imgareaselect-0.9.10/scripts/jquery.imgareaselect.pack.js"></script>
<script type="text/javascript" src="inc/main.js"></script>
</head>

<body>
<div id="blanket" style="display:none;background-color:transparent;"><div id="popUpDiv" style="opacity:0;"></div></div>
<div id="container">

<div id="header">

<div class="tab">
<a href="index.php?page=Config">Configure</a>
</div>

<div class="tab">
<a href="index.php?page=Scans">Scanned Files</a>
</div>

<div class="tab">
<a href="index.php?page=Scan">Use Scanner</a>
</div>

<div class="tab">
<a href="/"><?php echo $_SERVER['SERVER_NAME']; ?></a>
</div>

</div>

<div id="new_mes"> </div>

<noscript id="nojs">
<div style="height:auto;" class="message">
<h2>JavaScript Disabled</h2>
<p>This application requires JavaScript to function. Please enable JavaScript, then reload this page.
</p>
</div>
</noscript>

<!--[if lt IE 9]>
<div style="height:auto;text-align:center;" class="message ie">
<h2>Error: Legacy browsers are not supported</h2>
<p>Please install <a href="http://www.mozilla.com/firefox/">Mozilla Firefox</a> (Recommended) alternatively, you may use<br/><a href="http://lmgtfy.com/?q=Internet+Explorer+9+Download&l=1">Internet Explorer 9</a> (Windows Vista and 7 only) or <a href="http://code.google.com/chrome/chromeframe/">Google Chrome Frame</a>.</p>
</div>
<![endif]-->
