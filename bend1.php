<?php
require('fpdf.php');  // You need to download and include the FPDF library

// Retrieve form data
$name = $_POST['name'];
$registerNo = $_POST['registerNo'];
$SelectYear = $_POST['SelectYear'];
$courseType = $_POST['courseType'];
$leaveType = $_POST['leaveType'];
$number = $_POST['number'];
$startDate = $_POST['startDate'];
$endDate = $_POST['endDate'];
$text = $_POST['text'];
$place = $_POST['place'];
$date = $_POST['date'];

// Generate PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// Add content to PDF
$pdf->Cell(0, 10, 'Leave Letter', 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(0, 10, 'From:', 0, 1, 'L');
$pdf->Cell(0, 10, "Name: $name", 0, 1, 'L');
$pdf->Cell(0, 10, "Register No: $registerNo", 0, 1, 'L');
$pdf->Cell(0, 10, "Year: $SelectYear", 0, 1, 'L');
$pdf->Cell(0, 10, "Course Type: $courseType", 0, 1, 'L');
$pdf->Cell(0, 10, 'Department: COMPUTER APPLICATIONS', 0, 1, 'L');
$pdf->Ln(10);
$pdf->Cell(0, 10, 'To:', 0, 1, 'L');
$pdf->MultiCell(0, 10, "Head of the Department,\nDepartment of Computer Applications,\nBharathiar University,\nCoimbatore.", 0);
$pdf->Ln(10);
$pdf->MultiCell(0, 10, "Respected Sir/Madam,\n\nSubject: Leave Request", 0);
$pdf->Cell(0, 10, "Leave Type: $leaveType", 0, 1, 'L');
$pdf->Ln(10);
$pdf->MultiCell(0, 10, "I am writing to request $number days of leave From: $startDate To: $endDate.\nThe reason for my leave is: $text", 0);
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Please let me know if this leave request is approved.', 0, 1, 'L');
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Thank you for considering my request.', 0, 1, 'C');
$pdf->Ln(10);
$pdf->Cell(0, 10, "Place: $place", 0, 1, 'L');
$pdf->Cell(0, 10, "Date of Leave: $date", 0, 1, 'L');
$pdf->Ln(10);
$pdf->Cell(0, 10, 'Sincerely,', 0, 1, 'L');
$pdf->Cell(0, 10, "Name: $name", 0, 1, 'L');

// Save the PDF file
$pdf->Output('leave_letter.pdf', 'F');

// Send email with the PDF attachment
$to = 'coordinator@example.com';  // Replace with the actual coordinator's email
$subject = 'Leave Request';
$message = 'Please find the attached leave request.';
$headers = "From: $name <$email>\r\n";
$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
$file_path = 'leave_letter.pdf';

mail($to, $subject, $message, $headers, "-f$name");

// Redirect the user back to the form
header('Location: index.html');
exit();
?>
