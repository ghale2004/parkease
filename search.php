<?php include 'includes/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - ParkEase Nepal</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Reset default margin and padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-color: #f5f5f5;
        }

        /* Main content wrapper */
        .main-content {
            flex: 1;
            padding: 80px 0 60px; /* Adjust based on your header/footer height */
            width: 100%;
            position: relative;
        }

        .search-results {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .search-header {
            margin-bottom: 2rem;
            text-align: center;
        }

        .search-header h1 {
            color: #333;
            margin-bottom: 1.5rem;
        }

        .search-form {
            max-width: 600px;
            margin: 0 auto 2rem;
            display: flex;
            gap: 0.5rem;
        }

        .search-form input[type="text"] {
            flex: 1;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }

        .search-form button {
            padding: 0.8rem 1.5rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            white-space: nowrap;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }

        .parking-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
            transition: transform 0.2s;
        }

        .parking-card:hover {
            transform: translateY(-5px);
        }

        .parking-info {
            padding: 1.5rem;
        }

        .parking-info h3 {
            margin: 0 0 1rem;
            color: #333;
            font-size: 1.25rem;
        }

        .parking-info p {
            margin: 0.5rem 0;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .parking-info .price {
            font-size: 1.2rem;
            color: #007bff;
            font-weight: bold;
            margin: 1rem 0;
        }

        .book-btn {
            width: 100%;
            padding: 0.8rem;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
            font-size: 1rem;
        }

        .book-btn:hover {
            background-color: #0056b3;
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: #666;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 60px 0 40px;
            }

            .search-form {
                flex-direction: column;
            }

            .results-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="main-content">
        <div class="search-results">
            <div class="search-header">
                <h1>Search Results</h1>
                <form class="search-form" action="search.php" method="GET">
                    <input type="text" name="location" placeholder="Enter location" value="<?php echo htmlspecialchars($_GET['location'] ?? ''); ?>">
                    <button type="submit">Search</button>
                </form>
            </div>

            <div class="results-grid">
                <?php
                // Database connection
                $conn = new mysqli("localhost", "root", "", "parkease");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                if (isset($_GET['location']) && !empty($_GET['location'])) {
                    $location = '%' . $conn->real_escape_string($_GET['location']) . '%';
                    
                    $sql = "SELECT * FROM parking_spots WHERE location LIKE ? ORDER BY rating DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $location);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            ?>
                            <div class="parking-card">
                                <div class="parking-info">
                                    <h3><?php echo htmlspecialchars($row['name']); ?></h3>
                                    <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></p>
                                    <p><i class="fas fa-car"></i> <?php echo htmlspecialchars($row['available_spots']); ?> spots available</p>
                                    <p><i class="fas fa-star"></i> <?php echo number_format($row['rating'], 1); ?>/5 (<?php echo $row['reviews_count']; ?> reviews)</p>
                                    <p class="price">Rs. <?php echo number_format($row['price_per_hour'], 2); ?>/hour</p>
                                    <button onclick="bookParking(<?php echo $row['id']; ?>)" class="book-btn">Book Now</button>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<div class="no-results">No parking spots found in this location. Please try a different search.</div>';
                    }

                    $stmt->close();
                } else {
                    echo '<div class="no-results">Please enter a location to search for parking spots.</div>';
                }

                $conn->close();
                ?>
            </div>
        </div>
    </div>

    <script>
    function bookParking(parkingId) {
        window.location.href = `booking.php?parking_id=${parkingId}`;
    }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>
</html>