



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
