<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleEditHistoryModel extends Model
{
    protected $table            = 'tblarticle_edit_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'article_id',
        'editor_id',
        'editing_start',
        'editing_end',
        'remarks',
    ];

    public function getActiveEdit(string $articleId, int $editorId): ?array
    {
        return $this
            ->where('article_id', $articleId)
            ->where('editor_id', $editorId)
            ->where('editing_end', null)
            ->orderBy('id', 'DESC')
            ->first();
    }

    public function startEditing(
        string $articleId,
        int $editorId
    ): int|bool {
        $existing = $this->getActiveEdit(
            $articleId,
            $editorId
        );

        if ($existing) {
            return (int) $existing['id'];
        }

        return $this->insert([
            'article_id'    => $articleId,
            'editor_id'     => $editorId,
            'editing_start' => date('Y-m-d H:i:s'),
        ]);
    }

    public function finishEditing(
        string $articleId,
        int $editorId,
        ?string $remarks = null
    ): bool {
        $history = $this->getActiveEdit(
            $articleId,
            $editorId
        );

        if (! $history) {
            return false;
        }

        return (bool) $this->update(
            $history['id'],
            [
                'editing_end' => date('Y-m-d H:i:s'),
                'remarks'     => $remarks,
            ]
        );
    }
}