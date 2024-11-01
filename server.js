const express = require('express');
const bodyParser = require('body-parser');
const uploadRoutes = require('./routes/upload');
const serverControlRoutes = require('./routes/serverControl');
const path = require('path');

const app = express();
const PORT = process.env.PORT || 3000;

// Middleware
app.use(bodyParser.json());
app.use(bodyParser.urlencoded({ extended: true }));
app.use(express.static(path.join(__dirname, 'public')));

// Routes
app.use('/upload', uploadRoutes);
app.use('/server', serverControlRoutes);

// Démarrer le serveur
app.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
}); 