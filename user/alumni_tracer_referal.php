<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alumni Tracer Study</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<style>
    :root {
        --primary-color: #2d6936;
        --primary-hover: #1f4d27;
        --secondary-color: #1e40af;
        --background-color: #f4f6f8;
        --surface-color: #ffffff;
        --card-bg: #f8fafc;
        --border-color: #e5e7eb;
        --text-primary: #111827;
        --text-secondary: #374151;
        --text-muted: #6b7280;
        --success-color: #2d6936;
        --danger-color: #dc2626;
        --shadow-sm: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
        --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
        --radius: 8px;
        --radius-lg: 12px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        background: var(--background-color);
        min-height: 100vh;
        padding: 10px;
        margin: 0;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
    }

    .container {
        max-width: auto;
        margin: 20px auto;
        background: var(--surface-color);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
        overflow: hidden;
    }

    .form-step {
        padding: 32px;
        background: var(--surface-color);
    }

    .header-section {
        text-align: center;
        margin-bottom: 40px;
        padding-bottom: 24px;
        border-bottom: 2px solid var(--border-color);
    }

    .header-section h2 {
        font-size: 28px;
        color: var(--text-primary);
        margin: 0 0 16px 0;
        font-weight: 700;
    }

    .thank-you-message {
        background: linear-gradient(135deg, #f0f9f4 0%, #ecfdf5 100%);
        border: 1px solid #a7f3d0;
        border-radius: var(--radius);
        padding: 24px;
        margin-bottom: 32px;
    }

    .thank-you-message p {
        margin: 0 0 12px 0;
        color: var(--text-secondary);
        line-height: 1.6;
        font-size: 15px;
    }

    .thank-you-message p:last-child {
        margin-bottom: 0;
    }

    .alumni-section {
        margin-bottom: 32px;
    }

    .section-title {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 24px;
        padding-bottom: 12px;
        border-bottom: 1px solid var(--border-color);
    }

    .section-title h3 {
        margin: 0;
        font-size: 20px;
        color: var(--text-primary);
        font-weight: 600;
    }

    .section-title .icon {
        width: 24px;
        height: 24px;
        background: var(--primary-color);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 12px;
    }

    .alumni-container {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .person-card {
        background: var(--card-bg);
        border: 2px solid var(--border-color);
        border-radius: var(--radius-lg);
        padding: 24px;
        position: relative;
        transition: all 0.3s ease;
    }

    .person-card:hover {
        border-color: var(--primary-color);
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .person-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 16px;
        border-bottom: 1px solid var(--border-color);
    }

    .person-header h4 {
        margin: 0;
        color: var(--text-primary);
        font-size: 18px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .person-number {
        background: var(--primary-color);
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
    }

    .remove-person-btn {
        background: var(--danger-color);
        color: white;
        border: none;
        border-radius: 50%;
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 14px;
    }

    .remove-person-btn:hover {
        background: #b91c1c;
        transform: scale(1.1);
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }

    @media (min-width: 640px) {
        .form-row {
            grid-template-columns: 1fr 1fr;
        }

        .form-row.full-width {
            grid-template-columns: 1fr;
        }
    }

    .form-group {
        position: relative;
    }

    .form-group label {
        display: block;
        color: var(--text-secondary);
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .form-group label .required {
        color: var(--danger-color);
        font-size: 12px;
    }

    .form-group .field-icon {
        color: var(--text-muted);
        font-size: 12px;
    }

    .input-wrapper {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-muted);
        font-size: 16px;
        z-index: 1;
    }

    input[type="text"],
    input[type="email"] {
        width: 100%;
        padding: 12px 16px;
        border: 2px solid var(--border-color);
        border-radius: var(--radius);
        background-color: var(--surface-color);
        font-size: 14px;
        color: var(--text-primary);
        transition: all 0.2s ease;
    }

    .has-icon input {
        padding-left: 40px;
    }

    input:focus {
        outline: none;
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(45, 105, 54, 0.1);
    }

    .add-person-section {
        text-align: center;
        padding: 24px;
        background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
        border: 2px dashed var(--border-color);
        border-radius: var(--radius-lg);
        margin: 24px 0;
        transition: all 0.3s ease;
    }

    .add-person-section:hover {
        border-color: var(--primary-color);
        background: linear-gradient(135deg, #f0f9f4 0%, #ecfdf5 100%);
    }

    .add-person-btn {
        background: var(--primary-color);
        color: white;
        border: none;
        border-radius: var(--radius);
        padding: 14px 28px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.2s ease;
        box-shadow: var(--shadow-sm);
    }

    .add-person-btn:hover {
        background: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .button-group {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-top: 40px;
        padding-top: 24px;
        border-top: 2px solid var(--border-color);
    }

    .btn {
        padding: 14px 28px;
        border-radius: var(--radius);
        font-weight: 600;
        font-size: 15px;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        min-width: 120px;
        justify-content: center;
    }

    .prev-btn {
        background-color: var(--text-muted);
        color: white;
    }

    .prev-btn:hover {
        background-color: #4b5563;
    }

    .submit-btn {
        background-color: var(--primary-color);
        color: white;
        box-shadow: var(--shadow-sm);
    }

    .submit-btn:hover {
        background-color: var(--primary-hover);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .error {
        border-color: var(--danger-color) !important;
    }

    .error-message {
        color: var(--danger-color);
        font-size: 12px;
        margin-top: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .alumni-count {
        background: var(--primary-color);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Loading and Notification styles */
    #loadingOverlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 2000;
        backdrop-filter: blur(4px);
    }

    .loading-content {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 20px;
        background: white;
        padding: 32px;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-lg);
    }

    .loading-spinner {
        width: 48px;
        height: 48px;
        border: 4px solid #e5e7eb;
        border-top: 4px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    .loading-text {
        color: var(--text-primary);
        font-size: 16px;
        font-weight: 500;
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

    .notification-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1000;
    }

    .notification {
        background: white;
        padding: 16px 20px;
        border-radius: var(--radius);
        box-shadow: var(--shadow-lg);
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-width: 320px;
        max-width: 450px;
        animation: slideIn 0.3s ease-out;
        border-left: 4px solid transparent;
    }

    .notification.success {
        background: white;
        color: black;
        border-left-color: var(--success-color);
    }

    .notification.error {
        background: white;
        color: black;
        border-left-color: var(--danger-color);
    }

    .notification-close {
        background: none;
        border: none;
        color: currentColor;
        cursor: pointer;
        padding: 4px;
        margin-left: 12px;
        font-size: 18px;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .notification-close:hover {
        background: rgba(255, 255, 255, 0.2);
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

    /* Mobile responsive */
    @media (max-width: 640px) {
        body {
            padding: 10px;
        }

        .container {
            margin: 0;
        }

        .form-step {
            padding: 20px;
        }

        .person-card {
            padding: 16px;
        }

        .button-group {
            flex-direction: column;
        }

        .btn {
            width: 100%;
        }

        .notification {
            min-width: 280px;
            margin: 0 10px 12px 10px;
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
        <form id="alumniTracerForm" method="POST" action="process_tracer_referal.php">
            <div class="form-step">
                <div class="header-section">
                    <h2>🎓 Thank You for Your Participation!</h2>
                </div>

                <div class="thank-you-message">
                    <p><strong>Help us reach more alumni!</strong> If you know other graduates from your institution, please refer them below so we can also invite them to participate in this important study.</p>
                    <p>Kindly include their full name, email address, and contact number. Your referrals will greatly help in making this study more meaningful and comprehensive.</p>
                </div>

                <div class="alumni-section">
                    <div class="section-title">
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3>Alumni Referrals</h3>
                        <span class="alumni-count" id="alumniCount">1 Person</span>
                    </div>

                    <div id="alumniContainer" class="alumni-container">
                        <div class="person-card" data-person="1">
                            <div class="person-header">
                                <h4>
                                    <span class="person-number">1</span>
                                    Alumni Referral
                                </h4>
                                <button type="button" class="remove-person-btn" onclick="removePerson(this)" title="Remove this person">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>

                            <div class="form-row full-width">
                                <div class="form-group">
                                    <label for="name1">
                                        <i class="fas fa-user field-icon"></i>
                                        Full Name
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper has-icon">
                                        <i class="fas fa-user input-icon"></i>
                                        <input type="text" id="name1" name="graduate_name[]" placeholder="Enter full name" required>
                                    </div>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label for="address1">
                                        <i class="fas fa-envelope field-icon"></i>
                                        Email Address
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper has-icon">
                                        <i class="fas fa-envelope input-icon"></i>
                                        <input type="email" id="address1" name="graduate_address[]" placeholder="Enter email address" required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="contact1">
                                        <i class="fas fa-phone field-icon"></i>
                                        Contact Number
                                        <span class="required">*</span>
                                    </label>
                                    <div class="input-wrapper has-icon">
                                        <i class="fas fa-phone input-icon"></i>
                                        <input type="text" id="contact1" name="graduate_contact[]" placeholder="Enter contact number" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="add-person-section">
                        <button type="button" id="addPersonBtn" class="add-person-btn" onclick="addPerson()">
                            <i class="fas fa-plus"></i>
                            Add Another Alumni
                        </button>
                        <p style="margin: 8px 0 0 0; color: var(--text-muted); font-size: 13px;">
                            You can add multiple alumni to help expand our study
                        </p>
                    </div>
                </div>

                <div class="button-group">
                    <button type="button" class="btn prev-btn">
                        <i class="fas fa-arrow-left"></i>
                        Previous
                    </button>
                    <button type="submit" class="btn submit-btn">
                        <i class="fas fa-paper-plane"></i>
                        Submit Referrals
                    </button>
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
                document.body.style.overflow = 'hidden';
            }

            function hideLoading() {
                const overlay = document.getElementById('loadingOverlay');
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }

            const form = document.getElementById('alumniTracerForm');
            let personCount = 1;

            function updateAlumniCount() {
                const countElement = document.getElementById('alumniCount');
                const count = document.querySelectorAll('.person-card').length;
                countElement.textContent = `${count} Person${count !== 1 ? 's' : ''}`;
            }

            window.addPerson = function() {
                personCount++;
                const container = document.getElementById('alumniContainer');
                const newPerson = document.createElement('div');
                newPerson.className = 'person-card';
                newPerson.setAttribute('data-person', personCount);

                newPerson.innerHTML = `
                    <div class="person-header">
                        <h4>
                            <span class="person-number">${personCount}</span>
                            Alumni Referral
                        </h4>
                        <button type="button" class="remove-person-btn" onclick="removePerson(this)" title="Remove this person">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="form-row full-width">
                        <div class="form-group">
                            <label for="name${personCount}">
                                <i class="fas fa-user field-icon"></i>
                                Full Name
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper has-icon">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" id="name${personCount}" name="graduate_name[]" placeholder="Enter full name" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="address${personCount}">
                                <i class="fas fa-envelope field-icon"></i>
                                Email Address
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper has-icon">
                                <i class="fas fa-envelope input-icon"></i>
                                <input type="email" id="address${personCount}" name="graduate_address[]" placeholder="Enter email address" required>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="contact${personCount}">
                                <i class="fas fa-phone field-icon"></i>
                                Contact Number
                                <span class="required">*</span>
                            </label>
                            <div class="input-wrapper has-icon">
                                <i class="fas fa-phone input-icon"></i>
                                <input type="text" id="contact${personCount}" name="graduate_contact[]" placeholder="Enter contact number" required>
                            </div>
                        </div>
                    </div>
                `;

                container.appendChild(newPerson);
                updateAlumniCount();

                // Smooth scroll to new person
                newPerson.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                // Focus on first input
                setTimeout(() => {
                    newPerson.querySelector('input[type="text"]').focus();
                }, 300);
            };

            window.removePerson = function(button) {
                const personCards = document.querySelectorAll('.person-card');
                if (personCards.length > 1) {
                    const personCard = button.closest('.person-card');
                    personCard.style.animation = 'slideOut 0.3s ease-out forwards';

                    setTimeout(() => {
                        personCard.remove();
                        updateAlumniCount();
                        updatePersonNumbers();
                    }, 300);
                } else {
                    NotificationSystem.show('You must have at least one alumni referral', 'error', 3000);
                }
            };

            function updatePersonNumbers() {
                const personCards = document.querySelectorAll('.person-card');
                personCards.forEach((card, index) => {
                    const numberSpan = card.querySelector('.person-number');
                    numberSpan.textContent = index + 1;
                });
            }

            // Form validation
            form.addEventListener('input', function(e) {
                if (e.target.hasAttribute('required')) {
                    e.target.classList.remove('error');
                    const errorMessage = e.target.parentNode.parentNode.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }
                }
            });

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Validate all required fields
                const requiredFields = this.querySelectorAll('[required]');
                let isValid = true;

                requiredFields.forEach(field => {
                    const errorMessage = field.parentNode.parentNode.querySelector('.error-message');
                    if (errorMessage) {
                        errorMessage.remove();
                    }

                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'error-message';
                        errorDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> This field is required';
                        field.parentNode.parentNode.appendChild(errorDiv);
                    } else {
                        field.classList.remove('error');
                    }
                });

                if (!isValid) {
                    NotificationSystem.show('Please fill in all required fields', 'error');
                    // Scroll to first error
                    const firstError = this.querySelector('.error');
                    if (firstError) {
                        firstError.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        firstError.focus();
                    }
                    return;
                }

                const formData = new FormData(this);
                showLoading('Submitting your alumni referrals...');

                fetch('user/process_tracer_referal.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        hideLoading();
                        if (data.status === 'success') {
                            NotificationSystem.show('Alumni referrals submitted successfully!', 'success');
                            setTimeout(() => {
                                window.location.href = 'Account?section=Alumni-Tracer-Referal';
                            }, 2000);
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

            // Initialize count
            updateAlumniCount();
        });
    </script>
</body>

</html>