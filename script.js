document.getElementById('uploadForm').addEventListener('submit', async function (event) {
    event.preventDefault();
    
    const files = document.getElementById('blueprintFiles').files;
    if (files.length === 0) {
        updateStatus('Veuillez sélectionner un fichier.', 'error');
        return;
    }
    
    const formData = new FormData();
    for (const file of files) {
        formData.append('files', file);
    }

    updateStatus('Envoi en cours...', 'info');

    try {
        const response = await fetch('/upload', {
            method: 'POST',
            body: formData,
        });
        
        if (response.ok) {
            updateStatus('Upload réussi!', 'success');
        } else {
            throw new Error('Erreur lors de l\'envoi');
        }
    } catch (error) {
        updateStatus(`Erreur : ${error.message}`, 'error');
    }
});

function updateStatus(message, status) {
    const statusDiv = document.getElementById('status');
    statusDiv.textContent = message;
    statusDiv.className = status;
}
