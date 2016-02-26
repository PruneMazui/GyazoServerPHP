	<meta http-equiv="X-UA-Compatible" content="ie=edge" />

	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="Content-Style-Type" content="text/css" />
	<meta http-equiv="Content-Script-Type" content="text/javascript" />

	<meta name="language" content="Japanese" />
	<meta name="Targeted-Geographic-Area" content="Japan" />
	<meta name="copyright" content="Copyright &copy; " />
	<link rel="shortcut icon" href="/favicon.ico" >

	<title>Gyazo Plus</title>

{strip}
	{** css **}
	{$cssfiles[] = "/lib/bootstrap-3.3.4-dist/css/bootstrap.min.css"}
	{$cssfiles[] = "/lib/jquery-ui-1.11.4.custom/jquery-ui.min.css"}
	{$cssfiles[] = "/lib/jQuery.GI.TheWall.js-master/assets/css/GITheWall.css"}
	{$cssfiles[] = "/style/style.css"}


	{** javascript **}

	{* jquery *}
	{$jsfiles[] = "/lib/jquery-1.11.2.min.js"}
	{$jsfiles[] = "/lib/jquery-ui-1.11.4.custom/jquery-ui.min.js"}

	{* その他ライブラリ *}
	{$jsfiles[] = "/lib/bootstrap-3.3.4-dist/js/bootstrap.min.js"}
	{$jsfiles[] = "/lib/jQuery.GI.TheWall.js-master/jQuery.GI.TheWall.min.js"}
{/strip}

{stylesheet files=$cssfiles enable=false build=false combine="/style/combine.css" addtime=true}
{javascript files=$jsfiles  enable=false build=false combine="/js/combine.js"   addtime=true}

