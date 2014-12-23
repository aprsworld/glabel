<?
session_start();
if ( isset($_REQUEST['printer']) ) {
	
		setcookie('print',$_REQUEST['printer'],time()+3600);
	
	
}

require '../glabel_config.php';
$title="Custom Labels";
require $_SERVER['DOCUMENT_ROOT'].'/world_head.php';


$labelSize=$_REQUEST['size'];
$numCopies=$_REQUEST['nCopies'];
if ( $labelSize==1 ) {
	$outToPrinter = "OD\nJF\nS2\nD10\nq830\nQ150,24\nN\n";

	$inText = $_REQUEST['ta'];
	$replace = $inText;
	$lines = explode("\n",strtoupper($inText));

	if ( count($lines)==1&&$lines[0]=="" ) {
	//	echo "nothing in here";
		$print = false;
	} else {
		$print = true;
	//	print_r($lines);
	}



	$NumOfLines = count($lines);
	$StartX = 200;
	$StartY = 5;
	$center = 0;
	$sizeMod = 0;
	$addSpace = 37;
	$freeSpace = 11;


	if ( $NumOfLines<5 ) {
		for($i = 0;$i<$NumOfLines;$i++ ) {//check if there is enough space
			$lines[$i]=trim($lines[$i]);
			if ( strlen($lines[$i])>25 ) {
				$lines[$i]=subStr($lines[$i],0,25);
			}
		}
	} else {
		$print=false;
	}
	if ( $print == true ) {
		for($i = 0;$i<$NumOfLines;$i++ ) {
			$center= (strlen($lines[$i])*8);
			$outToPrinter= $outToPrinter .  "A";//prints ascii string
			$outToPrinter= $outToPrinter . ($StartX-$center);//Starting x-coordinate
			$outToPrinter= $outToPrinter . ",";//Starting x-coordinate
			$outToPrinter= $outToPrinter . $StartY;//Starting y-coordinate
			$outToPrinter= $outToPrinter . ",0,";//orientation. add 1 for every 90degrees
			$outToPrinter= $outToPrinter . 4;//font 1-5
			$outToPrinter= $outToPrinter . ",";
			$outToPrinter= $outToPrinter . 1;
			$outToPrinter= $outToPrinter . ",";
			$outToPrinter= $outToPrinter . 1;//horizontal stretch, vertical stretch
			$outToPrinter= $outToPrinter . ",N,\"";//N for normal, R for reversed
			$outToPrinter= $outToPrinter . trim($lines[$i]);
			$outToPrinter= $outToPrinter . "\"";
			$outToPrinter= $outToPrinter . "\n";
			$StartY+=$addSpace;
		}
		$outToPrinter= $outToPrinter . "\nP".$numCopies."\n";
	//	echo $outToPrinter;
//		echo "Printing Small Label";
	} else {
		$outToPrinter=  "not enough room on the label or empty label";
		echo $outToPrinter;
	}
}else if ( $labelSize==2 ) {

	$outToPrinter = "OD\nJF\nS2\nD10\nq830\nQ1200,24\nN\n";

	$inText = $_REQUEST['ta'];
	$replace = $inText;
	$lines = explode("\n",strtoupper($inText));
	
	if ( count($lines)==1&&$lines[0]=="" ) {
	//	echo "nothing in here";
		$print = false;
	} else {
		$print = true;
	//	print_r($lines);
	}



	$NumOfLines = count($lines);
	$StartX = 400;
	$StartY = 600;
	$center = 0;
	$sizeMod = 0;
	$addSpace = 0;
	$freeSpace = 11;



	for($i = 0;$i<$NumOfLines;$i++ ) {//check if there is enough space
		$lines[$i]=trim($lines[$i]);
		if ( strlen($lines[$i])<9 ) {
			$freeSpace-=3;
		}
		else if ( strlen($lines[$i])<17 ) {
			$freeSpace-=2;
		}
		else if ( strlen($lines[$i])<30 ) {
			$freeSpace-=1;
		}
	}
		if ( $freeSpace<0 ) {
			$print=false;//The text takes up too much room.
			$StartX=800;

		} else {
			$StartX=800-(($freeSpace/2)*66)+(15*$NumOfLines);
		}
	if ( $print == true ) {
		for($i = 0;$i<$NumOfLines;$i++ ) {
			if ( strlen($lines[$i])<9 ) {
				$size = "5";
				$sizeMod = "4";
				$center = 75*strlen(trim($lines[$i]));
				$addSpace=66*3;
			}	
			else if ( strlen($lines[$i])<17 ) {
				$size = "5";
				$sizeMod = "2";
				$center = 35*strlen(trim($lines[$i]));
				$addSpace=66*2;
			}
			else if ( strlen($lines[$i])<34 ) {
				$size = "5";
				$sizeMod = "1";
				$center = 18*strlen(trim($lines[$i]));
				$addSpace=66;	
			} else {
				$lines[$i]=substr($lines[$i],0,33);
				$size = "5";
				$sizeMod="1";
				$center = 18*strlen(trim($lines[$i]));
				$addSpace=66;
			}
			$outToPrinter= $outToPrinter .  "A";//prints ascii string
			$outToPrinter= $outToPrinter . $StartX;//Starting x-coordinate
			$outToPrinter= $outToPrinter . ",";//Starting x-coordinate
			$outToPrinter= $outToPrinter . ($StartY-$center);//Starting y-coordinate
			$outToPrinter= $outToPrinter . ",1,";//orientation. add 1 for every 90degrees
			$outToPrinter= $outToPrinter . $size;//font 1-5
			$outToPrinter= $outToPrinter . ",";
			$outToPrinter= $outToPrinter . $sizeMod;
			$outToPrinter= $outToPrinter . ",";
			$outToPrinter= $outToPrinter . $sizeMod;//horizontal stretch, vertical stretch
			$outToPrinter= $outToPrinter . ",N,\"";//N for normal, R for reversed
			$outToPrinter= $outToPrinter . trim($lines[$i]);//Data to be printed
			$outToPrinter= $outToPrinter . "\"";
			$outToPrinter= $outToPrinter . "\n";
			$StartX-=$addSpace+30;
		}
		$outToPrinter= $outToPrinter . "\nP".$numCopies."\n";
	//	echo $outToPrinter;
//		echo "Printing Large Label";
	} else {
		$outToPrinter=  "not enough room on the label or label is empty";
		echo $outToPrinter;
	}



}


if ( $print ) {
	/* get array of printers [ [ip,port,size,description],... ] */

	$printers = get_printers();
	$printer = $_REQUEST['printer'];
	$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
	if ( $labelSize==1 ) {
		if ( $printers[$printer][2]=="small" ) {
			$checkSocket=socket_connect($sock,$printers[$printer][0], $printers[$printer][1]);//printer
		} else {
			printf("Printer type does not match label type");
		}
	}else if ( $labelSize==2 ) {
		if ( $printers[$printer][2]=="large" ) {
			$checkSocket=socket_connect($sock,$printers[$printer][0], $printers[$printer][1]);//printer
		} else {
			printf("Printer type does not match label type");
		}
	}
//	$checkSocket=socket_connect($sock,"192.168.10.130", 9100);//test IP
	if ( $checkSocket == true ) {
//		printf("Socket connected\n");
	}
	if ( $sock == true ) {
//		printf("Socket created!\n");
	}
	$len = strlen($outToPrinter);
//	printf("should send %d bytes\n",$len);
	$sent=socket_send($sock, $outToPrinter, $len, MSG_EOF);
//	printf("%d bytes sent\n",$sent);
	socket_close($sock);
}
?>




<html>
<head>
	<title>Label Printer </title>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.min.js"></script>
	<script>
		function removePunct(str ) {
			
			str=str.replace("!"," ");
			str=str.replace("@"," ");
			str=str.replace("'"," ");
			str=str.replace("\""," ");
			str=str.replace("("," ");
			str=str.replace(")"," ");
			str=str.replace("."," ");
			str=str.replace("-"," ");
			str=str.replace("+"," ");
			str=str.replace("="," ");
			str=str.replace("["," ");
			str=str.replace("]"," ");
			str=str.replace("{"," ");
			str=str.replace("}"," ");
			str=str.replace("?"," ");
			return str;


		}
		function previewLargeLabel( ) {
				var x = document.getElementById("IDta");
				x.value=removePunct(x.value);
				var text = x.value.split("\n");
				var prev = "<div style=\"text-align:center;display:table-cell;"
				prev = prev+"vertical-align: middle;height:233;margin:auto;width:350px;"
				prev = prev+"font-family:Lucida Console,Monaco,monospace;border-style:solid;border-width:1px;\">";
				for(var i=0;i<text.length;i++ ) {
					var prevSize = 1;
					if ( text[i].length<9 ) {
						prevSize = 4;
					}else if ( text[i].length<17 ) {
						prevSize = 2;
					}else if ( text[i].length>32 ) {
						text[i]=text[i].substring(0,33);
					}
					prev = prev + "<div style = \"font-size:"+ prevSize + "em; \">" + text[i].toUpperCase() + "</div>";
				}
				prev = prev + "</div>";	
				$("#preview").html(prev);
			}
		function previewSmallLabel( ) {
				var x = document.getElementById("IDta");
				var text = x.value.split("\n");
				var prev = "<div style=\"text-align:center;display:table-cell;"
				prev = prev+"height:100;margin:auto;width:275px;"
				prev = prev+"font-family:Lucida Console,Monaco,monospace;border-style:solid;border-width:1px;\">";
				for(var i=0;i<text.length;i++ ) {
					if ( text[i].length>25 ) {
						text[i]=text[i].substring(0,25);
					}
					prev = prev + "<br \">" + text[i].toUpperCase();
				}
				prev = prev + "</div>";	
				$("#preview").html(prev);
			}
		function makeprev( ) {
			var x = document.getElementById("selector");
			var choice = x.options[x.selectedIndex].value;
			if ( choice==1 ) {
				previewSmallLabel();
				document.getElementById("printerS").selectedIndex = "0";
			}
			if ( choice==2 ) {
				previewLargeLabel();
				document.getElementById("printerS").selectedIndex = "2";
			}	
		}
	</script>
</head>
<body onload="makeprev()">
	<h1>Custom Label</h1>
	To choose another kind of label to print out, please use the link at the bottom of the page to go back. Using the back button on the browser may print out extra labels.
	<br /><br />
	<div>
		<select name="size" id="selector" onchange="makeprev()" form="printform">
			<option value="1"<?
				if ( $labelsize==1 ) {
					echo "selected";
				}	
			?>>Small Label</option>
			<option value="2"<?
				if ( $labelSize==2 ) {
					echo "selected";
				}	
			?>>Large Label</option>
		</select>
		<select name="nCopies" form="printform">
			<option value="1">1</option>
			<option value="2">2</option>
			<option value="3">3</option>
			<option value="4">4</option>
			<option value="5">5</option>
			<option value="6">6</option>
			<option value="7">7</option>
			<option value="8">8</option>
			<option value="9">9</option>
			<option value="10">10</option>
			<option value="11">11</option>
			<option value="12">12</option>
			<option value="13">13</option>
			<option value="14">14</option>
			<option value="15">15</option>
		</select>
		<form method="get" action="/glabel/custom/" id="printform">
			<textarea id="IDta" name="ta" rows="5" cols="34" onkeyup="makeprev()"><?echo $replace;?></textarea>
			<br />
		<select id="printerS" name="printer">
			<?
			$printers=get_printers();
			$selected="0";
			if ( isset($_REQUEST['printer']) ) {
				$selected=$_REQUEST['printer'];
			} else {
				if ( isset($_COOKIE['print']) ) {
					$selected=$_COOKIE['print'];
				}
			}
			for($i=0;$i<count($printers);$i++ ) {
				if ( $i==$selected ) {
					printf("<option value = \"%s\" selected>%s</option>",$i,$printers[$i][3]);
				} else {
					printf("<option value = \"%s\">%s</option>",$i,$printers[$i][3]);
				}
			}
			?>
		</select>	
			<br />
			<input type="submit" value="Print"> 
		</form>
	</div>
	<h1>Preview</h1>	
	<div id="preview"></div>
	<a href='/glabel'><h1>Back</h1></a>

</body>
<?
require $_SERVER['DOCUMENT_ROOT'] . '/world_foot.php';
?>
</html>
