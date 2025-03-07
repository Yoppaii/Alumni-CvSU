<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

        .privacy-card {
            background: white;
            border-radius: 8px;
            box-shadow: var(--shadow-md);
            overflow: hidden;
            margin-bottom: 20px;
        }

        .privacy-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .privacy-header h1 {
            font-size: 24px;
            color: #111827;
            margin: 0 0 8px 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .privacy-header h1 i {
            color: var(--primary-color);
        }

        .privacy-header p {
            color: #6b7280;
            font-size: 14px;
            margin: 0;
        }

        .privacy-sections {
            padding: 8px 0;
        }

        .privacy-section {
            padding: 16px 24px;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }

        .privacy-section:last-child {
            border-bottom: none;
        }

        .privacy-section:hover {
            background-color: #f9fafb;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .icon-wrapper {
            width: 40px;
            height: 40px;
            background-color: #ecfdf5;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-wrapper i {
            color: var(--primary-color);
            font-size: 16px;
        }

        .section-title h3 {
            color: #111827;
            font-size: 16px;
            font-weight: 500;
            margin: 0;
        }

        .section-content {
            margin-left: 52px;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.5;
            display: none;
        }

        .section-content.active {
            display: block;
        }

        .chevron {
            color: #9ca3af;
            transition: transform 0.2s ease;
        }

        .chevron.active {
            transform: rotate(90deg);
        }

        @media (max-width: 640px) {
            .privacy-section {
                padding: 12px 16px;
            }

            .icon-wrapper {
                width: 32px;
                height: 32px;
            }

            .section-content {
                margin-left: 44px;
            }
        }
    </style>
<body>
    <div class="main-container">
        <div class="privacy-card">
            <div class="privacy-header">
                <h1><i class="fas fa-user-shield"></i> Privacy Policy</h1>
                <p>Learn how we collect, use, and protect your data</p>
            </div>
            <div class="privacy-sections">
                <div class="privacy-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="icon-wrapper">
                                <i class="fas fa-eye"></i>
                            </div>
                            <h3>Privacy Policy Overview</h3>
                        </div>
                        <i class="fas fa-chevron-right chevron"></i>
                    </div>
                    <div class="section-content">
                        <p>We are committed to protecting your privacy and ensuring the security of your personal information. This policy outlines our comprehensive approach to data protection and privacy management. We believe in transparency and want you to understand exactly how we handle your information when you use our services.</p>
                        <p>Our privacy practices comply with all applicable laws and regulations, including GDPR and other international privacy standards. We regularly review and update our practices to ensure the highest level of data protection.</p>
                    </div>
                </div>

                <div class="privacy-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="icon-wrapper">
                                <i class="fas fa-database"></i>
                            </div>
                            <h3>Data Collection</h3>
                        </div>
                        <i class="fas fa-chevron-right chevron"></i>
                    </div>
                    <div class="section-content">
                        <p>We collect several types of information to provide and improve our services:</p>
                        <ul style="margin-top: 10px; margin-bottom: 10px; padding-left: 20px;">
                            <li>Personal Information: Name, email address, contact details, and account credentials</li>
                            <li>Technical Data: IP address, browser type, device information, and operating system</li>
                            <li>Usage Data: How you interact with our services, including pages visited and features used</li>
                            <li>Location Data: General location based on IP address or precise location if permitted</li>
                        </ul>
                        <p>We collect this information through:</p>
                        <ul style="margin-top: 10px; padding-left: 20px;">
                            <li>Direct user input when you create an account or use our services</li>
                            <li>Automated technologies like cookies and tracking pixels</li>
                            <li>Third-party sources, such as our service providers and partners</li>
                        </ul>
                    </div>
                </div>

                <div class="privacy-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="icon-wrapper">
                                <i class="fas fa-cogs"></i>
                            </div>
                            <h3>How Data is Used</h3>
                        </div>
                        <i class="fas fa-chevron-right chevron"></i>
                    </div>
                    <div class="section-content">
                        <p>Your data helps us provide and improve our services in the following ways:</p>
                        <ul style="margin-top: 10px; margin-bottom: 10px; padding-left: 20px;">
                            <li>Service Delivery: Processing your requests, managing your account, and providing core functionality</li>
                            <li>Personalization: Customizing content and recommendations based on your preferences</li>
                            <li>Communication: Sending important updates, newsletters, and responding to your inquiries</li>
                            <li>Security: Protecting your account and our services from unauthorized access</li>
                            <li>Analytics: Understanding how our services are used and identifying areas for improvement</li>
                        </ul>
                        <p>All data processing is conducted in accordance with applicable privacy laws and our internal data protection policies.</p>
                    </div>
                </div>

                <div class="privacy-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="icon-wrapper">
                                <i class="fas fa-cookie"></i>
                            </div>
                            <h3>Cookies and Tracking</h3>
                        </div>
                        <i class="fas fa-chevron-right chevron"></i>
                    </div>
                    <div class="section-content">
                        <p>We use various tracking technologies to enhance your experience:</p>
                        <ul style="margin-top: 10px; margin-bottom: 10px; padding-left: 20px;">
                            <li>Essential Cookies: Required for basic website functionality</li>
                            <li>Analytics Cookies: Help us understand how visitors use our site</li>
                            <li>Preference Cookies: Remember your settings and preferences</li>
                            <li>Marketing Cookies: Used to deliver relevant advertisements</li>
                        </ul>
                        <p>You can manage your cookie preferences through:</p>
                        <ul style="margin-top: 10px; padding-left: 20px;">
                            <li>Browser settings: Configure or disable cookies</li>
                            <li>Our cookie management tool: Fine-tune your preferences</li>
                            <li>Opt-out links: Available in our communications</li>
                        </ul>
                    </div>
                </div>

                <div class="privacy-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="icon-wrapper">
                                <i class="fas fa-handshake"></i>
                            </div>
                            <h3>Third-Party Services</h3>
                        </div>
                        <i class="fas fa-chevron-right chevron"></i>
                    </div>
                    <div class="section-content">
                        <p>We work with trusted third-party service providers who help us operate our website and deliver services. These partners may include:</p>
                        <ul style="margin-top: 10px; margin-bottom: 10px; padding-left: 20px;">
                            <li>Analytics providers (e.g., Google Analytics)</li>
                            <li>Payment processors for secure transactions</li>
                            <li>Cloud storage and hosting providers</li>
                            <li>Customer support and communication tools</li>
                        </ul>
                        <p>All third-party providers are:</p>
                        <ul style="margin-top: 10px; padding-left: 20px;">
                            <li>Bound by strict confidentiality agreements</li>
                            <li>Required to maintain appropriate security measures</li>
                            <li>Prohibited from using your data for their own purposes</li>
                            <li>Regularly audited for compliance</li>
                        </ul>
                    </div>
                </div>

                <div class="privacy-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="icon-wrapper">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <h3>Data Security</h3>
                        </div>
                        <i class="fas fa-chevron-right chevron"></i>
                    </div>
                    <div class="section-content">
                        <p>We implement comprehensive security measures to protect your data:</p>
                        <ul style="margin-top: 10px; margin-bottom: 10px; padding-left: 20px;">
                            <li>Encryption: All data is encrypted in transit and at rest</li>
                            <li>Access Controls: Strict authentication and authorization procedures</li>
                            <li>Monitoring: 24/7 security monitoring and threat detection</li>
                            <li>Regular Security Audits: Internal and external security assessments</li>
                            <li>Employee Training: Regular security awareness training for all staff</li>
                        </ul>
                        <p>We maintain industry-standard security certifications and regularly update our security protocols to address new threats.</p>
                    </div>
                </div>

                <div class="privacy-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="icon-wrapper">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3>Data Retention</h3>
                        </div>
                        <i class="fas fa-chevron-right chevron"></i>
                    </div>
                    <div class="section-content">
                        <p>Our data retention policies balance user privacy with business needs:</p>
                        <ul style="margin-top: 10px; margin-bottom: 10px; padding-left: 20px;">
                            <li>Active Accounts: Data retained while your account is active</li>
                            <li>Inactive Accounts: Data archived after 12 months of inactivity</li>
                            <li>Deleted Accounts: Data permanently deleted within 30 days</li>
                            <li>Legal Requirements: Some data retained as required by law</li>
                        </ul>
                        <p>You can request data deletion at any time, subject to legal retention requirements.</p>
                    </div>
                </div>

                <div class="privacy-section">
                    <div class="section-header">
                        <div class="section-title">
                            <div class="icon-wrapper">
                                <i class="fas fa-user-check"></i>
                            </div>
                            <h3>User Rights</h3>
                        </div>
                        <i class="fas fa-chevron-right chevron"></i>
                    </div>
                    <div class="section-content">
                        <p>Under data protection laws, you have several important rights:</p>
                        <ul style="margin-top: 10px; margin-bottom: 10px; padding-left: 20px;">
                            <li>Right to Access: Request copies of your personal data</li>
                            <li>Right to Rectification: Correct inaccurate or incomplete data</li>
                            <li>Right to Erasure: Request deletion of your personal data</li>
                            <li>Right to Restrict Processing: Limit how we use your data</li>
                            <li>Right to Data Portability: Receive and transfer your data</li>
                            <li>Right to Object: Object to processing of your data</li>
                        </ul>
                        <p>To exercise these rights:</p>
                        <ul style="margin-top: 10px; padding-left: 20px;">
                            <li>Email us at privacy@example.com</li>
                            <li>Use our privacy dashboard in account settings</li>
                            <li>Submit a request through our support center</li>
                        </ul>
                        <p>We will respond to all legitimate requests within 30 days.</p>
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.privacy-section').forEach(section => {
            section.addEventListener('click', () => {
                const content = section.querySelector('.section-content');
                const chevron = section.querySelector('.chevron');
                
                content.classList.toggle('active');
                chevron.classList.toggle('active');
                
                document.querySelectorAll('.privacy-section').forEach(otherSection => {
                    if (otherSection !== section) {
                        otherSection.querySelector('.section-content').classList.remove('active');
                        otherSection.querySelector('.chevron').classList.remove('active');
                    }
                });
            });
        });
    </script>
</body>
</html>