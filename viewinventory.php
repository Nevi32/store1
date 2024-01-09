<?php
// Start the session
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Inventory</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">

    <style>
          body, html {
            height: 100%;
            margin: 0;
            font-family: 'Arial', sans-serif;
        }

        #dashboard {
            display: flex;
            height: 100vh; /* Use viewport height to make sure the layout covers the entire screen */
        }

        #sidebar {
            width: 150px;
            background-color: #333;
            color: #fff;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
            position: fixed; /* Fix the sidebar position */
            height: 100%;
        }

        #content {
            flex: 1;
            padding: 20px;
            margin-left: 200px; /* Adjust content area margin to accommodate the fixed sidebar */
        }
        #sidebar a {
            color: #fff;
            text-decoration: none;
            margin-bottom: 15px;
            width: 100%;
            display: flex;
            align-items: center;
        }
        #sidebar i {
            margin-right: 10px;
        }

        #sidebar a:not(:last-child) {
            margin-bottom: 60px;
        }                                                                                                                                                                                                                   #user-info {
            display: none;
            color: #fff;
            margin-top: 5px;
            padding: 10px;
            background-color: #555;
            border-radius: 15px;
        }

        #content {
            flex: 1;
            padding: 20px;
        }

        #main-entry-table-container {
            margin-top: 20px;
        }

        #main-entry-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        #main-entry-table th,
        #main-entry-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        #main-entry-table th {
            background-color: #3498db;
            color: #fff;
        }

        #main-entry-table tbody tr:hover {
            background-color: #f5f5f5;
        }                                                                                                                                                                                                               .more-info-button {
            background-color: #4caf50;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            margin: 2px 2px;
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0, 0, 0);
            background-color: rgba(0, 0, 0, 0.4);
            padding-top: 60px;
        }

        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
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
        }                                                                                                                                                                                                                  .modal-header {
            padding: 2px 16px;
            background-color: #5cb85c;
            color: white;
        }

        .modal-body {
            padding: 2px 16px;
        }

        #search-bar {
            margin-top: 20px;
        }
        #search-bar label {
            margin-right: 10px;
        }
        #search-bar input {
            margin-right: 10px;
        }
        #search-bar button {
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div id="dashboard">
        <div id="sidebar">
            <div class="welcome-message" id="welcome-message"></div>
            <a href="#" id="user-icon" onclick="toggleUserInfo();"><i class="fas fa-user"></i> User</a>
            <div id="user-info">
                <!-- User info will be displayed here using JavaScript -->
            </div>
            <a href="fetch_notifications.php"><i class="fas fa-bell"></i> Notifications</a>
            <a href="mpesa-c2b.html"><i class="fas fa-coins"></i> Mpesa C2B</a>
            <a href="mpesa-b2b.html"><i class="fas fa-exchange-alt"></i> Mpesa B2B</a>
            <a href="home.html" onclick="redirectToPage('home.html');"><i class="fas fa-home"></i> Dashboard</a>
             <a href="#" id="logoutLink"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    <div id="content">
        <div class="alert" id="alert-message"></div>

        <!-- Add the search bar -->
        <div id="search-bar">
            <label for="product-search">Search Product:</label>
            <input type="text" id="product-search" placeholder="Enter product name">
            <button onclick="searchProduct()">Search</button>
        </div>

        <div id="main-entry-table-container">
            <h3>Store Inventory Information</h3>
            <table id="main-entry-table">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>Total Quantity</th>
                        <th>Quantity Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="main-entry-table-body">
                    <!-- Main entry data will be dynamically inserted here -->
                </tbody>
            </table>
        </div>

        <div id="detailed-entries-modal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <h2>Detailed info on Entries</h2>
                </div>
                <div class="modal-body" id="modal-body-content">
                    <!-- Detailed entry data will be dynamically inserted here -->
                </div>
            </div>
        </div>

        <script>
            let mainEntryData;

            function viewInventory() {
                // Fetch main entry data from session
                const mainEntryDataSession = <?php echo json_encode($_SESSION['main_entry_data'] ?? null); ?>;

                if (mainEntryDataSession) {
                    // Update the content of the current page with the main entry data
                    mainEntryData = mainEntryDataSession;
                    displayMainEntryData(mainEntryData);
                    // Display more info button for each main entry
                    addMoreInfoButtons(mainEntryData);
                } else {
                    showAlert('No main entry data available.');
                }

                // Detailed entry data is already stored in the window object during PHP processing
            }

            function displayMainEntryData(mainEntryData, searchTerm) {
                // Display main entry data in a table
                var tableBody = document.querySelector('#main-entry-table-body');
                tableBody.innerHTML = ''; // Clear existing table content

                mainEntryData.forEach(function (entry) {
                    // Check if the product name contains the search term (case-insensitive)
                    if (!searchTerm || entry.product_name.toLowerCase().includes(searchTerm.toLowerCase())) {
                        var row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${entry.product_name}</td>
                            <td>${entry.category}</td>
                            <td>${entry.total_quantity}</td>
                            <td>${entry.quantity_description}</td>
                            <td><button class="more-info-button" onclick="showDetailedEntries(${entry.main_entry_id})">More Info</button></td>
                        `;
                        tableBody.appendChild(row);
                    }
                });
            }

            function addMoreInfoButtons(mainEntryData) {
                // Add more info buttons for each main entry
                // (Removed as per your request since they are unnecessary)
            }

            function showDetailedEntries(mainEntryId) {
                // Retrieve detailed entries for the selected main entry ID
                const detailedEntries = <?php echo json_encode($_SESSION['detailed_entry_data'] ?? null); ?>;
                const filteredEntries = detailedEntries.filter(function (entry) {
                    return entry.main_entry_id === mainEntryId;
                });

                // Display detailed entries in a modal
                var modalBody = document.querySelector('#modal-body-content');
                modalBody.innerHTML = ''; // Clear existing modal content

                filteredEntries.forEach(function (entry) {
                    var row = document.createElement('div');
                    row.innerHTML = `
                        <p>Quantity: ${entry.quantity}</p>
                        <p>Quantity Description: ${entry.quantity_description}</p>
                        <p>Price: ${entry.price !== null ? entry.price : 'N/A'}</p>
                        <p>Date: ${entry.record_date}</p>
                    `;
                    modalBody.appendChild(row);
                });

                // Show the modal
                document.getElementById('detailed-entries-modal').style.display = 'block';
            }

            function closeModal() {
                // Close the modal
                document.getElementById('detailed-entries-modal').style.display = 'none';
            }

            function showAlert(message) {
                // Show an alert message
                var alertMessage = document.getElementById('alert-message');
                alertMessage.innerHTML = message;
                alertMessage.style.display = 'block';

                // Hide the alert after 3 seconds
                setTimeout(function () {
                    alertMessage.style.display = 'none';
                }, 3000);
            }

            function searchProduct() {
                // Get the search input value
                var searchInput = document.getElementById('product-search').value;

                // Display main entry data based on the search input
                displayMainEntryData(mainEntryData, searchInput);
            }

            // Call viewInventory when the page is loaded
            document.addEventListener('DOMContentLoaded', function () {
                viewInventory();
            });
               document.getElementById('logoutLink').addEventListener('click', function(event) {
    // Prevent the default behavior of the link
    event.preventDefault();

    // Redirect the user to the logout.php file for logout
    window.location.href = 'logout.php';
  });
        </script>
    </div>
</body>

</html>

