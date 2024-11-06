#!/bin/bash
cd /home/sfserver
./sfserver details > startup.txt
if grep -q "STARTED" startup.txt; then
echo "Serveur ON" > status.txt
else
echo "Serveur OFF" > status.txt
fi
