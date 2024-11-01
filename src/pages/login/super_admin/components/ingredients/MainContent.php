<link rel="stylesheet" href="MainContent.css">

<div id="main-content">
    <div class="tooltip" id="tooltip" style="display: none;">Tooltip Text</div>
    <div class="container">
        <div class="header">
            <h2>Ingredients Data</h2>
            <div class="btn-wrapper">
                <a href="#" class="btn" onclick="openAddModal()"><img src="../../assets/plus-circle.svg" alt=""> Add New Ingredient</a>
            </div>
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
                        <option value="ingredientsID" selected>Code</option>
                        <option value="name">NAME</option>
                        <option value="addedBy">Added By</option>
                        <option value="date">Date</option>

                    </select>

                    <span class="sort-label">ORDER:</span>
                    <select class="select2" id="sortOrder">
                        <option value="asc" selected>Ascending</option>
                        <option value="desc">Descending</option>
                    </select>
                </div>

                <a href="#" class="icon_btn" id="refresh-btn"><img src="../../assets/refresh-ccw.svg" alt=""></a>
            </div>

            <div class="loader" id="loader" style="display:none;"></div>
            <div class="table" id="account-table"> <?php include 'IngredientsTable.php'; ?></div>

        </div>


        <blockquote class="mobile-note">
            <strong>Note:</strong> On mobile devices, access is limited to viewing only. You cannot edit, add, or remove content.
        </blockquote>

    </div>
    <?php include 'add-ingredient.php' ?>
    <?php include 'SuccessErrorModal.php'; ?>
    <script src="SuccessErrorModal.js"></script>
</div>
<script src="ingredients.js"></script>