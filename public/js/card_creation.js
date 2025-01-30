// card_creation.js
document.addEventListener('DOMContentLoaded', () => {
    setupFormListeners();
    loadExistingCard();
});

function setupFormListeners() {
    const formInputs = document.querySelectorAll('#cardDetailForm input, #cardDetailForm textarea');
    formInputs.forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    document.getElementById('saveCard').addEventListener('click', saveCard);
}

async function loadExistingCard() {
    const urlParams = new URLSearchParams(window.location.search);
    const cardId = urlParams.get('id');

    if (cardId) {
        try {
            const response = await fetch(`/api/cards/${cardId}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const card = await response.json();

            // Populate form fields
            document.getElementById('cardName').value = card.card_name;
            document.getElementById('fullName').value = card.full_name;
            document.getElementById('jobTitle').value = card.job_title || '';
            document.getElementById('email').value = card.email || '';
            document.getElementById('phone').value = card.phone || '';
            document.getElementById('address').value = card.address || '';
            document.getElementById('qrUrl').value = card.qr_url || '';

            // Update preview
            updatePreview();

            // Generate QR code if URL exists
            if (card.qr_url) {
                generateQRCode(card.qr_url);
            }
        } catch (error) {
            console.error('Error loading card:', error);
            alert('Failed to load card details. Please try again.');
        }
    }
}

function updatePreview() {
    const previewData = {
        fullName: document.getElementById('fullName').value || 'Full Name',
        jobTitle: document.getElementById('jobTitle').value || 'Job Title',
        email: document.getElementById('email').value || 'Email',
        phone: document.getElementById('phone').value || 'Phone',
        address: document.getElementById('address').value || 'Address'
    };

    // Update preview elements
    document.getElementById('previewFullName').textContent = previewData.fullName;
    document.getElementById('previewJobTitle').textContent = previewData.jobTitle;
    document.getElementById('previewEmail').textContent = previewData.email;
    document.getElementById('previewPhone').textContent = previewData.phone;
    document.getElementById('previewAddress').textContent = previewData.address;

    const qrUrl = document.getElementById('qrUrl').value;
    if (qrUrl) {
        generateQRCode(qrUrl);
    }
}

function generateQRCode(url) {
    const qrContainer = document.getElementById('qrCode');
    qrContainer.innerHTML = '';
    new QRCode(qrContainer, {
        text: url,
        width: 100,
        height: 100
    });
}

async function saveCard() {
    try {
        const cardData = {
            cardName: document.getElementById('cardName').value,
            fullName: document.getElementById('fullName').value,
            jobTitle: document.getElementById('jobTitle').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            qrUrl: document.getElementById('qrUrl').value
        };

        const urlParams = new URLSearchParams(window.location.search);
        const cardId = urlParams.get('id');
        
        // Updated URLs to match the API routes
        const url = cardId ? `/api/cards/${cardId}` : '/api/cards';
        const method = cardId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(cardData)
        });

        const data = await response.json();

        if (!response.ok) {
            if (response.status === 422) {
                // Validation errors
                const errorMessages = Object.values(data.messages).flat().join('\n');
                throw new Error(errorMessages);
            } else {
                throw new Error(data.error || 'Failed to save card');
            }
        }

        alert('Card saved successfully!');
        window.location.href = '/homepage';

    } catch (error) {
        console.error('Error saving card:', error);
        alert(error.message || 'Failed to save card');
    }
}

// Zoom Controls
const zoomInButton = document.getElementById('zoomIn');
const zoomOutButton = document.getElementById('zoomOut');
const resetZoomButton = document.getElementById('resetZoom');
const cardDesign = document.querySelector('.card'); // Target the card itself

let scale = 1;

zoomInButton.addEventListener('click', () => {
    scale += 0.1;
    cardDesign.style.transform = `scale(${scale})`; // Apply zoom to the card
});

zoomOutButton.addEventListener('click', () => {
    scale -= 0.1;
    if (scale < 0.1) scale = 0.1; // Prevent zooming out too much
    cardDesign.style.transform = `scale(${scale})`; // Apply zoom to the card
});

resetZoomButton.addEventListener('click', () => {
    scale = 1;
    cardDesign.style.transform = `scale(${scale})`; // Reset zoom
});