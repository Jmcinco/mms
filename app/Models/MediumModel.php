<?php

namespace App\Models;

use CodeIgniter\Model;

class MediumModel extends Model
{
    protected $table         = 'tblmedium';
    protected $primaryKey    = 'medium_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'medium_name'
    ];
}