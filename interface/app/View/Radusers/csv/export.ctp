<?php

foreach ($usersData as $userData) {
    $this->Csv->addRow($userData);
}

echo $this->Csv->render($filename);
?> 
