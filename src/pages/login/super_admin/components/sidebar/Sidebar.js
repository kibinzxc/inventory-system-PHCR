let isCollapsed = false; // Set to false by default

// Load the sidebar state from localStorage on page load
document.addEventListener('DOMContentLoaded', function () {
    const sidebarState = JSON.parse(localStorage.getItem('sidebarState'));

    // Check if sidebarState exists and set isCollapsed accordingly
    if (sidebarState && sidebarState.collapsed) {
        isCollapsed = true; // Set the state based on saved preference
    }

    // Immediately update the sidebar and toggle button state without animation
    updateSidebarState(true); // Pass true to avoid animation
});


function updateSidebarState(avoidAnimation = false) {
    const sidebar = document.getElementById("mySidebar");
    const mainContent = document.getElementById("main-content");
    const logo = document.getElementById("sidebarLogo");
    const toggleArrow = document.querySelector(".toggle-btn"); // Get the toggle button

    // If avoiding animation, set transition to none
    if (avoidAnimation) {
        sidebar.style.transition = 'none';
        toggleArrow.style.transition = 'none'; // Prevent animation on toggle arrow
    } else {
        sidebar.style.transition = 'width 0.5s'; // Allow transition for toggles
        toggleArrow.style.transition = 'transform 0.5s'; // Allow transition for toggle arrow
    }

    if (isCollapsed) {
        sidebar.classList.add("collapsed");
        mainContent.style.marginLeft = window.matchMedia("(max-width: 768px)").matches ? "20px" : "60px"; // Adjust for mobile
        mainContent.style.overflow = window.matchMedia("(max-width: 768px)").matches ? "hidden" : "auto"; // Hide overflow on mobile
        logo.classList.add("hidden");
        toggleArrow.style.transform = 'rotate(180deg)'; // Rotate arrow when collapsed
    } else {
        sidebar.classList.remove("collapsed");
        mainContent.style.marginLeft = window.matchMedia("(max-width: 768px)").matches ? "60px" : "250px"; // Adjust for mobile
        mainContent.style.overflow = "auto"; // Show overflow when sidebar is expanded
        logo.classList.remove("hidden");
        toggleArrow.style.transform = 'rotate(0deg)'; // Reset arrow rotation when expanded
    }

    // Reset transition style after initial update
    if (avoidAnimation) {
        setTimeout(() => {
            sidebar.style.transition = 'width 0.5s'; // Enable transition for future toggles
            toggleArrow.style.transition = 'transform 0.5s'; // Enable transition for future toggles
        }, 0);
    }
}

// Function to toggle the sidebar
function toggleSidebar() {
    // Toggle the state and update the sidebar accordingly
    isCollapsed = !isCollapsed;

    updateSidebarState(); // Update the sidebar state based on the new value

    // Save the current state to localStorage
    const settings = { collapsed: isCollapsed };
    localStorage.setItem('sidebarState', JSON.stringify(settings));
}


// Function to update the title with the notification count or app name
const originalTitle = document.title;
function updateTitle(count) {
    if (showNotification) {
        document.title = `(${count}) Notifications`;
    } else {
        document.title = originalTitle;
    }
    showNotification = !showNotification;
}

// Simulated notification count
let notificationCount = 3; // Set this to your current notification count
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

// Hide tooltip when mouse leaves sidebar
toggleButton.addEventListener('mouseleave', function () {
    tooltip.style.display = 'none'; // Hide tooltip
});

// Hide tooltip when mouse leaves sidebar
const sidebar = document.querySelector('.sidebar');
sidebar.addEventListener('mouseleave', () => {
    tooltip.style.display = 'none'; // Hide tooltip
});
