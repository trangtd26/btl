<?php
session_start();

$errors = [];
$data = [
    'first_name' => '',
    'last_name' => '',
    'email' => '',
    'invoice_id' => '',
    'pay_for' => [],
    'additional' => '',
    'receipt' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // First name
    if (empty($_POST["first_name"])) {
        $errors[] = "First name is required";
    } else {
        $data['first_name'] = htmlspecialchars($_POST["first_name"]);
    }

    // Last name
    if (empty($_POST["last_name"])) {
        $errors[] = "Last name is required";
    } else {
        $data['last_name'] = htmlspecialchars($_POST["last_name"]);
    }

    // Email
    if (empty($_POST["email"])) {
        $errors[] = "Email is required";
    } elseif (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    } else {
        $data['email'] = htmlspecialchars($_POST["email"]);
    }

    // Invoice ID
    if (empty($_POST["invoice_id"])) {
        $errors[] = "Invoice ID is required";
    } else {
        $data['invoice_id'] = htmlspecialchars($_POST["invoice_id"]);
    }

    // Pay For (checkboxes)
    $data['pay_for'] = !empty($_POST["pay_for"]) ? $_POST["pay_for"] : [];

    // File upload
    if (!empty($_FILES["receipt"]["name"])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $target_file = $target_dir . time() . "_" . basename($_FILES["receipt"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowed) && $_FILES["receipt"]["size"] <= 1000000) {
            if (move_uploaded_file($_FILES["receipt"]["tmp_name"], $target_file)) {
                $data['receipt'] = $target_file;
            } else {
                $errors[] = "Error uploading file.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, PNG & GIF files are allowed (max 1MB).";
        }
    } else {
        $errors[] = "Receipt file is required.";
    }

    // Additional info
    $data['additional'] = htmlspecialchars($_POST["additional"]);

    // If valid, store in session and redirect
    if (empty($errors)) {
        $_SESSION["data"] = $data;
        header("Location: result.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Receipt Upload Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eef2f3;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border-radius: 5px;
            width: 400px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .header {
            background: #f5a623;
            color: #fff;
            padding: 10px;
            text-align: center;
            margin: -20px -20px 20px -20px;
            border-top-left-radius: 5px;
            border-top-right-radius: 5px;
        }
        h2 {
            text-align: center;
            color: #333;
            margin: 0;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }
        .name-group, .email-group {
            display: flex;
            justify-content: space-between;
        }
        .name-group input, .email-group input {
            width: 48%;
        }
        input[type="text"], input[type="email"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 100%;
        }
        .checkbox-group {
            margin-bottom: 15px;
        }
        .checkbox-group label {
            font-weight: normal;
            color: #555;
            margin-right: 10px;
        }
        .file-upload {
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            color: #888;
        }
        .file-upload input[type="file"] {
            display: none;
        }
        .file-upload label {
            font-weight: normal;
            color: #007bff;
            cursor: pointer;
        }
        textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            resize: vertical;
        }
        input[type="submit"] {
            background: #333;
            color: white;
            padding: 10px;
            border: none;
            width: 100%;
            border-radius: 4px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #555;
        }
        ul.error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Payment Receipt Upload Form</h2>

    <?php if (!empty($errors)): ?>
        <ul class="error">
            <?php foreach ($errors as $error) echo "<li>$error</li>"; ?>
        </ul>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">
        <div class="form-group name-group">
            <label>Name</label>
            <input type="text" name="first_name" value="<?= $data['first_name'] ?>" placeholder="First Name">
            <input type="text" name="last_name" value="<?= $data['last_name'] ?>" placeholder="Last Name">
        </div>

        <div class="form-group email-group">
            <label>Email</label>
            <input type="email" name="email" value="<?= $data['email'] ?>" placeholder="example@example.com">
            <input type="text" name="invoice_id" value="<?= $data['invoice_id'] ?>" placeholder="Invoice ID">
        </div>

        <div class="form-group">
            <label>Pay For</label>
            <div class="checkbox-group">
                <label><input type="checkbox" name="pay_for[]" value="15K Category" <?= in_array("15K Category", $data['pay_for']) ? "checked" : "" ?>> 15K Category</label>
                <label><input type="checkbox" name="pay_for[]" value="35K Category" <?= in_array("35K Category", $data['pay_for']) ? "checked" : "" ?>> 35K Category</label>
                <label><input type="checkbox" name="pay_for[]" value="55K Category" <?= in_array("55K Category", $data['pay_for']) ? "checked" : "" ?>> 55K Category</label>
                <label><input type="checkbox" name="pay_for[]" value="75K Category" <?= in_array("75K Category", $data['pay_for']) ? "checked" : "" ?>> 75K Category</label>
                <label><input type="checkbox" name="pay_for[]" value="116K Category" <?= in_array("116K Category", $data['pay_for']) ? "checked" : "" ?>> 116K Category</label>
                <label><input type="checkbox" name="pay_for[]" value="Shuttle One Way" <?= in_array("Shuttle One Way", $data['pay_for']) ? "checked" : "" ?>> Shuttle One Way</label>
                <label><input type="checkbox" name="pay_for[]" value="Shuttle Two Ways" <?= in_array("Shuttle Two Ways", $data['pay_for']) ? "checked" : "" ?>> Shuttle Two Ways</label>
                <label><input type="checkbox" name="pay_for[]" value="Training Cap Merchandise" <?= in_array("Training Cap Merchandise", $data['pay_for']) ? "checked" : "" ?>> Training Cap Merchandise</label>
                <label><input type="checkbox" name="pay_for[]" value="Compressport T-Shirt Merchandise" <?= in_array("Compressport T-Shirt Merchandise", $data['pay_for']) ? "checked" : "" ?>> Compressport T-Shirt Merchandise</label>
                <label><input type="checkbox" name="pay_for[]" value="Buf Merchandise" <?= in_array("Buf Merchandise", $data['pay_for']) ? "checked" : "" ?>> Buf Merchandise</label>
                <label><input type="checkbox" name="pay_for[]" value="Other" <?= in_array("Other", $data['pay_for']) ? "checked" : "" ?>> Other</label>
            </div>
        </div>

        <div class="form-group">
            <label>Please upload your payment receipt.</label>
            <div class="file-upload">
                <input type="file" name="receipt" id="receipt">
                <label for="receipt">Browse Files</label><br>
                <span>Drag and drop files here</span><br>
                <small>.jpg, .jpeg, .png, .gif (1mb max.)</small>
            </div>
        </div>

        <div class="form-group">
            <label>Additional Information</label>
            <textarea name="additional"><?= $data['additional'] ?></textarea>
        </div>

        <input type="submit" value="Submit">
    </form>
</div>
</body>
</html>