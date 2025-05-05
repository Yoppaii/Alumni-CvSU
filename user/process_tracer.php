<?php
require('../main_db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Asia/Manila');

// Set error handling to prevent HTML errors being output
ini_set('display_errors', 0);
error_reporting(E_ALL);

function sendTracerSubmissionEmail($email, $fullName)
{
    $mail = new PHPMailer(true);

    $message = '<!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Alumni Tracer Form Invitation</title>
    </head>
    <body style="margin: 0; padding: 0; background-color: #f4f4f4; font-family: Arial, sans-serif;">
        <div style="max-width: 600px; margin: 20px auto; background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
            <div style="background: #4682B4; color: white; padding: 20px; text-align: center;">
                <h2 style="margin: 0;">Alumni Tracer System Invitation</h2>
            </div>
            
            <div style="padding: 20px;">
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px;">
                    <h2 style="color: #4682B4; margin-top: 0;">Dear ' . htmlspecialchars($fullName) . ',</h2>
                    
                    <p style="font-size: 16px; line-height: 1.5;">You have been recommended by one of your friends to sign up for our Alumni Tracer System.</p>
                    
                    <div style="background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border: 1px solid #eee;">
                        <h3 style="margin-top: 0; color: #333;">Join Our Alumni Network</h3>
                        <p style="margin: 5px 0;">We would like to invite you to register and fill up the Alumni Tracer Form in our system.</p>
                        <p style="margin: 5px 0;">Your participation will help our institution track the career paths of our graduates and improve our academic programs.</p>
                        <p style="margin: 5px 0;">Your input is valuable for our institution\'s continuous improvement efforts.</p>
                        
                        <!-- Website Link Button -->
                        <div style="text-align: center; margin-top: 20px;">
                            <a href="https://alumnicvsu.com/" style="background-color: #4682B4; color: white; padding: 12px 25px; text-decoration: none; border-radius: 4px; font-weight: bold; display: inline-block;">Visit Our Website</a>
                        </div>
                    </div>
                    
                    <div style="background: #e8f4fd; border: 1px solid #d1e9ff; color: #0c5460; padding: 15px; border-radius: 4px; margin-top: 20px;">
                        <p style="margin: 0;">If you have any questions or need assistance with the registration process, please don\'t hesitate to contact us.</p>
                    </div>
                </div>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; text-align: center; font-size: 12px; color: #666;">
                <p style="margin: 5px 0;">This is an automated message, please do not reply to this email.</p>
                <p style="margin: 5px 0;">Alumni Tracer System</p>
                <p style="margin: 5px 0;"><a href="https://alumnicvsu.com/" style="color: #4682B4; text-decoration: none;">alumnicvsu.com</a></p>
                <p style="margin: 5px 0;">Time sent: ' . date('Y-m-d H:i:s') . '</p>
            </div>
        </div>
    </body>
    </html>';

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'roomreservation.csumc@gmail.com'; // Use your email account
        $mail->Password = 'bpqazltzfyacofjd'; // Use your email password or app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('roomreservation.csumc@gmail.com', 'Alumni Tracer System');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Invitation to Join Alumni Tracer System';
        $mail->Body = $message;

        return $mail->send();
    } catch (Exception $e) {
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}

$logged_user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $mysqli->begin_transaction();

        $check_sql = "SELECT id FROM personal_info WHERE user_id = ?";
        $check_stmt = $mysqli->prepare($check_sql);
        $check_stmt->bind_param("i", $logged_user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            throw new Exception("You have already submitted a tracer form.");
        }
        $check_stmt->close();

        // Step 1: Personal Information
        $stmt = $mysqli->prepare("INSERT INTO personal_info (user_id, civil_status, sex, birthday, course, campus, residence) 
                               VALUES (?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "issssss",
            $logged_user_id,
            $_POST['civilStatus'],
            $_POST['sex'],
            $_POST['birthday'],
            $_POST['course'],
            $_POST['campus'],
            $_POST['residence']
        );
        $stmt->execute();
        $form_id = $mysqli->insert_id;
        $stmt->close();

        // Step 2: Educational Background
        $stmt = $mysqli->prepare("INSERT INTO educational_background (
            user_id, 
            personal_info_id, 
            degree_specialization,
            college_university,
            year_graduated,
            honors_or_awards,
            professional_exams, 
            highest_education, 
            reason_for_taking
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "iisssisss",
            $logged_user_id,
            $form_id,
            $_POST['degree_specialization'],
            $_POST['college_university'],
            $_POST['year_graduated'],
            $_POST['honors_or_awards'],
            $_POST['professionalExams'],
            $_POST['highestEducation'],
            $_POST['reasons']
        );
        $stmt->execute();
        $stmt->close();

        // Step 3: Training/Advance Studies
        $stmt = $mysqli->prepare("INSERT INTO training_studies (user_id, personal_info_id, training_title, duration_credits, institution, advance_reason) 
                               VALUES (?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "iissss",
            $logged_user_id,
            $form_id,
            $_POST['trainingTitle'],
            $_POST['duration'],
            $_POST['institution'],
            $_POST['advanceReason']
        );
        $stmt->execute();
        $stmt->close();

        // Step 4: Employment Data
        $stmt = $mysqli->prepare("INSERT INTO employment_data (
            user_id, personal_info_id, employment_status, present_employment_status, 
            self_employed_skills, present_occupation, business_line, work_place
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "iissssss",
            $logged_user_id,
            $form_id,
            $_POST['employmentStatus'],
            $_POST['presentEmploymentStatus'],
            $_POST['selfEmployedSkills'],
            $_POST['presentOccupation'],
            $_POST['businessLine'],
            $_POST['workPlace']
        );
        $stmt->execute();
        $stmt->close();

        if ($_POST['employmentStatus'] === 'no' && isset($_POST['unemploymentReasons'])) {
            $stmt = $mysqli->prepare("INSERT INTO unemployment_reasons (user_id, personal_info_id, reason) VALUES (?, ?, ?)");
            foreach ($_POST['unemploymentReasons'] as $reason) {
                $stmt->bind_param("iis", $logged_user_id, $form_id, $reason);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Step 5: Job Experience
        $stmt = $mysqli->prepare("INSERT INTO job_experience (
            user_id, personal_info_id, first_job, course_related
        ) VALUES (?, ?, ?, ?)");

        $stmt->bind_param(
            "iiss",
            $logged_user_id,
            $form_id,
            $_POST['firstJob'],
            $_POST['courseRelated']
        );
        $stmt->execute();
        $stmt->close();

        if ($_POST['firstJob'] === 'yes' && isset($_POST['stayingReasons'])) {
            $stmt = $mysqli->prepare("INSERT INTO staying_reasons (user_id, personal_info_id, reason) VALUES (?, ?, ?)");
            foreach ($_POST['stayingReasons'] as $reason) {
                $stmt->bind_param("iis", $logged_user_id, $form_id, $reason);
                $stmt->execute();
            }
            $stmt->close();
        }

        if ($_POST['courseRelated'] === 'yes' && isset($_POST['acceptingReasons'])) {
            $stmt = $mysqli->prepare("INSERT INTO accepting_reasons (user_id, personal_info_id, reason) VALUES (?, ?, ?)");
            foreach ($_POST['acceptingReasons'] as $reason) {
                $stmt->bind_param("iis", $logged_user_id, $form_id, $reason);
                $stmt->execute();
            }
            $stmt->close();
        }

        // Step 6: Job Duration and Finding
        $stmt = $mysqli->prepare("INSERT INTO job_duration (
            user_id, personal_info_id, first_job_duration, job_finding_method, time_to_land,
            job_level, current_job, initial_earning, curriculum_relevant, suggestions
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "iissssssss",
            $logged_user_id,
            $form_id,
            $_POST['firstJobDuration'],
            $_POST['jobFinding'],
            $_POST['timeToLand'],
            $_POST['jobLevel'],
            $_POST['currentJob'],
            $_POST['initialEarning'],
            $_POST['curriculumRelevant'],
            $_POST['suggestions']
        );
        $stmt->execute();
        $stmt->close();

        if (isset($_POST['competencies'])) {
            $stmt = $mysqli->prepare("INSERT INTO competencies (user_id, personal_info_id, competency) VALUES (?, ?, ?)");
            foreach ($_POST['competencies'] as $competency) {
                $stmt->bind_param("iis", $logged_user_id, $form_id, $competency);
                $stmt->execute();
            }
            $stmt->close();
        }

        $mysqli->commit();

        echo json_encode(['status' => 'success', 'message' => 'Form submitted successfully']);
    } catch (Exception $e) {
        $mysqli->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}
