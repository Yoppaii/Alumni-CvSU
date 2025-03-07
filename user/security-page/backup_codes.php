<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Backup Codes Management</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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
            margin: 0;
        }

        .back-button {
            display: flex;
            align-items: center;
            color: #6b7280;
            text-decoration: none;
            font-size: 14px;
            margin: 20px auto;
            max-width: 1200px;
            padding: 0 20px;
        }

        .back-button i {
            margin-right: 8px;
        }

        .back-button:hover {
            color: #374151;
        }

        .backup-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px 20px;
        }

        .backup-main-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            margin-bottom: 24px;
            overflow: hidden;
        }

        .backup-card-content {
            padding: 24px;
            display: flex;
            gap: 24px;
        }

        .backup-content-left {
            flex: 1;
        }

        .backup-content-right {
            width: 200px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .backup-heading {
            font-size: 24px;
            color: #111827;
            margin: 0 0 12px 0;
        }

        .backup-description {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .backup-toggle-button {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s;
        }

        .backup-toggle-button:hover {
            background-color: #235228;
        }

        .backup-toggle-button.off {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .backup-grids-container {
            display: none;
            gap: 20px;
            margin-top: 20px;
        }

        .backup-grids-container.show {
            display: flex;
        }

        .backup-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            flex: 1;
        }

        .backup-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .backup-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .backup-header h1 i {
            color: var(--primary-color);
        }

        .backup-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .backup-content {
            padding: 24px;
        }

        .backup-codes-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }

        .backup-code-item {
            font-family: monospace;
            font-size: 16px;
            padding: 12px;
            background: #f3f4f6;
            border-radius: 4px;
            text-align: center;
            letter-spacing: 1px;
            color: #374151;
        }

        .backup-actions {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding-top: 16px;
            border-top: 1px solid #e5e7eb;
        }

        .backup-btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .backup-btn-secondary {
            background-color: #f3f4f6;
            color: #374151;
        }

        .backup-btn-secondary:hover {
            background-color: #e5e7eb;
        }

        .backup-btn-primary {
            background-color: var(--primary-color);
            color: white;
        }

        .backup-btn-primary:hover {
            background-color: #245329;
        }

        .backup-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .backup-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .backup-modal-content {
            background-color: white;
            padding: 24px;
            border-radius: 8px;
            width: 90%;
            max-width: 400px;
        }

        .backup-modal-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 16px;
            color: #111827;
        }

        .backup-modal-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 24px;
        }

        .backup-loading-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .backup-loading-content {
            text-align: center;
        }

        .backup-loading-content img {
            width: 60px;
            height: 60px;
            margin-bottom: 10px;
        }

        .backup-loading-content p {
            margin: 0;
            color: #374151;
            font-size: 16px;
            font-weight: 500;
        }

        .backup-notification {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 16px;
            border-radius: 6px;
            background: white;
            box-shadow: var(--shadow-md);
            align-items: center;
            gap: 12px;
            z-index: 2100;
            max-width: 350px;
            transform: translateX(150%);
            transition: transform 0.3s ease-in-out;
        }

        .backup-notification.show {
            transform: translateX(0);
        }

        .backup-notification i {
            font-size: 20px;
        }

        .backup-notification.success {
            border-left: 4px solid var(--primary-color);
        }

        .backup-notification.success i {
            color: var(--primary-color);
        }

        .backup-notification.error {
            border-left: 4px solid #dc2626;
        }

        .backup-notification.error i {
            color: #dc2626;
        }

        @media (max-width: 1024px) {
            .backup-grids-container {
                flex-direction: column;
            }

            .backup-content-right {
                display: none;
            }
        }

        @media (max-width: 640px) {
            .backup-codes-grid {
                grid-template-columns: 1fr;
            }
            
            .backup-actions {
                flex-direction: column;
            }
            
            .backup-btn {
                width: 100%;
            }

            .backup-modal-buttons {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <a href="Account?section=security-settings" class="back-button">
        <i class="fas fa-arrow-left"></i>
        Back to Security Settings
    </a>

    <div class="backup-container">
        <div class="backup-main-card">
            <div class="backup-card-content">
                <div class="backup-content-left">
                    <h1 class="backup-heading">Your account is not protected by Backup Codes</h1>
                    <p class="backup-description">
                        Get backup codes to use when you can't access your other two-factor sign-in methods. Keep them somewhere safe.
                    </p>
                    <button class="backup-toggle-button" id="backupToggleButton">
                        Turn on Backup Codes
                    </button>
                </div>
            </div>
        </div>

        <div class="backup-grids-container" id="backupGridsContainer">
            <div class="backup-card">
                <div class="backup-header">
                    <h1><i class="fas fa-key"></i> Generate Backup Codes</h1>
                    <p>Keep these backup codes in a safe place. Each code can only be used once if you lose access to your account.</p>
                </div>
                <div class="backup-content">
                    <div class="backup-codes-grid" id="backupGenerateGrid">
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                    </div>
                    <div class="backup-actions">
                        <button id="backupGenerateBtn" class="backup-btn backup-btn-secondary">Generate New Codes</button>
                        <button id="backupSaveBtn" class="backup-btn backup-btn-primary" disabled>Save Codes</button>
                    </div>
                </div>
            </div>

            <div class="backup-card">
                <div class="backup-header">
                    <h1><i class="fas fa-history"></i> Active Backup Codes</h1>
                    <p>These are your active backup codes. Keep them secure and don't share them with anyone.</p>
                </div>
                <div class="backup-content">
                    <div class="backup-codes-grid" id="backupActiveGrid">
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                        <div class="backup-code-item">- - - - - -</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="backup-modal" id="confirmDisableModal">
        <div class="backup-modal-content">
            <h3 class="backup-modal-title">Turn off Backup Codes?</h3>
            <p style="margin-bottom: 16px; font-size: 14px; color: #6b7280;">
                This will remove all your backup codes. You won't be able to use them to sign in.
            </p>
            <div class="backup-modal-buttons">
                <button class="backup-btn backup-btn-secondary" onclick="closeConfirmModal()">Cancel</button>
                <button class="backup-btn backup-btn-primary" style="background-color: #dc2626;" onclick="confirmDisableBackupCodes()">Turn off</button>
            </div>
        </div>
    </div>

    <div class="backup-loading-overlay" id="backupLoadingOverlay">
        <div class="backup-loading-content">
            <img src="/Alumni-CvSU/asset/GIF/Spinner-mo.gif" alt="Loading">
            <p>Please wait...</p>
        </div>
    </div>

    <div class="backup-notification" id="backupNotification">
        <i class="fas fa-check-circle"></i>
        <div class="backup-notification-content">
            <h3 class="backup-notification-title">Success</h3>
            <p class="backup-notification-message" id="notificationMessage"></p>
        </div>
    </div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        let backupCodesEnabled = false;
        let generatedCodes = [];
        
        const generateBtn = document.getElementById('backupGenerateBtn');
        const saveBtn = document.getElementById('backupSaveBtn');
        const toggleButton = document.getElementById('backupToggleButton');
        const gridsContainer = document.getElementById('backupGridsContainer');
        const generateGrid = document.getElementById('backupGenerateGrid');
        const activeGrid = document.getElementById('backupActiveGrid');
        const loadingOverlay = document.getElementById('backupLoadingOverlay');
        const confirmModal = document.getElementById('confirmDisableModal');
        const notification = document.getElementById('backupNotification');

        initializeComponent();

        toggleButton.addEventListener('click', handleToggleClick);
        generateBtn?.addEventListener('click', handleGenerateClick);
        saveBtn?.addEventListener('click', handleSaveClick);

        async function initializeComponent() {
            showLoading();
            try {
                await checkBackupCodesStatus();
                if (backupCodesEnabled) {
                    await loadExistingCodes();
                }
            } catch (error) {
                console.error('Error initializing component:', error);
                showNotification('Error loading backup codes', 'error');
            } finally {
                hideLoading();
            }
        }

        async function checkBackupCodesStatus() {
            try {
                const response = await fetch('user/security-page/get_backup_codes_status.php');
                const data = await response.json();
                
                if (data.success) {
                    backupCodesEnabled = data.hasBackupCodes;
                    updateUI();
                } else {
                    throw new Error('Failed to fetch backup codes status');
                }
            } catch (error) {
                console.error('Error checking status:', error);
                showNotification('Error checking backup codes status', 'error');
            }
        }

        function updateUI() {
            if (backupCodesEnabled) {
                toggleButton.textContent = 'Turn off Backup Codes';
                toggleButton.classList.add('off');
                document.querySelector('.backup-heading').textContent = 
                    'Your account is protected with Backup Codes';
                gridsContainer.classList.add('show');
            } else {
                toggleButton.textContent = 'Turn on Backup Codes';
                toggleButton.classList.remove('off');
                document.querySelector('.backup-heading').textContent = 
                    'Your account is not protected by Backup Codes';
                gridsContainer.classList.remove('show');
            }
        }

        async function loadExistingCodes() {
            try {
                const response = await fetch('user/security-page/fetch_codes.php');
                const data = await response.json();

                if (data.success) {
                    initializeCodeSlots(activeGrid);

                    if (data.codes && data.codes.length > 0) {
                        const activeCodeElements = activeGrid.getElementsByClassName('backup-code-item');
                        data.codes.forEach((codeObj, index) => {
                            if (index < 12 && activeCodeElements[index]) {
                                activeCodeElements[index].textContent = codeObj.code;
                            }
                        });
                    }
                    
                    initializeCodeSlots(generateGrid);
                }
            } catch (error) {
                console.error('Error loading codes:', error);
                showNotification('Failed to load existing codes', 'error');
            }
        }

        function initializeCodeSlots(container) {
            if (!container) return;
            
            container.innerHTML = '';
            for (let i = 0; i < 12; i++) {
                const codeItem = document.createElement('div');
                codeItem.className = 'backup-code-item';
                codeItem.textContent = '- - - - - -';
                container.appendChild(codeItem);
            }
        }

        function generateRandomCode() {
            return String(Math.floor(Math.random() * 1000000)).padStart(6, '0');
        }

        function generateBackupCodes() {
            const codes = [];
            for (let i = 0; i < 12; i++) {
                codes.push(generateRandomCode());
            }
            return codes;
        }

        function updateCodesDisplay(codes) {
            if (!generateGrid) return;
            
            const codeElements = generateGrid.getElementsByClassName('backup-code-item');
            for (let i = 0; i < codes.length; i++) {
                if (codeElements[i]) {
                    codeElements[i].textContent = codes[i];
                }
            }
        }

        function handleGenerateClick() {
            showLoading();
            if (generateBtn) generateBtn.disabled = true;

            setTimeout(() => {
                generatedCodes = generateBackupCodes();
                updateCodesDisplay(generatedCodes);
                if (saveBtn) saveBtn.disabled = false;
                if (generateBtn) generateBtn.disabled = false;
                hideLoading();
            }, 1000);
        }

        async function handleSaveClick() {
            if (!saveBtn || !generateBtn) return;
            
            showLoading();
            saveBtn.disabled = true;

            try {
                const response = await fetch('user/security-page/save_codes.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ codes: generatedCodes })
                });

                const data = await response.json();

                if (data.success) {
                    await new Promise(resolve => setTimeout(resolve, 1500));
                    await loadExistingCodes();
                    saveBtn.disabled = true;
                    generatedCodes = [];
                    backupCodesEnabled = true;
                    updateUI();
                    showNotification('Backup codes have been saved successfully');
                } else {
                    throw new Error(data.message || 'Failed to save codes');
                }
            } catch (error) {
                console.error('Error saving codes:', error);
                showNotification('An error occurred while saving the codes', 'error');
                saveBtn.disabled = false;
            } finally {
                hideLoading();
            }
        }


        function handleToggleClick() {
            if (!backupCodesEnabled) {
                enableBackupCodes();
            } else {
                confirmModal.style.display = 'flex';
            }
        }

        async function enableBackupCodes() {
            showLoading();
            try {
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                gridsContainer.classList.add('show');
                await loadExistingCodes();
                backupCodesEnabled = true;
                updateUI();

                await new Promise(resolve => setTimeout(resolve, 500));
                showNotification('Backup codes enabled successfully');
            } catch (error) {
                console.error('Error enabling backup codes:', error);
                showNotification('Error enabling backup codes', 'error');
            } finally {
                hideLoading();
            }
        }

        async function disableBackupCodes() {
            showLoading();
            try {
                await new Promise(resolve => setTimeout(resolve, 1500));
                
                const response = await fetch('user/security-page/clear_backup_codes.php', {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.success) {
                    backupCodesEnabled = false;
                    updateUI();
                    closeConfirmModal();
                    
                    await new Promise(resolve => setTimeout(resolve, 500));
                    showNotification('Backup codes disabled successfully');
                } else {
                    throw new Error(data.message || 'Failed to disable backup codes');
                }
            } catch (error) {
                console.error('Error disabling backup codes:', error);
                showNotification('Error disabling backup codes', 'error');
            } finally {
                hideLoading();
            }
        }

        function showLoading() {
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }
        }

        function hideLoading() {
            if (loadingOverlay) {
                loadingOverlay.style.display = 'none';
            }
        }

        function showNotification(message, type = 'success') {
            if (!notification) return;
            
            const notificationIcon = notification.querySelector('i');
            const notificationMessage = notification.querySelector('#notificationMessage');
            
            if (notificationIcon) {
                notificationIcon.className = type === 'success' ? 
                    'fas fa-check-circle' : 'fas fa-exclamation-circle';
            }
            
            if (notificationMessage) {
                notificationMessage.textContent = message;
            }

            notification.style.background = type === 'success' ? '#10b981' : '#dc2626';
            notification.style.display = 'flex';
            
            setTimeout(() => {
                notification.style.display = 'none';
            }, 3000);
        }

        window.closeConfirmModal = function() {
            if (confirmModal) {
                confirmModal.style.display = 'none';
            }
        };

        window.confirmDisableBackupCodes = async function() {
            await disableBackupCodes();
        };

        window.addEventListener('click', function(event) {
            if (event.target === confirmModal) {
                closeConfirmModal();
            }
        });
    });
</script>
</body>
</html>