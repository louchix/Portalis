function uploadBlueprint() {
    const fileInput = document.getElementById('fileInput');
    const formData = new FormData();
    formData.append('file', fileInput.files[0]);

    fetch('/upload', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        alert('Blueprint uploadé avec succès');
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

function controlServer(action) {
    fetch(`/control/${action}`, {
        method: 'GET'
    })
    .then(response => response.text())
    .then(data => {
        alert(`Serveur ${action} avec succès`);
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}