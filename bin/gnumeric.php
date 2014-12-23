<?
function read_part_web($part_no) {
	$url=sprintf("http://www.aprsworld.com/order/productCSV.php?partNo=%s",$part_no);
	$fp=fopen($url,"r");
	$p=fgetcsv($fp,1024);
	fclose($fp);

	/* 
	gnumeric file is this format:
	[0] => APRS1200
	[1] => SMD Fuses .5A 60V 10A Imax
	[2] => 650-SMD050F-2
	[3] => Mouser
	[4] => SMD050F-2
	[5] => Tyco Electronics / Raychem
	[6] => compliant

	aprsworld.com is in this format:
	[0] => APRS6000
	[1] => upc
	[2] => Wind Data Logger, module only
	[3] => 0.65
	[4] => compliant
	[5] => 1 public
	[6] => USA
	[7] => active
	*/

	$a=array();
	$a[0]=$p[0];
	$a[1]=$p[2];
	$a[2]=$p[0];
	$a[3]='APRS World';
	$a[4]=$p[0];
	$a[5]='APRS World';
	$a[6]=$p[4];
	
	return $a;
}



function read_part($part_no) {
	$part_no = strtoupper($part_no);
	$partfile = "compress.zlib:///home/world/parts/parts.gnumeric";

	$xml = simplexml_load_file($partfile);
	foreach ($xml->children('gnm',TRUE) as $child1) {
		if ($child1->getName() == "Sheets") {
			foreach ($child1->children('gnm',TRUE) as $child2) {
				foreach ($child2->children('gnm',TRUE) as $child3) {
					/* verify sheet name */
					if ($child3->getName() == "Name") {
						if ($child3 != "parts.csv") break 2;
					}

					$rowMatch=-1;
					$rowData=array();

					if ($child3->getName() == "Cells") {
						foreach ($child3->children('gnm',TRUE) as $child4) {
							// child4 is a cell
//							print_r($child4);
							$row = (int) $child4->attributes()->Row;
							$col = (int) $child4->attributes()->Col;

							/* check if our part number matches */
							if ( 0 == $col && $part_no == $child4 ) {
//								printf("# found part number on row=%d\n",$row);
								$rowMatch=$row;
							}

							if ( $rowMatch == $row ) {
								$row_data[] = "$child4";
//								printf("# added \"%s\" to row_data[]\n",$child4);
							}

							if ( $rowMatch != -1 && $row != $rowMatch ) {
								return $row_data;
							}

//							$val = "$child4"; // quotes evaluate to the type, string or numeric
//							$val = (is_numeric($val)) ? round($val,3) : $val;
//							$ccaarr[$row][$col] = $val;
						}
					}
				}
			}
		}
	}

	return read_part_web($part_no);
}


if ( false ) {
	header('Content-type: text/plain');
	$partNo="APRS1200";
	$partNo="APRS6000";
	$partNo="APRS5814M";

	$part = read_part($partNo);
	if ( false != $part ) {
		printf("# Part \"%s\" found!\n",$partNo);

		print_r($part);
	}
	printf("\n");
}

?>
