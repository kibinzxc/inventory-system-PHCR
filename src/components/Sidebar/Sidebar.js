let isCollapsed = false;

// Load the sidebar state from localStorage on page load
document.addEventListener('DOMContentLoaded', function () {
    const sidebarState = JSON.parse(localStorage.getItem('sidebarState'));

    if (sidebarState && sidebarState.collapsed) {
        isCollapsed = true; // Set the state based on saved preference
        const sidebar = document.getElementById("mySidebar");
        const mainContent = document.getElementById("main-content");
        const logo = document.getElementById("sidebarLogo");
        
        // Apply collapsed styles
        sidebar.classList.add("collapsed");
        mainContent.style.marginLeft = "60px";
        logo.classList.add("hidden");
    }
});

function toggleSidebar() {
    const sidebar = document.getElementById("mySidebar");
    const mainContent = document.getElementById("main-content");
    const logo = document.getElementById("sidebarLogo");
    const toggleButton = document.getElementById("toggleSidebar");

    // Check if the screen is mobile
    const isMobile = window.matchMedia("(max-width: 768px)").matches; // Adjust based on your design

    if (isCollapsed) {
        // Expand sidebar
        sidebar.classList.remove("collapsed");
        mainContent.style.marginLeft = isMobile ? "60px" : "250px"; // Set margin according to view
        logo.classList.remove("hidden");
        isCollapsed = false;
    } else {
        // Collapse sidebar
        sidebar.classList.add("collapsed");
        mainContent.style.marginLeft = isMobile ? "60px" : "60px"; // Set to 60px for mobile, 0px for desktop
        logo.classList.add("hidden");
        toggleButton.setAttribute("data-tooltip", "Expand"); // Change tooltip text to "Expand"
        isCollapsed = true;
    }

    // Save the current state to localStorage
    const settings = { collapsed: isCollapsed };
    localStorage.setItem('sidebarState', JSON.stringify(settings));
}
// Function to update the title with the notification count or app name
function updateTitle(count) {
    if (showNotification) {
        document.title = `(${count}) Notifications - Pizza Hut`;
    } else {
        document.title = 'Pizza Hut';
    }
    showNotification = !showNotification;
}

// Simulated notification count
let notificationCount = 5; // Set this to your current notification count
let showNotification = true; // Flag to track which title to show

// Update the title every 2 seconds
setInterval(() => {
    updateTitle(notificationCount);
}, 2000);

// Initialize title with the app name
updateTitle(notificationCount);

// Tooltip functionality for collapsed sidebar and toggle button
const sidebarLinks = document.querySelectorAll('.bglinks a, .bglink_footer a'); // Include Logout link
const tooltip = document.getElementById('tooltip');
const toggleButton = document.getElementById("toggleSidebar");

// Function to check if the screen is mobile
function isMobile() {
    return window.matchMedia("(max-width: 768px)").matches; // Adjust based on your design
}

// Show tooltip on hover over sidebar links and toggle button
sidebarLinks.forEach(link => {
    link.addEventListener('mouseenter', function () {
        if (isCollapsed && !isMobile()) { // Check if the sidebar is collapsed and not mobile
            tooltip.textContent = this.getAttribute('data-tooltip'); // Set tooltip text
            tooltip.style.display = 'block'; // Show tooltip
            const rect = this.getBoundingClientRect(); // Get link's position
            tooltip.style.left = `${rect.right + 13}px`; // Position tooltip to the right of the link
            tooltip.style.top = `${rect.top + 8}px`; // Align vertically
        }
    });

    link.addEventListener('mouseleave', function () {
        tooltip.style.display = 'none'; // Hide tooltip
    });
});

// Show tooltip for the toggle button
toggleButton.addEventListener('mouseenter', function () {
    if (!isMobile()) { // Only show tooltip if not on mobile
        if (isCollapsed) {
            tooltip.textContent = "Expand Sidebar"; // Show "Expand" when collapsed
        } else {
            tooltip.textContent = "Shrink Sidebar"; // Show "Shrink" when expanded
        }
        tooltip.style.display = 'block'; // Show tooltip
        const rect = toggleButton.getBoundingClientRect(); // Get toggle button's position
        tooltip.style.left = `${rect.right + 15}px`; // Position tooltip to the right of the button
        tooltip.style.top = `${rect.top + 1}px`; // Align vertically
    }
});

toggleButton.addEventListener('mouseleave', function () {
    tooltip.style.display = 'none'; // Hide tooltip
});

// Hide tooltip when mouse leaves sidebar
const sidebar = document.querySelector('.sidebar');
sidebar.addEventListener('mouseleave', () => {
    tooltip.style.display = 'none'; // Hide tooltip
});