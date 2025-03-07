<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campus Details</title>
    <link rel="stylesheet" href="/asset/css/pages-css/index_wat.css">
    <link rel="icon" href="/asset/images/res1.png" type="image/x-icon">
    <link rel="stylesheet" href="/asset/css/portal-css/loading-lol.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="loading-overlay" id="loadingOverlay">
        <img src="../../../asset/GIF/Spinner-mo.gif" alt="Loading...">
    </div>
    <header>
        <nav>
            <div class="logo">
                <img src="../../../asset/images/res1.png" alt="Logo">
                <h1>Alumni Cavite State University</h1>
            </div>
            <div class="hamburger" id="hamburger">&#9776;</div>
            <div class="nav-btns">
                <a href="/index" class="nav-link">Home</a>
                <a href="/Campus-Satellite" class="nav-link">Campuses</a>
            </div>
        </nav>
        <div class="top-menu" id="topMenu">
            <ul>
                <li><a href="/portal/login" class="nav-link">Home</a></li>
                <li><a href="/Campus-Satellite" class="nav-link">Campus</a></li>
            </ul>
        </div>
    </header>
    <section class="campus-details">
        <div class="campus-info">
            <h1 id="campus-name">Cavite City Campus</h1>
            <img id="campus-img" src="../../../CvSU/main/network/campuses/Cavite-City.jpg" alt="Cavite City Campus">
            <p id="campus-description">
                Welcome to Cavite City Campus! This campus offers a variety of programs in fields such as engineering,
                business, and technology. Located in the heart of Cavite City, it provides a vibrant and dynamic
                environment for learning.
            </p>
            <p><strong>Location:</strong> Cavite City, Cavite</p>
            <p><strong>Programs Offered:</strong> Bachelor of Science in Engineering, Business Administration, Information Technology</p>

            <h2>Courses Offered:</h2>
            <ul id="campus-courses">
            </ul>
        </div>
    </section>

    <footer class="custom-footer">
        <div class="footer-container">
            <div class="footer-logo-section">
                <img src="../../../asset/images/res1.png" alt="CvSU Logo" class="footer-logo">
                <h2 class="footer-brand-title">Alumni Cavite State University</h2>
            </div>

            <div class="footer-about-section">
                <h3 class="footer-section-heading"><i class="fas fa-info-circle"></i> About</h3>
                <p class="footer-rights">All rights reserved &copy; 2024 Alumni Cavite State University</p>
                <hr class="divider">
                <p class="footer-about-paragraph">
                    The Alumni Cavite State University is an online booking website where anyone, whether a guest or an
                    alumnus of our campus, can reserve a room. It aims to provide excellent service and ensure client
                    satisfaction.
                </p>
            </div>

            <div class="footer-contact-section">
                <h3 class="footer-section-heading"><i class="fas fa-phone-alt"></i> Contact Us</h3>
                <div class="contact-info">
                    <div class="contact-details">
                        <p class="contact-address"><i class="fas fa-map-marker-alt"></i> Indang-Trece Martires Road 4122 Indang</p>
                        <p class="contact-email"><i class="fas fa-envelope"></i> alumniaffairs@cvsu.edu.ph</p>
                        <p class="contact-hours"><i class="fas fa-clock"></i> Service Hours: Monday to Friday (8:30AM - 5:30PM);<br>Saturday (8:30AM - 12:30PM)</p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <script src="../../../asset/javascript/pages-js/index_burger.js"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        const campusId = urlParams.get('id'); 
        if (campusId) {
            document.getElementById('campus-name').textContent = getCampusName(campusId);
            document.getElementById('campus-img').src = `/RS/CvSU/main/network/campuses/${campusId}.jpg`;
            document.getElementById('campus-description').textContent = getCampusDescription(campusId);
            loadCourses(campusId); 
        }
        function getCampusName(id) {
            const names = {
                'cavite-city': 'Cavite City Campus',
                'carmona': 'Carmona Campus',
                'silang': 'Silang Campus',
                'trece': 'Trece Martires City Campus',
                'gentri': 'General Trias Campus',
                'imus': 'Imus Campus',
                'naic': 'Naic Campus',
                'ccat': 'CCAT Campus',
                'tanza': 'Tanza Campus',
                'maragondon': 'Maragondon Campus'
            };
            return names[id] || 'Unknown Campus';
        }

        function getCampusDescription(id) {
            const descriptions = {
                'cavite-city': 'Cavite City Campus offers a variety of programs in engineering, business, and technology. Located in the heart of Cavite City, it is a hub for academic growth.',
                'carmona': 'Carmona Campus specializes in business and technology education. With modern facilities and experienced faculty, it prepares students for careers in various industries.',
                'silang': 'Silang Campus offers diverse academic programs, focusing on technology, arts, and humanities. The campus provides a serene learning environment perfect for academic pursuit.',
                'trece': 'Trece Martires City Campus is known for its strong programs in agriculture and environmental sciences. The campus features extensive research facilities and experimental farms.',
                'gentri': 'General Trias Campus excels in business and engineering education. Its strategic location provides easy access to industrial parks and business centers.',
                'imus': 'Imus Campus is renowned for its comprehensive programs in education and social sciences. The campus maintains strong ties with local industries for student internships.',
                'naic': 'Naic Campus specializes in agricultural technology and business management. Its location provides students with hands-on experience in both urban and rural development.',
                'ccat': 'CCAT Campus is the university\'s center for agricultural education and research. It features state-of-the-art facilities for agricultural studies and food technology.',
                'tanza': 'Tanza Campus offers programs in business, hospitality, and information technology. The campus provides modern facilities for practical training and development.'
            };
            return descriptions[id] || 'No description available.';
        }
        function loadCourses(id) {
            const courses = {
                'cavite-city': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education',
                    'BS Business Management',
                    'BS Computer Science',
                    'BS Hospitality Management (formerly BS Hotel and Restaurant Management)',
                    'BS Information Technology',
                    'Teacher Certificate Program'
                ],
                'silang': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education',
                    'BS Business Administration',
                    'BS Computer Science',
                    'BS Hospitality Management (formerly BS Hotel and Restaurant Management)',
                    'BS Information Technology',
                    'BS Psychology',
                    'BS Tourism Management',
                    'Laboratory / Science High School'
                ],
                'tanza': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education',
                    'BS Business Management',
                    'BS Hospitality Management (formerly BS Hotel and Restaurant Management)',
                    'BS Information Technology',
                    'BS Tourism Management',
                    'BS Psychology'
                ],
                'maragondon': [
                    'Laboratory / Science High School',
                    'Elementary Education'
                ],
                'carmona': [
                    'BS Business Administration',
                    'BS Information Technology',
                    'BS Entrepreneurship',
                    'BS Office Administration',
                    'Associate in Computer Technology'
                ],
                'trece': [
                    'BS Agriculture',
                    'BS Environmental Science',
                    'BS Agricultural Engineering',
                    'BS Development Communication',
                    'BS Agricultural Technology'
                ],
                'gentri': [
                    'BS Business Administration',
                    'BS Mechanical Engineering',
                    'BS Electrical Engineering',
                    'BS Industrial Engineering',
                    'BS Information Technology'
                ],
                'imus': [
                    'Bachelor of Elementary Education',
                    'Bachelor of Secondary Education',
                    'BS Psychology',
                    'BS Social Work',
                    'BS Public Administration'
                ],
                'naic': [
                    'BS Agricultural Technology',
                    'BS Business Management',
                    'BS Information Technology',
                    'BS Office Administration'
                ],
                'ccat': [
                    'BS Agriculture',
                    'BS Food Technology',
                    'BS Agricultural Engineering',
                    'BS Agribusiness',
                    'Certificate in Agricultural Technology'
                ]
            };

            const coursesList = courses[id] || [];
            const coursesContainer = document.getElementById('campus-courses');
            coursesContainer.innerHTML = ''; 

            coursesList.forEach(course => {
                const li = document.createElement('li');
                li.textContent = course;
                coursesContainer.appendChild(li);
            });
        }
    </script>
</body>
</html>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const loadingOverlay = document.getElementById('loadingOverlay');
    const campusLinks = document.querySelectorAll('.nav-link');

    campusLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const href = this.getAttribute('href');
            
            loadingOverlay.style.display = 'flex';
            
            setTimeout(() => {
                window.location.href = href;
            }, 800); 
        });
    });
});
</script>
<style>
.custom-footer {
background-color: white;
color: #388e3c;
border-top: 2px solid #4CAF50;
border-bottom: 2px solid #4CAF50;
padding: 5px 0;
width: 100%;
}

.footer-container {
max-width: 100%;
margin: 0 auto;
display: flex;
justify-content: space-between;
padding: 0 10px;
}

.footer-logo-section {
display: flex;
align-items: center;
gap: 10px;
flex: 0 1 25%;
}

.footer-logo {
width: 80px;
height: auto;
}

.footer-brand-title {
font-size: 1.5rem;
margin: 0;
color: #388e3c;
}

.footer-section-heading {
color: #388e3c;
margin-bottom: 3px;
font-size: 1rem;
text-align: center;
}

.footer-about-section,
.footer-contact-section {
text-align: left;
flex: 0 1 35%;
}

.footer-about-paragraph,
.contact-details p {
font-size: 0.8rem;
line-height: 1.4;
margin: 0;
}

.contact-info {
display: flex;
flex-direction: column;
gap: 4px;
}

.contact-details {
padding-left: 15px;
text-align: left;
}

.contact-hours {
font-size: 1rem;
line-height: 1.3;
}

.footer-rights {
font-size: 0.8rem;
text-align: center;
margin-top: 10px;
color: #388e3c;
}

hr.divider {
border: 0;
border-top: 1px solid #388e3c;
margin: 10px 0;
}

@media (max-width: 768px) {
.footer-container {
flex-direction: column;
padding: 0 15px;
}

.footer-logo-section {
display: none;
}

.footer-about-paragraph,
.contact-details p {
font-size: 0.9rem;
padding-left: 0;
}

.footer-about-section,
.footer-contact-section {
flex: 0 1 100%;
text-align: left;
}

.contact-details {
padding-left: 0;
}
}
.campus-details {
max-width: 1200px;
margin: 2rem auto;
padding: 0 20px;
}

.campus-info {
background-color: white;
border-radius: 8px;
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
padding: 2rem;
}

.campus-info h1 {
color: #388e3c;
font-size: 2.5rem;
margin-bottom: 1.5rem;
text-align: center;
}

.campus-info img {
width: 100%;
max-width: 800px;
height: 400px;
object-fit: cover;
border-radius: 8px;
margin: 0 auto 2rem;
display: block;
box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.campus-info p {
font-size: 1.1rem;
line-height: 1.6;
margin-bottom: 1rem;
color: #333;
}

.campus-info h2 {
color: #388e3c;
font-size: 1.8rem;
margin: 2rem 0 1rem;
}

#campus-courses {
list-style-type: none;
padding: 0;
display: grid;
grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
gap: 1rem;
}

#campus-courses li {
background-color: #f5f5f5;
padding: 1rem;
border-radius: 4px;
border-left: 4px solid #388e3c;
transition: transform 0.3s ease;
}

#campus-courses li:hover {
transform: translateX(5px);
background-color: #e8f5e9;
}

footer {
background-color: #333;
color: white;
text-align: center;
padding: 1rem;
margin-top: 3rem;
}

@media (max-width: 768px) {
.campus-info h1 {
    font-size: 2rem;
}

.campus-info img {
    height: 300px;
}

#campus-courses {
    grid-template-columns: 1fr;
}
}

@media (max-width: 480px) {
.campus-info {
    padding: 1rem;
}

.campus-info h1 {
    font-size: 1.8rem;
}

.campus-info img {
    height: 250px;
}
}
</style>
