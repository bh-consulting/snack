all:
	sudo dpkg-deb --build paquet

install:
	sudo dpkg -i paquet.deb
	sudo apt-get install -f

get:

	cp -R BH-Consulting/trunk/code/install/paquet .

	sudo rm -R -f paquet/.svn
	sudo rm -R -f paquet/DEBIAN/.svn/
	sudo rm -R -f paquet/var/.svn/
	sudo rm -R -f paquet/var/www/.svn/
	sudo rm -R -f paquet/etc/.svn/
	sudo rm -R -f paquet/etc/freeradius/.svn
	sudo rm -R -f paquet/etc/freeradius/sites-available/.svn
	sudo rm -R -f paquet/etc/freeradius/sites-enabled/.svn
