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

    // Charger les sauvegardes dès que la page est chargée
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
            console.log(data); // Vérifiez ce que le serveur renvoie
            const saveListElement = document.getElementById('saveList');
            saveListElement.innerHTML = ''; // Réinitialiser la liste

            if (data.error) {
                saveListElement.innerHTML = `<li>${data.error}</li>`;
            } else if (data.files && Array.isArray(data.files) && data.files.length > 0) { // Vérifiez si data.files est un tableau non vide
                data.files.forEach(file => {
                    const li = document.createElement('li');
                    li.textContent = file; // Ajouter chaque fichier à la liste
                    saveListElement.appendChild(li);
                });
            } else {
                saveListElement.innerHTML = '<li>Aucune sauvegarde trouvée.</li>'; // Message par défaut
            }
            console.log('Liste des sauvegardes récupérée.');
        })
        .catch(error => {
            console.error('Erreur lors de la récupération des sauvegardes:', error);
            const saveListElement = document.getElementById('saveList');
            saveListElement.innerHTML = '<li>Erreur lors de la récupération des sauvegardes.</li>'; // Afficher un message d'erreur
        });
}

// Vérifier l'état du serveur toutes les 10 secondes
setInterval(loadSaves, 10000);
