<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleArchiveModel extends Model
{
    protected $table            = 'tblarticle_archive';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $useTimestamps = false;

    protected $allowedFields = [
        'article_id',
        'archived_by',
        'archived_at',
        'archive_remarks',
        'created_at',
    ];
}