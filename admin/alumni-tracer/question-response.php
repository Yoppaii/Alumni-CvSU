<?php
// Array of all questions
$questions = [
    ["id" => 1, "title" => "Civil Status", "description" => "Alumni civil status information"],
    ["id" => 2, "title" => "Sex", "description" => "Gender demographics"],
    ["id" => 3, "title" => "Course", "description" => "Program or course taken"],
    ["id" => 4, "title" => "Campus", "description" => "Campus location attended"],
    ["id" => 5, "title" => "Location Residence", "description" => "Current residence information"],
    ["id" => 6, "title" => "Educational Attainment", "description" => "Baccalaureate Degree Only"],
    ["id" => 7, "title" => "Degree(s) & Specialization(s)", "description" => "Information about degrees and specializations"],
    ["id" => 8, "title" => "College or University", "description" => "Institution information"],
    ["id" => 9, "title" => "Year Graduated", "description" => "Year of graduation"],
    ["id" => 10, "title" => "Honor(s) or Award(s) Received", "description" => "Academic achievements"],
    ["id" => 11, "title" => "Professional Examination(s) Passed", "description" => "Professional certifications"],
    ["id" => 12, "title" => "Highest Level of Education", "description" => "Highest educational attainment"],
    ["id" => 13, "title" => "Reason(s) for Taking Course", "description" => "Motivation for pursuing degree(s)"],
    ["id" => 14, "title" => "Title of Training or Advance Study", "description" => "Advanced education information"],
    ["id" => 15, "title" => "Duration and Credits Earned", "description" => "Length of study and credits"],
    ["id" => 16, "title" => "Name of Training Institution", "description" => "Institution for advanced studies"],
    ["id" => 17, "title" => "Motivation for Advanced Studies", "description" => "What made you pursue advance studies"],
    ["id" => 18, "title" => "Current Employment", "description" => "Are you presently employed?"],
    ["id" => 19, "title" => "Unemployment Reasons", "description" => "Reason(s) why you are not yet employed"],
    ["id" => 20, "title" => "Present Employment Status", "description" => "Current employment situation"],
    ["id" => 21, "title" => "Self-Employment Skills", "description" => "Skills from college applied in self-employment"],
    ["id" => 22, "title" => "Present Occupation", "description" => "Current job role"],
    ["id" => 23, "title" => "Company Business Line", "description" => "Major line of business of the company"],
    ["id" => 24, "title" => "Place of Work", "description" => "Work location information"],
    ["id" => 25, "title" => "First Job After College", "description" => "Is this your first job after college?"],
    ["id" => 26, "title" => "Job Retention Reasons", "description" => "What are your reason(s) for staying on the job?"],
    ["id" => 27, "title" => "Course Relevance to First Job", "description" => "Is your first job related to your course?"],
    ["id" => 28, "title" => "Job Acceptance Reasons", "description" => "What were your reasons for accepting the job?"],
    ["id" => 29, "title" => "Job Change Reasons", "description" => "What were your reason(s) for changing job?"],
    ["id" => 30, "title" => "First Job Duration", "description" => "How long did you stay in your first job?"],
    ["id" => 31, "title" => "Job Search Method", "description" => "How did you find your first job?"],
    ["id" => 32, "title" => "Time to Land First Job", "description" => "How long did it take to find first job?"],
    ["id" => 33, "title" => "Job Level Position", "description" => "Current position level"],
    ["id" => 34, "title" => "Current/Present Job", "description" => "Details about current position"],
    ["id" => 35, "title" => "Initial Monthly Earning", "description" => "Initial gross monthly earning in first job"],
    ["id" => 36, "title" => "Curriculum Relevance", "description" => "Was the curriculum relevant to first job?"],
    ["id" => 37, "title" => "Useful College Competencies", "description" => "Competencies from college useful in first job"]
];
?>

<style>
.question-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    padding: 20px;
}

.question-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    overflow: hidden;
}

.question-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.card-header {
    background: #10b981;
    color: white;
    padding: 15px;
    font-size: 1.1em;
    font-weight: bold;
}

.card-body {
    padding: 15px;
}

.card-description {
    color: #666;
    margin-bottom: 15px;
    min-height: 40px;
}

.view-details-btn {
    background: #10b981;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
    transition: background 0.2s;
}

.view-details-btn:hover {
    background: #059669;
}

/* Modal styles */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.modal-content {
    position: relative;
    background: white;
    margin: 10% auto;
    padding: 20px;
    width: 80%;
    max-width: 600px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.close-btn {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 24px;
    cursor: pointer;
    color: #666;
}

.modal-header {
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.modal-title {
    font-size: 1.5em;
    color: #333;
}

.stats-container {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    margin-top: 15px;
}

@media (max-width: 768px) {
    .question-grid {
        grid-template-columns: 1fr;
    }
    
    .modal-content {
        width: 95%;
        margin: 5% auto;
    }
}
</style>

<div class="question-grid">
    <?php foreach ($questions as $question): ?>
        <div class="question-card">
            <div class="card-header">
                <?php echo htmlspecialchars($question['title']); ?>
            </div>
            <div class="card-body">
                <div class="card-description">
                    <?php echo htmlspecialchars($question['description']); ?>
                </div>
                <button class="view-details-btn" onclick="showDetails(<?php echo $question['id']; ?>)">
                    View Details
                </button>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Modal -->
<div id="detailsModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeModal()">&times;</span>
        <div class="modal-header">
            <h2 id="modalTitle" class="modal-title"></h2>
        </div>
        <div id="modalBody">
            <div id="questionDescription"></div>
            <div class="stats-container">
                <h3>Response Statistics</h3>
                <div id="questionStats">
                    <!-- Statistics will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const questions = <?php echo json_encode($questions); ?>;

function showDetails(questionId) {
    const question = questions.find(q => q.id === questionId);
    if (!question) return;

    // Update modal content
    document.getElementById('modalTitle').textContent = question.title;
    document.getElementById('questionDescription').textContent = question.description;
    
    // You can load additional statistics here via AJAX
    document.getElementById('questionStats').innerHTML = `
        <p>Total Responses: Loading...</p>
        <p>Average Rating: Loading...</p>
        <p>Most Common Response: Loading...</p>
    `;

    // Show modal
    document.getElementById('detailsModal').style.display = 'block';
    
    // Optional: Load statistics via AJAX
    loadQuestionStats(questionId);
}

function closeModal() {
    document.getElementById('detailsModal').style.display = 'none';
}

function loadQuestionStats(questionId) {
    // Example AJAX call to load statistics
    // Replace with your actual endpoint
    fetch(`get_question_stats.php?id=${questionId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('questionStats').innerHTML = `
                <p>Total Responses: ${data.totalResponses}</p>
                <p>Average Rating: ${data.averageRating}</p>
                <p>Most Common Response: ${data.commonResponse}</p>
            `;
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
            document.getElementById('questionStats').innerHTML = 'Error loading statistics';
        });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('detailsModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});
</script>