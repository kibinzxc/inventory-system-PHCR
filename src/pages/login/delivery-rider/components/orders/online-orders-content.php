<link rel="stylesheet" href="archive.css">

<div id="main-content">
    <div class="container">
        <div class="header">
            <h1>Online Orders</h1>
            <div class="btn-wrapper">
                <div class="btn-wrapper">

                </div>
            </div>
        </div>

        <!-- Table Container Section -->
        <!-- Utility Buttons and Sorting Options -->
        <div class="btncontents">
            <!-- <a href="https://www.flaticon.com/free-icons/inventory" title="inventory icons">Inventory icons created by Nhor Phai - Flaticon</a> -->
            <a href="#" class=" active" style="font-size:2rem;">Delivery</a>
            <!-- <a href=" https://www.flaticon.com/free-icons/summary" title="summary icons">Summary icons created by Flat Icons - Flaticon</a> -->
            <!-- <a href="https://www.flaticon.com/free-icons/restaurant" title="restaurant icons">Restaurant icons created by Freepik - Flaticon</a> -->
        </div>
    </div>

    <div class="loader" id="loader" style="display:none;"></div>
    <div class="table" id="online-orders-table" style="justify-content:center;">
        <?php include 'online-orders-table.php'; ?>
    </div>

    <!-- Mobile View Access Note -->

</div>

</div>
<script src="archive.js"></script>