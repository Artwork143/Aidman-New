<?php
include 'db_connect.php'; // Ensure your database connection file is included

// Check if the resident ID is passed
if (isset($_GET['id'])) {
    $resident_id = $_GET['id'];
    
    // Fetch the resident details based on ID
    $stmt = $conn->prepare("SELECT * FROM resident_list WHERE id = ?");
    $stmt->bind_param("i", $resident_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    // If resident found, fetch data into variables
    if ($result->num_rows > 0) {
        $resident = $result->fetch_assoc();
        $firstname = $resident['firstname'];
        $middlename = $resident['middlename'];
        $lastname = $resident['lastname'];
        $suffix = $resident['suffix'];
        $gender = $resident['gender'];
        $age = $resident['age'];
        $marital_status = $resident['marital_status'];
        $purok = $resident['purok'];
    } else {
        echo "Resident not found.";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $suffix = $_POST['suffix'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $marital_status = $_POST['marital_status'];
    $purok = $_POST['purok'];

    // Update resident details
    $stmt = $conn->prepare("UPDATE resident_list SET firstname = ?, middlename = ?, lastname = ?, suffix = ?, gender = ?, age = ?, marital_status = ?, purok = ? WHERE id = ?");
    $stmt->bind_param("ssssssisi", $firstname, $middlename, $lastname, $suffix, $gender, $age, $marital_status, $purok, $resident_id);

    if ($stmt->execute()) {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'success',
                    title: 'Resident Updated Successfully',
                    text: 'The resident details have been updated.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'resident_lists.php';
                });
            };
        </script>";
    } else {
        echo "<script>
            window.onload = function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to update resident. Please try again.',
                });
            };
        </script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resident</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: linear-gradient(to right, #6a11cb, #2575fc); /* Gradient background */
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .form-container {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            width: 100%;
            max-width: 450px;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .form-container:hover {
            transform: scale(1.03);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        h1 {
            font-size: 36px;
            color: #09203f;
            text-align: center;
            margin-bottom: 25px;
            text-shadow: 2px 2px 5px rgba(0,0,0,0.3); /* Added text shadow */
            animation: fadeIn 1.5s ease-in-out; /* Animation */
        }
        @keyframes fadeIn {
            0% { opacity: 0; transform: translateY(-50px); }
            100% { opacity: 1; transform: translateY(0); }
        }
        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 5px;
            display: block;
        }
        .input-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }
        .input-group input, .input-group select {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
            font-size: 14px;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .input-group input:focus, .input-group select:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .input-group input[type="number"] {
            -moz-appearance: textfield;
        }
        .input-group input#suffix {
            color: #aaa; /* Grey color */
        }
        .input-group input#suffix::placeholder {
            color: #aaa;
            font-style: italic;
        }
        .submit-button {
            width: 100%;
            padding: 12px;
            background-color: #28a745;
            color: #fff;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .submit-button:hover {
            background-color: #218838;
        }
        .cancel-button {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #007bff;
            font-size: 14px;
            text-decoration: none;
        }
        .cancel-button:hover {
            color: #0056b3;
        }
        @media (max-width: 600px) {
            .form-container {
                width: 90%;
                padding: 20px;
            }
            .input-group {
                grid-template-columns: 1fr;
            }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="form-container">
        <h1>Edit Resident</h1>
        <form action="edit_resident.php?id=<?php echo $resident_id; ?>" method="POST">
            <div class="input-group">
                <div>
                    <label for="firstname">First Name:</label>
                    <input type="text" id="firstname" name="firstname" value="<?php echo $firstname; ?>" required>
                </div>
                <div>
                    <label for="middlename">Middle Name:</label>
                    <input type="text" id="middlename" name="middlename" value="<?php echo $middlename; ?>" required>
                </div>
            </div>
            
            <div class="input-group">
                <div>
                    <label for="lastname">Last Name:</label>
                    <input type="text" id="lastname" name="lastname" value="<?php echo $lastname; ?>" required>
                </div>
                <div>
                    <label for="suffix">Suffix (Optional):</label>
                    <input type="text" id="suffix" name="suffix" value="<?php echo $suffix; ?>" placeholder="e.g., Jr.">
                </div>
            </div>

            <div class="input-group">
                <div>
                    <label for="gender">Gender:</label>
                    <select id="gender" name="gender" required>
                        <option value="Male" <?php if ($gender == 'Male') echo 'selected'; ?>>Male</option>
                        <option value="Female" <?php if ($gender == 'Female') echo 'selected'; ?>>Female</option>
                        <option value="Other" <?php if ($gender == 'Other') echo 'selected'; ?>>Other</option>
                    </select>
                </div>
                <div>
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" value="<?php echo $age; ?>" required>
                </div>
            </div>

            <div class="input-group">
                <div>
                    <label for="marital_status">Marital Status:</label>
                    <select id="marital_status" name="marital_status" required>
                        <option value="Single" <?php if ($marital_status == 'Single') echo 'selected'; ?>>Single</option>
                        <option value="Married" <?php if ($marital_status == 'Married') echo 'selected'; ?>>Married</option>
                        <option value="Divorce" <?php if ($marital_status == 'Divorce') echo 'selected'; ?>>Divorce</option>
                    </select>
                </div>
                <div>
                    <label for="purok">Purok:</label>
                    <select id="purok" name="purok" required>
                        <option value="Villa Cristina Zone 1" <?php if ($purok == 'Villa Cristina Zone 1') echo 'selected'; ?>>Villa Cristina Zone 1</option>
                        <option value="Brgy Proper" <?php if ($purok == 'Brgy Proper') echo 'selected'; ?>>Brgy Proper</option>
                        <option value="Tilapia Street" <?php if ($purok == 'Tilapia Street') echo 'selected'; ?>>Tilapia Street</option>
                        <option value="Bamboo Street" <?php if ($purok == 'Bamboo Street') echo 'selected'; ?>>Bamboo Street</option>
                        <option value="Punta Baybay" <?php if ($purok == 'Punta Baybay') echo 'selected'; ?>>Punta Baybay</option>
                        <option value="Villa Cristina Zone 2" <?php if ($purok == 'Villa Cristina Zone 2') echo 'selected'; ?>>Villa Cristina Zone 2</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="submit-button">Update Resident</button>
            <a href="resident_lists.php" class="cancel-button">Cancel</a>
        </form>
    </div>
</body>
</html>

