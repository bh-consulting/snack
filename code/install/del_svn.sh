#!/bin/bash

sudo rm -R paquet/.svn
sudo rm -R paquet/DEBIAN/.svn/
sudo rm -R paquet/var/.svn/
sudo rm -R paquet/var/www/.svn/
sudo rm -R paquet/etc/.svn/
sudo rm -R paquet/etc/freeradius/.svn
sudo rm -R paquet/etc/freeradius/sites-available/.svn
sudo rm -R paquet/etc/freeradius/sites-enabled/.svn

exit 0
