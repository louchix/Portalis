document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM entièrement chargé et analysé.');

    // Vérifier l'état du serveur dès que la page est chargée
    checkServerStatus();
    // Appeler la fonction pour charger les sauvegardes
    loadSaves();

    // Exemple de version, vous pouvez le mettre à jour dynamiquement
    const version = "2.0.2d"; // Remplacez par la version actuelle

    // Mettre à jour le footer avec la version
    document.getElementById('version').textContent = `Version: ${version}`;
});

// Définition de la fonction checkServerStatus
function checkServerStatus() {
    console.log('Vérification de l\'état du serveur...');
    fetch('controlServer.php?action=status')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau : ' + response.statusText);
            }
            return response.json(); // Traiter la réponse comme JSON
        })
        .then(data => {
            const statusElement = document.getElementById('serverStatus');
            // Réinitialiser les classes avant d'ajouter la nouvelle classe
            statusElement.classList.remove('is-info', 'is-success', 'is-danger');

            if (data.status === 'ON') {
                statusElement.classList.add('is-success'); // Changer en vert
                statusElement.textContent = 'État du serveur : Serveur ON';
            } else if (data.status === 'OFF') {
                statusElement.classList.add('is-danger'); // Changer en rouge
                statusElement.textContent = 'État du serveur : Serveur OFF';
            } else {
                statusElement.classList.add('is-info'); // État inconnu
                statusElement.textContent = `État du serveur : ${data.message}`;
            }
            console.log(`État du serveur récupéré : ${data.status}`);
        })
        .catch(error => {
            console.error('Erreur lors de la vérification de l\'état du serveur:', error);
        });
}

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
            return response.text(); // Traiter la réponse comme texte d'abord
        })
        .then(data => {
            console.log('Réponse brute du serveur:', data); // Affichez la réponse brute
            const jsonData = JSON.parse(data); // Essayez de parser le JSON ici
            const saveList = document.getElementById('saveList');
            saveList.innerHTML = ''; // Réinitialiser la liste

            if (jsonData.files) {
                if (Array.isArray(jsonData.files)) {
                    jsonData.files.forEach(file => {
                        const listItem = document.createElement('li');
                        listItem.innerHTML = `<a href="controlServer.php?action=download&file=${encodeURIComponent(file)}">${file}</a>`;
                        saveList.appendChild(listItem);
                    });
                } else {
                    saveList.innerHTML = '<li>Aucune sauvegarde trouvée.</li>';
                    console.error('Erreur : data.files n\'est pas un tableau.', jsonData);
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
