document.getElementById('fileInput').addEventListener('change', function() {
    const fileNameSpan = document.querySelector('.file-name');
    const file = this.files[0];
    if (file) {
        fileNameSpan.textContent = file.name;
    } else {
        fileNameSpan.textContent = 'Aucun fichier sélectionné';
    }
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