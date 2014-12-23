<?

function get_printers(){

	/*
	$Printers[x][0] is the IP
	$Printers[x][1] is the Port
	$Printers[x][2] is the label size
	$Printers[x][3] is the printer description
	*/

	$Printers = array(	
	/*Office Printer*/
	array("192.168.10.37",9100,"small","2 x 0.75 Office label Printer"),
	
	/*Shipping Printer*/
	array("192.168.10.38",9100,"small","2 x 0.75 Shipping Label Printer"),
	/*Large*/
	array("192.168.10.33",9100,"large","4 x 6 Office label Printer")
	);
	return $Printers;
}
	


?>
