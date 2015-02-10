#!/usr/bin/php -q
<?
$args=$_SERVER["argv"];

/*
arguments:

printf("Label Line 1: Serial: %s\n",$previousSerial);
printf("Label Line 2: MAC: %s\n",$addressMAC);
printf("Label Line 3: Programmed: %s\n",Date("Y-m-d"));

1: Serial number
2: mac address to print
3: number of labels to print

*/


$serialNumber=$args[1];
$macAddress=$args[2];
$nLabels=$args[3];

$date = sprintf("Programmed: %s",date('Y-m-d'));

echo $serialNumber."\n";
echo $macAddress."\n";
echo $date."\n";

$outToPrinter=sprintf('OD
JF
S2
D10
q830
Q150,24
N
A5,5,0,3,2,4,65,N,"%s"
A5,40,0,4,1,1,N,"MAC: %s"
A5,80,0,4,1,1,N,"Programmed: %s"
P%d
',$serialNumber,$macAddress,$date,$nLabels);

echo $outToPrinter;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
$checkSocket = socket_connect($sock,"192.168.10.130", 9100);
$error = socket_last_error();

echo $error;

/*
OD
JF
S2
D10
q830
Q150,24

N

; MAC barcode
A5,5,0,3,2,4,65,N,"<? echo $serialNumber; ?>"

; pretty MAC address below bar code
A5,40,0,4,1,1,N,"MAC: <? echo $macAddress; ?>"

A5,80,0,4,1,1,N,"MAC: <? echo $date; ?>"

; print one label now
P<? echo $nLabels; ?>
*/


?>
