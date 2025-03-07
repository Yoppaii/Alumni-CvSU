<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'main_db.php';

$projectRoot = $_SERVER['DOCUMENT_ROOT'] . '/Alumni-CvSU';
$uploadDir = $projectRoot . '/asset/uploads';

if (!file_exists($uploadDir)) {
   mkdir($uploadDir, 0777, true);
}

function getRoomImages($room_id) {
   global $mysqli;
   $stmt = $mysqli->prepare("SELECT * FROM room_images WHERE room_id = ?");
   $stmt->bind_param("i", $room_id);
   $stmt->execute();
   return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

if (isset($_POST['upload'])) {
   $room_id = $_POST['room_id'];
   $current_count = count(getRoomImages($room_id));
   
   if ($current_count >= 5) {
       echo "<script>alert('Maximum 5 images allowed per room');</script>";
   } else {
       $file = $_FILES['image'];
       $fileName = time() . '_' . $file['name'];
       $target = $uploadDir . '/' . $fileName;
       
       if (move_uploaded_file($file['tmp_name'], $target)) {
           $stmt = $mysqli->prepare("INSERT INTO room_images (room_id, image_path) VALUES (?, ?)");
           $stmt->bind_param("is", $room_id, $fileName);
           $stmt->execute();
       } else {
           error_log("Upload failed: " . error_get_last()['message']);
       }
   }
}

if (isset($_POST['delete'])) {
   $image_id = $_POST['image_id'];
   $stmt = $mysqli->prepare("SELECT image_path FROM room_images WHERE id = ?");
   $stmt->bind_param("i", $image_id);
   $stmt->execute();
   $result = $stmt->get_result()->fetch_assoc();
   
   if ($result) {
       unlink($uploadDir . '/' . $result['image_path']);
       $stmt = $mysqli->prepare("DELETE FROM room_images WHERE id = ?");
       $stmt->bind_param("i", $image_id);
       $stmt->execute();
   }
}

$rooms = [
   'Room 1', 'Room 2', 'Room 3', 'Room 4', 'Room 5', 
   'Room 6', 'Room 7', 'Room 8', 'Board Room', 
   'Conference Room', 'Lobby', 'Building'
];

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 0;
?>

<!DOCTYPE html>
<html>
<head>
   <style>
       .RIM-container {
           max-width: 1200px;
           margin: 0 auto;
           padding: 20px;
       }

       .RIM-card {
           background: white;
           border-radius: 10px;
           box-shadow: 0 2px 4px rgba(0,0,0,0.1);
           padding: 20px;
       }

       .RIM-tabs {
           display: flex;
           flex-wrap: wrap;
           border-bottom: 2px solid #e5e7eb;
           margin-bottom: 20px;
           gap: 10px;
       }

       .RIM-tab {
           padding: 10px 20px;
           cursor: pointer;
           border-bottom: 2px solid transparent;
           margin-bottom: -2px;
           color: #6b7280;
           transition: all 0.3s ease;
       }

       .RIM-tab:hover {
           color: #10b981;
       }

       .RIM-tab.active {
           color: #10b981;
           border-bottom-color: #10b981;
           font-weight: 600;
       }

       .RIM-section {
           display: none;
       }

       .RIM-section.active {
           display: block;
       }

       .RIM-grid {
           display: grid;
           grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
           gap: 1rem;
           margin-top: 1rem;
       }

       .RIM-image-container {
           position: relative;
       }

       .RIM-image-container img {
           width: 100%;
           height: 150px;
           object-fit: cover;
           border-radius: 4px;
       }

       .RIM-delete-btn {
           position: absolute;
           top: 5px;
           right: 5px;
           background: #ff4444;
           color: white;
           border: none;
           border-radius: 50%;
           width: 24px;
           height: 24px;
           cursor: pointer;
           font-size: 14px;
       }

       .RIM-upload-form {
           margin-top: 1rem;
           padding: 1rem;
           background: #f9fafb;
           border-radius: 8px;
       }

       .RIM-preview {
           margin-top: 10px;
           display: none;
       }

       .RIM-preview img {
           max-width: 150px;
           max-height: 150px;
           object-fit: cover;
           border-radius: 4px;
       }

       @media (max-width: 768px) {
           .RIM-tabs {
               overflow-x: auto;
               flex-wrap: nowrap;
               -webkit-overflow-scrolling: touch;
           }
       }
   </style>
</head>
<body>
   <div class="RIM-container">
       <div class="RIM-card">
           <div class="RIM-tabs">
               <?php foreach ($rooms as $index => $room): ?>
                   <div class="RIM-tab <?php echo ($current_tab == $index) ? 'active' : ''; ?>" 
                        data-tab="<?php echo $index; ?>">
                       <?php echo $room; ?>
                   </div>
               <?php endforeach; ?>
           </div>

           <?php foreach ($rooms as $index => $room): ?>
               <div class="RIM-section <?php echo ($current_tab == $index) ? 'active' : ''; ?>" 
                    id="RIM-room-<?php echo $index; ?>">
                   <h3><?php echo $room; ?> Images</h3>
                   
                   <div class="RIM-grid">
                       <?php 
                       $images = getRoomImages($index + 1);
                       foreach ($images as $image): 
                       ?>
                           <div class="RIM-image-container">
                               <img src="asset/uploads/<?php echo $image['image_path']; ?>" 
                                    alt="<?php echo $room; ?>">
                               <form method="POST">
                                   <input type="hidden" name="image_id" value="<?php echo $image['id']; ?>">
                                   <button type="submit" name="delete" class="RIM-delete-btn">×</button>
                               </form>
                           </div>
                       <?php endforeach; ?>
                   </div>
                   
                   <?php if (count($images) < 5): ?>
                       <form method="POST" enctype="multipart/form-data" class="RIM-upload-form">
                           <input type="hidden" name="room_id" value="<?php echo $index + 1; ?>">
                           <input type="file" name="image" accept="image/*" required onchange="previewImage(this)">
                           <button type="submit" name="upload">Upload Image</button>
                           <div class="RIM-preview" id="RIM-preview-<?php echo $index; ?>">
                               <img src="" alt="Preview">
                           </div>
                       </form>
                   <?php endif; ?>
               </div>
           <?php endforeach; ?>
       </div>
   </div>

   <script>
   document.addEventListener('DOMContentLoaded', function() {
       const tabs = document.querySelectorAll('.RIM-tab');
       
       tabs.forEach(tab => {
           tab.addEventListener('click', () => {
               const tabId = tab.getAttribute('data-tab');
               
               tabs.forEach(t => t.classList.remove('active'));
               tab.classList.add('active');
               
               document.querySelectorAll('.RIM-section').forEach(section => {
                   section.classList.remove('active');
               });
               document.getElementById(`RIM-room-${tabId}`).classList.add('active');
               
               const currentUrl = new URL(window.location.href);
               currentUrl.searchParams.set('tab', tabId);
               history.pushState({}, '', currentUrl.toString());
           });
       });
   });

   function previewImage(input) {
       const roomId = input.closest('form').querySelector('input[name="room_id"]').value;
       const preview = document.getElementById(`RIM-preview-${roomId - 1}`);
       const previewImg = preview.querySelector('img');
       
       if (input.files && input.files[0]) {
           const reader = new FileReader();
           
           reader.onload = function(e) {
               previewImg.src = e.target.result;
               preview.style.display = 'block';
           }
           
           reader.readAsDataURL(input.files[0]);
       }
   }
   </script>
</body>
</html>