Prérequis pour faire fonctionner le site Portalis
Prérequis
Avant de commencer, assurez-vous d'avoir les éléments suivants :
1.Installation de LinuxGSM et de Satisfactory: Assurez-vous que LinuxGSM est installé sur votre serveur via l'utilisateur sfserver dans le dossier /home/sfserver. 
2.Mise en service du serveur Satisfactory avec systemd
Pour transformer votre serveur Satisfactory géré avec LinuxGSM en service sous Linux, vous pouvez créer un fichier de service systemd. Cela vous permettra de démarrer, arrêter et redémarrer le serveur automatiquement et d’assurer qu'il démarre lors du démarrage du système. Voici comment procéder :
 1.Créer un fichier de service systemd : Ouvrez un fichier de service avec l'éditeur de votre choix :
   sudo nano /etc/systemd/system/sfserver.service

 2.Ajouter la configuration suivante dans le fichier sfserver.service 
   [Unit]
   Description=Satisfactory Server managed by LinuxGSM
   After=network.target

   [Service]
   Type=simple
   User=sfserver
   WorkingDirectory=/home/sfserver
   ExecStart=/home/sfserver/sfserver start
   ExecStop=/home/sfserver/sfserver stop
   ExecReload=/home/sfserver/sfserver restart
   Restart=on-failure

   [Install]
   WantedBy=multi-user.target

 3.Recharger systemd pour prendre en compte le nouveau service :
    sudo systemctl daemon-reload
 
 4.Activer et démarrer le service :
     sudo systemctl enable sfserver
     sudo systemctl start sfserver

3.Mettre les scripts nécéssaire au check du status du serveur
 1. Sous /home/sfserver créer un dossier script et y rajouter les scripts
 2. créer une tache crontab pour executer les script en backup sous le user sfserver:
  crontab -e
  @reboot /home/sfserver/script/bouclesstatus.sh

4.Pensez à mettre en sudoer le user sfserver sur le commande suivante
 vi /etc/sudoers

Puis rajouter ces lignes:
   www-data ALL=(sfserver) NOPASSWD: /bin/sleep
   www-data ALL=(sfserver) NOPASSWD: /usr/bin/systemctl stop sfserver
   www-data ALL=(sfserver) NOPASSWD: /usr/bin/systemctl restart sfserver
   www-data ALL=(sfserver) NOPASSWD: /usr/bin/systemctl status sfserver
   www-data ALL=(sfserver) NOPASSWD: /bin/sleep
   www-data ALL=(sfserver) NOPASSWD: /bin/mkdir
   www-data ALL=(sfserver) NOPASSWD: /bin/chown
   www-data ALL=(sfserver) NOPASSWD: /home/sfserver

