<?
$title="MAC Address and Serial Number Label Printer";
require 'glabel_config.php';
require $_SERVER['DOCUMENT_ROOT'] . '/world_head.php';

$errors=array();
$nCopies=$_REQUEST['nCopies'];
if ( ! is_numeric($nCopies) || $nCopies < 1 || $nCopies > 10 ) 
	$nCopies=1;


if ( isset($_REQUEST['mac']) && '' != trim($_REQUEST['mac']) ) {
	$m=strtoupper(trim($_REQUEST['mac']));

	$macAddresses = explode("\n",$m);
//	$printers=get_printers();
//	$printer = "| /bin/nc -w 2 ".$printers[$_REQUEST['printer']][0]." ".$printers[$_REQUEST['printer']][1]; 
//	printf($printer);
	for ( $i=0 ; $i<count($macAddresses) ; $i++ ) {
		$macAddresses[$i]=strtoupper(trim($macAddresses[$i]));

		/* should strip any non-hexadecimal characters */

		/* check if valid - length, etc */
		if($_REQUEST['SorM']==1){
			if ( 12 != strlen($macAddresses[$i]) ) {
				$errors[]=sprintf("Invalid MAC address %s.",$macAddresses[$i]);
				printSerialLabel($macAddresses[$i]);

			} else {
					
					printMacLabel($macAddresses[$i]);
					
					//	printMacLabel($macAddresses[$i]);
			//$cmd = sprintf("/var/www/html/maclabels/printMAC %s %d",$macAddresses[$i],$nCopies);
			//exec($cmd);
			}
		}else if($_REQUEST['SorM']==2){
					printf("madeit");
					printSerialLabel($macAddresses[$i]);

				}

	}
}


if ( count($errors) ) {
	printf("<h2>Error(s) Encountered</h2>");
	printf("<ol>\n");
	for ( $i=0 ; $i<count($errors) ; $i++ ) {
		printf("\t<li>%s</li>\n",htmlspecialchars($errors[$i]));
	}
	printf("</ol>\n");
}
function printSerialLabel($serial){
	$nCopies = $_REQUEST['nCopies'];
	$printer = $_REQUEST['printer'];
	$printers = get_printers();
	$centerBarCode=180-(14*strlen($serial));
	$centertext=200-(7*strlen($serial));
	$outToPrinter=
		"OD\nJF\nS2\nD10\nq830\nQ150,24\n\nN\n\nB".$centerBarCode.",30,0,3,2,4,65,N,\"".$serial."\"\nA155,5,0,4,1,1,N,\"SERIAL\"\nA".$centertext.",100,0,4,1,1,N,\"".$serial."\"\nP".$nCopies."\n";
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	$checkSocket = socket_connect($sock,$printers[$printer][0], $printers[$printer][1]);
//	$checkSocket = socket_connect($sock,"192.168.10.130", 9100);
	$len = strlen($outToPrinter);
	socket_send($sock, $outToPrinter, $len, MSG_EOF);
	socket_close($sock);
}
function printMacLabel($macOnly){
/*
OD
JF
S2
D10
q830
Q150,24

N

; MAC barcode
B5,5,0,3,2,4,65,N,"<? echo $macOnly; ?>"

; pretty MAC address below bar code
A5,80,0,4,1,1,N,"MAC: <? echo $macPretty; ?>"

; print one label now
P<? echo $nLabels; ?>
*/
	$macParts=str_split($macOnly,2);
	$macPretty=sprintf("%s:%s:%s:%s:%s:%s",$macParts[0],$macParts[1],$macParts[2],$macParts[3],$macParts[4],$macParts[5]);

	$nCopies = $_REQUEST['nCopies'];
	$printer = $_REQUEST['printer'];
	$printers = get_printers();
	$outToPrinter=
	"OD\nJF\nS2\nD10\nq830\nQ150,24\n\nN\n\nB5,5,0,3,2,4,65,N,\"".$macOnly."\"\nA5,80,0,4,1,1,N,\"MAC: ".$macPretty."\"\nP".$nCopies."\n";
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
//	$checkSocket = socket_connect($sock,"192.168.10.130", 9100);
	$checkSocket = socket_connect($sock,$printers[$printer][0], $printers[$printer][1]);
	$len = strlen($outToPrinter);
	socket_send($sock, $outToPrinter, $len, MSG_EOF);
	socket_close($sock);
}
?>
<html>
  <head>
    
    <script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script>
	
	
	
	
function enterlisten(evt) {
		//checked everytime a key is pressed in the text area
        var charCode = (evt.which) ? evt.which : event.keyCode
		 if (charCode == 13){//if enter is pressed then print
			printlabel();
		 }	
}


function printlabel() {	
	
		var x=document.getElementById("IDMac"); //grabs the inputted mac address
		
		var mac = x.value.toUpperCase()
		var copies = document.getElementById("numcopies").value; //grabs the selected number of copies to print
		var printer = document.getElementById("printerNumber").value;
		var SorM = document.getElementById("SM").value;
		x.value = "";
		if(SorM==1){
			if(validMacAddress(mac.trim())){//checks if valid
				mac=removecolons(mac);//removes colons or any other kinds of separators
				
				
				$('#invalid').hide(); //if invaild is showing on the screen it will hide that
				$('#valid').show();//shows valid on the screen
				setTimeout(function(){$('#valid').hide()},1000);//after a second valid is hidden
				
				var address =  "http://192.168.10.130/maclabels/?mac="+mac+"&nCopies="+copies+"&printer="+printer+"&SorM="+SorM
				//$('#taoutput').html(address);
				if(true){
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.open("GET", address, true);
					xmlhttp.send();
				}
		
			}else{
				$('#valid').hide(); //hides valid if it is currently on the screen
				$('#invalid').show();//shows invalid
				setTimeout(function(){$('#invalid').hide()},1000);//hides invalid after a second has passed
				var address =  "http://192.168.10.130/maclabels/?mac="+mac+"&nCopies="+copies+"&printer="+printer+"&SorM=2"
				//$('#taoutput').html(address);
				if(true){
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.open("GET", address, true);
					xmlhttp.send();
				}
			}
		}else if(SorM==2){
				var address =  "http://192.168.10.130/maclabels/?mac="+mac+"&nCopies="+copies+"&printer="+printer+"&SorM="+SorM
				//$('#taoutput').html(address);
				if(true){
					var xmlhttp = new XMLHttpRequest();
					xmlhttp.open("GET", address, true);
					xmlhttp.send();
				}
			

		}
		document.myform.mytextfield.focus();//gives text area the focus, just incase it doesnt have it already

}


function validMacAddress(mac){//checks if the give mac address could be a valid one
		var count = 0;
		
		for(var i = 0;i<mac.length;i++){
			if(mac.charAt(i)=="A") {
				count++;
			}
			else if(mac.charAt(i)=="B"){
				count++;
			}
			else if(mac.charAt(i)=="C"){
				count++;
			}
			else if(mac.charAt(i)=="D"){
				count++;
			}
			else if(mac.charAt(i)=="E"){
				count++;
			}
			else if(mac.charAt(i)=="F"){
				count++;
			}
			else if(mac.charAt(i)==":"){
				//these are left blank because we want to ignore them as they do not effect the validity of the mac address
			}
			else if(mac.charAt(i)==" "){
				
			}
			else if(mac.charAt(i)=="-"){
				
			}
			else if(mac.charAt(i)==";"){
				
			}
			else if(!isNaN(mac.charAt(i))){//checks if the current character is a number
				count++;
			}else{
				return false;//if the current character is not 0-9 or A-F or one of the specified separators, then the mac address is invalid
			}		
		}
		if(count==12) {//mac address have twelve numbers/letters
		
			return true;
			
		}else{
		
			return false;//if it is more or less than 12 then we have an invalid mac address
		
		}
}


function removecolons(mac){//this just iterates through the string and adds each character that isnt a separator to a new string
		var cleanedMac="";
		
		for(var i = 0;i<mac.length;i++){
			if(mac.charAt(i)=="A") {
				cleanedMac+=mac.charAt(i);
			}
			else if(mac.charAt(i)=="B"){
				cleanedMac+=mac.charAt(i);
			}
			else if(mac.charAt(i)=="C"){
				cleanedMac+=mac.charAt(i);
			}
			else if(mac.charAt(i)=="D"){
				cleanedMac+=mac.charAt(i);
			}
			else if(mac.charAt(i)=="E"){
				cleanedMac+=mac.charAt(i);
			}
			else if(mac.charAt(i)=="F"){
				cleanedMac+=mac.charAt(i);
			}
			else if(mac.charAt(i)==":"){
				//left blank because we do not want to add the separators to the cleaned mac address
			}
			else if(mac.charAt(i)==" "){
				
			}
			else if(mac.charAt(i)=="-"){
				
			}
			else if(mac.charAt(i)==";"){
				
			}
			else if(!isNaN(mac.charAt(i))){
				cleanedMac+=mac.charAt(i);
			}
			
		}
		return cleanedMac
}


function refocus(){
	//refocuses onto the text area after number of copies is selected
	document.myform.mytextfield.focus();
}

	</script>
    <title>MAC Address Label Printer</title>
	</head>
<body onload="document.myform.mytextfield.focus();" style="text-align:center;">
	<span id="test" style="font-size: 1.25em;">MAC Address and Serial Number Label Printer</span><br />
		
		<form name="myform">
			<select id="SM" name="SorM" >
				<option value="1">Mac</option>
				<option value="2">Serial</option>
			</select>
			<br />
			<textarea name="mytextfield" id="IDMac" rows="2" onkeyup="enterlisten(event)"></textarea><br />
			<!--<input type="checkbox" name="cbautoprint" id="autoprint" value="yes">Automatically print-->
			Print to:
			<select id="printerNumber" name="printer">
			<?
			$printers=get_printers();

			for($i = 0; $i<count($printers);$i++){
				if($printers[$i][2]=="small"){
					printf("<option value = \"%s\">%s</option>",$i,$printers[$i][3]);
				}
			}
			?>

			</select>

		</form>
		
		Number of copies
		<select id="numcopies" onchange="refocus()">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>	
		</select>
		<br />
		<button type="button" onclick="printlabel()">Print</button>
		<br /><br />
		<span id="valid" style="padding: 15px; display: none; background-color: green; color: white; font-size: 2em; font-weight: bold;">Valid</span>
		<span id="invalid MAC Address" style="padding: 15px; display: none; background-color: red; color: white; font-size: 2em; font-weight: bold;">Invalid</span>

	<a href='../glabel'><h1>Back</h1></a>
	
	</body>

<?
require $_SERVER['DOCUMENT_ROOT'] . '/world_foot.php';
?>
