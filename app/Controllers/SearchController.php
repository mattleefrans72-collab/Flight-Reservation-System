<?php

namespace App\Controllers;

use App\Core\App;

class SearchController
{
    public function index()
    {
        header('Content-Type: application/json');

        $search = $_GET['search'] ?? '';
        $search = "%{$search}%";
        if (strlen($search) < 3) {
            echo json_encode([]);
            return;
        }
        $db = App::resolve('Core\Database');
        $airports = $db->query("SELECT * FROM airport_summary
                        WHERE name LIKE ?
                        OR iata_code LIKE ?
                        OR municipality LIKE ?
                        OR region LIKE ?
                        OR country LIKE ?", [$search, $search, $search, $search, $search])->fetchAll();
       echo json_encode($airports);
        
    }
}