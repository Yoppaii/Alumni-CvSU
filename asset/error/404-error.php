<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary: #16a34a;
            --background: #f0fdf4;
            --text: #166534;
            --subtle: #dcfce7;
        }

        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--background);
            color: var(--text);
            padding: 1rem;
        }

        .error-container {
            max-width: auto;
            width: 100%;
            padding: 3rem;
            text-align: center;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .error-number {
            font-size: 7rem;
            font-weight: 500;
            color: var(--primary);
            line-height: 1;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: fadeIn 0.6s ease forwards;
        }

        .error-title {
            font-size: 1.5rem;
            font-weight: 500;
            margin-bottom: 1rem;
            color: var(--text);
            opacity: 0;
            animation: fadeIn 0.6s 0.2s ease forwards;
        }

        .error-message {
            color: #374151;
            margin-bottom: 2rem;
            line-height: 1.6;
            opacity: 0;
            animation: fadeIn 0.6s 0.3s ease forwards;
        }

        .error-actions {
            display: flex;
            gap: 1rem;
            justify-content: center;
            opacity: 0;
            animation: fadeIn 0.6s 0.4s ease forwards;
        }

        .error-button {
            padding: 0.75rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
            font-size: 0.95rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .primary-button {
            background: var(--primary);
            color: white;
        }

        .primary-button:hover {
            background: #15803d;
            transform: translateY(-1px);
        }

        .secondary-button {
            background: var(--subtle);
            color: var(--primary);
        }

        .secondary-button:hover {
            background: #bbf7d0;
            transform: translateY(-1px);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .error-container {
                padding: 2rem;
                margin: 1rem;
            }

            .error-number {
                font-size: 5rem;
            }

            .error-actions {
                flex-direction: column;
            }

            .error-button {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-number">404</div>
        <h2 class="error-title">Page Not Found</h2>
        <p class="error-message">
            The page you're looking for doesn't exist or has been moved.
        </p>
        <div class="error-actions">
            <a href="/Alumni-CvSU/index" class="error-button primary-button">
                <i class="fas fa-home"></i>
                Home
            </a>
            <a href="/RS/contact" class="error-button secondary-button">
                <i class="fas fa-envelope"></i>
                Contact
            </a>
        </div>
    </div>
</body>
</html>