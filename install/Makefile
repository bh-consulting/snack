all: clean
	sudo cp -a paquet paquet_deb
	sudo cp ../db/*.sql paquet_deb/tmp/snack/
	sudo cp -R ../interface paquet_deb/home/snack/interface
	sudo find paquet_deb -name .svn -exec rm -r {} +
	sudo chmod 0440 paquet_deb/etc/sudoers.d/snack
	sudo chown root:root paquet_deb/etc/sudoers.d/snack
	sudo chmod 777 -R paquet_deb/home/snack/interface/app/tmp
	sudo dpkg-deb --build paquet_deb snack.deb

install:
	sudo dpkg -i snack.deb || (sudo apt-get update && sudo apt-get -y --force-yes -f install)

clean:
	sudo rm -fr paquet_deb snack.deb

uninstall: clean
	sudo apt-get autoremove --purge snack
