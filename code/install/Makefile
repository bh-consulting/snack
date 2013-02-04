all:
	sudo rm -fr paquet_deb
	sudo cp -a paquet paquet_deb
	sudo find paquet_deb -name .svn -exec rm -r {} +
	sudo chmod 0440 paquet_deb/etc/sudoers.d/snack
	sudo chown root:root paquet_deb/etc/sudoers.d/snack
	sudo dpkg-deb --build paquet_deb Radius-BH-Consulting.deb

install:
	sudo dpkg -i Radius-BH-Consulting.deb || sudo apt-get -y --force-yes -f install
	sudo cp -R ../interface /home/pi/snack
	sudo find /home/pi/snack -name .svn -exec rm -r {} +

clean:
	sudo rm -fr paquet_deb Radius-BH-Consulting.deb

uninstall: clean
	-sudo delgroup snack
	-echo "drop database radius;" | mysql -uroot -pbh
	sudo rm -fr /home/pi/snack
	sudo rm -fr /etc/sudoers.d/snack
	sudo rm -fr /etc/apache2/sites-available/snack
	sudo rm -fr /etc/apache2/sites-enabled/snack
	sudo apt-get autoremove --purge radius-bh-consulting
