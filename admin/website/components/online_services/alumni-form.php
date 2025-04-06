<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guest Profile Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
    <style>
        .guest-profile-container {
            max-width: 97%;
            margin: 50px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .header-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .logo-container {
            display: flex;
            justify-content: flex-start;
            flex: 0 0 120px; 
            margin-left: 20px; 
        }

        .logo-image {
            width: 120px;  
            height: 120px;
            border-radius: 50%;
            margin: 0;
            margin-right: 0;
            margin-left: 0;
        }

        .text-container {
            flex: 2;
            text-align: left;
        }

        .text-container p {
            margin: 2px 0;
            color: #333;
        }

        .university-name {
            font-size: 22px;
            font-weight: 700;
            color: #4CAF50;
            text-align: center;
        }

        .campus-name {
            font-size: 16px;
            font-weight: 500;
            color: #333;
            text-align: center;
        }

        .alumni-association {
            font-size: 14px;
            font-weight: 500;
            color: #333;
            text-align: center;
        }

        .form-heading {
            text-align: center;
            font-size: 24px;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .form-inputs-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .input-field {
            margin-bottom: 16px;
        }

        .input-field label {
            font-size: 14px;
            color: #333;
            margin-bottom: 6px;
        }

        #last-name,
        #first-name,
        #position,
        #phone-number,
        #email,
        #second-address,
        #accompanying-persons,
        #address {
            width: 100%;
            padding: 10px;
            font-size: 14px;
            border: 1px solid #ddd;
            border-radius: 6px;
            margin-top: 6px;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        #last-name:focus,
        #first-name:focus,
        #position:focus,
        #phone-number:focus,
        #email:focus,
        #second-address:focus,
        #accompanying-persons:focus,
        #address:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 4px rgba(76, 175, 80, 0.5);
            outline: none;
        }

        .submit-button {
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

        .submit-button:hover {
            background-color: #45a049;
        }

        @media (max-width: 768px) {
            .guest-profile-container {
                padding: 15px;
            }

            .form-heading {
                font-size: 22px;
            }

            .logo-image {
                max-width: 80px;
            }

            .form-inputs-container {
                grid-template-columns: 1fr 1fr;
            }

            .submit-button {
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .form-inputs-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
<body>
<section class="guest-profile-container">
    <div class="header-container">
        <div class="logo-container">
            <img src="user/bg/res1.png" alt="Cavite State University Logo" class="logo-image">
        </div>
        <div class="text-container">
            <p class="university-name">CAVITE STATE UNIVERSITY</p>
            <p class="campus-name">DON SEVERINO DELAS ALAS CAMPUS</p>
            <p class="alumni-association">ALUMNI ASSOCIATION, INC.</p>
        </div>
    </div>

    <h3 class="form-heading">Example: Booking Form Information</h3>
    <form id="guest-profile-form"> 
        <div class="form-inputs-container">
            <div class="input-field">
                <label for="last-name">Last Name:</label>
                <input type="text" id="last-name" name="last_name" required placeholder="Last name" value="De la Cruz" readonly>
            </div>
            <div class="input-field">
                <label for="first-name">First Name:</label>
                <input type="text" id="first-name" name="first_name" required placeholder="First name" value="Juan" readonly>
            </div>
            <div class="input-field">
                <label for="position">Position:</label>
                <input type="text" id="position" name="position" required placeholder="Position" value="Software Engineer" readonly>
            </div>
        </div>

        <div class="form-inputs-container">
            <div class="input-field">
                <label for="address">Address:</label>
                <input type="text" id="address" name="address" required placeholder="Address" value="123 Cavite St, Tagaytay" readonly>
            </div>
            <div class="input-field">
                <label for="phone-number">Phone Number:</label>
                <input type="tel" id="phone-number" name="phone_number" required placeholder="Phone number" value="09171234567" readonly>
            </div>
            <div class="input-field">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" required placeholder="Email address" value="juan.delacruz@email.com" readonly>
            </div>
        </div>

        <div class="form-inputs-container">
            <div class="input-field">
                <label for="second-address">Second Address:</label>
                <input type="text" id="second-address" name="second_address" placeholder="Second address" value="456 Manila Ave, Makati" readonly>
            </div>
            <div class="input-field">
                <label for="accompanying-persons">Accompanying Person(s):</label>
                <input type="text" id="accompanying-persons" name="accompanying_persons" placeholder="Accompanying persons" value="Maria De la Cruz" readonly>
            </div>
        </div>

        <button type="submit" class="submit-button">Submit</button>
    </form>
</section>
</body>
</html>
