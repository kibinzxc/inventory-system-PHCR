<link rel="stylesheet" href="transfers-report.css">

<?php
include '../../connection/database.php';

// Query to fetch items from the 'daily_inventory' table
$sql = "SELECT inventoryID, name, uom FROM daily_inventory ORDER BY name ASC";
$result = $conn->query($sql);
$inventoryItems = [];

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

// Fetch inventory items
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $inventoryItems[] = $row;
    }
}

// Fetch transfer data (for Transfers In and Transfers Out)
$sql_transfers = "SELECT inventoryID, transfers_in, transfers_out FROM daily_inventory WHERE inventoryID IN (" . implode(',', array_column($inventoryItems, 'inventoryID')) . ")";
$transferResult = $conn->query($sql_transfers);
$transfers = [];

if ($transferResult->num_rows > 0) {
    while ($row = $transferResult->fetch_assoc()) {
        $transfers[$row['inventoryID']] = $row;
    }
}

$conn->close();

// Get search query if it exists
$searchQuery = isset($_GET['search']) ? strtolower($_GET['search']) : '';
?>

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Transfers In & Out</h1>
            <div class="btn-wrapper">
                <a href="items.php" class="btn"><img src="../../assets/arrow-left.svg" alt=""> Back</a>
            </div>
        </div>
        <div class="table_container">
            <div class="search-container">
                <input type="text" name="search" id="search" placeholder="Search" class="search_btn" value="<?= htmlspecialchars($searchQuery) ?>">
                <div id="dropdown" class="dropdown"></div>
                <button type="button" id="searchBtn" class="btn3">Search</button>
                <button type="button" id="show-all-btn" class="btn3" style="display: none;">Show All</button>
            </div>

            <form class="transfer-form" method="POST" action="submit_transfers.php">
                <div class="form-row header-row">
                    <span>Item Name</span>
                    <span>Transfers In</span>
                    <span>Transfers Out</span>
                    <span>Unit of Measurement</span>
                </div>
                <div class="scroll">
                    <?php
                    // Filter items based on search query
                    $filteredItems = array_filter($inventoryItems, function ($item) use ($searchQuery) {
                        return !$searchQuery || strpos(strtolower($item['name']), $searchQuery) !== false;
                    });

                    if (count($filteredItems) === 0 && $searchQuery) {
                        echo "<center><p>No results found for '$searchQuery'</p><center>";
                    } else {
                        foreach ($filteredItems as $item):
                            $fullUOM = isset($uom_map[$item['uom']]) ? $uom_map[$item['uom']] : $item['uom'];
                    ?>
                            <div class="form-row" id="item-<?= $item['inventoryID'] ?>">
                                <label><?= htmlspecialchars($item['name']) ?></label>

                                <!-- Transfer In Input Field, prefilled with existing value if available -->
                                <input type="number" name="transfers_in[<?= $item['inventoryID'] ?>]"
                                    value="<?= isset($transfers[$item['inventoryID']]) ? $transfers[$item['inventoryID']]['transfers_in'] : 0 ?>"
                                    placeholder="0">

                                <!-- Transfer Out Input Field, prefilled with existing value if available -->
                                <input type="number" name="transfers_out[<?= $item['inventoryID'] ?>]"
                                    value="<?= isset($transfers[$item['inventoryID']]) ? $transfers[$item['inventoryID']]['transfers_out'] : 0 ?>"
                                    placeholder="0">

                                <p class="label-uom"><?= htmlspecialchars($fullUOM) ?></p>
                            </div>
                    <?php endforeach;
                    } ?>
                </div>
                <hr class="horizontal-border">
                <div class="submit-btn-container">
                    <a href="items.php" class="cancelBtn">Cancel</a>
                    <button type="submit">Submit Transfers In & Out</button>
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