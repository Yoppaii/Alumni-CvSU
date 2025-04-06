<form class="alumni-id-form">
    <div class="alumni-id-header-container">
        <div class="alumni-id-logo-container">
            <img src="user/bg/res1.png" alt="Cavite State University Logo" class="alumni-id-logo">
        </div>

        <div class="alumni-id-text-container">
            <p class="alumni-id-university-name">CAVITE STATE UNIVERSITY</p>
            <p class="alumni-id-campus-name">DON SEVERINO DELAS ALAS CAMPUS</p>
            <p class="alumni-id-alumni-association">ALUMNI ASSOCIATION, INC.</p>
            <p class="alumni-id-sec-registration">SEC Registration No. 2023110126538-08</p>
            <p class="alumni-id-location">Indang, Cavite, Philippines</p>
        </div>
    </div>

    <h2>Example: Alumni ID Card Form</h2>

    <div class="alumni-form-row">
        <div class="alumni-form-column">
            <label for="alumni-last-name">Last Name</label>
            <input type="text" id="alumni-last-name" name="last_name" value="Dela Cruz" readonly>

            <label for="alumni-first-name">First Name</label>
            <input type="text" id="alumni-first-name" name="first_name" value="Juan" readonly>

            <label for="alumni-middle-name">Middle Name</label>
            <input type="text" id="alumni-middle-name" name="middle_name" value="Cruz" readonly>

        </div>
        <div class="alumni-form-column">
            <label for="alumni-email">Email</label>
            <input type="email" id="alumni-email" name="email" value="juan.delacruz@email.com" readonly>

            <label for="alumni-course-name">Course</label>
            <input type="text" id="alumni-course-name" name="course" value="Bachelor of Science in Computer Science" readonly>

            <label for="alumni-year-graduated">Year Graduated</label>
            <input type="number" id="alumni-year-graduated" name="year_graduated" value="2020" readonly>

        </div>
        <div class="alumni-form-column">
            <label for="alumni-highschool-graduated">High School Graduated</label>
            <input type="text" id="alumni-highschool-graduated" name="highschool_graduated" value="Cavite High School" readonly>

            <label for="alumni-membership-type">Membership Type</label>
            <select id="alumni-membership-type" name="membership_type" disabled>
                <option value="5_years" selected>5 Years</option>
                <option value="lifetime">Lifetime</option>
            </select>
        </div>
    </div>

    <button type="submit">Submit</button>
</form>

<style>
    .alumni-id-header-container {
        display: flex;
        align-items: center;
        justify-content: flex-start; 
        padding: 15px;
        background-color: #ffffff;
        color: black;
        border-radius: 8px 8px 0 0;
        margin-bottom: 50px;
    }

    .alumni-id-logo-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .alumni-id-logo {
        width: 120px;  
        height: 120px;
        border-radius: 50%;
        margin: 0;
        margin-right: 0;
        margin-left: 0;
    }

    .alumni-id-text-container {
        flex-grow: 1;
        padding-left: 20px;
        text-align: left;  
    }

    .alumni-id-university-name {
        font-size: 20px;
        font-weight: 600;
        margin-bottom: 5px;
        text-align: center;
    }

    .alumni-id-campus-name {
        font-size: 18px;
        font-weight: 400;
        margin-bottom: 5px;
        text-align: center;
    }

    .alumni-id-alumni-association {
        font-size: 16px;
        font-weight: 300;
        margin-bottom: 5px;
        text-align: center;
    }

    .alumni-id-sec-registration,
    .alumni-id-location {
        font-size: 12px;
        font-weight: 200;
        text-align: center;
    }

    .alumni-id-form {
        max-width: 97%; 
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 8px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        border: 1px solid #ddd;
        font-family: 'Poppins', sans-serif;
    }

    .alumni-id-form h2 {
        text-align: center;
        font-size: 24px;
        color: #4CAF50;
        margin-bottom: 20px;
    }

    .alumni-form-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 20px;
    }

    .alumni-form-column {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .alumni-id-form label {
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
        color: #333;
    }

    .alumni-id-form input,
    .alumni-id-form select {
        width: 100%;
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ddd;
        border-radius: 6px;
        background-color: #f9f9f9;
        color: #444;
        font-size: 14px;
        transition: border-color 0.3s ease, background-color 0.3s ease;
        box-sizing: border-box; 
    }

    .alumni-id-form input:focus,
    .alumni-id-form select:focus {
        border-color: #3C8D40;
        background-color: #fff;
    }

    .alumni-id-form input[type="number"] {
        -moz-appearance: textfield;
        appearance: textfield;
    }

    .alumni-id-form button {
        width: 200px;
        padding: 12px;
        background-color: #3C8D40;
        color: white;
        font-size: 16px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
        margin: 20px auto;
        display: block;
    }

    .alumni-id-form button:hover {
        background-color: #2a6a33;
        transform: translateY(-2px);
    }

    .alumni-id-form button:active {
        transform: translateY(1px);
    }

    @media (max-width: 768px) {
        .alumni-id-form {
            padding: 15px;
        }

        .alumni-id-form h2 {
            font-size: 18px;
        }

        .alumni-id-form label {
            font-size: 12px;
        }

        .alumni-id-form input,
        .alumni-id-form select {
            font-size: 12px;
            padding: 8px;
        }

        .alumni-id-form button {
            font-size: 14px;
            padding: 8px;
        }

        .alumni-form-row {
            flex-direction: column;
        }

        .alumni-form-column {
            margin-bottom: 15px; 
        }

        .alumni-id-header-container {
            flex-direction: column;
            align-items: center;
        }

        .alumni-id-logo-container {
            margin-left: 0; 
            margin-bottom: 20px; 
        }

        .alumni-id-text-container {
            text-align: center;
            padding-left: 0;
        }
    }
</style>
