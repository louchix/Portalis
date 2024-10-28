document.getElementById('uploadForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const formData = new FormData(this);
    const statusDiv = document.getElementById('status');
    statusDiv.textContent = 'Envoi en cours...';
    statusDiv.className = '';

    fetch('upload.php', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        statusDiv.textContent = data.message;
        statusDiv.className = data.success ? 'success' : 'error';
    })
    .catch(error => {
        statusDiv.textContent = 'Erreur lors de l’envoi';
        statusDiv.className = 'error';
    });
});
