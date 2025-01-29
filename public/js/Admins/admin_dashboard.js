// DOM Elements
const totalUsers = document.querySelector('.metric-card:nth-child(1) .metric-value');
const totalCards = document.querySelector('.metric-card:nth-child(2) .metric-value');
const recentActivity = document.querySelector('.metric-card:nth-child(3) .metric-value');
const systemStatus = document.querySelector('.metric-card:nth-child(4) .status-online');
const activityLog = document.querySelector('.activity-log ul');
const quickActions = document.querySelectorAll('.action-card');

// Add refresh button to the overview section
const overviewSection = document.querySelector('.overview');
const refreshButton = document.createElement('button');
refreshButton.innerHTML = 'Refresh Data';
refreshButton.className = 'refresh-button';
overviewSection.insertBefore(refreshButton, overviewSection.firstChild);

// Format numbers with commas
function formatNumber(number) {
    return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Show loading state
function setLoadingState(isLoading) {
    const metrics = document.querySelectorAll('.metric-value, .status-online');
    if (isLoading) {
        metrics.forEach(metric => metric.classList.add('loading'));
        refreshButton.disabled = true;
    } else {
        metrics.forEach(metric => metric.classList.remove('loading'));
        refreshButton.disabled = false;
    }
}

// Update dashboard data
async function updateDashboardData() {
    try {
        setLoadingState(true);
        
        const response = await fetch('/api/admin/dashboard-data', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            credentials: 'same-origin'
        });
        if (!response.ok) {
            throw new Error('Failed to fetch dashboard data');
        }
        
        const data = await response.json();
        
        // Update metrics
        totalUsers.textContent = formatNumber(data.totalUsers);
        totalCards.textContent = formatNumber(data.totalCards);
        recentActivity.textContent = `${data.todayNewCards} New Cards (Today)`;
        systemStatus.textContent = data.systemStatus;
        
        // Update activity log
        activityLog.innerHTML = '';
        if (data.recentActivity && data.recentActivity.length > 0) {
            data.recentActivity.forEach(activity => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <strong>${activity.user}</strong> 
                    ${activity.action} - 
                    <span class="timestamp">${activity.timestamp}</span>
                `;
                activityLog.appendChild(li);
            });
        } else {
            const li = document.createElement('li');
            li.textContent = 'No recent activity';
            activityLog.appendChild(li);
        }
    } catch (error) {
        console.error('Error updating dashboard:', error);
        // Show error message to user
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error-message';
        errorDiv.textContent = 'Failed to update dashboard data. Please try again.';
        document.querySelector('.overview').appendChild(errorDiv);
        
        // Remove error message after 5 seconds
        setTimeout(() => {
            errorDiv.remove();
        }, 5000);
    } finally {
        setLoadingState(false);
    }
}

// Quick Actions (Redirect to respective pages)
quickActions.forEach(action => {
    action.addEventListener('click', function(event) {
        event.preventDefault();
        const href = this.getAttribute('href');
        if (href) {
            window.location.href = href;
        }
    });
});

// Add refresh button click handler
refreshButton.addEventListener('click', updateDashboardData);

// Auto-refresh every 5 minutes
const AUTO_REFRESH_INTERVAL = 5 * 60 * 1000; // 5 minutes in milliseconds
let autoRefreshInterval;

function startAutoRefresh() {
    autoRefreshInterval = setInterval(updateDashboardData, AUTO_REFRESH_INTERVAL);
}

function stopAutoRefresh() {
    clearInterval(autoRefreshInterval);
}

// Stop auto-refresh when tab is not visible
document.addEventListener('visibilitychange', () => {
    if (document.hidden) {
        stopAutoRefresh();
    } else {
        updateDashboardData(); // Refresh immediately when tab becomes visible
        startAutoRefresh();
    }
});

// Initialize dashboard
function initializeDashboard() {
    // Initial data load
    updateDashboardData();
    
    // Start auto-refresh
    startAutoRefresh();
}

// Add CSS for loading state and error message
const style = document.createElement('style');
style.textContent = `
    .loading {
        opacity: 0.5;
        pointer-events: none;
    }
    
    .refresh-button {
        padding: 8px 16px;
        background-color: #4a5568;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin-bottom: 16px;
    }
    
    .refresh-button:hover {
        background-color: #2d3748;
    }
    
    .refresh-button:disabled {
        background-color: #718096;
        cursor: not-allowed;
    }
    
    .error-message {
        background-color: #fed7d7;
        border: 1px solid #f56565;
        color: #c53030;
        padding: 12px;
        border-radius: 4px;
        margin-top: 12px;
    }
`;
document.head.appendChild(style);

// Run initialization when the page loads
window.addEventListener('load', initializeDashboard);