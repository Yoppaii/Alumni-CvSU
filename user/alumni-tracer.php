<?php
ob_start();
require('main_db.php');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    header("Location: login.php");
    exit();
}

$logged_user_id = $_SESSION['user_id'];
$check_sql = "SELECT id FROM personal_info WHERE user_id = ?";
$check_stmt = $mysqli->prepare($check_sql);
$check_stmt->bind_param("i", $logged_user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    ob_end_clean();
    echo "<script>window.location.href = 'Account?section=home';</script>";
    exit();
}
$check_stmt->close();

ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Tracer Study</title>
</head>
<style>
    :root {
        --primary-color: #2d6936;
        --secondary-color: #1e40af;
        --background-color: #f4f6f8;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    body {
        background: var(--background-color);
        min-height: 100vh;
        padding: 10px;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .container {
        max-width: auto;
        margin: 20px auto;
        background: white;
        border-radius: 8px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .form-step {
        padding: 24px;
        background: white;
    }

    h2 {
        font-size: 24px;
        color: #111827;
        margin: 0 0 24px 0;
        padding-bottom: 16px;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        color: #374151;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }

    select,
    input[type="date"],
    input[type="text"],
    textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        background-color: white;
        font-size: 14px;
        color: #111827;
        transition: all 0.2s ease;
    }

    select:focus,
    input[type="date"]:focus,
    input[type="text"]:focus,
    textarea:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(45, 105, 54, 0.1);
    }

    .checkbox-group {
        display: flex;
        flex-direction: column;
        gap: 12px;
        margin-top: 8px;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #4b5563;
        font-size: 14px;
        cursor: pointer;
    }

    input[type="checkbox"] {
        width: 16px;
        height: 16px;
        border: 1.5px solid #d1d5db;
        border-radius: 4px;
        cursor: pointer;
    }

    .step-indicators {
        display: flex;
        justify-content: center;
        gap: 12px;
        padding: 24px;
        background: white;
        border-bottom: 1px solid #e5e7eb;
    }

    .step {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #e5e7eb;
        color: #6b7280;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .step.active {
        background-color: var(--primary-color);
        color: white;
    }

    .step.completed {
        background-color: #4ade80;
        color: white;
    }

    .button-group {
        display: flex;
        justify-content: space-between;
        margin-top: 24px;
        padding-top: 16px;
        border-top: 1px solid #e5e7eb;
    }

    .btn {
        padding: 12px 24px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
    }

    .prev-btn {
        background-color: #6b7280;
        color: white;
    }

    .next-btn,
    .submit-btn {
        background-color: var(--primary-color);
        color: white;
    }

    .prev-btn:hover {
        background-color: #4b5563;
    }

    .next-btn:hover,
    .submit-btn:hover {
        background-color: #1f4d27;
    }

    .error {
        border-color: #dc2626 !important;
    }

    .error-message {
        color: #dc2626;
        font-size: 12px;
        margin-top: 4px;
    }

    .person-entry {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 16px;
    }

    .person-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }

    .person-header h3 {
        margin: 0;
        color: #374151;
        font-size: 16px;
    }

    .remove-person-btn {
        background: #ef4444;
        color: white;
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: background-color 0.2s;
    }

    .add-person-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: 6px;
        padding: 12px 24px;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        margin: 16px 0;
    }

    @media (max-width: 640px) {
        .container {
            margin: 10px;
        }

        .form-step {
            padding: 16px;
        }

        .step {
            width: 32px;
            height: 32px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
        }
    }

    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 2000;
    }

    .loading-content {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 4px solid rgba(255, 255, 255, 0.3);
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .loading-text {
        color: white;
        font-size: 14px;
        font-weight: 500;
        animation: pulse 1.5s ease-in-out infinite;
        margin: 0;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes pulse {
        0% {
            opacity: 0.6;
        }

        50% {
            opacity: 1;
        }

        100% {
            opacity: 0.6;
        }
    }

    .loading-overlay-show {
        animation: fadeIn 0.3s ease-in-out forwards;
    }

    .loading-overlay-hide {
        animation: fadeOut 0.3s ease-in-out forwards;
    }

    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .notification {
        background: white;
        padding: 15px 20px;
        border-radius: 6px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 300px;
        max-width: 450px;
        animation: slideIn 0.3s ease-out;
    }

    .notification.success {
        background: #2d6936;
        color: white;
        border-left: 4px solid #1a4721;
    }

    .notification.error {
        background: #dc2626;
        color: white;
        border-left: 4px solid #991b1b;
    }

    .notification-close {
        background: none;
        border: none;
        color: currentColor;
        cursor: pointer;
        padding: 0 5px;
        margin-left: 10px;
        font-size: 20px;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }

        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }
</style>

<body>
    <div id="loadingOverlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <div class="loading-text">Processing your request...</div>
        </div>
    </div>

    <div class="notification-container" id="notificationContainer"></div>

    <div class="container">
        <form id="alumniTracerForm" method="POST" action="process_tracer.php">

            <div class="step-indicators">
                <div class="step active" data-step="1">1</div>
                <div class="step" data-step="2">2</div>
                <div class="step" data-step="3">3</div>
                <div class="step" data-step="4">4</div>
                <div class="step" data-step="5">5</div>
                <div class="step" data-step="6">6</div>
                <div class="step" data-step="7">7</div>
            </div>

            <!-- Step 1: Personal Information -->
            <div class="form-step" id="step1">
                <h2>Personal Information</h2>
                <div class="form-group">
                    <label for="civilStatus">Civil Status</label>
                    <select name="civilStatus" id="civilStatus" required>
                        <option value="">Select Civil Status</option>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                        <option value="separated">Separated</option>
                        <option value="single-parent">Single Parent</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="sex">Sex</label>
                    <select name="sex" id="sex" required>
                        <option value="">Select Sex</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="birthday">Birthday</label>
                    <input type="date" name="birthday" id="birthday" required>
                </div>

                <div class="form-group">
                    <label for="course">Course</label>
                    <select name="course" id="course" required>
                        <option value="">Select Course</option>
                        <option value="BS Information Technology">BS Information Technology</option>
                        <option value="BS Agriculture">BS Agriculture</option>
                        <option value="BS Computer Science">BS Computer Science</option>
                        <option value="BS Animal Science">BS Animal Science</option>
                        <option value="BS Forestry">BS Forestry</option>
                        <option value="BS Agricultural Engineering">BS Agricultural Engineering</option>
                        <option value="BS Food Technology">BS Food Technology</option>
                        <option value="BS Business Administration">BS Business Administration</option>
                        <option value="BS Hospitality Management">BS Hospitality Management</option>
                        <option value="BS Science in Accountancy">BS Science in Accountancy</option>
                        <option value="BS Civil Engineering">BS Civil Engineering</option>
                        <option value="BS Electrical Engineering">BS Electrical Engineering</option>
                        <option value="BS Mechanical Engineering">BS Mechanical Engineering</option>
                        <option value="BS Electronics Engineering">BS Electronics Engineering</option>
                        <option value="BS Environmental Science">BS Environmental Science</option>
                        <option value="BS Criminology">BS Criminology</option>
                        <option value="BS Psychology">BS Psychology</option>
                        <option value="BS Social Work">BS Social Work</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="campus">Campus</label>
                    <select name="campus" id="campus" required>
                        <option value="">Select Campus</option>
                        <option value="main">Main Campus</option>
                        <option value="Cavite City Campus">Cavite City Campus</option>
                        <option value="Gen. Mariano Alvarez Campus">Gen. Mariano Alvarez Campus</option>
                        <option value="Bacoor Campus">Bacoor Campus</option>
                        <option value="Tanza Campus">Tanza Campus</option>
                        <option value="Naic Campus">Naic Campus</option>
                        <option value="Silang Campus">Silang Campus</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="residence">Location Residence</label>
                    <select name="residence" id="residence" required>
                        <option value="">Select Location in Cavite</option>
                        <option value="bacoor">Bacoor</option>
                        <option value="cavite_city">Cavite City</option>
                        <option value="dasmarinas">Dasmariñas</option>
                        <option value="imus">Imus</option>
                        <option value="tagaytay">Tagaytay</option>
                        <option value="general_trias">General Trias</option>
                        <option value="trece_martires">Trece Martires</option>
                        <option value="alfonso">Alfonso</option>
                        <option value="amadeo">Amadeo</option>
                        <option value="antipolo">Antipolo</option>
                        <option value="carmona">Carmona</option>
                        <option value="gen_mariano_alvarez">Gen. Mariano Alvarez (GMA)</option>
                        <option value="indang">Indang</option>
                        <option value="kawit">Kawit</option>
                        <option value="magallanes">Magallanes</option>
                        <option value="maragondon">Maragondon</option>
                        <option value="mendez">Mendez</option>
                        <option value="naic">Naic</option>
                        <option value="noveleta">Noveleta</option>
                        <option value="rosario">Rosario</option>
                        <option value="silang">Silang</option>
                        <option value="tanza">Tanza</option>
                        <option value="ternate">Ternate</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="button" class="btn next-btn">Next</button>
                </div>
            </div>

            <!-- Step 2: Educational Background -->
            <div class="form-step" id="step2" style="display: none;">
                <h2>Educational Background</h2>

                <div class="form-group">
                    <label>Educational Attainment (Baccalaureate Degree Only)</label>
                    <div>
                        <label for="degree_specialization">Degree(s) & Specialization(s)</label>
                        <input type="text" name="degree_specialization" id="degree_specialization" placeholder="e.g., BS Computer Science" required>
                    </div>
                    <div>
                        <label for="college_university">College or University</label>
                        <input type="text" name="college_university" id="college_university" placeholder="e.g., Cavite State University" required>
                    </div>
                    <div>
                        <label for="year_graduated">Year Graduated</label>
                        <input type="number" name="year_graduated" id="year_graduated" placeholder="e.g., 2023" min="1900" max="2100" required>
                    </div>
                    <div>
                        <label for="honors_or_awards">Honor(s) or Award(s) Received</label>
                        <input type="text" name="honors_or_awards" id="honors_or_awards" placeholder="e.g., Magna Cum Laude">
                    </div>
                </div>

                <div class="form-group">
                    <label for="professionalExams">Professional Examination(s) Passed</label>
                    <select name="professionalExams" id="professionalExams" required>
                        <option value="">Select Professional Examination</option>
                        <option value="licensure">Licensure Examination</option>
                        <option value="civil_service">Civil Service Examination</option>
                        <option value="board">Board Examination</option>
                        <option value="certification">Professional Certification</option>
                        <option value="none">None</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="highestEducation">What is your highest level of education completed?</label>
                    <select name="highestEducation" id="highestEducation" required>
                        <option value="">Select Highest Education</option>
                        <option value="bachelors">Bachelor's Degree</option>
                        <option value="masters">Master's Degree</option>
                        <option value="doctorate">Doctorate Degree</option>
                        <option value="post_doctorate">Post-Doctorate</option>
                        <option value="vocational">Vocational/Technical Course</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Reason(s) for taking the course(s) or pursuing degree(s)</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="high_grades_course">
                            High grades in the course or subject area(s) related to the course
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="good_grades_hs">
                            Good grades in high school
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="parent_relative_influence">
                            Influence of parents or relatives
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="peer_influence">
                            Peer influence
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="role_model">
                            Inspired by a role model
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="strong_passion">
                            Strong passion for the profession
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="immediate_employment">
                            Prospect for immediate employment
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="status_prestige">
                            Status or prestige of the profession
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="course_availability">
                            Availability of course offering in chosen institution
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="career_advancement">
                            Prospect of career advancement
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="affordable">
                            Affordable for the family
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="reasons[]" value="attractive_compensation">
                            Prospect of attractive compensation
                        </label>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn prev-btn">Previous</button>
                    <button type="button" class="btn next-btn">Next</button>
                </div>
            </div>

            <!-- Step 3: Training/Advance Studies -->
            <div class="form-step" id="step3" style="display: none;">
                <h2>Training(s)/Advance Studies Attended After College</h2>

                <p class="form-instruction">Please list down all professional or work-related training program(s) including advance studies you have attended after college.</p>

                <div class="form-group">
                    <label for="trainingTitle">Title of Training or Advance Study</label>
                    <input type="text" name="trainingTitle" id="trainingTitle" placeholder="Enter the title of your training or advance study" required>
                </div>

                <div class="form-group">
                    <label for="duration">Duration and Credits Earned</label>
                    <input type="text" name="duration" id="duration" placeholder="e.g., 6 months - 30 credits" required>
                </div>

                <div class="form-group">
                    <label for="institution">Name of Training Institution/College/University</label>
                    <input type="text" name="institution" id="institution" placeholder="Enter the name of the institution" required>
                </div>

                <div class="form-group">
                    <label for="advanceReason">What made you pursue advance studies?</label>
                    <select name="advanceReason" id="advanceReason" required>
                        <option value="">Select your reason</option>
                        <option value="career_advancement">Career Advancement</option>
                        <option value="professional_development">Professional Development</option>
                        <option value="higher_salary">Higher Salary Prospects</option>
                        <option value="personal_growth">Personal Growth and Interest</option>
                        <option value="job_requirement">Job Requirement</option>
                        <option value="academic_interest">Academic Interest</option>
                        <option value="research_opportunity">Research Opportunities</option>
                        <option value="industry_demand">Industry Demand</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="button" class="btn prev-btn">Previous</button>
                    <button type="button" class="btn next-btn">Next</button>
                </div>
            </div>

            <!-- Step 4: Employment Data -->
            <div class="form-step" id="step4" style="display: none;">
                <h2>Employment Data</h2>

                <div class="form-group">
                    <label for="employmentStatus">Are you presently employed?</label>
                    <select name="employmentStatus" id="employmentStatus" required>
                        <option value="">Select Answer</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group" id="unemploymentReasons">
                    <label>Reason(s) why you are not yet employed (Check all that apply)</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="unemploymentReasons[]" value="advanced_study">
                            Advanced study
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="unemploymentReasons[]" value="family_concern">
                            Family concern
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="unemploymentReasons[]" value="health">
                            Health-related reason(s)
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="unemploymentReasons[]" value="no_job_opportunity">
                            No job opportunity
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="unemploymentReasons[]" value="lack_experience">
                            Lack of work experience
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="presentEmploymentStatus">Present Employment Status</label>
                    <select name="presentEmploymentStatus" id="presentEmploymentStatus" required>
                        <option value="">Select Status</option>
                        <option value="regular">Regular/Permanent</option>
                        <option value="contractual">Contractual</option>
                        <option value="self_employed">Self-employed</option>
                        <option value="casual">Casual</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="selfEmployedSkills">If self-employed, what skills acquired in college were you able to apply in your work?</label>
                    <input type="text" name="selfEmployedSkills" id="selfEmployedSkills" placeholder="Enter skills here">
                </div>

                <div class="form-group">
                    <label for="presentOccupation">Present Occupation</label>
                    <input type="text" name="presentOccupation" id="presentOccupation" placeholder="Enter your present occupation">
                </div>

                <div class="form-group">
                    <label for="businessLine">Major line of business of the company</label>
                    <select name="businessLine" id="businessLine" required>
                        <option value="">Select Business Line</option>
                        <option value="agriculture">Agriculture, Hunting and Forestry</option>
                        <option value="Mining and Quarrying">Mining and Quarrying</option>
                        <option value="Fishing">Fishing</option>
                        <option value="Manufacturing">Manufacturing</option>
                        <option value="Electricity, Gas and Water Supply">Electricity, Gas and Water Supply</option>
                        <option value="Construction">Construction</option>
                        <option value="Wholesale and Retail Trade">Wholesale and Retail Trade</option>
                        <option value="Hotels and Restaurants">Hotels and Restaurants</option>
                        <option value="Transport Storage and Communication">Transport Storage and Communication</option>
                        <option value="Financial Intermediation">Financial Intermediation</option>
                        <option value="Education">Education</option>
                        <option value="Public Administration and Defense">Public Administration and Defense</option>
                        <option value="Health and Social Work">Health and Social Work</option>
                        <option value="Extra-territorial Organizations and Bodies">Extra-territorial Organizations and Bodies</option>
                        <option value="Education">Extra-territorial Organizations and Bodies</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="workPlace">Place of Work</label>
                    <select name="workPlace" id="workPlace" required>
                        <option value="">Select Location</option>
                        <option value="local">Local</option>
                        <option value="abroad">Abroad</option>
                        <option value="work_from_home">Work From Home</option>
                        <option value="hybrid">Hybrid</option>
                    </select>
                </div>

                <div class="button-group">
                    <button type="button" class="btn prev-btn">Previous</button>
                    <button type="button" class="btn next-btn">Next</button>
                </div>
            </div>

            <!-- Step 5: Job Experience and Reasons -->
            <div class="form-step" id="step5" style="display: none;">
                <h2>Job Experience and Reasons</h2>

                <div class="form-group">
                    <label for="firstJob">Is this your first job after college?</label>
                    <select name="firstJob" id="firstJob" required>
                        <option value="">Select Answer</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>What are your reason(s) for staying on the job?</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="stayingReasons[]" value="salary">
                            Salary and benefits
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="stayingReasons[]" value="career_growth">
                            Career growth
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="stayingReasons[]" value="work_environment">
                            Good working environment
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="stayingReasons[]" value="location">
                            Strategic location
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="stayingReasons[]" value="work_life_balance">
                            Work-life balance
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="stayingReasons[]" value="Peer influence">
                            Peer influence
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="stayingReasons[]" value="work_life_balance">
                            Work-life balance
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="courseRelated">Is your first job related to the course you took up in college?</label>
                    <select name="courseRelated" id="courseRelated" required>
                        <option value="">Select Answer</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>What were your reasons for accepting the job?</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="acceptingReasons[]" value="salaryBenefits">
                            Salaries and benefits
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="acceptingReasons[]" value="careerChallenge">
                            Career challenge
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="acceptingReasons[]" value="related_to_course">
                            Related to special skills
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="acceptingReasons[]" value="proximity">
                            Proximity to residence
                        </label>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn prev-btn">Previous</button>
                    <button type="button" class="btn next-btn">Next</button>
                </div>
            </div>

            <!-- Step 6: Job Duration and Finding -->
            <div class="form-step" id="step6" style="display: none;">
                <h2>Job Duration and Finding</h2>

                <div class="form-group">
                    <label>What were your reason(s) for changing job?</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="changingReasons[]" value="salary">
                            Better salary
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="changingReasons[]" value="career_growth">
                            Career growth
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="changingReasons[]" value="work_environment">
                            Better work environment
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="firstJobDuration">How long did you stay in your first job?</label>
                    <select name="firstJobDuration" id="firstJobDuration" required>
                        <option value="">Select Duration</option>
                        <option value="less_than_6months">Less than 6 months</option>
                        <option value="6months_1year">6 months to 1 year</option>
                        <option value="1_2years">1-2 years</option>
                        <option value="more_than_2years">More than 2 years</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jobFinding">How did you find your first job?</label>
                    <select name="jobFinding" id="jobFinding" required>
                        <option value="">Select Method</option>
                        <option value="job_fair">Job Fair</option>
                        <option value="advertisement">Advertisement</option>
                        <option value="recommendation">Recommendation</option>
                        <option value="walk_in">Walk-in Application</option>
                        <option value="online">Online Job Portal</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="timeToLand">How long did it take you to land your first job?</label>
                    <select name="timeToLand" id="timeToLand" required>
                        <option value="">Select Duration</option>
                        <option value="less_than_1month">Less than 1 month</option>
                        <option value="1_6months">1-6 months</option>
                        <option value="7_11months">7-11 months</option>
                        <option value="1year_more">1 year or more</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="jobLevel">Job Level Position</label>
                    <select name="jobLevel" id="jobLevel" required>
                        <option value="">Select Position</option>
                        <option value="entry">Entry Level</option>
                        <option value="junior">Junior Level</option>
                        <option value="mid">Mid Level</option>
                        <option value="senior">Senior Level</option>
                        <option value="management">Management Level</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="currentJob">Current or Present Job</label>
                    <select name="currentJob" id="currentJob" required>
                        <option value="">Select Job Type</option>
                        <option value="permanent">Permanent</option>
                        <option value="contractual">Contractual</option>
                        <option value="project_based">Project Based</option>
                        <option value="freelance">Freelance</option>
                        <option value="Self-employed">Self-employed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="initialEarning">What is your initial gross monthly earning in your first job after college?</label>
                    <select name="initialEarning" id="initialEarning" required>
                        <option value="">Select Range</option>
                        <option value="below_10k">Below ₱10,000</option>
                        <option value="10k_20k">₱10,000 - ₱20,000</option>
                        <option value="21k_30k">₱21,000 - ₱30,000</option>
                        <option value="31k_40k">₱31,000 - ₱40,000</option>
                        <option value="above_40k">Above ₱40,000</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="curriculumRelevant">Was the curriculum you had in college relevant to your first job?</label>
                    <select name="curriculumRelevant" id="curriculumRelevant" required>
                        <option value="">Select Answer</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Competencies learned in college that were very useful in your first job</label>
                    <div class="checkbox-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="competencies[]" value="communication">
                            Communication skills
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="competencies[]" value="human_relations">
                            Human relations skills
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="competencies[]" value="entrepreneurial">
                            Entrepreneurial skills
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="competencies[]" value="information_technology">
                            Information technology skills
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="competencies[]" value="problem_solving">
                            Problem-solving skills
                        </label>
                        <label class="checkbox-label">
                            <input type="checkbox" name="competencies[]" value="critical_thinking">
                            Critical thinking skills
                        </label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="suggestions">List down suggestions to further improve your course curriculum:</label>
                    <textarea name="suggestions" id="suggestions" rows="4" placeholder="Enter your suggestions here"></textarea>
                </div>

                <div class="button-group">
                    <button type="button" class="btn prev-btn">Previous</button>
                    <button type="button" class="btn next-btn">Next</button>
                </div>
            </div>

            <!-- Step 7: Thank You -->
            <div class="form-step" id="step7" style="display: none;">
                <h2>Thank You!</h2>
                <div class="thank-you-message">
                    <p>Thank you for taking time out to fill out this questionnaire. Please return this GTS to your institution.</p>
                    <p>Being one of the alumni of your institution, may we request you to list down the names of other college graduates (AY 2000-2001 to AY 2003-2004) from your institution including their addresses and contact numbers. Their participation will also be needed to make this study more meaningful and useful.</p>
                </div>

                <div id="alumniContainer">
                    <div class="person-entry">
                        <div class="person-header">
                            <h3>Person 1</h3>
                            <button type="button" class="remove-person-btn" onclick="removePerson(this)">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="form-group">
                            <label for="name1">Name</label>
                            <input type="text" id="name1" name="graduate_name[]" placeholder="Enter full name">
                        </div>
                        <div class="form-group">
                            <label for="address1">Full Address</label>
                            <input type="text" id="address1" name="graduate_address[]" placeholder="Enter complete address">
                        </div>
                        <div class="form-group">
                            <label for="contact1">Contact Number</label>
                            <input type="text" id="contact1" name="graduate_contact[]" placeholder="Enter contact number">
                        </div>
                    </div>
                </div>

                <button type="button" id="addPersonBtn" class="add-person-btn" onclick="addPerson()">
                    <i class="fas fa-plus"></i> Add Another Person
                </button>

                <div class="button-group">
                    <button type="button" class="btn prev-btn">Previous</button>
                    <button type="submit" class="btn submit-btn">Submit</button>
                </div>
            </div>
        </form>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const NotificationSystem = {
                container: null,
                init: function() {
                    this.container = document.getElementById('notificationContainer');
                },

                show: function(message, type = 'error', duration = 5000) {
                    if (!this.container) return;

                    const notification = document.createElement('div');
                    notification.className = `notification ${type}`;

                    const messageSpan = document.createElement('span');
                    messageSpan.textContent = message;

                    const closeButton = document.createElement('button');
                    closeButton.className = 'notification-close';
                    closeButton.innerHTML = '×';
                    closeButton.onclick = () => this.remove(notification);

                    notification.appendChild(messageSpan);
                    notification.appendChild(closeButton);
                    this.container.appendChild(notification);

                    setTimeout(() => this.remove(notification), duration);
                },

                remove: function(notification) {
                    notification.style.animation = 'slideOut 0.3s ease-out forwards';
                    setTimeout(() => {
                        if (notification.parentElement === this.container) {
                            this.container.removeChild(notification);
                        }
                    }, 300);
                }
            };

            NotificationSystem.init();

            function showLoading(message = 'Processing your request...') {
                const overlay = document.getElementById('loadingOverlay');
                const loadingText = overlay.querySelector('.loading-text');
                if (loadingText) {
                    loadingText.textContent = message;
                }
                overlay.style.display = 'flex';
                overlay.classList.add('loading-overlay-show');
                overlay.classList.remove('loading-overlay-hide');
                document.body.style.overflow = 'hidden';
            }

            function hideLoading() {
                const overlay = document.getElementById('loadingOverlay');
                overlay.classList.add('loading-overlay-hide');
                overlay.classList.remove('loading-overlay-show');
                setTimeout(() => {
                    overlay.style.display = 'none';
                    document.body.style.overflow = '';
                }, 300);
            }

            const form = document.getElementById('alumniTracerForm');
            const steps = document.querySelectorAll('.form-step');
            const stepIndicators = document.querySelectorAll('.step');
            const nextButtons = document.querySelectorAll('.next-btn');
            const prevButtons = document.querySelectorAll('.prev-btn');
            let currentStep = 1;

            const employmentStatus = document.getElementById('employmentStatus');
            const unemploymentReasons = document.getElementById('unemploymentReasons');
            const presentEmploymentStatus = document.getElementById('presentEmploymentStatus');
            const selfEmployedSkills = document.getElementById('selfEmployedSkills');
            const firstJobSelect = document.getElementById('firstJob');
            const stayingReasonsGroup = document.querySelector('input[name="stayingReasons[]"]')?.closest('.form-group');
            const courseRelatedSelect = document.getElementById('courseRelated');
            const acceptingReasonsGroup = document.querySelector('input[name="acceptingReasons[]"]')?.closest('.form-group');

            showStep(currentStep);
            initializeConditionalLogic();

            nextButtons.forEach(button => {
                button.addEventListener('click', () => {
                    if (validateStep(currentStep)) {
                        currentStep++;
                        showStep(currentStep);
                    } else {
                        NotificationSystem.show('Please fill in all required fields', 'error');
                    }
                });
            });

            prevButtons.forEach(button => {
                button.addEventListener('click', () => {
                    currentStep--;
                    showStep(currentStep);
                });
            });

            function initializeConditionalLogic() {
                const reasonsGroup = document.querySelector('input[name="reasons[]"]')?.closest('.checkbox-group');
                if (reasonsGroup) {
                    const labels = reasonsGroup.querySelectorAll('.checkbox-label');
                    labels.forEach(label => {
                        const checkbox = label.querySelector('input[type="checkbox"]');
                        if (checkbox) {
                            const radio = document.createElement('input');
                            radio.type = 'radio';
                            radio.name = 'reasons';
                            radio.value = checkbox.value;
                            checkbox.parentNode.replaceChild(radio, checkbox);
                        }
                    });
                }

                if (unemploymentReasons) unemploymentReasons.style.display = 'none';
                if (selfEmployedSkills) selfEmployedSkills.closest('.form-group').style.display = 'none';
                if (stayingReasonsGroup) stayingReasonsGroup.style.display = 'none';
                if (acceptingReasonsGroup) acceptingReasonsGroup.style.display = 'none';

                if (employmentStatus) {
                    employmentStatus.addEventListener('change', function() {
                        if (unemploymentReasons) {
                            unemploymentReasons.style.display = this.value === 'no' ? 'block' : 'none';
                            if (this.value === 'yes') {
                                const checkboxes = unemploymentReasons.querySelectorAll('input[type="checkbox"]');
                                checkboxes.forEach(cb => cb.checked = false);
                            }
                        }
                    });
                }

                if (presentEmploymentStatus && selfEmployedSkills) {
                    presentEmploymentStatus.addEventListener('change', function() {
                        const skillsGroup = selfEmployedSkills.closest('.form-group');
                        skillsGroup.style.display = this.value === 'self_employed' ? 'block' : 'none';
                        if (this.value !== 'self_employed') {
                            selfEmployedSkills.value = '';
                        }
                    });
                }

                if (firstJobSelect && stayingReasonsGroup) {
                    firstJobSelect.addEventListener('change', function() {
                        stayingReasonsGroup.style.display = this.value === 'yes' ? 'block' : 'none';
                        if (this.value === 'no') {
                            const checkboxes = stayingReasonsGroup.querySelectorAll('input[type="checkbox"]');
                            checkboxes.forEach(cb => cb.checked = false);
                        }
                    });
                }

                if (courseRelatedSelect && acceptingReasonsGroup) {
                    courseRelatedSelect.addEventListener('change', function() {
                        acceptingReasonsGroup.style.display = this.value === 'yes' ? 'block' : 'none';
                        if (this.value === 'no') {
                            const checkboxes = acceptingReasonsGroup.querySelectorAll('input[type="checkbox"]');
                            checkboxes.forEach(cb => cb.checked = false);
                        }
                    });
                }
            }

            function validateStep(stepNumber) {
                const currentStepElement = document.getElementById(`step${stepNumber}`);
                const requiredFields = currentStepElement.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                        if (!field.nextElementSibling?.classList.contains('error-message')) {
                            const errorMessage = document.createElement('div');
                            errorMessage.className = 'error-message';
                            errorMessage.textContent = 'This field is required';
                            field.parentNode.insertBefore(errorMessage, field.nextSibling);
                        }
                    } else {
                        field.classList.remove('error');
                        const errorMessage = field.nextElementSibling;
                        if (errorMessage?.classList.contains('error-message')) {
                            errorMessage.remove();
                        }
                    }
                });

                return isValid;
            }

            function showStep(stepNumber) {
                steps.forEach(step => {
                    step.style.display = 'none';
                });

                const currentStepElement = document.getElementById(`step${stepNumber}`);
                if (currentStepElement) {
                    currentStepElement.style.display = 'block';
                }

                updateStepIndicators(stepNumber);

                const prevButtons = document.querySelectorAll('.prev-btn');
                prevButtons.forEach(button => {
                    button.style.display = stepNumber === 1 ? 'none' : 'inline-block';
                });

                const nextButtons = document.querySelectorAll('.next-btn');
                nextButtons.forEach(button => {
                    button.style.display = stepNumber === steps.length ? 'none' : 'inline-block';
                });
            }

            function updateStepIndicators(stepNumber) {
                stepIndicators.forEach((indicator, index) => {
                    if (index + 1 < stepNumber) {
                        indicator.classList.add('completed');
                        indicator.classList.remove('active');
                    } else if (index + 1 === stepNumber) {
                        indicator.classList.add('active');
                        indicator.classList.remove('completed');
                    } else {
                        indicator.classList.remove('active', 'completed');
                    }
                });
            }

            form.addEventListener('input', function(e) {
                if (e.target.hasAttribute('required')) {
                    e.target.classList.remove('error');
                    const errorMessage = e.target.nextElementSibling;
                    if (errorMessage?.classList.contains('error-message')) {
                        errorMessage.remove();
                    }
                }
            });

            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    if (validateStep(currentStep)) {
                        const formData = new FormData(this);

                        showLoading('Submitting your tracer form...');

                        fetch('user/process_tracer.php', {
                                method: 'POST',
                                body: formData
                            })
                            .then(response => response.json())
                            .then(data => {
                                hideLoading();
                                if (data.status === 'success') {
                                    NotificationSystem.show('Form submitted successfully!', 'success');
                                    setTimeout(() => {
                                        window.location.href = 'Account?section=home';
                                    }, 1500);
                                } else {
                                    NotificationSystem.show(data.message || 'Error submitting form', 'error');
                                }
                            })
                            .catch(error => {
                                hideLoading();
                                console.error('Error:', error);
                                NotificationSystem.show('An error occurred while submitting the form', 'error');
                            });
                    } else {
                        NotificationSystem.show('Please fill in all required fields', 'error');
                    }
                });
            }

            let personCount = 1;

            window.addPerson = function() {
                personCount++;
                const container = document.getElementById('alumniContainer');
                const newPerson = document.createElement('div');
                newPerson.className = 'person-entry';
                newPerson.innerHTML = `
                    <div class="person-header">
                        <h3>Person ${personCount}</h3>
                        <button type="button" class="remove-person-btn" onclick="removePerson(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="name${personCount}">Name</label>
                        <input type="text" id="name${personCount}" name="graduate_name[]" placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label for="address${personCount}">Full Address</label>
                        <input type="text" id="address${personCount}" name="graduate_address[]" placeholder="Enter complete address">
                    </div>
                    <div class="form-group">
                        <label for="contact${personCount}">Contact Number</label>
                        <input type="text" id="contact${personCount}" name="graduate_contact[]" placeholder="Enter contact number">
                    </div>
                `;
                container.appendChild(newPerson);
            };

            window.removePerson = function(button) {
                if (personCount > 1) {
                    const personEntry = button.closest('.person-entry');
                    personEntry.remove();
                    personCount--;
                    const persons = document.querySelectorAll('.person-entry');
                    persons.forEach((person, index) => {
                        person.querySelector('h3').textContent = `Person ${index + 1}`;
                    });
                }
            };
        });
    </script>
</body>

</html>