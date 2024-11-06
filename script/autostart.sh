#!/bin/bash
cd /home/sfserver
./sfserver details > autostart.txt
if grep -q "STARTED" autostart.txt; then
echo "Serveur ON"
else
echo "Serveur OFF"
echo "Démarrage en cours..."
./sfserver stop /force
./sfserver start
echo "Serveur ON"
fi

#rm startup.txt
