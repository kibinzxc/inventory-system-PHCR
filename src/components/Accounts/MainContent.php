<link rel="stylesheet" href="MainContent.css">

<div id="main-content">
    <div class="tooltip" id="tooltip" style="display: none;">Tooltip Text</div>
    <div class="container">
        <div class="header">
            <h2>Account Management</h2>
            <a href="addAccount.php" class="btn"><img src="../../assets/plus-circle.svg" alt=""> Add Account</a>
        </div>

        <div class="table_container">
            <div class="btns_container">
                <a href="#" class="icon_btn"><img src="../../assets/printer.svg" alt=""></a>
                <a href="#" class="icon_btn"><img src="../../assets/save.svg" alt=""></a>
                <input type="text" name="search" id="search" placeholder="Search" class="search_btn">

                <div class="sort-container">
                    <img src="../../assets/filter.svg" alt="" class="filter_icon">
                    <span class="sort-label">SORT BY:</span>
                    <select class="select" id="sort">
                        <option value="uid" selected>EMPLOYEE ID</option>
                        <option value="name">NAME</option>
                        <option value="position">POSITION</option>
                        <option value="userType">USER TYPE</option>
                    </select>
                </div>
                <a href="#" class="icon_btn" id="refresh-btn"><img src="../../assets/refresh-ccw.svg" alt=""></a>
            </div>


            <div class="loader" id="loader" style="display:none;"></div>
            <div class="table" id="account-table"> <?php include 'AccountTable.php'; ?></div>
        </div>
    </div>
</div>

<!-- Include the external JS file -->
<script src="Accounts.js"></script>