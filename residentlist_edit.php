<?php
include 'db_connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Fetch existing resident details
    $sql = "SELECT * FROM resident_list WHERE id=?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $resident = $result->fetch_assoc();
        } else {
            echo "<script>alert('Resident not found.'); window.location.href='resident_lists.php';</script>";
            exit;
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
        exit;
    }
}

if (isset($_POST['edit'])) {
    $id = $_POST['id'];
    $fullname = $_POST['fullname'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $purok = $_POST['purok'];

    // Check if the connection is valid
    if ($conn) {
        $sql = "UPDATE resident_list SET fullname=?, gender=?, age=?, purok=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("ssisi", $fullname, $gender, $age, $purok, $id);
            if ($stmt->execute()) {
                echo "<script>
                    window.onload = function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Record Updated Successfully',
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
                            text: 'Failed to update record. Please try again.',
                        });
                    };
                </script>";
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Database connection error: " . mysqli_connect_error();
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resident</title>
    <style>
        /* Container for the entire page */
        .edit-resident-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: #f4f4f9;
            font-family: Arial, sans-serif;
        }

        /* Form container */
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            width: 400px;
            text-align: center;
        }

        /* Form title */
        .form-container h1 {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        /* Labels and inputs */
        .form-container label {
            display: block;
            text-align: left;
            margin-bottom: 5px;
            font-size: 14px;
            color: #555;
        }

        .form-container input, 
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
        }

        .form-container input:focus, 
        .form-container select:focus {
            border-color: #007bff;
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.5);
        }

        /* Submit button */
        .submit-button {
            display: inline-block;
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-button:hover {
            background-color: #0056b3;
        }

        /* Cancel button */
        .cancel-button {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #007bff;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .cancel-button:hover {
            color: #0056b3;
        }
    </style>
    <!-- Include SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="edit-resident-container">
        <div class="form-container">
            <h1>Edit Resident</h1>
            <form action="residentlist_edit.php" method="POST">
                <input type="hidden" name="id" value="<?php echo isset($resident['id']) ? $resident['id'] : ''; ?>">
                
                <label for="fullname">Fullname:</label>
                <input type="text" id="fullname" name="fullname" value="<?php echo isset($resident['fullname']) ? $resident['fullname'] : ''; ?>" required>
                
                <label for="gender">Gender:</label>
                <select id="gender" name="gender" required>
                    <option value="Male" <?php echo (isset($resident['gender']) && $resident['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                    <option value="Female" <?php echo (isset($resident['gender']) && $resident['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                    <option value="Other" <?php echo (isset($resident['gender']) && $resident['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                </select>

                <label for="age">Age:</label>
                <input type="number" id="age" name="age" value="<?php echo isset($resident['age']) ? $resident['age'] : ''; ?>" required>
                
                <label for="marital status">Marital status:</label>
                <select id="marital status" name="marital status" required>
                    <option value="Single" <?php echo (isset($resident['marital status']) && $resident['marital status'] == 'Single') ? 'selected' : ''; ?>>Single</option>
                    <option value="Married" <?php echo (isset($resident['marital status']) && $resident['marital status'] == 'Married') ? 'selected' : ''; ?>>Married</option>
                    <option value="Divorce" <?php echo (isset($resident['marital status']) && $resident['marital status'] == 'Divorce') ? 'selected' : ''; ?>>Divorce</option>
                </select>                
                
                <label for="purok">Purok:</label>
                <select id="purok" name="purok" required>
                    <option value="Villa Cristina Zone 1" <?php echo (isset($resident['purok']) && $resident['purok'] == 'Villa Cristina Zone 1') ? 'selected' : ''; ?>>Villa Cristina Zone 1</option>
                    <option value="Daga" <?php echo (isset($resident['purok']) && $resident['purok'] == 'Daga') ? 'selected' : ''; ?>>Daga</option>
                    <option value="Brgy Proper" <?php echo (isset($resident['purok']) && $resident['purok'] == 'Brgy Proper') ? 'selected' : ''; ?>>Brgy Proper</option>
                    <option value="Tilapia Street" <?php echo (isset($resident['purok']) && $resident['purok'] == 'Tilapia Street') ? 'selected' : ''; ?>>Tilapia Street</option>
                    <option value="Bamboo Street" <?php echo (isset($resident['purok']) && $resident['purok'] == 'Bamboo Street') ? 'selected' : ''; ?>>Bamboo Street</option>
                    <option value="Punta Baybay" <?php echo (isset($resident['purok']) && $resident['purok'] == 'Punta Baybay') ? 'selected' : ''; ?>>Punta Baybay</option>
                    <option value="Villa Cristina Zone 2" <?php echo (isset($resident['purok']) && $resident['purok'] == 'Villa Cristina Zone 2') ? 'selected' : ''; ?>>Villa Cristina Zone 2</option>
                </select>    
                <button type="submit" name="edit" class="submit-button">Update Resident</button>
                <a href="resident_lists.php" class="cancel-button">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
