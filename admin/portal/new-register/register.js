document.addEventListener('DOMContentLoaded', () => {
    // Cache DOM elements
    const form = document.getElementById('registerForm');
    const stepIndicators = Array.from(document.querySelectorAll('.step'));
    const steps = Array.from(document.querySelectorAll('.form-step'));

    // Get radio buttons
    const alumniRadio = document.getElementById('alumniRadio');
    const guestRadio = document.getElementById('guestRadio');

    // Navigation buttons
    const proceedToStep2Btn = document.getElementById('proceedToStep2Btn');
    const backToStep1FromVerificationBtn = document.getElementById('backToStep1FromVerificationBtn');
    const verifyAlumniBtn = document.getElementById('verifyAlumniBtn');
    const backToVerifyBtn = document.getElementById('backToVerifyBtn');
    const proceedToStep3Btn = document.getElementById('proceedToStep3Btn');
    const sendCodeBtn = document.getElementById('sendCodeBtn');
    const backToStep2Btn = document.getElementById('backToStep2Btn');

    // Step containers
    const step1 = document.getElementById('step1');
    const alumniVerificationStep = document.getElementById('alumniVerificationStep');
    const step2 = document.getElementById('step2');
    const step3 = document.getElementById('step3');

    // Info boxes
    const alumniInfoBox = document.getElementById('alumniInfoBox');
    const guestInfoBox = document.getElementById('guestInfoBox');

    // Initialize form
    let currentStep = 0;
    let totalSteps = 4; // Default for Alumni
    let loadingStartTime = 0; // Track when loading started
    let loadingMinDuration = 0; // Minimum duration for loading in milliseconds

    // Set up the step indicators based on user type
    function setupStepIndicators(isAlumni) {
        // Reset all indicators first
        stepIndicators.forEach(step => {
            step.style.display = 'flex'; // Show all by default
        });

        if (!isAlumni) {
            // If Guest, hide the Alumni Verification step indicator
            totalSteps = 3;

            // Hide the second step indicator and adjust the others
            stepIndicators[1].style.display = 'none';

            // Update the numbers on the remaining steps
            stepIndicators[2].textContent = '2';
            stepIndicators[3].textContent = '3';
        } else {
            // If Alumni, show all 4 steps
            totalSteps = 4;

            // Reset the step numbers
            stepIndicators[1].style.display = 'flex';
            stepIndicators[1].textContent = '2';
            stepIndicators[2].textContent = '3';
            stepIndicators[3].textContent = '4';
        }

        // Initial update of step indicators
        updateStepIndicators(0);
    }

    // Show specific step and update indicators
    function showStep(index) {
        // Hide all steps first
        steps.forEach(step => step.style.display = 'none');

        // Update indicators
        updateStepIndicators(index);

        // Show the appropriate step based on user type and index
        const userType = getSelectedUserType();

        if (userType === 'Alumni') {
            // For Alumni: 4-step flow
            if (index === 0) {
                step1.style.display = 'block';
            } else if (index === 1) {
                alumniVerificationStep.style.display = 'block';
            } else if (index === 2) {
                step2.style.display = 'block';
            } else if (index === 3) {
                step3.style.display = 'block';
            }
        } else {
            // For Guest: 3-step flow (skip verification)
            if (index === 0) {
                step1.style.display = 'block';
            } else if (index === 1) {
                step2.style.display = 'block';
            } else if (index === 2) {
                step3.style.display = 'block';
            }
        }

        currentStep = index;
    }

    // Update step indicators based on current step
    function updateStepIndicators(index) {
        stepIndicators.forEach((indicator, i) => {
            // First reset all classes
            indicator.classList.remove('active', 'completed');

            // For a 3-step process (Guest), we need to adjust the index
            const userType = getSelectedUserType();

            if (userType === 'Guest' && i > 0) {
                // For Guest flow, skip the verification step in indicators
                if (i === 1) {
                    // Skip the verification indicator for Guests
                    return;
                }

                // For Guest, map step 2->1 and 3->2 in the indicators logic
                if (i < index + 1) {
                    indicator.classList.add('completed');
                } else if (i === index + 1) {
                    indicator.classList.add('active');
                }
            } else {
                // Normal flow for Alumni
                if (i < index) {
                    indicator.classList.add('completed');
                } else if (i === index) {
                    indicator.classList.add('active');
                }
            }
        });
    }

    // Helper to get selected user type
    function getSelectedUserType() {
        // Check for selection card first
        const selectedCard = document.querySelector('.selection-card.selected');
        if (selectedCard) {
            return selectedCard.getAttribute('data-value');
        }

        // Fall back to radio button
        const selectedRadio = document.querySelector('input[name="user_type"]:checked');
        return selectedRadio ? selectedRadio.value : 'Alumni'; // Default to Alumni
    }

    // Set up card selection
    function setupSelectionCards() {
        const cards = document.querySelectorAll('.selection-card');

        if (!cards.length) return; // Exit if cards don't exist

        cards.forEach(card => {
            card.addEventListener('click', function () {
                // Remove selected class from all cards
                cards.forEach(c => c.classList.remove('selected'));

                // Add selected class to clicked card
                this.classList.add('selected');

                // Update the hidden radio button
                const value = this.getAttribute('data-value');
                if (value === 'Alumni') {
                    document.getElementById('alumniRadio').checked = true;

                    // Show alumni info box if exists
                    if (alumniInfoBox) {
                        alumniInfoBox.style.display = 'block';
                    }

                    // Hide guest info box if exists
                    if (guestInfoBox) {
                        guestInfoBox.style.display = 'none';
                    }
                } else {
                    document.getElementById('guestRadio').checked = true;

                    // Hide alumni info box if exists
                    if (alumniInfoBox) {
                        alumniInfoBox.style.display = 'none';
                    }

                    // Show guest info box if exists
                    if (guestInfoBox) {
                        guestInfoBox.style.display = 'block';
                    }
                }

                // Trigger change event on radio button to update the form flow
                const event = new Event('change');
                if (value === 'Alumni') {
                    document.getElementById('alumniRadio').dispatchEvent(event);
                } else {
                    document.getElementById('guestRadio').dispatchEvent(event);
                }
            });
        });
    }

    // Set up radio button event listeners
    alumniRadio.addEventListener('change', function () {
        if (this.checked) {
            setupStepIndicators(true);

            // Update selection cards if they exist
            const alumniCard = document.querySelector('.selection-card[data-value="Alumni"]');
            if (alumniCard) {
                document.querySelectorAll('.selection-card').forEach(c => c.classList.remove('selected'));
                alumniCard.classList.add('selected');
            }

            // Show alumni info box if exists
            if (alumniInfoBox) {
                alumniInfoBox.style.display = 'block';
            }

            // Hide guest info box if exists
            if (guestInfoBox) {
                guestInfoBox.style.display = 'none';
            }
        }
    });

    guestRadio.addEventListener('change', function () {
        if (this.checked) {
            setupStepIndicators(false);

            // Update selection cards if they exist
            const guestCard = document.querySelector('.selection-card[data-value="Guest"]');
            if (guestCard) {
                document.querySelectorAll('.selection-card').forEach(c => c.classList.remove('selected'));
                guestCard.classList.add('selected');
            }

            // Hide alumni info box if exists
            if (alumniInfoBox) {
                alumniInfoBox.style.display = 'none';
            }

            // Show guest info box if exists
            if (guestInfoBox) {
                guestInfoBox.style.display = 'block';
            }
        }
    });

    // Set up button event listeners
    proceedToStep2Btn.addEventListener('click', function () {
        const userType = getSelectedUserType();

        if (userType === 'Alumni') {
            // For Alumni, go to verification step
            showStep(1);

        } else {
            // For Guest, skip to personal info
            showStep(1);
        }
    });

    backToStep1FromVerificationBtn.addEventListener('click', function () {
        window.location.reload();

        showStep(0);
    });

    verifyAlumniBtn.addEventListener('click', function () {
        // Validate alumni verification fields
        const alumniIdCardNo = document.getElementById('alumni_id_card_no').value.trim();
        const verifyFirstName = document.getElementById('verify_first_name').value.trim();
        const verifyMiddleName = document.getElementById('verify_middle_name').value.trim();
        const verifyLastName = document.getElementById('verify_last_name').value.trim();

        if (!alumniIdCardNo) {
            showToast('Please enter your Alumni ID Card Number', false);
            return;
        }

        if (!verifyFirstName) {
            showToast('Please enter your First Name', false);
            return;
        }

        if (!verifyLastName) {
            showToast('Please enter your Last Name', false);
            return;
        }

        // Show loading indicator with a minimum 2-second duration
        showLoading();

        setTimeout(() => {
            fetch('/Alumni-CvSU/admin/portal/new-register/verify-alumni.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    alumni_id: alumniIdCardNo,
                    alumni_first_name: verifyFirstName,
                    alumni_middle_name: verifyMiddleName,
                    alumni_last_name: verifyLastName
                })
            })
                .then(response => response.json())
                .then(data => {
                    // Hide loading indicator (will respect minimum duration)
                    hideLoading();

                    if (data.verified) {
                        // Pre-fill the personal information form with the verified data
                        const firstNameField = document.getElementById('register-firstname');
                        const middleNameField = document.getElementById('register-middlename');
                        const lastNameField = document.getElementById('register-lastname');

                        // Set values
                        firstNameField.value = verifyFirstName;
                        middleNameField.value = verifyMiddleName;
                        lastNameField.value = verifyLastName;

                        // Make fields read-only
                        firstNameField.setAttribute('readonly', 'readonly');
                        middleNameField.setAttribute('readonly', 'readonly');
                        lastNameField.setAttribute('readonly', 'readonly');

                        // Add visual styling to indicate read-only state
                        const readOnlyStyle = 'background-color: #f0f0f0; cursor: not-allowed;';
                        firstNameField.setAttribute('style', readOnlyStyle);
                        middleNameField.setAttribute('style', readOnlyStyle);
                        lastNameField.setAttribute('style', readOnlyStyle);

                        // Optional: Add tooltip to explain why fields are read-only
                        const readOnlyTooltip = 'This field is locked based on verified alumni information.';
                        firstNameField.setAttribute('title', readOnlyTooltip);
                        middleNameField.setAttribute('title', readOnlyTooltip);
                        lastNameField.setAttribute('title', readOnlyTooltip);

                        showToast('Alumni status verified successfully! Your name details have been locked.', false);

                        // Proceed to personal info step
                        showStep(2);
                    } else {
                        showToast(data.message || 'Alumni verification failed. Please check your details.');
                    }
                })
                .catch(error => {
                    // Hide loading indicator (will respect minimum duration)
                    hideLoading();

                    showToast('An error occurred. Please try again. Details: ' + error.message, false);
                    console.error('Error:', error);
                });
        }, 500); // Simulate a delay for loading


    });

    backToVerifyBtn.addEventListener('click', function () {
        const userType = getSelectedUserType();

        if (userType === 'Alumni') {
            // Go back to verification step
            showStep(1);
        } else {
            // Go back to selection step
            showStep(0);
        }
    });

    proceedToStep3Btn.addEventListener('click', function () {
        // Validate personal information fields
        if (!validatePersonalInfo()) {
            return;
        }

        const userType = getSelectedUserType();

        if (userType === 'Alumni') {
            // For Alumni, go to step 3 (account creation)
            showStep(3);
        } else {
            // For Guest, go to step 2 (account creation)
            showStep(2);
        }
    });

    backToStep2Btn.addEventListener('click', function () {
        const userType = getSelectedUserType();

        if (userType === 'Alumni') {
            // Go back to verification step
            showStep(2);
        } else {
            // Go back to selection step
            showStep(1);
        }
    });

    // Validate personal info fields
    function validatePersonalInfo() {
        const firstName = document.getElementById('register-firstname').value.trim();
        const lastName = document.getElementById('register-lastname').value.trim();
        const address = document.getElementById('register-address').value.trim();
        const phone = document.getElementById('register-phone').value.trim();
        const position = document.getElementById('register-position').value.trim();

        if (!firstName || !lastName) {
            showToast('Please enter your full name', false);
            return false;
        }

        if (!address) {
            showToast('Please enter your address', false);
            return false;
        }

        if (!phone) {
            showToast('Please enter your phone number', false);
            return false;
        }

        if (!position) {
            showToast('Please enter your position or role', false);
            return false;
        }

        return true;
    }

    // Password toggle functionality
    document.getElementById('passwordToggle').addEventListener('click', function () {
        const passwordField = document.getElementById('register-password');
        togglePasswordVisibility(passwordField, this);
    });

    document.getElementById('confirmPasswordToggle').addEventListener('click', function () {
        const confirmPasswordField = document.getElementById('register-confirmPassword');
        togglePasswordVisibility(confirmPasswordField, this);
    });

    document.querySelector('.login-link a').addEventListener('click', function (e) {
        e.preventDefault();
        showLoading();
        setTimeout(() => {
            window.location.href = this.href;
        }, 500);
    });

    function togglePasswordVisibility(inputField, icon) {
        if (inputField.type === 'password') {
            inputField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            inputField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    // Validate email and password
    function validateEmailPassword(email, password, confirmPassword) {
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            showToast('Please enter a valid email address', false);
            return false;
        }

        // Password validation
        if (password.length < 8) {
            showToast('Password must be at least 8 characters long', false);
            return false;
        }

        // Password match validation
        if (password !== confirmPassword) {
            showToast('Passwords do not match', false);
            return false;
        }

        return true;
    }

    // Handle send code button
    sendCodeBtn.addEventListener('click', function () {
        // Get form field values with proper null checks and trim
        const email = document.getElementById('register-email')?.value?.trim() || '';
        const password = document.getElementById('register-password')?.value?.trim() || '';
        const confirmPassword = document.getElementById('register-confirmPassword')?.value?.trim() || '';

        // Validate email and password fields
        if (!email) {
            showToast('Email is required.', false);
            return;
        }
        if (!password) {
            showToast('Password is required.', false);
            return;
        }
        if (!confirmPassword) {
            showToast('Please confirm your password.', false);
            return;
        }

        // Validate email and password
        if (!validateEmailPassword(email, password, confirmPassword)) {
            return;
        }

        // Other validation and form submission logic from the original code...
        const firstName = document.getElementById('register-firstname')?.value?.trim() || '';
        const middleName = document.getElementById('register-middlename')?.value?.trim() || '';
        const lastName = document.getElementById('register-lastname')?.value?.trim() || '';
        const address = document.getElementById('register-address')?.value?.trim() || '';
        const telephone = document.getElementById('register-telephone')?.value?.trim() || '';
        const phoneNumber = document.getElementById('register-phone')?.value?.trim() || '';
        const position = document.getElementById('register-position')?.value?.trim() || '';
        const userType = getSelectedUserType();

        // Check required fields
        if (!firstName) {
            showToast('First name is required.', false);
            return;
        }
        if (!lastName) {
            showToast('Last name is required.', false);
            return;
        }
        if (!address) {
            showToast('Address is required.', false);
            return;
        }
        if (!phoneNumber) {
            showToast('Phone number is required.', false);
            return;
        }
        if (!position) {
            showToast('Position is required.', false);
            return;
        }

        // Show loading indicator
        showLoading();

        const formData = new FormData();

        // Only append values that exist
        formData.append('email', email);
        formData.append('password', password);

        // Only append optional fields if they have a value
        if (firstName) formData.append('firstName', firstName);
        if (middleName) formData.append('middleName', middleName);
        if (lastName) formData.append('lastName', lastName);
        if (address) formData.append('address', address);
        if (telephone) formData.append('telephone', telephone);
        if (phoneNumber) formData.append('phoneNumber', phoneNumber);
        if (position) formData.append('position', position);
        if (userType) formData.append('userType', userType);

        // If user is alumni, add alumni ID card number
        if (userType === 'Alumni') {
            const alumniIdCardNo = document.getElementById('alumni_id_card_no')?.value?.trim() || '';
            if (alumniIdCardNo) {
                formData.append('alumniIdCardNo', alumniIdCardNo);
            }
        }

        // Use fetch for the actual API call
        fetch('/Alumni-CvSU/admin/Sending-Code.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                hideLoading();

                if (data.status === 'error') {
                    // Display error message but DO NOT redirect
                    showToast(data.message, false);
                } else if (data.status === false) {
                    // Only store data and redirect if successful
                    showToast(data.message, false);

                    // Save form data to sessionStorage
                    try {
                        sessionStorage.setItem('register_email', email);
                        sessionStorage.setItem('register_firstName', firstName);
                        sessionStorage.setItem('register_lastName', lastName);
                        sessionStorage.setItem('register_middleName', middleName);
                        sessionStorage.setItem('register_address', address);
                        sessionStorage.setItem('register_telephone', telephone);
                        sessionStorage.setItem('register_phoneNumber', phoneNumber);
                        sessionStorage.setItem('register_position', position);
                        sessionStorage.setItem('register_userType', userType);

                        // If user is alumni, also save alumni ID
                        if (userType === 'Alumni') {
                            const alumniIdCardNo = document.getElementById('alumni_id_card_no')?.value?.trim() || '';
                            if (alumniIdCardNo) {
                                sessionStorage.setItem('register_alumniIdCardNo', alumniIdCardNo);
                            }
                        }
                    } catch (e) {
                        console.warn('Could not save registration data to sessionStorage', e);
                    }

                    // Only redirect on success after a short delay for user to see success message
                    setTimeout(() => {
                        window.location.href = '?Cavite-State-University=verify';
                    }, 1500);
                }
            })
            .catch(error => {
                hideLoading();
                showToast('An error occurred. Please try again. Details: ' + error.message, false);
                console.error('Error:', error);
                // Do not redirect on error
            });
    });

    // Existing utility functions with improvements
    function showToast(message, isSuccess = true) {
        const toast = document.getElementById('view-booking-toast');
        const toastMessage = toast.querySelector('.view-booking-toast-message');
        toastMessage.textContent = message;

        // Dynamically set icon
        const iconDiv = toast.querySelector('.view-booking-toast-icon');
        iconDiv.innerHTML = isSuccess
            ? '<i class="fas fa-check-circle"></i>'
            : '<i class="fas fa-exclamation-triangle"></i>';

        toast.className = 'view-booking-toast';
        if (isSuccess) {
            toast.classList.add('view-booking-toast-success');
        } else {
            toast.classList.add('view-booking-toast-error');
        }

        toast.style.display = 'flex';

        setTimeout(() => {
            toast.style.display = 'none';
        }, 3000);

        const closeToast = toast.querySelector('.view-booking-toast-close');
        closeToast.onclick = () => {
            toast.style.display = 'none';
        };
    }


    // IMPROVED: showLoading function with minimum duration parameter
    function showLoading(minDuration = 0) {
        loadingStartTime = Date.now();
        loadingMinDuration = minDuration;

        let overlay = document.getElementById('loadingOverlay');
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = 'loadingOverlay';
            overlay.style.position = 'fixed';
            overlay.style.top = '0';
            overlay.style.left = '0';
            overlay.style.width = '100%';
            overlay.style.height = '100%';
            overlay.style.backgroundColor = 'rgba(0,0,0,0.5)';
            overlay.style.display = 'flex';
            overlay.style.justifyContent = 'center';
            overlay.style.alignItems = 'center';
            overlay.style.zIndex = '9999';
            overlay.style.transition = 'opacity 0.3s ease';

            const spinner = document.createElement('div');
            spinner.className = 'loading-spinner';
            spinner.style.width = '50px';
            spinner.style.height = '50px';
            spinner.style.border = '5px solid #f3f3f3';
            spinner.style.borderTop = '5px solid #3498db';
            spinner.style.borderRadius = '50%';
            spinner.style.animation = 'spin 1s linear infinite';

            // Add loading text below the spinner
            const loadingText = document.createElement('div');
            loadingText.textContent = 'Verifying...';
            loadingText.style.color = 'white';
            loadingText.style.marginTop = '15px';
            loadingText.style.fontWeight = 'bold';
            loadingText.style.textShadow = '1px 1px 2px rgba(0,0,0,0.7)';

            const loadingContainer = document.createElement('div');
            loadingContainer.style.display = 'flex';
            loadingContainer.style.flexDirection = 'column';
            loadingContainer.style.alignItems = 'center';

            loadingContainer.appendChild(spinner);
            loadingContainer.appendChild(loadingText);

            const style = document.createElement('style');
            style.textContent = '@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }';

            document.head.appendChild(style);
            overlay.appendChild(loadingContainer);
            document.body.appendChild(overlay);
        }

        overlay.style.display = 'flex';
        overlay.style.opacity = '1';
        overlay.classList.add('loading-overlay-show');
    }

    // IMPROVED: hideLoading function that respects minimum duration
    function hideLoading() {
        const overlay = document.getElementById('loadingOverlay');
        if (overlay) {
            const currentTime = Date.now();
            const elapsedTime = currentTime - loadingStartTime;

            if (elapsedTime < loadingMinDuration) {
                // If we haven't met the minimum duration yet, wait before hiding
                const remainingTime = loadingMinDuration - elapsedTime;
                setTimeout(() => {
                    hideLoadingOverlay(overlay);
                }, remainingTime);
            } else {
                // We've already met or exceeded the minimum duration, hide immediately
                hideLoadingOverlay(overlay);
            }
        }
    }

    // Helper function to handle the actual overlay hiding
    function hideLoadingOverlay(overlay) {
        overlay.style.opacity = '0';
        setTimeout(() => {
            overlay.style.display = 'none';
            overlay.classList.remove('loading-overlay-show');
        }, 300);
    }

    // Initialize the form with default settings
    setupSelectionCards(); // Initialize card selection
    setupStepIndicators(true); // Default to Alumni flow
    showStep(0); // Show the first step
});