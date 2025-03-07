<head>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>

<footer class="custom-footer">
    <div class="footer-container">
        <!-- Bottom Row with Navigation Links and Copyright -->
        <div class="footer-bottom-row">
            <div class="footer-legal-container">
                <div class="footer-nav-links">
                    <ul>
                        <li><a href="/about"><i class="fas fa-info-circle"></i> About</a></li>
                        <li><a href="?pages=contact-us"><i class="fas fa-phone-alt"></i> Contact Us</a></li>
                        <li><a href="?pages=terms-and-conditions"><i class="fas fa-file-contract"></i> Terms & Conditions</a></li>
                        <li><a href="?pages=privacy-policy"><i class="fas fa-user-shield"></i> Privacy Policy</a></li>
                    </ul>
                </div>
                <p class="footer-rights">
                    <img src="asset/images/res1.png" alt="CvSU Logo" class="footer-rights-logo">
                    All rights reserved &copy; 2024 Alumni Cavite State University
                </p>
            </div>
        </div>
    </div>
</footer>

<style>
    .custom-footer {
        background-color: #f5f5f5;
        color: #000000;
        padding: 20px 0;
        width: 100%;
        box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
    }

    .footer-container {
        max-width: 100%;
        margin: 0 auto;
        padding: 0 20px;
    }

    .footer-bottom-row {
        border-top: 1px solid rgba(0, 0, 0, 0.1);
        padding-top: 20px;
    }

    .footer-legal-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .footer-nav-links ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        justify-content: center;
        gap: 30px;
    }

    .footer-nav-links a {
        color: #000000;
        text-decoration: none;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .footer-nav-links a:hover {
        text-decoration: underline;
    }

    .footer-rights {
        font-size: 0.9rem;
        margin: 0;
        color: #000000;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .footer-rights-logo {
        height: 1em;
        width: auto;
        vertical-align: middle;
    }

    @media (max-width: 768px) {
        .footer-nav-links ul {
            flex-direction: column;
            align-items: center;
            gap: 15px;
        }

        .footer-rights {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>