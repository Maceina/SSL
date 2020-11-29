<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
	</head>
<body>
	<b>Demo CA laboratoriniam darbui</b>.       Pagal jūsų užklausą išduos sertifikatą.<br>
	<small><table><tr><td valign="top"><b>CA parametrai</b></td>
		<td valign="top">
		<table align="center"><tr><td>Country Name</td><td>LT</td></tr>
		<tr><td>State or Province Name</td><td>Kaunas</td></tr>
		<tr><td>Locality Name</td><td>Kaunas</td></tr>
		<tr><td>Organization Name</td><td>KTU</td></tr>
		<tr><td>Organizational Unit Name</td><td>Sertifikatu tarnyba</td></tr>
		<tr><td>Common Name</td><td>Lab.darbo.CA</td></tr></table>
	</td></tr></table></small>
<form method='post'>
<table align="center">
	<tr><td>Prašymo (csr) failo vardas:</td><td>
		 <input name='csr' type="text" size="50" required 
			value="<?php echo isset($_POST['csr']) ? $_POST['csr'] : '' ?>" > </td></tr>
	 <tr><td></td><td><small>Nurodykite pilną kelią</small></td></tr>
     <tr><td> Galiojimo laikas:</td><td>
	  	 <input name='laikas' type="number" min="1" max="366" value="365"> 
	 	 &larr; <small>dienų skaičius</small></td></tr>
</table>
	<center><input type='submit' name='ok' value='siųsti'></center>
  </form>

<?php
	if(isset($_POST['ok']) && !empty($_POST['csr'])) 
{	$csr=$_POST['csr'];
	$laikas=$_POST['laikas'];

//$csr="/home/stud/ssllab/my_requests/pirmas.csr";
// echo $csr;

$subject=openssl_csr_get_subject("file://$csr");
 if (!$subject)   die("Klaida: nerastas csr failas");
 if ($subject['C'] != 'LT' || $subject['ST'] != 'Kaunas'
	 || $subject['L'] != 'Kaunas' || $subject['O'] != 'KTU')
	    die("Klaida: nesutampa organizacijos atributai");

 // tikrinti ar nesikartoja raktai
$pkey = openssl_csr_get_public_key("file://$csr");
$info = openssl_pkey_get_details($pkey);
//echo $info['key'];
 $raktai = fopen("/home/stud/ssllab/demoCA/raktai", "r") or die("System:nėra raktų failo");
 $iraktas="";while (!feof($raktai)){
	$rakteil=fgets($raktai);
	if ($rakteil == "\n"){  //tuscia eilute skiria raktus
		if ($iraktas ==  $info['key']) die("Klaida: jau išduotas sertifikatas su tokiu raktu");
		else  $iraktas=""; //bus sekantis raktas
		}
	$iraktas=$iraktas.$rakteil;}
 fclose ($raktai);
 
$cakey="/home/stud/ssllab/demoCA/private_keys/CA_key.pem";
$cacert = "/home/stud/ssllab/demoCA/CA_cert.pem";
 // koks eil.nr
$nrf=fopen("/home/stud/ssllab/demoCA/serial","r") or die("System:nėra serial failo");
fclose($nrf);
$nr =trim(file_get_contents("/home/stud/ssllab/demoCA/serial"));
$usercert = openssl_csr_sign("file://$csr",
							 "file://$cacert",
							 array("file://$cakey","stud"), 
							 $laikas, 
							 array('digest_alg'=>'sha256'),
							 $nr);
if ($e = error_get_last()) die("System: klaida formuojant sertifikata:".$e);
openssl_x509_export($usercert, $certout);
//echo $certout;

 // sertifikato irasymas
$frez="/home/stud/ssllab/demoCA/newcerts/cert".str_pad($nr,2,"0",STR_PAD_LEFT).".pem";
$rez = fopen($frez, "w") or die("System: negaliu įrašyti sertifikato");
fwrite($rez,$certout);
fclose($rez);
 // pakeisti serial
$nrf=fopen("/home/stud/ssllab/demoCA/serial","w");
$nr = ($nr+1);
fwrite($nrf, str_pad($nr,2,"0",STR_PAD_LEFT));
fclose($nrf);
 // irasyti rakta
  $raktai = fopen("/home/stud/ssllab/demoCA/raktai", "a");
 fwrite($raktai,$info['key']."\n");
 fclose ($raktai);
 echo "Pagal jūsų užklausą išduotas sertifikatas  <b>".$subject['CN']."</b>  tapatybei patvirtinti<br>";
 echo "Sertifikatas yra faile:<b> ".$frez."</b> <br>";
 echo "Nukopijuokite jį į savo katalogą ir pakeiskite vardą į jums reikalingą.";
}
?>
	</body></html>
	
 