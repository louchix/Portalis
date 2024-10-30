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

// Fonction pour démarrer, arrêter ou redémarrer le serveur
function controlServer(action) {
    const statusDiv = document.getElementById('status');
    statusDiv.textContent = `${action.charAt(0).toUpperCase() + action.slice(1)} en cours...`;
    statusDiv.className = '';

    fetch(`server_control.php?action=${action}`, {
        method: 'POST',
    })
    .then(response => response.json())
    .then(data => {
        statusDiv.textContent = data.status;
        statusDiv.className = data.status === 'Serveur ON' ? 'success' : 'error';
    })
    .catch(error => {
        statusDiv.textContent = 'Erreur lors de l\'exécution de l\'action';
        statusDiv.className = 'error';
    });
}

// Ajout des écouteurs d'événements pour les boutons
document.getElementById('startButton').addEventListener('click', () => controlServer('start'));
document.getElementById('stopButton').addEventListener('click', () => controlServer('stop'));
document.getElementById('restartButton').addEventListener('click', () => controlServer('restart'));
