<link rel="stylesheet" href="archive.css">

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Online Orders</h1>
            <div class="btn-wrapper">
                <div class="btn-wrapper">
                    <a href="orders.php" class="btn"><img src="../../assets/external-link.svg" alt=""> Point-of-Sale</a>
                    <a href="order-logs.php" class="btn"><img src="../../assets/file-text.svg" alt=""> Logs</a>
                </div>
            </div>
        </div>

        <!-- Table Container Section -->
        <!-- Utility Buttons and Sorting Options -->
        <div class="btncontents">
            <!-- <a href="https://www.flaticon.com/free-icons/inventory" title="inventory icons">Inventory icons created by Nhor Phai - Flaticon</a> -->
            <a href="items.php" class="active"><img src="../../assets/inventory.png" class="img-btn-link">Preparing</a>
            <!-- <a href="https://www.flaticon.com/free-icons/summary" title="summary icons">Summary icons created by Flat Icons - Flaticon</a> -->
            <!-- <a href="https://www.flaticon.com/free-icons/restaurant" title="restaurant icons">Restaurant icons created by Freepik - Flaticon</a> -->
        </div>
    </div>

    <div class="loader" id="loader" style="display:none;"></div>
    <div class="table" id="online-orders-table">
        <?php include 'online-orders-table.php'; ?>
    </div>

    <!-- Mobile View Access Note -->
    <blockquote class="mobile-note">
        <strong>Note:</strong> On mobile devices, access is limited to viewing only. You cannot edit, add, or remove content.
    </blockquote>
</div>

</div>
<script src="archive.js"></script>