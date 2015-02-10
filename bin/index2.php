<?
/*
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
//*/
session_start();
if(isset($_REQUEST['printer'])){
	
		setcookie('print',$_REQUEST['printer'],time()+3600);
	
	
}
$debug= true;
$title="Bin Label Printer";
require '../glabel_config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/world_head.php';
$table="<table>";
$errors=array();
$outToPrinter = "OD\nJF\nS2\nD10\nq830\nQ150,24\nN\n";
$printer = 1;
$printer = $_REQUEST['printer'];



if ( isset($_REQUEST['partNumber']) && '' != trim($_REQUEST['partNumber']) ) {
//	require $_SERVER['DOCUMENT_ROOT'] . '/packinglist/gnumeric.php';
	require 'gnumeric.php';
	$p=strtoupper(trim($_REQUEST['partNumber']));
	$pNumbers = explode("\n",$p);

	$last='';
	
	$itemlist=array();//holds onto items that have already been looked up	
	
//	print_r($pNumbers);

	for( $i=0; $i<count($pNumbers); $i++){
		
		$pNumbers[$i]=trim($pNumbers[$i]);
	}
	
	for ( $i=0 ; $i<count($pNumbers) ; $i++ ) {
		
		if(in_array($pNumbers[$i],$itemlist)){

	//		printf("<br />skipped print and look up<br />");	

		}else{
		
			array_push($itemlist,$pNumbers[$i]);//adds item to the list so it wont be looked up again
			$copies=count(array_keys($pNumbers,$pNumbers[$i]));//this finds the amount of times the item is found in the array so we can print out that many copies
	//		printf("%s was found %s times",$pNumbers[$i],$copies);
			$part = read_part($pNumbers[$i]);//looks up the number
			//printf("PART: %s",$part);
		//	print_r($part);
			if ( '' == $part[0] ) {
				$errors[]=sprintf("Part number %s not found.",$pNumbers[$i]);
			} else {
				$printers = get_printers();
				//print_r($printers);
				if ( $printers[$printer][2] == "small" ) {
					printLabel($part[0],$part[1],$copies);
					//echo "small";
				} else {
					printLargeLabel($part[0],$part[1],$copies);
					//echo "large";
				}
				$table =$table."<tr><td>". $part[0]."</td><td>: ".$part[1].".</td></tr>";
				sleep(1);
//				$cmd = sprintf("/usr/local/bin/printRemoveable %s",$part[0]);
//				exec($cmd);
			}

		}	
	}
}

$printers = get_printers();//gets a 2 dimensional array containing a list of label printers on the server
/* $p0 is the serial number, $p1 is the text description, $numToPrint is the number of labels to print */
function printLabel($p0,$p1,$numToPrint){
	$nCopies = $_REQUEST['nCopies'];
	$printer = $_REQUEST['printer'];
	$printers = get_printers();


	$outToPrinter=	"OD\n".
			"JF\n".
			"S2\n".
			"D10\n".
			"q830\n".
			"Q150,24\n".
			"\n".
			"N\n".
			"\n".
			"A15,5,0,4,1,1,N,\"".substr($p0,0,4)."\"\n". //APRS
			"A14,35,0,4,1,1,N,\"" . substr($p0,4)."\"\n". //serialNumber without prefix
			"B90,5,0,3,2,4,65,N,\"". $p0."\"\n". //barcode
			"A15,75,0,4,1,1,N,\"". str_replace('"','\"',substr($p1,0,24))."\"\n". // first 24 chars of description
			"A15,100,0,4,1,1,N,\"".str_replace('"','\"',substr($p1,24,24))."\"\n".// The rest of the description that will fit
			"P".($numToPrint*$nCopies)."\n";


	//printf($outToPrinter);
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	$checkSocket = socket_connect($sock,$printers[$printer][0], $printers[$printer][1]);
//	$checkSocket = socket_connect($sock,"192.168.10.130", 9100);
	$len = strlen($outToPrinter);
	socket_send($sock, $outToPrinter, $len, MSG_EOF);
	socket_close($sock);
	
}

function printLargeLabel($p0,$p1,$numToPrint){
	$nCopies = $_REQUEST['nCopies'];
	$printer = $_REQUEST['printer'];
	$printers = get_printers();
	
	$p0 = strtoupper($p0);
	$p1 = strtoupper($p1);
	
	$outToPrinter=	"OD\n". 
			"JF\n". 
			"S2\n". 
			"D10\n". 
			"q830\n". 
			"Q1200,24\n". 
			"\n".
			"N\n". 
			"\n";

	if ( strlen($p0) > 8 ) {
		$outToPrinter.= sprintf("A795,20,1,5,4,2,N,\"%s\"\n",$p0);
	} else {
		$outToPrinter.= sprintf("A795,20,1,5,4,4,N,\"%s\"\n",$p0);
	}
	
	$outToPrinter.= sprintf("A467,60,1,5,1,1,N,\"%s\"\n",str_replace('"',"''",substr($p1,0,28))). 
			sprintf("A371,60,1,5,1,1,N,\"%s\"\n",str_replace('"',"''",substr($p1,28,28))).
			//"B90,5,0,3,2,4,65,N,\APRS9111\"\n".
			sprintf("B275,40,1,3,2,8,130,N,\"%s\"\n",$p0).
			"P".($numToPrint*$nCopies)."\n";

	
	//printf($outToPrinter);
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	$checkSocket = socket_connect($sock,$printers[$printer][0], $printers[$printer][1]);
//	$checkSocket = socket_connect($sock,"192.168.10.130", 9100);
	$len = strlen($outToPrinter);
	socket_send($sock, $outToPrinter, $len, MSG_EOF);
	socket_close($sock);
	
}




if ( count($errors) ) {
	printf("<h2>Error(s) Encountered</h2>");
	printf("<ol>\n");
	for ( $i=0 ; $i<count($errors) ; $i++ ) {
		printf("\t<li>%s</li>\n",htmlspecialchars($errors[$i]));
	}
	printf("</ol>\n");
}

?>
<table>
<tr>
<td>
<h1>Bin Labels</h1>
To choose another kind of label to print out, please use the link at the bottom of the page to go back. Using the back button on the browser may print out extra labels.<br /><br />
<form method="get" action="/glabel/bin/index2.php">
<textarea name="partNumber" rows="20">
</textarea>
<br />
Print to:
<select name="printer">
			<?
			$printers=get_printers();
			$selected="0";
			if(isset($_REQUEST['printer'])){
				$selected=$_REQUEST['printer'];
			}else{
				if(isset($_COOKIE['print'])){
					$selected=$_COOKIE['print'];
				}
			}
			for($i=0;$i<count($printers);$i++){
				if($i==$selected){
					printf("<option value = \"%s\" selected>%s</option>",$i,$printers[$i][3]);
				}else{
					printf("<option value = \"%s\">%s</option>",$i,$printers[$i][3]);
				}
			}
			?>

</select>
<br />
Number of copies:
<select name="nCopies">
	<option value = "1">1</option>
	<option value = "2">2</option>
	<option value = "3">3</option>
	<option value = "4">4</option>
	<option value = "5">5</option>
	<option value = "6">6</option>
	<option value = "7">7</option>
	<option value = "8">8</option>
	<option value = "9">9</option>
	<option value = "10">10</option>
	<option value = "11">11</option>
	<option value = "12">12</option>
	<option value = "13">13</option>
	<option value = "14">14</option>
	<option value = "15">15</option>
</select>
<br />
<input type="submit" value="Schedule Bin Labels for Printing">
</form>

<a href='/glabel'><h1>Back</h1></a>
</td>
<td id="dump">
<?
$table=$table."</table>";
echo$table;

?>
</td>
</tr>
<?
require $_SERVER['DOCUMENT_ROOT'] . '/world_foot.php';
?>
