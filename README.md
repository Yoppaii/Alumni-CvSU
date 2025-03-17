
# 🎓 Alumni-CvSU  

The **Alumni Booking System** is a web-based platform designed to simplify the **room booking process** for alumni at **Cavite State University (CvSU)**. This system enables alumni to efficiently **reserve available rooms** for various events, meetings, or gatherings, ensuring a **seamless experience** for both users and administrators.  

## 🚀 How to Clone the Repository  

Follow these steps to clone the project to your local machine:  

### 1️⃣ Open Terminal (or Git Bash on Windows).  

### 2️⃣ Navigate to the directory where you want to store the project.  
Run the following command, replacing `path/to/directory` with your actual folder path:  
```sh
cd path/to/directory
```
📌 **Example:** If you're using XAMPP, you might store the project in the `htdocs` folder:  
```sh
cd E:\xampp\htdocs\Alumni-CvSU
```

### 3️⃣ Clone the repository.  
Run this command to download the project files from GitHub:  
```sh
git clone https://github.com/Yoppaii/Alumni-CvSU.git
```

### 4️⃣ Navigate into the cloned repository.  
Move into the project folder:  
```sh
cd Alumni-CvSU
```

### 5️⃣ Check the repository status.  
Ensure everything is correctly cloned and check the Git status:  
```sh
git status
```

---

## 🗄️ How to Import the SQL Database  

To set up the database, follow these steps:  

### 1️⃣ Open **phpMyAdmin**  
- If you’re using **XAMPP**, go to:  
  🔗 [http://localhost/phpmyadmin](http://localhost/phpmyadmin)  

### 2️⃣ Create a New Database  
- Click on **Databases** in the top menu.  
- In the **Create database** field, enter:  
  ```
  room_reservation
  ```
- Click **Create**.  

### 3️⃣ Import the Database  
- Click on the **room_reservation** database you just created.  
- Navigate to the **Import** tab.  
- Click **Choose File** and select the SQL file from your project.  
  - The file is usually named **room_reservation.sql** and should be located in the project folder.  
- Click **Go** to start the import process.  

### 4️⃣ Verify the Import  
- Once the import is complete, check the database tables under **room_reservation** in phpMyAdmin.  
- If you see multiple tables, the import was successful! ✅  

---

🎉 Your database is now set up! You’re ready to start using the Alumni Booking System. Let me know if you need further assistance. 🚀
