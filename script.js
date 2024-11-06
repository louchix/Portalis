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

    // Vérifier l'état du serveur dès que la page est chargée
    checkServerStatus();

    listSaves();
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
    const loadingText = document.querySelector('.loading-text'); // Sélectionnez le texte

    // Ajoutez la classe de chargement pour l'animation
    loadingText.classList.add('loading');

    fetch(`controlServer.php?action=${action}`)
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
    })
    .finally(() => {
        // Retirez la classe de chargement après la réponse
        loadingText.classList.remove('loading');
    });
}

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

function listSaves() {
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

function loadSaves() {
    fetch('controlServer.php?action=list_saves')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau : ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            console.log('Données récupérées:', data); // Log des données récupérées
            const saveList = document.getElementById('saveList');
            saveList.innerHTML = ''; // Réinitialiser la liste

            // Vérifiez si data.files est un objet et convertissez-le en tableau
            if (data.files && typeof data.files === 'object') {
                const filesArray = Object.values(data.files); // Convertir l'objet en tableau

                if (filesArray.length > 0) {
                    console.log('Fichiers trouvés:', filesArray); // Log des fichiers trouvés
                    filesArray.forEach(file => {
                        const li = document.createElement('li');
                        
                        // Créer un lien pour le téléchargement
                        const downloadLink = document.createElement('a');
                        downloadLink.textContent = file; // Nom du fichier
                        downloadLink.href = `controlServer.php?action=download&file=${encodeURIComponent(file)}`; // URL de téléchargement
                        downloadLink.target = '_blank'; // Ouvrir dans un nouvel onglet
                        downloadLink.className = 'download-link'; // Ajouter une classe pour le style si nécessaire

                        // Ajouter le lien au li
                        li.appendChild(downloadLink);
                        saveList.appendChild(li);
                    });
                } else {
                    saveList.innerHTML = '<p>Aucune sauvegarde trouvée.</p>'; // Message par défaut
                }
            } else {
                saveList.innerHTML = '<p>Aucune sauvegarde trouvée.</p>'; // Message par défaut
                console.error('Erreur : data.files n\'est pas un objet valide.', data);
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des sauvegardes:', error);
        });
}

// Appeler la fonction pour charger les sauvegardes
loadSaves();

// Vérifier l'état du serveur toutes les 10 secondes
setInterval(checkServerStatus, 10000);
