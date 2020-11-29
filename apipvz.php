<?php
// apipvz.php: paima iš lenteles "keliones2" paskutinius n ('kiek') į nurodytą miestą ('kur') įrašus 
// Jei kur nenurodyta =visi miestai, jei kiek nenurodyta=visi įrašai
//
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *");  // atsakinėti bet kam
$server = "localhost";
$user = "stud";
$password = "stud";
$database= "pvz";
$lentele="keliones2";
	
	//paimti parametrų reikšmes iš užklausos
$miestas=substr($_GET['kur'],0,19); 						// kad tilptų - nukerpam
$kiek=$_GET['kiek'];
if (($miestas && !preg_match("#^[a-zA-ZĄČĘĖĮŠŲŪŽąčęėįšųūž]+$#", $miestas)) ||    // tik raidės arba tuščia
	($kiek!="" && ($kiek<1 || $kiek>99))){  									// tik teigiamas sk arba tuščia
		$outp=array("Nekorektiški parametrai"=>"kur:".$miestas." kiek:".$kiek);
		http_response_code(400);
	goto end;
}
$conn=new mysqli($server,$user,$password,$database);
if ($conn->connect_error){
	$outp=array("Negaliu prisijungti prie MySQL"=>$conn->connect_error);
	http_response_code(500);
	goto end;
}
$sql = ("SET CHARACTER SET utf8"); $conn->query($sql);// del lietuviškų raidžių

// suformuojam sql užklausą pagal parametrus $miestas ir paskutinius $kiek įrašų
if (!empty($kiek))$sql="SELECT * FROM ( ";
else $sql="";
$sql= $sql."SELECT * FROM $lentele ";
if (!empty($miestas))$sql=$sql." WHERE kur='".$miestas."'";
if (!empty($kiek))$sql=$sql." ORDER BY id DESC LIMIT ".$kiek.") sub ORDER BY id ASC";

//$outp=$sql; goto end;  //testavimui kaip atrodo sql užklausa

if (!$result = $conn->query($sql)){
	$outp=array("Negaliu nuskaityti ". $lentele => $conn->error);
	http_response_code(500);
}else
	if (mysqli_num_rows($result)==0 ){
		$outp=array("Nėra kelionių į "=>$miestas);
		http_response_code(400);
	}else{
		$outp = $result->fetch_all(MYSQLI_ASSOC);
		http_response_code(200);
	}
end:
echo json_encode($outp);
?>