<?php
require('../main_db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit();
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
        
        $stmt->bind_param("issssss", 
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
        
        $stmt->bind_param("iisssisss",
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
        
        $stmt->bind_param("iissss",
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
        
        $stmt->bind_param("iissssss",
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
        
        $stmt->bind_param("iiss",
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
        
        $stmt->bind_param("iissssssss",
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

        // Step 7: Other Alumni Information
        if (isset($_POST['graduate_name'])) {
            $stmt = $mysqli->prepare("INSERT INTO other_alumni (
                user_id, personal_info_id, name, address, contact_number
            ) VALUES (?, ?, ?, ?, ?)");
            
            for ($i = 0; $i < count($_POST['graduate_name']); $i++) {
                if (!empty($_POST['graduate_name'][$i])) {
                    $stmt->bind_param("iisss",
                        $logged_user_id,
                        $form_id,
                        $_POST['graduate_name'][$i],
                        $_POST['graduate_address'][$i],
                        $_POST['graduate_contact'][$i]
                    );
                    $stmt->execute();
                }
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
?>