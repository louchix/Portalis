from flask import Flask, render_template, request, redirect, url_for
import os

app = Flask(__name__)

# Chemin vers le dossier des blueprints
BLUEPRINTS_DIR = '/chemin/vers/blueprints'

@app.route('/')
def dashboard():
    return render_template('dashboard.html')

@app.route('/upload', methods=['POST'])
def upload_blueprint():
    if 'file' not in request.files:
        return redirect(url_for('dashboard'))
    
    file = request.files['file']
    if file.filename == '':
        return redirect(url_for('dashboard'))
    
    file.save(os.path.join(BLUEPRINTS_DIR, file.filename))
    return redirect(url_for('dashboard'))

@app.route('/control/<action>')
def control_server(action):
    if action in ['start', 'stop', 'restart']:
        os.system(f'./lgsm.sh {action}')
    return redirect(url_for('dashboard'))

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
