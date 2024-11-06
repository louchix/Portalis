document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM entièrement chargé et analysé.');

    // Vérifier l'état du serveur dès que la page est chargée
    checkServerStatus();
    // Appeler la fonction pour charger les sauvegardes
    loadSaves();
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
        // Recharger la liste des sauvegardes après un upload réussi
        loadSaves();
    })
    .catch(error => {
        console.error('Erreur:', error);
    });
}

function loadSaves() {
    console.log('Récupération de la liste des sauvegardes...');
    fetch('controlServer.php?action=list_saves')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau : ' + response.statusText);
            }
            return response.json(); // Traiter la réponse comme JSON
        })
        .then(data => {
            const saveList = document.getElementById('saveList');
            saveList.innerHTML = ''; // Réinitialiser la liste

            if (data.files) {
                if (Array.isArray(data.files)) {
                    data.files.forEach(file => {
                        const listItem = document.createElement('li');
                        listItem.innerHTML = `<a href="controlServer.php?action=download&file=${encodeURIComponent(file)}">${file}</a>`;
                        saveList.appendChild(listItem);
                    });
                } else {
                    saveList.innerHTML = '<li>Aucune sauvegarde trouvée.</li>';
                    console.error('Erreur : data.files n\'est pas un tableau.', data);
                }
            } else {
                saveList.innerHTML = '<li>Aucune sauvegarde trouvée.</li>';
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des sauvegardes:', error);
        });
}

// Vérifier l'état du serveur toutes les 10 secondes
setInterval(checkServerStatus, 10000);
