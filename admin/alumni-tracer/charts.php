<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Graduate Tracer Survey</title>
    <style>
        .survey-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .survey-table th, .survey-table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .survey-table th {
            background-color: #f4f4f4;
        }
        .analytics-btn {
            background-color: #4CAF50;
            color: white;
            padding: 8px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }
        .analytics-btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Graduate Tracer Survey Results</h2>
        <table class="survey-table">
            <thead>
                <tr>
                    <th>Question Number</th>
                    <th>Question</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Array with question data including short names for URLs
                $questions = array(
                    1 => ["question" => "Sex", "section" => "sex"],
                    2 => ["question" => "Campus", "section" => "campus"],
                    3 => ["question" => "Civil Status", "section" => "civil-status"],
                    4 => ["question" => "Course", "section" => "course"],
                    5 => ["question" => "Location Residence", "section" => "location"],
                    6 => ["question" => "Degree(s) & Specialization(s)", "section" => "degree"],
                    7 => ["question" => "College or University", "section" => "college"],
                    8 => ["question" => "Year Graduated", "section" => "year-grad"],
                    9 => ["question" => "Honor(s) or Award(s) Received", "section" => "honors"],
                    10 => ["question" => "Professional Examination(s) Passed", "section" => "exams"],
                    11 => ["question" => "What is your highest level of education completed?", "section" => "educ-level"],
                    12 => ["question" => "Reason(s) for taking the course(s) or pursuing degree(s)", "section" => "course-reason"],
                    13 => ["question" => "Title of Training or Advance Study", "section" => "training"],
                    14 => ["question" => "Duration and Credits Earned", "section" => "credits"],
                    15 => ["question" => "Name of Training Institution/College/University", "section" => "institution"],
                    16 => ["question" => "What made you pursue advance studies?", "section" => "adv-studies"],
                    17 => ["question" => "Are you presently employed?", "section" => "employment"],
                    18 => ["question" => "Reason(s) why you are not yet employed", "section" => "unemployed-reason"],
                    19 => ["question" => "Present Employment Status", "section" => "emp-status"],
                    20 => ["question" => "If self-employed, what skills acquired in college were you able to apply in your work?", "section" => "self-emp-skills"],
                    21 => ["question" => "Present Occupation", "section" => "occupation"],
                    22 => ["question" => "Major line of business of the company", "section" => "business-line"],
                    23 => ["question" => "Place of Work", "section" => "work-place"],
                    24 => ["question" => "Is this your first job after college?", "section" => "first-job"],
                    25 => ["question" => "What are your reason(s) for staying on the job?", "section" => "job-retention"],
                    26 => ["question" => "Is your first job related to the course you took up in college?", "section" => "course-related"],
                    27 => ["question" => "What were your reasons for accepting the job?", "section" => "job-acceptance"],
                    28 => ["question" => "What were your reason(s) for changing job?", "section" => "job-change"],
                    29 => ["question" => "How long did you stay in your first job?", "section" => "first-job-duration"],
                    30 => ["question" => "How did you find your first job?", "section" => "job-search"],
                    31 => ["question" => "How long did it take you to land your first job?", "section" => "job-landing"],
                    32 => ["question" => "Job Level Position", "section" => "job-level"],
                    33 => ["question" => "Current or Present Job", "section" => "current-job"],
                    34 => ["question" => "What is your initial gross monthly earning in your first job after college?", "section" => "initial-salary"],
                    35 => ["question" => "Was the curriculum you had in college relevant to your first job?", "section" => "curriculum-relevance"],
                    36 => ["question" => "Competencies learned in college that were very useful in your first job", "section" => "competencies"]
                );

                foreach ($questions as $num => $data) {
                    echo "<tr>";
                    echo "<td>$num</td>";
                    echo "<td>{$data['question']}</td>";
                    echo "<td><a href='?section=view-{$data['section']}&question_id=$num' class='analytics-btn'>View Analytics</a></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>