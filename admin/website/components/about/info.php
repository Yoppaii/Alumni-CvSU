<div class="info-area">
    <h2 class="info-heading">Mandate, Mission, and Vision</h2>

    <div class="info-top-grid">
        <!-- Mandate Card -->
        <div class="info-portrait-card">
            <h3 class="info-portrait-title">Mandate</h3>
            <p class="info-portrait-text">
                Section 2 of Republic Act No. 8468, "An Act Converting the Don Severino Agricultural College in the Municipality of Indang, Province of Cavite into a State University, to be Known as the Cavite State University" states that:
            </p>
            <blockquote class="info-blockquote">
                "The University shall primarily provide advanced instruction and professional training in agriculture, science and technology, education and other related fields, undertake research and extension services, and provide progressive leadership in these areas."
            </blockquote>
        </div>

        <!-- Mission Card -->
        <div class="info-portrait-card">
            <h3 class="info-portrait-title">Mission</h3>
            <p class="info-portrait-text">
                Cavite State University shall provide excellent, equitable, and relevant educational opportunities in the arts, sciences, and technology through quality instruction and responsive research and development activities. It shall produce professional, skilled, and morally upright individuals for global competitiveness.
            </p>
            <p class="info-portrait-text">
                <strong class="info-lang-label">Hangarin ng Pamantasan:</strong>
                "Ang Cavite State University ay makapagbigay ng mahusay, pantay at makabuluhang edukasyon sa sining, agham at teknolohiya sa pamamagitan ng may kalidad na pagtuturo at tumutugon sa pangangailangang pananaliksik at mga gawaing pangkaunlaran. Makalikha ito ng mga indibidwal ng dalubhasa, may kasaysayan at kagandahan-asal sa pandaigdigang kakayahan."
            </p>
        </div>

        <!-- Vision Card -->
        <div class="info-portrait-card">
            <h3 class="info-portrait-title">Vision</h3>
            <p class="info-portrait-text">
                The premier university in historic Cavite, globally recognized for excellence in character development, academics, research, innovation, and sustainable community engagement.
            </p>
            <p class="info-portrait-text">
                <strong class="info-lang-label">Mithiin ng Pamantasan:</strong>
                "Ang nangungunang pamantasan sa makasaysayang Kabite na kinikilala sa kahusayan sa paghubog ng mga indibidwal na may pandaigdigang kakayahan at kagandahang asal."
            </p>
        </div>
    </div>

    <!-- Core Values Section -->
    <section class="info-section">
        <h3 class="info-section-title">Core Values</h3>
        <div class="info-values-grid">
            <div class="info-value-card">
                <i class="fas fa-balance-scale info-value-icon"></i>
                <h4 class="info-value-title">Truth</h4>
                <p class="info-value-text">We value integrity, transparency, and honesty in all our endeavors.</p>
            </div>
            <div class="info-value-card">
                <i class="fas fa-trophy info-value-icon"></i>
                <h4 class="info-value-title">Excellence</h4>
                <p class="info-value-text">We strive for excellence in all aspects of education, research, and community service.</p>
            </div>
            <div class="info-value-card">
                <i class="fas fa-hands-helping info-value-icon"></i>
                <h4 class="info-value-title">Service</h4>
                <p class="info-value-text">We commit to serve our stakeholders through meaningful contributions and sustainable programs.</p>
            </div>
        </div>
    </section>

    <!-- Quality Policy Section -->
    <section class="info-section info-quality-section">
        <h3 class="info-section-title">Quality Policy</h3>
        <p class="info-text">
            We commit to the highest standards of education, value our stakeholders, strive for continual improvement of our products and services, and uphold the University's tenets of Truth, Excellence, and Service to produce globally competitive and morally upright individuals.
        </p>
    </section>
</div>

<style>
.info-area {
    max-width: auto;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.info-heading {
    color: var(--cvsu-primary-green);
    font-size: 2rem;
    text-align: center;
    margin-bottom: 2rem;
    padding-bottom: 0.5rem;
    border-bottom: 3px solid var(--cvsu-primary-green);
}

/* Top section grid layout */
.info-top-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.info-portrait-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: var(--cvsu-shadow-sm);
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid var(--cvsu-light-green);
}

.info-portrait-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s ease;
    box-shadow: var(--cvsu-shadow-md);
}

.info-portrait-title {
    color: var(--cvsu-primary-green);
    font-size: 1.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--cvsu-light-green);
    text-align: center;
}

.info-portrait-text {
    color:#161616;
    line-height: 1.6;
    margin-bottom: 1rem;
    flex-grow: 1;
}

.info-section {
    margin-bottom: 2.5rem;
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: var(--cvsu-shadow-sm);
}

.info-section-title {
    color: var(--cvsu-primary-green);
    font-size: 1.5rem;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 2px solid var(--cvsu-light-green);
}

.info-text {
    color: var(--cvsu-text-dark);
    line-height: 1.6;
    margin-bottom: 1rem;
}

.info-blockquote {
    margin: 1rem 0;
    padding: 1rem 1.5rem;
    background-color: rgba(0, 100, 0, 0.05);
    border-left: 4px solid var(--cvsu-primary-green);
    font-style: italic;
    color: #161616;
    font-size: 0.95rem;
}

.info-lang-label {
    color: var(--cvsu-primary-green);
    display: block;
    margin-top: 1rem;
    font-weight: bold;
}

/* Core Values Styling */
.info-values-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.info-value-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    text-align: center;
    transition: transform 0.3s ease;
    border: 1px solid var(--cvsu-light-green);
}

.info-value-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--cvsu-shadow-md);
}

.info-value-icon {
    color: var(--cvsu-primary-green);
    font-size: 2rem;
    margin-bottom: 1rem;
}

.info-value-title {
    color: var(--cvsu-primary-green);
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.info-value-text {
    font-size: 0.9rem;
    margin-bottom: 0;
}

/* Quality Policy Section */
.info-quality-section {
    background-color: var(--cvsu-primary-green);
    color: white;
}

.info-quality-section .info-section-title {
    color: white;
    border-bottom-color: rgba(255, 255, 255, 0.2);
}

.info-quality-section .info-text {
    color: white;
    margin-bottom: 0;
}

/* Responsive Design */
@media (max-width: 992px) {
    .info-top-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .info-heading {
        font-size: 1.75rem;
    }

    .info-top-grid {
        grid-template-columns: 1fr;
    }

    .info-section, .info-portrait-card {
        padding: 1rem;
    }

    .info-section-title, .info-portrait-title {
        font-size: 1.25rem;
    }

    .info-values-grid {
        grid-template-columns: 1fr;
    }
}

/* Print-friendly styles */
@media print {
    .info-area {
        max-width: 100%;
        margin: 0;
        padding: 0;
    }

    .info-section, .info-portrait-card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
}
</style>