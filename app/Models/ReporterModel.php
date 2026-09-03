<?php

namespace App\Models;

use CodeIgniter\Model;

class ReporterModel extends Model
{
    protected $table         = 'tblreporter';
    protected $primaryKey    = 'reporter_id';
    protected $returnType    = 'array';
    protected $useTimestamps = false;

    protected $allowedFields = [
        'reporter_name'
    ];
}