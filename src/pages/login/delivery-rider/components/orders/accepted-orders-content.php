<?php error_reporting(1) ?>
<link rel="stylesheet" href="archive.css">
<link rel="stylesheet" href="online-orders.css">

<div id="main-content">
    <div class="container">
        <div class="header">
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
        <?php include 'accepted-orders-table.php'; ?>
    </div>

    <!-- Mobile View Access Note -->

</div>

</div>


<script>
    function handleFileChange(event, orderID) {
        const fileInput = event.target;
        const file = fileInput.files[0];
        const preview = document.getElementById(`image-preview-${orderID}`);
        const notDeliveredBtn = document.getElementById(`not-delivered-btn-${orderID}`);
        const deliveredBtn = document.getElementById(`delivered-btn-${orderID}`);

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = "block"; // Show image preview

                // Enable modal buttons when image is uploaded
                notDeliveredBtn.style.display = "inline-block";
                deliveredBtn.style.display = "inline-block";
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";

            // Disable modal buttons when no image is uploaded
            notDeliveredBtn.style.display = "none";
            deliveredBtn.style.display = "none";
        }
    }

    function previewImage(event) {
        const preview = document.getElementById("image-preview");
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = "block";
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = "none";
        }
    }

    function openModal(modalId) {
        document.getElementById(modalId).style.display = 'block';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).style.display = 'none';
    }

    window.onclick = function(event) {
        const modals = document.getElementsByClassName('modal');
        for (let i = 0; i < modals.length; i++) {
            if (event.target == modals[i]) {
                modals[i].style.display = 'none';
            }
        }
    };
</script>