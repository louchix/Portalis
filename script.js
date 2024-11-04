document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM entièrement chargé et analysé.');
    
    document.getElementById('fileInput').addEventListener('change', function() {
        const fileNameSpan = document.querySelector('.file-name');
        const file = this.files[0];
        if (file) {
            fileNameSpan.textContent = file.name;
            console.log(`Fichier sélectionné: ${file.name}`);
        } else {
            fileNameSpan.textContent = 'Aucun fichier sélectionné';
            console.log('Aucun fichier sélectionné.');
        }
    });

    // Fonction pour mettre à jour l'état du serveur
    function updateServerStatus() {
        fetch('controlServer.php?action=status')
            .then(response => response.text())
            .then(status => {
                const serverStatusDiv = document.getElementById('serverStatus');
                serverStatusDiv.textContent = status;

                // Changer la couleur en fonction de l'état
                if (status.includes('ON')) {
                    serverStatusDiv.className = 'notification is-success'; // Vert
                } else if (status.includes('OFF')) {
                    serverStatusDiv.className = 'notification is-danger'; // Rouge
                } else {
                    serverStatusDiv.className = 'notification is-warning'; // Jaune pour état inconnu
                }
            })
            .catch(error => console.error('Erreur:', error));
    }

    // Appel initial pour mettre à jour l'état du serveur
    updateServerStatus();
});

function uploadBlueprint() {
    const fileInput = document.getElementById('fileInput');
    const file = fileInput.files[0];
    const allowedExtensions = ['.sbp', '.sbpcfg'];

    console.log('Début du processus de téléchargement.');

    if (!file) {
        alert('Veuillez sélectionner un fichier.');
        console.log('Aucun fichier sélectionné.');
        return;
    }

    const fileExtension = file.name.slice(file.name.lastIndexOf('.')).toLowerCase();
    console.log(`Fichier sélectionné: ${file.name}, Extension: ${fileExtension}`);

    if (!allowedExtensions.includes(fileExtension)) {
        alert('Seuls les fichiers .sbp et .sbpcfg sont autorisés.');
        console.log('Extension de fichier non autorisée.');
        return;
    }

    const formData = new FormData();
    formData.append('file', file);
    console.log('FormData créé avec le fichier.');

    fetch('https://axiiom.org/controlServer.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Réponse reçue du serveur pour l\'upload.');
        if (!response.ok) {
            throw new Error('Erreur réseau : ' + response.statusText);
        }
        return response.text();
    })
    .then(data => {
        console.log('Réponse du serveur traitée.');
        console.log('Logs du serveur :\n' + data);
        alert(data.split('\n')[0]); // Affiche uniquement le premier message à l'utilisateur
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

function controlServer(action) {
    console.log(`Action du serveur: ${action}`);
    fetch(`https://axiiom.org/controlServer.php?action=${action}`)
    .then(response => {
        console.log(`Réponse reçue du serveur pour l'action ${action}.`);
        if (!response.ok) {
            throw new Error('Erreur réseau : ' + response.statusText);
        }
        return response.text();
    })
    .then(data => {
        console.log('Réponse du serveur traitée.');
        console.log('Logs du serveur :\n' + data);
        alert(data.split('\n')[0]); // Affiche uniquement le premier message à l'utilisateur
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

// Vérifier l'état du serveur toutes les 10 secondes
setInterval(updateServerStatus, 10000);