<?php

// For debug only
$USEREALWEBSITE = True;

// Gets the POST variable
$LICENSEPLATE = $_GET['reg'];

// Creates a variable by joining the entered LP and a URL
$BASEURL = "https://biluppgifter.se/fordon/" . $LICENSEPLATE;

if ($USEREALWEBSITE) {
    // Gets the headers from the HTTP Request 
    $responseHeaders = get_headers($BASEURL, 1);
    
    // Gets the HTTP Status Code from the headers and checks if it contains "429", if so, print an error message on our own that looks more legit. (Even tho it's the opposite haha)
    if (str_contains($responseHeaders[0], "429")) {
        echo "<h1>Too many Requests! Calm down and try again later</h1>";
    }
    // Assigns the $HTMLContent variable to HTML Data from the website to analyze using RegEx
    $HTMLContent = file_get_contents($BASEURL);

    file_put_contents("cache.txt", $HTMLContent);
}

else {
    $HTMLContent = file_get_contents("cache.txt");
}


// Fabrikat - The make of the vehicle | For example Volkswagen or Volvo 
preg_match('/<span class="label">Fabrikat<\/span>\n<span class="value">[a-z, A-Z]*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$fabrikat = str_replace("Fabrikat", "", strip_tags($matches[0][0]));

// Modell - The model of the vehicle | For example 316 Sprinter or V70
preg_match('/<span class="label">Modell<\/span>\n<span class="value">.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$modell = str_replace("Modell", "", strip_tags($matches[0][0]));

// Modelyear / Make year - The model and make year of the vehicle | For example 2009 / 2009
preg_match('/<span class="label">Fordonsår \/ Modellår<\/span>\n<span class="value">.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$years = str_replace("Fordonsår / Modellår", "", strip_tags($matches[0][0]));

// VIN Number - The VIN number of the vehicle | For example YV1BW694191105793
preg_match('/<span class="label">Chassinr \/ VIN<\/span>\n<span class="value">.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$vin = str_replace("Chassinr / VIN", "", strip_tags($matches[0][0]));

// Theft Status - The Theft Status  of the vehicle | For example "Ej rapporterat stulet"
preg_match('/<span class="label">Stöldstatus Sverige<\/span>\n<span class="value">\n.*\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$theft = str_replace("Stöldstatus Sverige", "", strip_tags($matches[0][0]));

// Traffic Status - The Traffic Status  of the vehicle | For example "I trafik"
preg_match('/<span class="label">Status<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$traffic_status = str_replace("Status", "", strip_tags($matches[0][0]));

// Import Status - The Import Status  of the vehicle | For example "Nej"
preg_match('/<span class="label">Import \/ Införsel<\/span>\n.*\n.*\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$import_status = str_replace("Import / Införsel", "", strip_tags($matches[0][0]));

// First Registered - The First Registered Date  of the vehicle | For example "2022-01-01"
preg_match('/<span class="label">Först registrerad<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$first_registered = str_replace("Först registrerad", "", strip_tags($matches[0][0]));

// First in traffic Date - The First in Traffic Date  of the vehicle | For example "2022-01-01"
preg_match('/<span class="label">Trafik i Sverige<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$first_in_traffic = str_replace("Trafik i Sverige", "", strip_tags($matches[0][0]));

// Amount of Owners - The Amount of Owners of the vehicle | For example "1"
preg_match('/<span class="label">Antal ägare<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$amountOfOwners = str_replace("Antal ägare", "", strip_tags($matches[0][0]));

// Latest Ownership Change - The Latest Ownership Change of the vehicle | For example "2022-01-01"
preg_match('/<span class="label">Senaste ägarbyte<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$latestOwnershipChange = str_replace("Senaste ägarbyte", "", strip_tags($matches[0][0]));

// Latest Inspection Date - The Latest Inspection Date of the vehicle | For example "2022-01-01"
preg_match('/<span class="label">Senast besiktigad<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$latestInspectionDate = str_replace("Senast besiktigad", "", strip_tags($matches[0][0]));

// Mileage On Latest Inspection - The Mileage On Latest Inspection of the vehicle | For example "10 111 mil"
preg_match('/.*<span class="value">.* mil<\/span>/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$mileageInspection = str_replace("", "", strip_tags($matches[0][0]));

// Next Latest Inspection Date - The Mileage On Latest Inspection of the vehicle | For example "2022-01-01"
preg_match('/<span class="label">Nästa besiktning senast<\/span>\n.*\n.*\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$nextInspection = str_replace("Nästa besiktning senast", "", strip_tags($matches[0][0]));

// Yearly Tax - The Yearly Tax of the vehicle | For example "5000kr"
preg_match('/<span class="label">Årlig skatt<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$yearlyTax = str_replace("Årlig skatt", "", strip_tags($matches[0][0]));

// Tax Month(s) - The Tax Month(s) of the vehicle | For example "Aug, Dec, Apr"
preg_match('/<span class="label">Skattemånad<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$taxMonths = str_replace("Skattemånad", "", strip_tags($matches[0][0]));

// Credit Status - The Credit Status of the vehicle | For example "Nej"
preg_match('/<span class="value" id="data-credit">.*<\/span>/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$creditStatus = str_replace("", "", strip_tags($matches[0][0]));

// Lease Status - The Lease Status of the vehicle | For example "Nej"
preg_match('/<span class="value" id="data-leasing">.*<\/span>/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$isLeased = str_replace("", "", strip_tags($matches[0][0]));

// Owner - The owner of the vehicle | Link to MerInfo
preg_match('/gtm-merinfo" href="http:\/\/.*\?/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$owner = str_replace("gtm-merinfo\" href=\"", "", strip_tags($matches[0][0]));

// Fuel - The Fuel of the vehicle | For example "Diesel"
preg_match('/<span class="label">Drivmedel<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$fuel = str_replace("Drivmedel", "", strip_tags($matches[0][0]));

// Top Speed - The Top Speed of the vehicle | For example "200km/h"
preg_match('/<span class="label">Toppfart<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$topSpeed = str_replace("Toppfart", "", strip_tags($matches[0][0]));

// Gearbox - The Gearbox of the vehicle | For example "Manuell"
preg_match('/<span class="label">Växellåda<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$gearbox = str_replace("Växellåda", "", strip_tags($matches[0][0]));

// 4wd - The Wheel Drive of the vehicle | For example "Nej"
preg_match('/<span class="label">Fyrhjulsdrift<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$fwd = str_replace("Fyrhjulsdrift", "", strip_tags($matches[0][0]));

// Color - The Color of the vehicle | For example "Grå"
preg_match('/<span class="label">Färg<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$color = str_replace("Färg", "", strip_tags($matches[0][0]));

// Chassi - The Chassi of the vehicle | For example "Grå"
preg_match('/<span class="label">Kaross<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$chassi = str_replace("Kaross", "", strip_tags($matches[0][0]));


// Total weight - The Total Weight of the vehicle | For example "2300kg"
preg_match('/<span class="label">Totalvikt<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$totalWeight = str_replace("Totalvikt", "", strip_tags($matches[0][0]));

// Total Loading Weight - The Total Loading Weight of the vehicle | For example "Max 619kg"
preg_match('/<span class="label">Lastvikt<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$loadingWeight = str_replace("Lastvikt", "", strip_tags($matches[0][0]));

// Total Trailer Weight - The Total Loading Weight of the vehicle | For example "Max 1800kg"
preg_match('/<span class="label">Släpvagnsvikt<\/span>\n.*/', $HTMLContent, $matches, PREG_OFFSET_CAPTURE);
$totalTrailerWeight = str_replace("Släpvagnsvikt", "", strip_tags($matches[0][0]));


if (!isset($theft)){
    echo"Not set!";
}

/*

// Echos the data to the page
echo "<h3>Fabrikat: " . $fabrikat . "</h3>";
echo "<h3>Modell: " . $modell . "</h3>";
echo "<h3>Year: " . $years . "</h3>";
echo "<h3>VIN: " . $vin . "</h3>";
echo "<h3>Theft: " . $theft . "</h3>";
echo "<h3>Traffic Status: " . $traffic_status . "</h3>";
echo "<h3>Import Status: " . $import_status . "</h3>";
echo "<h3>First Registered: " . $first_registered . "</h3>";
echo "<h3>First In Traffic: " . $first_in_traffic . "</h3>";
echo "<h3>Owners: " . $amountOfOwners . "</h3>";
echo "<h3>Latest Ownership Change: " . $latestOwnershipChange . "</h3>";
echo "<h3>Latest Inspection Date: " . $latestInspectionDate . "</h3>";
echo "<h3>Inspection Mileage: " . $mileageInspection . "</h3>";
echo "<h3>Next Inspection Latest: " . $nextInspection . "</h3>";
echo "<h3>Yearly Tax: " . $yearlyTax . "</h3>";
echo "<h3>Tax Month(s): " . $taxMonths . "</h3>";
echo "<h3>Bought With Credit: " . $creditStatus . "</h3>";
echo "<h3>Leased: " . $isLeased . "</h3>";
echo "<h3>Owner: " . $owner . "</h3>";
echo "<h3>Fuel: " . $fuel . "</h3>";
echo "<h3>Top Speed: " . $topSpeed . "</h3>";
echo "<h3>Gearbox: " . $gearbox . "</h3>";
echo "<h3>Four wheel drive: " . $fwd . "</h3>";
echo "<h3>Color: " . $color . "</h3>";
echo "<h3>Chassi: " . $chassi . "</h3>";
echo "<h3>Loading Weight: " . $loadingWeight . "</h3>";
echo "<h3>Total Weight: " . $totalWeight . "</h3>";
echo "<h3>Total Trailer Weight: " . $totalTrailerWeight . "</h3>";

*/
$arr = array(
'brand' => $fabrikat,
'model' => $modell,
'owner' => $owner, 
'year' => $years, 
'vin' => $vin,
'imported' => $import_status,
'stolen' => $theft,
'traffic_status' => $traffic_status,
'tax' => $yearlyTax,
'tax_months' => $taxMonths,
'credit' => $creditStatus,
'leased' => $isLeased,
'first_registered' => $first_registered,
'first_in_traffic' => $first_in_traffic,
'fuel' => $fuel,
'latest_inspection_date' => $latestInspectionDate,
'mileage' => $mileageInspection,
'next_inspection' => $nextInspection,
'color' => $color,
'gearbox' => $gearbox,
'topspeed' => $topSpeed,
'owners' => $amountOfOwners,
'latest_ownership_change' => $latestOwnershipChange,
'chassi' => $chassi,
'weight_loading' => $loadingWeight,
'weight_total' => $totalWeight,
'total_trailer_weight' => $totalTrailerWeight,
'creator' => 'Rasmus Schöld',
'data_owner' => 'Biluppgifter.se',
'notes' => 'I do NOT own the data. This is a webscraper just for fun. May be illegal to use. I am not responsible for what this is used for.'
);


foreach ($arr as $key => $value) {
    $arr[$key] = str_replace("\n", "", $value);
}

echo json_encode($arr);







?>