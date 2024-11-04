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

// Vérifier l'état du serveur toutes les 10 secondes
setInterval(checkServerStatus, 10000);

function loadSaves() {
    fetch('controlServer.php?action=list_saves')
        .then(response => {
            if (!response.ok) {
                throw new Error('Erreur réseau : ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            const saveList = document.getElementById('saveList');
            saveList.innerHTML = ''; // Réinitialiser la liste

            if (Array.isArray(data.files)) { // Vérifiez que data.files est un tableau
                data.files.forEach(file => {
                    const filePath = `/home/sfserver/.config/Epic/FactoryGame/Saved/SaveGames/server/${file}`;
                    const fileStats = getFileStats(filePath); // Fonction pour obtenir les stats du fichier

                    const card = document.createElement('div');
                    card.className = 'card column is-one-third'; // Ajouter la classe de carte et définir la largeur
                    card.innerHTML = `
                        <div class="card-content">
                            <h3 class="title is-4">${file}</h3>
                            <p>Date : ${fileStats.date}</p>
                            <p>Poids : ${fileStats.size} Ko</p>
                            <a href="controlServer.php?action=download&file=${encodeURIComponent(file)}" class="button is-link">Télécharger</a>
                        </div>
                    `;
                    saveList.appendChild(card);
                });
            } else {
                saveList.innerHTML = '<p>Aucune sauvegarde trouvée.</p>';
                console.error('Erreur : data.files n\'est pas un tableau.', data);
            }
        })
        .catch(error => {
            console.error('Erreur lors du chargement des sauvegardes:', error);
        });
}

// Fonction pour obtenir les statistiques du fichier
function getFileStats(filePath) {
    // Remplacez ceci par une méthode pour obtenir la date et la taille du fichier
    const stats = {
        date: new Date().toLocaleDateString(), // Remplacez par la date réelle
        size: Math.floor(Math.random() * 100) // Remplacez par la taille réelle en Ko
    };
    return stats;
}

// Appeler la fonction pour charger les sauvegardes
loadSaves();
