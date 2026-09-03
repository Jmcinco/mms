<?php

namespace App\Models;

use CodeIgniter\Model;

class SlantModel extends Model
{
    protected $table         = 'tblslant';
    protected $primaryKey    = 'slant_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'slant_name'
    ];
}