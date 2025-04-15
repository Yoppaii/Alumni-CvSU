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
        color: var(--cvsu-primary-green);
        font-size: 2rem;
        text-align: center;
        margin-bottom: 2rem;
        padding-bottom: 0.5rem;
        border-bottom: 3px solid var(--cvsu-primary-green);
        display: block;
        width: 100%;
    }

    .section-titles h2::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 50px;
        height: 2px;

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
        display: block;
        flex: 1;
        background: white;
        border-radius: 10px;
        box-shadow: var(--shadow-md);
        padding: 5rem;
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
        font-size: 1.5rem;
        line-height: 1.5;
        margin-bottom: 0.6rem;
        padding-left: 2rem;
        position: relative;
    }

    .text ul li::before {
        content: '→';
        color: var(--primary-green);
        position: absolute;
        left: 0;
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

    }
</style>

<!-- Updated HTML Structure -->
<div class="section-titles">
    <h2>Our Objectives</h2>
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
    </div>
</div>
</div>