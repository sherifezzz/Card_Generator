// homepage.js
document.addEventListener('DOMContentLoaded', () => {
    loadCards();
    setupEventListeners();
});

function setupEventListeners() {
    const createNewCardButton = document.querySelector('.create-new-card');
    if (createNewCardButton) {
        createNewCardButton.addEventListener('click', () => {
            window.location.href = '/card_creation';
        });
    }
}

async function loadCards() {
    try {
        const response = await fetch('/api/cards', {
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const cards = await response.json();
        displayCards(cards);
    } catch (error) {
        console.error('Error loading cards:', error);
        document.getElementById('projectsGrid').innerHTML = 
            '<p class="error-message">Failed to load cards. Please try again later.</p>';
    }
}

function displayCards(cards) {
    const projectsGrid = document.getElementById('projectsGrid');

    if (!cards || cards.length === 0) {
        projectsGrid.innerHTML = '<p class="no-cards">You haven\'t created any cards yet.</p>';
        return;
    }

    const cardsHTML = cards.map(card => `
        <div class="project-card" data-card-id="${card.card_id}">
            <h3>${card.card_name}</h3>
            <div class="card-details">
                <p>${card.full_name}</p>
                <p>${card.job_title || ''}</p>
            </div>
            <p>Created: ${new Date(card.created_at).toLocaleDateString()}</p>
            <div class="card-actions">
                <button class="edit-btn" onclick="editCard(${card.card_id})">Edit</button>
                <button class="download-btn" onclick="downloadCard(${card.card_id})">Download</button>
                <button class="delete-btn" onclick="deleteCard(${card.card_id})">Delete</button>
            </div>
        </div>
    `).join('');

    projectsGrid.innerHTML = cardsHTML;
}

async function editCard(cardId) {
    window.location.href = `/card_creation?id=${cardId}`;
}

async function downloadCard(cardId) {
    try {
        // Updated URL to use the correct API endpoint
        const response = await fetch(`/api/cards/${cardId}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error('Failed to fetch card data');
        }
        
        const card = await response.json();
        
        // Create a container that will be removed after capture
        const container = document.createElement('div');
        container.style.position = 'fixed';
        container.style.left = '-9999px';
        container.style.top = '0';
        container.style.padding = '20px';
        container.style.background = 'white';
        container.style.width = '400px';
        container.style.height = '200px';
        
        container.innerHTML = `
            <div class="card" style="
                width: 400px;
                height: 200px;
                background: white;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                display: flex;
                overflow: hidden;
                font-family: Arial, sans-serif;
            ">
                <div class="left-side" style="
                    width: 70%;
                    background: #333;
                    padding: 20px;
                    color: white;
                    position: relative;
                ">
                    <div class="name" style="
                        font-size: 18px;
                        margin-bottom: 5px;
                        font-weight: bold;
                    ">${card.full_name}</div>
                    <div class="title" style="
                        color: #40E0D0;
                        font-size: 14px;
                        margin-bottom: 20px;
                    ">${card.job_title || ''}</div>
                    <ul class="contact-info" style="
                        list-style: none;
                        padding: 0;
                        margin: 0;
                    ">
                        ${card.phone ? `<li style="margin-bottom: 10px; font-size: 12px;">${card.phone}</li>` : ''}
                        ${card.email ? `<li style="margin-bottom: 10px; font-size: 12px;">${card.email}</li>` : ''}
                        ${card.address ? `<li style="margin-bottom: 10px; font-size: 12px;">${card.address}</li>` : ''}
                    </ul>
                </div>
                <div class="right-side" style="
                    width: 30%;
                    background: white;
                    padding: 20px;
                    position: relative;
                    overflow: hidden;
                ">
                    <div style="
                        content: '';
                        position: absolute;
                        left: -50px;
                        top: 0;
                        width: 100px;
                        height: 100%;
                        background: #333;
                        transform: skewX(-15deg);
                    "></div>
                    ${card.qr_url ? `
                        <div style="
                            position: relative;
                            z-index: 1;
                            margin-top: 20px;
                        ">
                            <img 
                                src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=${encodeURIComponent(card.qr_url)}" 
                                alt="QR Code" 
                                style="width: 100px; height: 100px;"
                            />
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
        
        // Add to document
        document.body.appendChild(container);

        // If there's a QR code, wait for it to load
        if (card.qr_url) {
            const qrImage = container.querySelector('img');
            await new Promise((resolve) => {
                qrImage.onload = resolve;
                // Add timeout in case image fails to load
                setTimeout(resolve, 1000);
            });
        }

        // Capture the card
        const canvas = await html2canvas(container.querySelector('.card'), {
            scale: 2,
            logging: false,
            useCORS: true,
            allowTaint: true,
            backgroundColor: null
        });

        // Convert to JPEG and trigger download
        canvas.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = `${card.card_name || 'business-card'}.jpg`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        }, 'image/jpeg', 0.95);

        // Cleanup
        document.body.removeChild(container);
    } catch (error) {
        console.error('Error downloading card:', error);
        alert('Failed to download card. Please try again.');
    }
}

async function deleteCard(cardId) {
    if (!confirm('Are you sure you want to delete this card?')) {
        return;
    }

    try {
        const response = await fetch(`/api/cards/${cardId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        });

        if (response.ok) {
            await loadCards(); // Refresh the cards display
            alert('Card deleted successfully');
        } else {
            const data = await response.json();
            throw new Error(data.error || 'Failed to delete card');
        }
    } catch (error) {
        console.error('Error deleting card:', error);
        alert(error.message || 'Failed to delete card');
    }
}