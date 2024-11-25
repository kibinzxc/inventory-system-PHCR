<!-- Modal (Success/Error) -->
<div id="successModal" class="response-modal">

    <div class="response-modal-content">
        <div class="response-modal-header">
            <h2 id="modalTitle">Success!</h2>
            <span class="response-close-btn" onclick="closeModal2('successModal')">&times;</span>
        </div>
        <div class="response-modal-body">
            <p id="modalMessage">Order has been saved successfully.</p>
        </div>
    </div>
</div>


<!-- Error Modal -->
<div id="errorModal" class="response-modal">

    <div class="response-modal-content">
        <div class="response-modal-header">
            <h2>Error!</h2>
            <span class="response-close-btn" onclick="closeModal2('errorModal')">&times;</span>
        </div>
        <div class="response-modal-body">
            <p id="modalMessage">There was a problem saving your data. Please try again.</p>
        </div>
    </div>
</div>
<!-- Styles for the modal -->
<style>
    .response-modal {
        display: none;
        position: fixed;
        z-index: 5;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .response-modal-header {
        background-color: #343434;
        padding: 15px;
        color: #fff;
        text-align: left;
        font-size: 15px;
        position: relative;
        padding: 5px 25px;
    }

    .response-modal-content {
        background-color: #f4f4f4;
        margin: 15% auto;
        padding: 0;
        border-radius: 8px;
        width: 40%;
        max-width: 500px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    .response-modal-body {
        background-color: #fff;
        /* White */
        padding: 0 10px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        text-align: center;
        font-size: 1.2rem;
        /* Adds space between form elements */
    }


    .response-close-btn {
        position: absolute;
        right: 15px;
        top: 15px;
        font-size: 24px;
        font-weight: bold;
        /* Make the 'X' bold */
        cursor: pointer;
        color: #fff;
        /* Keep the color white */
        padding: 5px 10px;
        /* Add some padding for a clickable area */
        line-height: 1;
        /* Ensure the text aligns nicely */
        border-radius: 50%;
    }

    .response-close-btn:hover,
    .response-close-btn:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    /* Success/Error Styles */
    .response-modal.success {
        background-color: #4CAF50;
        /* Green */
    }

    .response-modal.success h2 {
        color: white;
    }

    .response-modal.error {
        background-color: #f44336;
        /* Red */
    }

    .response-modal.error h2 {
        color: white;
    }
</style>