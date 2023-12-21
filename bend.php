<?php

define('PDF_PATH', 'C:/xampp/htdocs/lll/pdf'); // Adjust this path accordingly
define('FROM_EMAIL', 'saikesavan1019@gmail.com');
define('COORDINATOR_EMAIL', 'kesavansai1019@gmail.com');
define('HOD_EMAIL', 'sudhagarlives123@gmail.com');

require 'vendor/autoload.php';

use Mpdf\Mpdf;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Twilio\Rest\Client;

// Function to connect to the database
function connectToDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "kesavan";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Function to generate PDF using mPDF
function generatePDF($name, $registerNo, $SelectYear, $courseType, $leaveType, $number, $startDate, $endDate, $reason, $place, $date) {
    $mpdf = new Mpdf();

    // Add a page
    $mpdf->AddPage();

    // Add content to the PDF
    $content = "
        <h1>Leave Request</h1>
        <p>Name: $name</p>
        <p>Register No: $registerNo</p>
        <p>Select Year: $SelectYear</p>
        <p>Course Type: $courseType</p>
        <p>Leave Type: $leaveType</p>
        <p>Number: $number</p>
        <p>Start Date: $startDate</p>
        <p>End Date: $endDate</p>
        <p>Reason: $reason</p>
        <p>Place: $place</p>
        <p>Date: $date</p>
    ";

    $mpdf->WriteHTML($content);

    // Define the temporary directory
    $tempDir = sys_get_temp_dir(); // Get the system's temporary directory

    // Generate a unique temporary file name
    $tempFile = tempnam($tempDir, 'leave_request_');

    // Save the PDF to the temporary file
    $tempPdfFilePath = $tempFile . '.pdf';
    $mpdf->Output($tempPdfFilePath, 'F');

    return $tempPdfFilePath;
}

// Function to send SMS using Twilio
function sendSMS($to, $message) {
    $twilio = new Client('AC02939c2dd40c74da3e14c90c56412548', '2dc819ae3c532b2f8ecdf5787c17e1ad');

    $message = $twilio->messages->create(
        $to,
        [
            'from' => '+14156305238', // Replace with your Twilio phone number
            'body' => $message,
        ]
    );
}

// Function to send email
function sendEmail($to, $subject, $body, $attachment = null) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'saikesavan1019@gmail.com';
    $mail->Password = 'ursy skpp fdmk snsv'; // Replace with your Gmail password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom(FROM_EMAIL, 'KESAVAMOORTHI BASKARAN');
    $mail->addAddress($to);

    if ($attachment) {
        $mail->addAttachment($attachment, 'LeaveRequest.pdf');
    }

    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    try {
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}

// Main Code
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $conn = connectToDatabase();

        // Retrieve form data
        $name = $_POST["name"];
        $registerNo = $_POST["registerNo"];
        $SelectYear = $_POST["SelectYear"];
        $courseType = $_POST["courseType"];
        $leaveType = $_POST["leaveType"];
        $number = $_POST["number"];
        $startDate = $_POST["startDate"];
        $endDate = $_POST["endDate"];
        $reason = $_POST["reason"];
        $place = $_POST["place"];
        $date = $_POST["date"];

        // Insert data into the database
        $sql = "INSERT INTO ks (name, registerNo, SelectYear, courseType, leaveType, number, startDate, endDate, reason, place, date, coordinator_approval, hod_approval) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        $coordinatorApproval = 'Pending';
        $hodApproval = 'Pending';

        $stmt->bind_param("sssssssssssss", $name, $registerNo, $SelectYear, $courseType, $leaveType, $number, $startDate, $endDate, $reason, $place, $date, $coordinatorApproval, $hodApproval);

        if (!$stmt->execute()) {
            throw new Exception("Error executing SQL statement: " . $stmt->error);
        }

        $stmt->close();

        // Generate PDF as before
        $pdf = generatePDF($name, $registerNo, $SelectYear, $courseType, $leaveType, $number, $startDate, $endDate, $reason, $place, $date);

        // Define the destination file path
        $pdfFilePath = PDF_PATH . DIRECTORY_SEPARATOR . '_leave_request.pdf';

        // Use mPDF's built-in method to save the PDF to a file
        // Use file_put_contents to save the PDF
        if (file_put_contents($pdfFilePath, file_get_contents($pdf))) {

            // PDF file has been successfully saved

            // Send email to coordinator
            $coordinatorSubject = 'Leave Request for Approval';
            $coordinatorBody = 'Hello Coordinator, ...';  // Customize the email body
            sendEmail(COORDINATOR_EMAIL, $coordinatorSubject, $coordinatorBody, $pdfFilePath);

            // Update database with coordinator approval status
            $updateSql = "UPDATE ks SET coordinator_approval = 'Pending Coordinator Approval' WHERE registerNo = ?";
            $updateStmt = $conn->prepare($updateSql);

            $updateStmt->bind_param("s", $registerNo);

            if (!$updateStmt->execute()) {
                throw new Exception("Error executing SQL statement: " . $updateStmt->error);
            }

            $updateStmt->close();

            // Send email to HOD
            $hodSubject = 'Leave Request for Approval';
            $hodBody = 'Hello HOD, ...';  // Customize the email body
            sendEmail(HOD_EMAIL, $hodSubject, $hodBody, $pdfFilePath);

            // Update database with HOD approval status
            $updateHodSql = "UPDATE ks SET hod_approval = 'Pending HOD Approval' WHERE registerNo = ?";
            $updateHodStmt = $conn->prepare($updateHodSql);

            $updateHodStmt->bind_param("s", $registerNo);

            if (!$updateHodStmt->execute()) {
                throw new Exception("Error executing SQL statement: " . $updateHodStmt->error);
            }

            $updateHodStmt->close();

            // Send SMS to the user
            $userMessage = 'Thankyou, Your leave request has been submitted sucessfully.';
            sendSMS('+916383322670', $userMessage);

            $conn->close();

            echo json_encode(['status' => 'success', 'message' => 'Leave request submitted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error saving PDF file.']);
        }
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Form not submitted.']);
}
?>
