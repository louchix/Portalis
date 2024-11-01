const express = require('express');
const { exec } = require('child_process');

const router = express.Router();

// Route pour contrôler le serveur
router.post('/:action', (req, res) => {
    const action = req.params.action;

    if (!['start', 'stop', 'restart'].includes(action)) {
        return res.status(400).json({ status: 'Action non valide' });
    }

    const command = `sudo -u sfserver /home/sfserver/sfserver ${action}`;
    exec(command, (error, stdout, stderr) => {
        if (error) {
            return res.status(500).json({ status: 'Erreur lors de l\'exécution de l\'action', error: stderr });
        }
        res.json({ status: stdout.trim() });
    });
});

module.exports = router; 