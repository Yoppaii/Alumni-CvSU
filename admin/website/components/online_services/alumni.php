<section class="ra-alumni-section">
    <div class="ra-container">
        <div class="ra-alumni-grid">
            <div class="ra-alumni-card membership-card">
                <div class="ra-card-header">
                    <i class="fas fa-users"></i>
                    <h3>Alumni Membership</h3>
                </div>
                
                <div class="ra-card-content">
                    <div class="ra-membership-content">
                        <div class="membership-flex">
                            <div class="ra-membership-info">
                                <p class="ra-description">Become a member and stay connected with the Cavite State University Alumni Association. Our membership provides exclusive benefits and access to various alumni services.</p>
                                <p class="ra-cta-text">If you want to enjoy all the benefits of being a part of the Cavite State University Alumni, we encourage you to register now!</p>
                                <a href="Account.php" class="ra-cta-button">Join Now <i class="fas fa-arrow-right"></i></a>
                            </div>
                            
                            <div class="ra-membership-options">
                                <div class="ra-option">
                                    <span class="ra-option-label">Membership Fee:</span>
                                    <span class="ra-option-price">₱500</span>
                                </div>
                                <div class="ra-option featured">
                                    <span class="ra-option-label">Lifetime Membership:</span>
                                    <span class="ra-option-price">₱1,500</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ra-alumni-card video-card">
                <div class="ra-card-header">
                    <i class="fas fa-play-circle"></i>
                    <h3>Watch Our Alumni Video</h3>
                </div>
                
                <div class="ra-card-content">
                    <div class="ra-video-wrapper">
                        <video 
                            id="alumniVideo"
                            class="ra-video-player" 
                            controls 
                            poster="asset/images/video-thumbnail.jpg">
                            <source src="asset/clip-video/AlumniVideo.mp4" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .ra-alumni-section {
        padding: 2rem 0;
        background: linear-gradient(to bottom, #f5f5f5 0%, white 100%);
    }

    .ra-container {
        width: 100%;
        padding: 0 1rem;
        max-width: auto;
        margin: 0 auto;
    }

    .ra-alumni-grid {
        display: grid;
        grid-template-columns: 3fr 2fr;
        gap: 1.5rem;
        align-items: stretch;
        margin: 0 auto;
    }

    .ra-alumni-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        transition: all 0.3s ease;
        animation: raFadeIn 0.6s ease-out;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .membership-card {
        width: 100%;
    }

    .video-card {
        width: 100%;
        min-width: 300px;
    }

    .membership-flex {
        display: flex;
        gap: 2rem;
        align-items: flex-start;
        height: 100%;
    }

    .ra-membership-info {
        flex: 1;
        padding-right: 1rem;
        display: flex;
        flex-direction: column;
    }

    .ra-membership-options {
        min-width: 250px;
        margin: 0;
    }

    .ra-card-header {
        background-color: #006400;
        color: white;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .ra-card-header i {
        font-size: 1.25rem;
    }

    .ra-card-header h3 {
        margin: 0;
        font-size: 1.25rem;
    }

    .ra-card-content {
        padding: 1.5rem;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }

    .ra-description {
        color: #333;
        margin-bottom: 1.5rem;
        font-size: 0.95rem;
    }

    .ra-option {
        background: #f5f5f5;
        padding: 1rem;
        border-radius: 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .ra-option.featured {
        background: #e8f5e9;
        border: 2px solid #006400;
    }

    .ra-option-label {
        font-weight: bold;
        color: #333;
    }

    .ra-option-price {
        font-size: 1.25rem;
        color: #006400;
        font-weight: bold;
    }

    .ra-cta-text {
        margin: 1.5rem 0;
        color: #333;
        font-size: 0.95rem;
    }

    .ra-cta-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background-color: #006400;
        color: white;
        text-decoration: none;
        padding: 0.75rem 1.5rem;
        border-radius: 8px;
        font-weight: bold;
        transition: all 0.3s ease;
        margin-top: auto;
    }

    .ra-cta-button:hover {
        background-color: #004d00;
        transform: translateY(-2px);
    }

    .ra-cta-button i {
        transition: all 0.3s ease;
    }

    .ra-cta-button:hover i {
        transform: translateX(5px);
    }

    .ra-video-wrapper {
        background: #f5f5f5;
        border-radius: 8px;
        padding: 1rem;
        height: 100%;
        display: flex;
        align-items: center;
    }

    .ra-video-player {
        width: 100%;
        height: auto;
        max-height: 315px;
        object-fit: contain;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    @keyframes raFadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 1024px) {
        .ra-alumni-grid {
            grid-template-columns: 1fr;
        }

        .membership-flex {
            flex-direction: column;
        }

        .ra-membership-info {
            padding-right: 0;
        }

        .ra-membership-options {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .ra-card-content {
            padding: 1rem;
        }

        .ra-option {
            flex-direction: column;
            text-align: center;
            gap: 0.5rem;
        }
    }
</style>