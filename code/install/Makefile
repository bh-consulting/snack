all:
	sudo rm -fr paquet_deb
	sudo cp -a paquet paquet_deb
	sudo find paquet_deb -name .svn -exec rm -r {} +
	sudo dpkg-deb --build paquet_deb Radius-BH-Consulting.deb

install:
	sudo dpkg -i Radius-BH-Consulting.deb || sudo apt-get -y --force-yes -f install

clean:
	sudo rm -fr paquet_deb Radius-BH-Consulting.deb
