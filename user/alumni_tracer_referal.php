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
    input[type="number"],
    input[type="email"],
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

    .alumniContainer {
        padding-top: 1rem;
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
        <form id="alumniTracerForm" method="POST" action="process_tracer_referal.php">

            <div class="form-step">
                <h2>Thank You!</h2>
                <div class="thank-you-message">
                    <p>Thank you for taking time out to fill out this questionnaire. Please return this GTS to your institution.</p>
                    <p>Being one of the alumni of your institution, may we request you to list down the names of other college graduates (AY 2000-2001 to AY 2003-2004) from your institution including their email addresses and contact numbers. Their participation will also be needed to make this study more meaningful and useful.</p>
                </div>

                <div id="alumniContainer" class="alumniContainer">
                    <div class="person-header">
                        <h3>Alumnus 1</h3>
                        <button type="button" class="remove-person-btn" onclick="removePerson(this)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="form-group">
                        <label for="name1">Name</label>
                        <input type="text" id="name1" name="graduate_name[]" placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label for="address1">Email Address</label>
                        <input type="email" id="address1" name="graduate_address[]" placeholder="Enter email address">
                    </div>
                    <div class="form-group">
                        <label for="contact1">Contact Number</label>
                        <input type="text" id="contact1" name="graduate_contact[]" placeholder="Enter contact number">
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


            form.addEventListener('input', function(e) {
                if (e.target.hasAttribute('required')) {
                    e.target.classList.remove('error');
                    const errorMessage = e.target.nextElementSibling;
                    if (errorMessage?.classList.contains('error-message')) {
                        errorMessage.remove();
                    }
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);

                showLoading('Submitting your tracer referal form...');

                fetch('user/process_tracer_referal.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.status === 'success') {
                            NotificationSystem.show('Form submitted successfully!', 'success');
                            setTimeout(() => {
                                window.location.href = 'Account?section=Alumni-Tracer-Referal';
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

            });
            let personCount = 1;

            window.addPerson = function() {
                personCount++;
                const container = document.getElementById('alumniContainer');
                const newPerson = document.createElement('div');
                newPerson.className = 'person-entry';
                newPerson.innerHTML = `
        <div class="person-header">
            <h3>Alumnus ${personCount}</h3>
            <button type="button" class="remove-person-btn" onclick="removePerson(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="form-group">
            <label for="name${personCount}">Name</label>
            <input type="text" id="name${personCount}" name="graduate_name[]" placeholder="Enter full name">
        </div>
        <div class="form-group">
            <label for="address${personCount}">Email Address</label>
            <input type="email" id="address${personCount}" name="graduate_address[]" placeholder="Enter email address">
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