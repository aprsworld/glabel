<?
$title=$display="Labels";
require $_SERVER['DOCUMENT_ROOT'].'/world_head.php';

?>

<div style="margin-left:auto;margin-right:auto; width:90%; text-align:center;">
	<h1>Labels</h1>
	<div style="display:inline-block; padding:50px; vertical-align:top;">
		<h2>Bin Label</h2><br />
		<a href="/glabel/bin/"><img style="height: 100px;" src="pics/bin.PNG"><a/>
	</div>
	<div style="display:inline-block; padding:50px; vertical-align:top;">
		<h2>Create a custom Label</h2><br />
		<a href="custom/"><img style="height: 100px;" src="pics/custom.PNG"></a>
	</div>
	<div style="display:inline-block; padding:50px; vertical-align:top;">
		<h2>Copy Label</h2><br />
		<a href="copies/"><img style="height: 100px;padding-bottom:10px;" src="pics/serial.PNG"><br><img style="height: 100px;" id="macSer" src="pics/mac.PNG"></a>
	</div>
</div>

<?
require $_SERVER['DOCUMENT_ROOT'] . '/world_foot.php';
?>
