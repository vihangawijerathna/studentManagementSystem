<?php
// Database Configuration
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'student_management';

// Create connection
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$create_table_query = "CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    date_of_birth DATE,
    gender ENUM('Male', 'Female', 'Other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($create_table_query);

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Add Student
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['date_of_birth'];
        $gender = $_POST['gender'];

        $sql = "INSERT INTO students (first_name, last_name, email, phone, date_of_birth, gender) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $first_name, $last_name, $email, $phone, $dob, $gender);
        
        if ($stmt->execute()) {
            $success_message = "Student added successfully!";
        } else {
            $error_message = "Error adding student: " . $stmt->error;
        }
        $stmt->close();
    }

    // Update Student
    if (isset($_POST['action']) && $_POST['action'] == 'update') {
        $id = $_POST['student_id'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $dob = $_POST['date_of_birth'];
        $gender = $_POST['gender'];

        $sql = "UPDATE students SET first_name=?, last_name=?, email=?, phone=?, date_of_birth=?, gender=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $dob, $gender, $id);
        
        if ($stmt->execute()) {
            $success_message = "Student updated successfully!";
        } else {
            $error_message = "Error updating student: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $sql = "DELETE FROM students WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        $success_message = "Student deleted successfully!";
    } else {
        $error_message = "Error deleting student: " . $stmt->error;
    }
    $stmt->close();
}

// Fetch students
$students_query = "SELECT * FROM students ORDER BY id DESC";
$students_result = $conn->query($students_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Management System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1, h2 {
            color: #333;
            text-align: center;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #dff0d8;
            color: #3c763d;
        }
        .alert-error {
            background-color: #f2dede;
            color: #a94442;
        }
        form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            display: block;
            width: 100%;
            padding: 10px;
            background: #5cb85c;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #4cae4c;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        .actions {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 8px 15px;
            background: #5bc0de;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin-right: 5px;
        }
        .btn-delete {
            background: #d9534f;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Student Management System</h1>

        <!-- Success/Error Messages -->
        <?php if(isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if(isset($error_message)): ?>
            <div class="alert alert-error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <!-- Add/Edit Student Form -->
        <form method="POST" action="">
            <h2>Add/Edit Student</h2>
            <input type="hidden" name="student_id" id="student_id">
            <input type="hidden" name="action" id="form_action" value="add">
            
            <input type="text" name="first_name" id="first_name" placeholder="First Name" required>
            <input type="text" name="last_name" id="last_name" placeholder="Last Name" required>
            <input type="email" name="email" id="email" placeholder="Email" required>
            <input type="tel" name="phone" id="phone" placeholder="Phone">
            
            <label>Date of Birth:</label>
            <input type="date" name="date_of_birth" id="date_of_birth">
            
            <select name="gender" id="gender">
                <option value="">Select Gender</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select>
            
            <button type="submit">Save Student</button>
        </form>

        <!-- Student List -->
        <h2>Student List</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($student = $students_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $student['id']; ?></td>
                    <td><?php echo $student['first_name'] . ' ' . $student['last_name']; ?></td>
                    <td><?php echo $student['email']; ?></td>
                    <td><?php echo $student['phone']; ?></td>
                    <td>
                        <a href="#" class="btn edit-btn" 
                           data-id="<?php echo $student['id']; ?>"
                           data-first-name="<?php echo $student['first_name']; ?>"
                           data-last-name="<?php echo $student['last_name']; ?>"
                           data-email="<?php echo $student['email']; ?>"
                           data-phone="<?php echo $student['phone']; ?>"
                           data-dob="<?php echo $student['date_of_birth']; ?>"
                           data-gender="<?php echo $student['gender']; ?>">
                            Edit
                        </a>
                        <a href="?delete=<?php echo $student['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        // Edit functionality
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Populate form with student data
                document.getElementById('student_id').value = this.getAttribute('data-id');
                document.getElementById('first_name').value = this.getAttribute('data-first-name');
                document.getElementById('last_name').value = this.getAttribute('data-last-name');
                document.getElementById('email').value = this.getAttribute('data-email');
                document.getElementById('phone').value = this.getAttribute('data-phone');
                document.getElementById('date_of_birth').value = this.getAttribute('data-dob');
                document.getElementById('gender').value = this.getAttribute('data-gender');
                
                // Change form action to update
                document.getElementById('form_action').value = 'update';
            });
        });
    </script>
</body>
</html>

<?php
// Close database connection
$conn->close();
?>