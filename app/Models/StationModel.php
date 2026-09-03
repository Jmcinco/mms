<?php

namespace App\Models;

use CodeIgniter\Model;

class StationModel extends Model
{
    protected $table         = 'tblstation';
    protected $primaryKey    = 'station_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'station_from',
        'station_name'
    ];
}