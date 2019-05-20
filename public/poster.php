<?php
use setasign\Fpdi\Fpdi;
require_once "../config.php";
require_once "../composer/vendor/setasign/fpdf/fpdf.php";
require_once "../composer/vendor/setasign/fpdi/src/autoload.php";
require_once "../classes/Material.php";
require_once "../classes/Candidate.php";
require_once "../classes/Database.php";
require_once "../classes/Web.php";

$material = new Material($_GET["track"]);

if( ! $material->constructed || $material->type !== "poster"){
    exit;
}
$candidate = new Candidate($material->candidate_id);

$source_file = "static/elections/".$candidate->db_code."/materials/".$material->track.".pdf";
if( $material->status !== 1 ){
    header("Location: ".$source_file);
    exit;
}

$watermark_location = "static/img/watermark.png";

// Initiate FPDI to get the size of the pdf
$get_size = new Fpdi('P','mm');
$get_size->setSourceFile($source_file);
$get_size->AddPage();
$size_import = $get_size->importPage(1);

// Get an array containing the sizes
$size = $get_size->getTemplatesize($size_import);

$pdf = new Fpdi($size["orientation"], "mm", array($size["width"], $size["height"]));
$page_count = $pdf->setSourceFile($source_file);
$pdf->SetRightMargin(0);

for( $current_page = 1; $current_page <= $page_count ; $current_page++ ){
    $pdf->AddPage();
    $imported_page = $pdf->importPage($current_page);

    $current_size = $pdf->getTemplatesize($size_import);
    $pdf->useTemplate($imported_page, 0, 0, $current_size["width"], $current_size["height"]);

    // Add in the watermark to that page
    $pdf->SetFont('Helvetica', "B", 8);
    $pdf->SetTextColor(0, 166, 81);

    // Calculate the position to place the watermark

    $image_xy = $size["width"] * 0.15;

    $pos_left = ($size["width"] * 0.95) - $image_xy;
    $pos_top = $size["height"] * 0.02;

    $pdf->SetXY($pos_left, $pos_top + $image_xy + ($size["height"] * 0.01));
    $pdf->Write(1, "APPROVED POSTER");
    $pdf->SetXY($pos_left, $pos_top + $image_xy + ($size["height"] * 0.03));
    $pdf->Write(1, "ID: ".strtoupper(bin2hex(random_bytes(5))));
    $pdf->SetXY($pos_left, $pos_top + $image_xy + ($size["height"] * 0.05));
    $pdf->Write(1, "UNTIL: Jan 20th, 2019");
    $pdf->Image($watermark_location, $pos_left, $pos_top,$image_xy, $image_xy);
}

$pdf->Output();