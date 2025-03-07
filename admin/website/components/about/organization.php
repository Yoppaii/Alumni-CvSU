<style>
:root {
    --primary-green: #2e7d32;
    --hover-green: #1b5e20;
    --light-green: #e8f5e9;
    --gray-light: #f5f5f5;
    --text-dark: #333;
    --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

.section-titles {
    display: flex;
    justify-content: space-between;
    max-width: 1200px;
    margin: 2rem auto 1rem;
    padding: 0 1rem;
}

.section-titles h2 {
    color: var(--primary-green);
    font-size: 1.2rem;
    position: relative;
    padding-bottom: 0.3rem;
    flex-basis: 50%;
    margin: 0;
}

.section-titles h2::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 50px;
    height: 2px;
    background: var(--primary-green);
    border-radius: 2px;
}

.organization-wrapper {
    display: flex;
    gap: 1.5rem;
    max-width: 1200px;
    margin: 1rem auto;
    padding: 0 1rem;
}

.organization-container {
    flex: 1;
    background: white;
    border-radius: 10px;
    box-shadow: var(--shadow-md);
    padding: 1.5rem;
    animation: fadeIn 0.6s ease-out;
}

.content-wrapper {
    display: flex;
    gap: 1.5rem;
    align-items: center;
}

.text {
    flex: 1;
}

.text p {
    color: var(--text-dark);
    font-size: 0.85rem;
    line-height: 1.5;
    margin: 0;
}

.text ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.text ul li {
    color: var(--text-dark);
    font-size: 0.85rem;
    line-height: 1.5;
    margin-bottom: 0.6rem;
    padding-left: 1rem;
    position: relative;
}

.text ul li::before {
    content: '→';
    color: var(--primary-green);
    position: absolute;
    left: 0;
}

.image {
    flex: 0 0 300px;
    background: var(--gray-light);
    border-radius: 8px;
    overflow: hidden;
    padding: 0.5rem;
}

.image img {
    width: 100%;
    height: auto;
    display: block;
    object-fit: contain;
}

@media (max-width: 768px) {
    .organization-wrapper {
        flex-direction: column;
    }

    .section-titles {
        flex-direction: column;
        gap: 1rem;
    }

    .section-titles h2 {
        flex-basis: auto;
    }

    .content-wrapper {
        flex-direction: column;
    }

    .image {
        flex: 0 0 auto;
        width: 100%;
        max-width: 300px;
        margin: 1rem auto 0;
    }
}
</style>

<!-- Updated HTML Structure -->
<div class="section-titles">
    <h2>Organizational Chart</h2>
    <h2>Our Objectives</h2>
</div>

<div class="organization-wrapper">
    <div class="organization-container">
        <div class="content-wrapper">
            <div class="text">
                <p>Our organizational chart provides a visual representation of the key departments and leadership hierarchy within our company. It helps clarify reporting lines and responsibilities, ensuring effective communication and collaboration across teams.</p>
            </div>
            <div class="image">
                <img src="asset/images/organization_chart/Association.jpg" alt="Organizational Chart">
            </div>
        </div>
    </div>

    <div class="organization-container">
        <div class="content-wrapper">
            <div class="text">
                <ul>
                    <li>To foster friendship and unity among members of the association for the protection of their common interest, aspirations, and welfare.</li>
                    <li>To initiate and undertake programs and activities that shall promote the socio-economic welfare of the members.</li>
                    <li>To promote high moral standards among members.</li>
                    <li>To partake in the social issues and concerns in the community, especially those affecting the organization and its members.</li>
                </ul>
            </div>
            <div class="image">
                <img src="asset/images/organization_chart/Organization.jpg" alt="Organization Chart">
            </div>
        </div>
    </div>
</div>