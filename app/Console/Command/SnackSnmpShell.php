<?php

class SnackSnmpShell extends AppShell {

    public $uses = array('Nas');

    private $xmlmib;

    public function main() {
        $this->init();
        $nas = $this->Nas->find('all');
        foreach ($nas as $n) {
            if ($n['Nas']['nasname'] != "127.0.0.1") {
                //$this->out($n['Nas']['nasname']);
                $n['Nas']['version'] = $this->_getIOS($n['Nas']['nasname'], 'public');
                $n['Nas']['image'] = $this->_getImage($n['Nas']['nasname'], 'public');
                $n['Nas']['serialnumber'] = $this->_getSerialNumber($n['Nas']['nasname'], 'public', $n['Nas']['image']);
                $n['Nas']['model'] = $this->_getModel($n['Nas']['nasname'], 'public', $n['Nas']['image']);
                $this->Nas->save($n);
            }
        }
    }

    private function init()
    {
        // Crée un nouveau fichier avec les permissions à 0644
        $file = new File(APP.'Console/mib.xml', true, 0644);
        $xml_string = $file->read(true, 'r');
        //echo $xml_string;
        $this->xmlmib = new SimpleXMLElement($xml_string);
    }

    private function _getIOS($host, $community) {
        $oid = $this->xmlmib->image[0]->oid;
        //echo $oid;
        $res = snmp2_get($host, $community, $oid);
        $res = strstr($res, 'Version');
        $res = explode(",", $res);
        $res = explode(" ", $res[0]);
        return $res[1];
    }

    private function _getImage($host, $community) {
        $oid = $this->xmlmib->image[0]->oid;
        $res = snmp2_get($host, $community, $oid);
        $ind = strrpos($res, 'Software');
        $res = substr($res, $ind);
        $res = explode("(", $res);
        $res = explode(")", $res[1]);
        return $res[0];
    }

    private function _getSerialNumber($host, $community, $model) {
        foreach ($this->xmlmib->serials->serial as $serial) {
            //echo $serial->pattern, ' oid : ', $serial->oid."\n";
            $pattern = $serial->pattern;
            $regex = "/".$pattern."/";
            if (preg_match($regex, $model, $matches)) {
                $oid = $serial->oid;
                $res = snmp2_get($host, $community, $oid);
                $res = explode("\"", $res);
                echo "SERIAL ".$res[1]."\n";
                return $res[1];
            }
        }
    }

    private function _getModel($host, $community, $model) {
        foreach ($this->xmlmib->models->model as $mod) {
            //echo $serial->pattern, ' oid : ', $serial->oid."\n";
            $pattern = $mod->pattern;
            $regex = "/".$pattern."/";
            if (preg_match($regex, $model, $matches)) {
                $oid = $mod->oid;
                $res = snmp2_get($host, $community, $oid);
                $res = explode("\"", $res);
                echo "MODEL ".$res[1]."\n";
                return $res[1];
            }
        }
    }

}

?>