<link rel="stylesheet" href="spoilage-report.css">

<?php
include '../../connection/database.php';

// Query to fetch items from the 'daily_inventory' table along with spoilage and remarks
$sql = "SELECT inventoryID, name, uom, spoilage, remarks FROM daily_inventory ORDER BY name ASC";
$result = $conn->query($sql);
$inventoryItems = [];

// Define UOM abbreviation to full name mapping
$uom_map = [
    'bag' => 'Bag',
    'bt' => 'Bottle',
    'box' => 'Box',
    'Gal' => 'Gallon',
    'grams' => 'Grams',
    'kg' => 'Kilograms',
    'L' => 'Liter',
    'pac' => 'Pack',
    'pc' => 'Piece',
    'rl' => 'Roll',
    'set' => 'Set',
    'tnk' => 'Tank'
];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inventoryItems[] = $row;
    }
}
$conn->close();

// Get search query if it exists
$searchQuery = isset($_GET['search']) ? strtolower($_GET['search']) : '';
?>

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Report Spoilage</h1>
            <div class="btn-wrapper">
                <a href="javascript:history.back()" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
            </div>
        </div>
        <div class="table_container">
            <div class="search-container">
                <input type="text" name="search" id="search" placeholder="Search" class="search_btn" value="<?= htmlspecialchars($searchQuery) ?>">
                <div id="dropdown" class="dropdown"></div>
                <button type="button" id="searchBtn" class="btn3">Search</button>
                <button type="button" id="show-all-btn" class="btn3" style="display: none;">Show All</button>
            </div>

            <form class="transfer-form" method="POST" action="submit_spoilage.php">
                <div class="form-row header-row">
                    <span>Item Name</span>
                    <span>Qty</span>
                    <span>UoM</span>
                    <span>Remarks</span>
                </div>
                <div class="scroll">
                    <?php
                    $filteredItems = array_filter($inventoryItems, function ($item) use ($searchQuery) {
                        return !$searchQuery || strpos(strtolower($item['name']), $searchQuery) !== false;
                    });

                    if (count($filteredItems) === 0 && $searchQuery) {
                        echo "<center><p>No results found for '$searchQuery'</p><center>";
                    } else {
                        foreach ($filteredItems as $item):
                            // Get the full UOM name using the mapping
                            $fullUOM = isset($uom_map[$item['uom']]) ? $uom_map[$item['uom']] : $item['uom']; // Default to abbreviation if no mapping found
                    ?>
                            <div class="form-row" id="item-<?= $item['inventoryID'] ?>">
                                <label><?= htmlspecialchars($item['name']) ?></label>
                                <input type="number" name="spoilage[<?= $item['inventoryID'] ?>]" placeholder="0" min="0" value="0">
                                <p class="label-uom"><?= htmlspecialchars($fullUOM) ?></p> <!-- Display full UOM name -->
                                <input type="text" name="remarks[<?= $item['inventoryID'] ?>]" placeholder="Add remarks" class="remarks-input" value="<?= htmlspecialchars($item['remarks']) ?>">
                            </div>
                        <?php endforeach; ?>
                    <?php } ?>
                </div>

                <hr class="horizontal-border">
                <div class="submit-btn-container">
                    <a href="items.php" class="cancelBtn">Cancel</a>
                    <button type="submit">Submit Spoilage Report</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const searchInput = document.getElementById("search");
        const dropdown = document.getElementById("dropdown");
        const searchBtn = document.getElementById("searchBtn");
        const showAllBtn = document.getElementById("show-all-btn");

        // Check if the URL contains a search query and show the "Show All" button if true
        const urlParams = new URLSearchParams(window.location.search);
        const searchQuery = urlParams.get('search');
        if (searchQuery) {
            showAllBtn.style.display = "block"; // Show "Show All" if search query is in the URL
        }

        // Function to trigger the search
        function triggerSearch(query) {
            // This will set the value of the search query and submit the form or initiate any action you need
            const url = new URL(window.location.href);
            url.searchParams.set('search', query);
            window.location.href = url.toString(); // Update the URL with the search query
        }

        // Search input handler
        searchInput.addEventListener("input", function() {
            const query = searchInput.value.toLowerCase();

            // If query is empty, hide the dropdown
            if (query === "") {
                dropdown.style.display = "none";
                return;
            }

            // Clear previous suggestions
            dropdown.innerHTML = "";

            // Filter inventory items based on the search query
            const filteredItems = <?= json_encode($inventoryItems) ?>.filter(item => item.name.toLowerCase().includes(query));

            // If no items match the query, hide the dropdown
            if (filteredItems.length === 0) {
                dropdown.style.display = "none";
                return;
            }

            // Display the dropdown
            dropdown.style.display = "block";

            // Populate the dropdown with matching items
            filteredItems.forEach(item => {
                const div = document.createElement("div");
                div.classList.add("dropdown-item");
                div.textContent = item.name;

                // When an item is clicked, set the search input to that item and trigger the search
                div.addEventListener("click", function() {
                    searchInput.value = item.name;
                    dropdown.style.display = "none"; // Hide the dropdown
                    triggerSearch(item.name); // Trigger the search with the selected item
                });

                dropdown.appendChild(div);
            });
        });

        // Trigger search when the search button is clicked
        searchBtn.addEventListener("click", function() {
            const query = searchInput.value.toLowerCase();
            if (query) {
                triggerSearch(query);
            }
        });

        // Handle the "Show All" button click
        showAllBtn.addEventListener("click", function() {
            searchInput.value = ""; // Clear the search input
            window.location.href = window.location.pathname; // Refresh the page to show all items (clear search query)
        });

        // Hide the dropdown if clicked outside the input
        document.addEventListener("click", function(event) {
            if (!searchInput.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.style.display = "none";
            }
        });
    });
</script>