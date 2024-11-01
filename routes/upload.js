const express = require('express');
const multer = require('multer');
const path = require('path');

const router = express.Router();
const uploadDir = path.join(__dirname, '../public/blueprint');

// Configuration de Multer pour l'upload
const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, uploadDir);
    },
    filename: (req, file, cb) => {
        cb(null, file.originalname);
    }
});

const upload = multer({
    storage: storage,
    limits: { fileSize: 500000 }, // Limite de 500 Ko
    fileFilter: (req, file, cb) => {
        const ext = path.extname(file.originalname);
        if (ext !== '.sbp' && ext !== '.sbpcfg') {
            return cb(new Error('Seuls les fichiers .sbp et .sbpcfg sont autorisés.'));
        }
        cb(null, true);
    }
});

// Route d'upload
router.post('/', upload.single('file'), (req, res) => {
    res.json({ success: true, message: `Le fichier ${req.file.originalname} a été uploadé.` });
});

// Gestion des erreurs
router.use((err, req, res, next) => {
    res.status(400).json({ success: false, message: err.message });
});

module.exports = router; 