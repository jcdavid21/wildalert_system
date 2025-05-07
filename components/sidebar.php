<?php include '../backend/config/config.php'; ?>

<?php
// Get topics (Fauna/Flora)
$topicQuery = "SELECT * FROM tbl_topic";
$topicResult = $conn->query($topicQuery);

// Prepare an array to store the categories by topic
$categoryByTopic = [];

// If we have topics, fetch categories for each
if ($topicResult->num_rows > 0) {
    while ($topic = $topicResult->fetch_assoc()) {
        // Get categories for this topic
        $categoryQuery = "SELECT * FROM tbl_category WHERE topic_id = " . $topic['topic_id'];
        $categoryResult = $conn->query($categoryQuery);
        
        $categories = [];
        if ($categoryResult->num_rows > 0) {
            while ($category = $categoryResult->fetch_assoc()) {
                $categories[] = $category;
            }
        }
        
        $categoryByTopic[$topic['topic_id']] = [
            'topic_name' => $topic['topic_name'],
            'categories' => $categories
        ];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            height: max-content;
            background-color: #f7f7f7;
            color: #333;
            position: absolute;
            left: 0;
            top: 78px;
            overflow-y: auto;
            transition: transform 0.3s ease;
            border-right: 1px solid #e0e0e0;
            z-index: 100;
        }

        .sidebar-header {
            background-color: #014421; /* Dark green */
            color: white;
            padding: 15px;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }

        .topic-header {
            background-color: #2a6041; /* Slightly lighter green */
            color: white;
            padding: 10px 15px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #014421;
        }

        .category-list {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .category-list.active {
            max-height: 1000px; /* Arbitrary large value */
        }

        .category-item {
            padding: 10px 15px 10px 30px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .category-item:hover {
            background-color: #e0e0e0;
        }

        .topic-header .icon {
            transition: transform 0.3s ease;
        }

        .topic-header.active .icon {
            transform: rotate(180deg);
        }

        /* Mobile sidebar toggle */
        #sidebarToggle {
            position: fixed;
            bottom: 20px;
            left: 20px;
            background-color: #014421;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            display: none;
        }

        @media screen and (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: 80%;
            }

            .sidebar.active {
                transform: translateX(0);
            }

            #sidebarToggle {
                display: flex;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            Species Categories
        </div>

        <?php foreach ($categoryByTopic as $topicId => $topic): ?>
            <div class="topic-section">
                <div class="topic-header" onclick="toggleTopic(this)">
                    <?php echo $topic['topic_name']; ?>
                    <span class="icon">▼</span>
                </div>
                <div class="category-list">
                    <?php foreach ($topic['categories'] as $category): ?>
                        <div class="category-item" onclick="location.href='library.php?category=<?php echo $category['category_id']; ?>'">
                            <?php echo $category['category_name']; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="sidebarToggle">
        <i class="fa fa-bars"></i>
    </div>

    <script>
        // Toggle topic dropdown
        function toggleTopic(element) {
            element.classList.toggle('active');
            const categoryList = element.nextElementSibling;
            categoryList.classList.toggle('active');
        }

        // Open the first topic by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstTopic = document.querySelector('.topic-header');
            if (firstTopic) {
                firstTopic.classList.add('active');
                firstTopic.nextElementSibling.classList.add('active');
            }
            
            // We'll let library.php handle the toggle functionality
        });
    </script>
</body>
</html>