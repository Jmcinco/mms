<?php

namespace App\Models;

use CodeIgniter\Model;

class ArticleModel extends Model
{
    protected $table            = 'tblarticle';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /*
    |--------------------------------------------------------------------------
    | ALLOWED FIELDS
    |--------------------------------------------------------------------------
    */
protected $allowedFields = [
    'id',
    'news_date',

    'entry_start',
    'entry_end',

    'editing_start',
    'editing_end',

    'content',
    'summary',

    'category',
    'sub_category',
    'gov_offices',

    'remarks',
    'slant',
    'type',
    'medium',
    'station',
    'program',
    'reporter',

    'alert',
    'status',

    'created_by',

    'locked_by',
    'locked_at',

    'created_at',
    'updated_at',
    'archived_at',
];

    /*
    |--------------------------------------------------------------------------
    | VALIDATION RULES
    |--------------------------------------------------------------------------
    */
protected $validationRules = [

    'content' => 'permit_empty',

    'status' => 'permit_empty|in_list[draft,submitted,editing,completed,archived]',

    'category' => 'permit_empty|max_length[100]',

    'type' => 'permit_empty|max_length[50]',

    'medium' => 'permit_empty|max_length[50]',

    'alert' => 'permit_empty|in_list[Yes,No]',
];
    protected $validationMessages = [];

    /*
    |--------------------------------------------------------------------------
    | LOCK SETTINGS
    |--------------------------------------------------------------------------
    */
    public const LOCK_TTL_SECONDS = 300;

    /*
    |--------------------------------------------------------------------------
    | WRITER ARTICLES
    |--------------------------------------------------------------------------
    |
    | Writers can see only their own Draft and Submitted articles.
    |
    */
    public function forWriter(int $userId)
    {
        return $this
            ->whereIn('status', [
                'draft',
                'submitted',
            ])
            ->where('created_by', $userId)
            ->orderBy('news_date', 'DESC');
    }

    /*
    |--------------------------------------------------------------------------
    | EDITOR ARTICLES
    |--------------------------------------------------------------------------
    |
    | Editors can view Submitted, Editing, and Completed articles.
    |
    */
    public function forEditor()
    {
        return $this
            ->whereIn('status', [
                'submitted',
                'editing',
                'completed',
            ])
            ->orderBy('news_date', 'DESC');
    }

    /*
    |--------------------------------------------------------------------------
    | ARCHIVED ARTICLES
    |--------------------------------------------------------------------------
    |
    | If user ID is provided, only return that user's archived articles.
    |
    */
    public function archivedFor(?int $userId = null)
    {
        $query = $this
            ->where('status', 'archived')
            ->orderBy('archived_at', 'DESC');

        if ($userId !== null) {
            $query->where('created_by', $userId);
        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | GENERATE ARTICLE ID
    |--------------------------------------------------------------------------
    */
    public function generateId(): string
    {
        return 'PMU-' . time() . random_int(100, 999);
    }

    /*
    |--------------------------------------------------------------------------
    | BUILD ARTICLE SUMMARY
    |--------------------------------------------------------------------------
    */
    public function buildSummary(
        string $plainText,
        int $len = 150
    ): string {
        $plainText = trim($plainText);

        return mb_strlen($plainText) > $len
            ? mb_substr($plainText, 0, $len) . '...'
            : $plainText;
    }

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD COUNTS
    |--------------------------------------------------------------------------
    */
    public function countToday(): int
    {
        return $this
            ->where('DATE(created_at)', date('Y-m-d'))
            ->countAllResults();
    }

    public function countByStatus(string $status): int
    {
        return $this
            ->where('status', $status)
            ->countAllResults();
    }

    public function recent(int $limit = 5): array
    {
        return $this
            ->orderBy('created_at', 'DESC')
            ->findAll($limit);
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK IF LOCK IS STILL ACTIVE
    |--------------------------------------------------------------------------
    */
    public function isLockLive(
        ?int $lockedBy,
        ?string $lockedAt
    ): bool {
        if (empty($lockedBy) || empty($lockedAt)) {
            return false;
        }

        $lockedTime = strtotime($lockedAt);

        if ($lockedTime === false) {
            return false;
        }

        return (time() - $lockedTime)
            <= self::LOCK_TTL_SECONDS;
    }

    /*
    |--------------------------------------------------------------------------
    | GET LOCK STATE
    |--------------------------------------------------------------------------
    */
    public function lockState(
        array $article,
        int $currentUserId
    ): array {
        $lockedBy = ! empty($article['locked_by'])
            ? (int) $article['locked_by']
            : null;

        $lockedAt = $article['locked_at'] ?? null;

        $live = $this->isLockLive(
            $lockedBy,
            $lockedAt
        );

        return [
            'locked_by' => $live
                ? $lockedBy
                : null,

            'locked_by_self' => $live
                && $lockedBy === $currentUserId,

            'is_locked' => $live
                && $lockedBy !== $currentUserId,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ACQUIRE EDIT LOCK
    |--------------------------------------------------------------------------
    |
    | Only SUBMITTED and EDITING articles can acquire an edit lock.
    |
    | COMPLETED and ARCHIVED articles can never return to EDITING.
    |
    */
    public function tryAcquireLock(
        string $id,
        int $userId
    ): bool {
        $article = $this->find($id);

        if (! $article) {
            return false;
        }

        $status = strtolower(
            trim((string) ($article['status'] ?? ''))
        );

        /*
        |--------------------------------------------------------------------------
        | ONLY ALLOW EDITING OF SUBMITTED OR EDITING ARTICLES
        |--------------------------------------------------------------------------
        */
        if (! in_array(
            $status,
            ['submitted', 'editing'],
            true
        )) {
            return false;
        }

        $state = $this->lockState(
            $article,
            $userId
        );

        /*
        |--------------------------------------------------------------------------
        | ARTICLE IS LOCKED BY ANOTHER EDITOR
        |--------------------------------------------------------------------------
        */
        if ($state['is_locked']) {
            return false;
        }

        /*
        |--------------------------------------------------------------------------
        | ACQUIRE LOCK AND SET STATUS TO EDITING
        |--------------------------------------------------------------------------
        */
        return (bool) $this->update(
            $id,
            [
                'locked_by' => $userId,
                'locked_at' => date('Y-m-d H:i:s'),
                'status'    => 'editing',
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | REFRESH EDIT LOCK
    |--------------------------------------------------------------------------
    */
    public function refreshLock(
        string $id,
        int $userId
    ): bool {
        $article = $this->find($id);

        if (! $article) {
            return false;
        }

        $state = $this->lockState(
            $article,
            $userId
        );

        /*
        |--------------------------------------------------------------------------
        | ONLY THE CURRENT LOCK OWNER CAN REFRESH THE LOCK
        |--------------------------------------------------------------------------
        */
        if (! $state['locked_by_self']) {
            return false;
        }

        return (bool) $this->update(
            $id,
            [
                'locked_at' => date('Y-m-d H:i:s'),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELEASE EDIT LOCK
    |--------------------------------------------------------------------------
    */
    public function releaseLock(
        string $id,
        int $userId
    ): bool {
        $article = $this->find($id);

        if (! $article) {
            return true;
        }

        /*
        |--------------------------------------------------------------------------
        | DO NOT RELEASE ANOTHER USER'S LOCK
        |--------------------------------------------------------------------------
        */
        if (
            ! empty($article['locked_by'])
            && (int) $article['locked_by'] !== $userId
        ) {
            return false;
        }

        return (bool) $this->update(
            $id,
            [
                'locked_by' => null,
                'locked_at' => null,
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ATTACH LOCK INFORMATION
    |--------------------------------------------------------------------------
    |
    | Adds:
    | - is_locked
    | - locked_by_self
    | - locked_by_name
    |
    */
    public function attachLockInfo(
        array $articles,
        int $currentUserId
    ): array {
        if (empty($articles)) {
            return $articles;
        }

        /*
        |--------------------------------------------------------------------------
        | GET UNIQUE LOCKED USER IDS
        |--------------------------------------------------------------------------
        */
        $lockedIds = [];

        foreach ($articles as $article) {
            if (! empty($article['locked_by'])) {
                $lockedIds[] = (int) $article['locked_by'];
            }
        }

        $lockedIds = array_values(
            array_unique(
                array_filter($lockedIds)
            )
        );

        $names = [];

        /*
        |--------------------------------------------------------------------------
        | GET USER NAMES
        |--------------------------------------------------------------------------
        */
        if (! empty($lockedIds)) {
            $users = (new UserModel())
                ->whereIn('user_id', $lockedIds)
                ->findAll();

            foreach ($users as $user) {
                $userId = (int) ($user['user_id'] ?? 0);

                if ($userId > 0) {
                    $names[$userId] = trim(
                        ($user['first_name'] ?? '')
                        . ' '
                        . ($user['last_name'] ?? '')
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | ATTACH LOCK DETAILS
        |--------------------------------------------------------------------------
        */
        foreach ($articles as &$article) {
            $state = $this->lockState(
                $article,
                $currentUserId
            );

            $article['is_locked'] = $state['is_locked'];

            $article['locked_by_self'] =
                $state['locked_by_self'];

            $article['locked_by_name'] =
                $state['is_locked']
                    ? (
                        $names[$state['locked_by']]
                        ?? 'Another editor'
                    )
                    : null;
        }

        unset($article);

        return $articles;
    }
}