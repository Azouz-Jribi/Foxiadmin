<?php
require_once('../lib/outils.php');
require_once('../lib/dbManager.php');
if(isset($_POST['host']) && isset($_POST['name']) && isset($_POST['user']) && isset($_POST['pass']))
{
	if(new PDO('mysql:host='.strip_tags($_POST['host']).';dbname='.strip_tags($_POST['name']).'',''.strip_tags($_POST['user']).'',''.strip_tags($_POST['pass']).'')){
		$content = '<?php ';
		$content .= 'DEFINE("HOST", "'.strip_tags($_POST['host']).'");';
		$content .= 'DEFINE("DBNAME", "'.strip_tags($_POST['name']).'");';
		$content .= 'DEFINE("USERNAME", "'.strip_tags($_POST['user']).'");';
		$content .= 'DEFINE("PASS", "'.strip_tags($_POST['pass']).'");';
		$content .= 'DEFINE("HOME_DIR", "'.str_replace('\\','/',strip_tags($_POST['dir'])).'");';
		$content .= '$db = new PDO("mysql:host=".HOST.";dbname=".DBNAME, USERNAME, PASS);';

		creatAllFile("../../config/config.php", $content);
		header('Location: step2.php');
	}
	else
	{
		header('Location: install.php');
	}
}
else
{
	header('Location: install.php');
}
?>