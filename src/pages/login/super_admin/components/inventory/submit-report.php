<link rel="stylesheet" href="submit-report.css">
<div id="reportModal" class="modal" style="display: <?php echo $showModal ? 'block' : 'none'; ?>;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Submit a Report</h2>
            <span class="close2">&times;</span>
        </div>

        <div class="modal-body report">
            <div class="btn-wrapper4">
                <a href="#" class="btn3" onclick="openAddModal()">Transfers in / Transfers out</a>
                <a href="#" class="btn3" onclick="openAddReport()">Deliveries / Purchases</a>
                <a href="#" class="btn3" onclick="openAddReport()">Waste / Spoilage</a>
            </div>
        </div>

    </div>
</div>

<script src="submit-report.js"></script>