<?php 
session_start();

// Include database connection
include '../connection/db.php';
// Fetch names, ratings, and feedback from the feedback table
$sql = "SELECT name, rating, feedback FROM feedback WHERE rating BETWEEN 4 AND 5";
$result = $conn->query($sql);

// Initialize arrays to store names, ratings, and feedback
$names = array();
$ratings = array();
$feedbacks = array();

// Fetch the data and store it in arrays
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $names[] = $row['name'];
        $ratings[] = $row['rating'];
        $feedbacks[] = $row['feedback'];
    }
}
// Fetch turf details from the database
$turfs = array();
$sql = "SELECT id, turf_name, location, size, address, image, time_slot, vacancy_status FROM play_turf";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while($row = $result->fetch_assoc()) {
        $turfs[] = $row;
    }
} else {
    echo "0 results";
}

// Close the connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>Dashboard</title>
    <link rel="icon" type="image/png" href="../images/logo.png">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <link rel="stylesheet" href="../css/dashboard.css">
    
    <style>
        /* Add your custom styles here */
        .modal {
    display: none;
    position: fixed;
    z-index: 1;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 0 auto; /* Center horizontally */
    margin-top: 10%; /* Adjust the top margin to center vertically */
    padding: 20px;
    border: 1px solid #888;
    width: 80%; /* Set the width of the modal */
    max-width: 500px; /* Limit the maximum width */
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: black;
    text-decoration: none;
    cursor: pointer;
}
.login-button {
    background-color: #6ed56d; /* Light green */
    color: white;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
    font-size: 16px;
    transition: background-color 0.3s, color 0.3s;
}

.login-button:hover {
    background-color: #5da8ff; /* Light blue */
}  
/* Define card container style */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            margin-top: 20px;
        }

        /* Define card style */
        .card {
            width: 300px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Define card hover effect */
        .card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        /* Define title style */
        .card h3 {
            font-size: 20px;
            color: #333;
            margin-bottom: 10px;
        }

        /* Define text style */
        .card p {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }

        /* Define star style */
        .stars {
            display: flex;
            justify-content: center;
            margin-bottom: 10px;
        }

        /* Define star icon style */
        .star-icon {
            color: yellow;
            font-size: 24px;
            margin: 0 2px;
            text-shadow: 0 0 1px black; /* Add black stroke */
        }

    </style>
</head>
<body>
<div class="navbar">
    <a href="#"><img src="../images/logo.png" alt="Your Logo" style="width: 100px; height: auto;"></a>
    <a href="#home-content">Home</a>
    
    <a href="#contact" onclick="showContactModal()">Contact</a>
    <a href="#about" onclick="showAboutModal()">About</a>
    <a href="login.php">Login</a>
</div>




<div id="home-content">
    <div class="turf-container">
        <?php foreach ($turfs as $turf): ?>
            <div class="turf-card">
                <img src="<?php echo htmlspecialchars($turf['image']); ?>" alt="Turf Image">
                <div class="turf-info">
                    <h3><?php echo htmlspecialchars($turf['turf_name']); ?></h3>
                    <p>Location: <?php echo htmlspecialchars($turf['location']); ?></p>
                    <p>Size: <?php echo htmlspecialchars($turf['size']); ?></p>
                    <p>Address: <?php echo htmlspecialchars($turf['address']); ?></p>
                    <p>Time Slot: <?php echo htmlspecialchars($turf['time_slot']); ?></p>
                    <p>Vacancy: <?php echo $turf['vacancy_status'] ? 'Available' : 'Not Available'; ?></p>
                    <!-- Form submission that passes the turf ID to booking.php -->
                    <form action="booking.php" method="get">
                        <input type="hidden" name="turf_id" value="<?php echo $turf['id']; ?>">
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <?php 
                                $buttonClass = $turf['vacancy_status'] ? ($turf['vacancy_status'] > 0 ? 'available' : 'not-available') : 'not-available';
                                $buttonDisabled = $turf['vacancy_status'] ? '' : 'disabled';
                            ?>
                            <button type="submit" class="book-now-button <?php echo $buttonClass; ?>" <?php echo $buttonDisabled; ?>>Book Now</button>
                        <?php else: ?>
                            <button type="button" onclick="showLoginModal()" class="book-now-button">Book Now</button>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<!-- Login Modal -->
<div id="loginModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeLoginModal()">&times;</span>
        <h2>Please Login</h2>
        <p>You need to login to book a turf.</p>
        <form action="login.php" method="get">
            <button type="submit" class="login-button">Login</button>
        </form>
        
        <!-- Button to close the modal -->
     
    </div>
</div>

<!-- JavaScript -->

<!-- Contact Modal -->
<div id="contactModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeContactModal()">&times;</span>
        <h2>Contact Us</h2>
        <p>Email: playarena@gmail.com</p>
        <p>Phone: +91 8689888488</p>
        <!-- Add contact form here -->
    </div>
</div>

<!-- About Modal -->
<div id="aboutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeAboutModal()">&times;</span>
        <h2>About Us</h2>
        <p>Welcome to PlayArena! We're passionate about sports and making it easy for you to find and book turf venues. Whether you're playing competitively or just for fun, we've got you covered. Join us and discover the perfect place to play!</p>
        <!-- Add about content here -->
    </div>
</div>

<!-- JavaScript -->
<script>
    function showLoginModal() {
        document.getElementById('loginModal').style.display = 'block';
    }

    function closeLoginModal() {
        document.getElementById('loginModal').style.display = 'none';
    }

    function showContactModal() {
        document.getElementById('contactModal').style.display = 'block';
    }

    function closeContactModal() {
        document.getElementById('contactModal').style.display = 'none';
    }

    function showAboutModal() {
        document.getElementById('aboutModal').style.display = 'block';
    }

    function closeAboutModal() {
        document.getElementById('aboutModal').style.display = 'none';
    }
</script>
<footer>
        <div class="card-container">
            <!-- Display individual ratings in cards -->
            <?php 
                foreach ($ratings as $index => $rating) {
                    echo '<div class="card">';
                    
                    echo '<h3>Rating: ';
                    for ($i = 0; $i < $rating; $i++) {
                        echo '<span class="star-icon">★</span>';
                    }
                    echo '</h3>';
                    
                    echo '<div class="stars">';
                    // Loop to display stars based on the rating
                    
                    echo '</div>';
                    echo '<p><strong>Name:</strong> ' . htmlspecialchars($names[$index]) . '</p>';
                    echo '<p><strong>Feedback:</strong> ' . htmlspecialchars($feedbacks[$index]) . '</p>';
                    echo '</div>';
                }
            ?>
        </div>
    </footer>

</body>
</html>
