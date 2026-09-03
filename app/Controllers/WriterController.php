<?php

namespace App\Controllers;

use App\Libraries\Permissions;
use App\Models\ArticleModel;
use App\Models\UserModel;

class WriterController extends BaseController
{
    protected ArticleModel $articleModel;
    protected UserModel $userModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->userModel    = new UserModel();
    }

    /**
     * =========================================================
     * WRITER DASHBOARD
     * =========================================================
     */
    public function index()
    {
        return view('UserDashboard', [
            'user' => $this->currentUser(),
        ]);
    }

    /**
     * =========================================================
     * DASHBOARD DATA
     * =========================================================
     *
     * Everything displayed here is scoped to the currently
     * logged-in writer.
     */
    public function dashboardData()
    {
        $userId = (int) session('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Unauthorized.',
                ]);
        }

        $today = date('Y-m-d');

        $yesterday = date(
            'Y-m-d',
            strtotime('-1 day')
        );

        /*
         * Helper for writer-scoped queries.
         */
        $base = static function (
            ArticleModel $model,
            int $userId
        ): ArticleModel {
            return (clone $model)
                ->where('created_by', $userId);
        };

        /*
         * -----------------------------------------------------
         * Statistics
         * -----------------------------------------------------
         */
        $todayCount = $base(
            $this->articleModel,
            $userId
        )
            ->where('DATE(created_at)', $today)
            ->countAllResults();

        $yesterdayCount = $base(
            $this->articleModel,
            $userId
        )
            ->where('DATE(created_at)', $yesterday)
            ->countAllResults();

        $total2025 = $base(
            $this->articleModel,
            $userId
        )
            ->where('YEAR(news_date)', 2025)
            ->countAllResults();

        $total2026 = $base(
            $this->articleModel,
            $userId
        )
            ->where('YEAR(news_date)', 2026)
            ->countAllResults();

        /*
         * -----------------------------------------------------
         * Writer information
         * -----------------------------------------------------
         */
        $me = $this->userModel->find($userId);

        $activities = [];

        if ($me) {
            $firstName = (string) ($me['first_name'] ?? '');
            $lastName  = (string) ($me['last_name'] ?? '');

            $activities[] = [
                'initials' => strtoupper(
                    substr($firstName, 0, 1)
                    . substr($lastName, 0, 1)
                ),

                'name' => trim(
                    $firstName . ' ' . $lastName
                ),

                'role' => ucfirst(
                    strtolower(
                        (string) ($me['role'] ?? 'WRITER')
                    )
                ),
            ];
        }

        /*
         * -----------------------------------------------------
         * Recent articles
         * -----------------------------------------------------
         */
        $recentArticles = $base(
            $this->articleModel,
            $userId
        )
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

        /*
         * -----------------------------------------------------
         * Last seven days
         * -----------------------------------------------------
         */
        $weekLabels = [];
        $weekValues = [];
        $sparkline  = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = date(
                'Y-m-d',
                strtotime("-{$i} day")
            );

            $weekLabels[] = date(
                'D',
                strtotime($day)
            );

            $count = $base(
                $this->articleModel,
                $userId
            )
                ->where('DATE(created_at)', $day)
                ->countAllResults();

            $weekValues[] = $count;
            $sparkline[]  = $count;
        }

        /*
         * -----------------------------------------------------
         * Monthly statistics
         * -----------------------------------------------------
         */
        $sparkline2025 = [];
        $sparkline2026 = [];

        for ($month = 1; $month <= 12; $month++) {
            $sparkline2025[] = $base(
                $this->articleModel,
                $userId
            )
                ->where('YEAR(news_date)', 2025)
                ->where('MONTH(news_date)', $month)
                ->countAllResults();

            $sparkline2026[] = $base(
                $this->articleModel,
                $userId
            )
                ->where('YEAR(news_date)', 2026)
                ->where('MONTH(news_date)', $month)
                ->countAllResults();
        }

        /*
         * -----------------------------------------------------
         * TOP NEWS — breakdown by Category and by Sub-Category
         * -----------------------------------------------------
         *
         * Scoped to this writer's own articles only, same as
         * every other stat on this dashboard. "Category" is a
         * single value per article (simple GROUP BY); "Sub-
         * Category" is a JSON-encoded array (an article can
         * belong to more than one), so it's tallied in PHP
         * after decoding each row.
         */
        $categoryRows = $base(
            $this->articleModel,
            $userId
        )
            ->select('category, COUNT(*) as cnt')
            ->where('category IS NOT NULL')
            ->where('category !=', '')
            ->groupBy('category')
            ->orderBy('cnt', 'DESC')
            ->findAll();

        $categoryTotal = array_sum(array_column($categoryRows, 'cnt'));

        $topCategories = array_map(static function ($row) use ($categoryTotal) {
            return [
                'label'   => $row['category'],
                'count'   => (int) $row['cnt'],
                'percent' => $categoryTotal > 0
                    ? round(($row['cnt'] / $categoryTotal) * 100, 1)
                    : 0,
            ];
        }, $categoryRows);

        $subCategoryRows = $base(
            $this->articleModel,
            $userId
        )
            ->select('sub_category')
            ->where('sub_category IS NOT NULL')
            ->where('sub_category !=', '')
            ->findAll();

        $subCategoryCounts = [];

        foreach ($subCategoryRows as $row) {
            $values = $this->decodeSubCategoryValues($row['sub_category'] ?? null);

            foreach ($values as $value) {
                $subCategoryCounts[$value] = ($subCategoryCounts[$value] ?? 0) + 1;
            }
        }

        arsort($subCategoryCounts);

        $subCategoryTotal = array_sum($subCategoryCounts);

        $topSubCategories = [];

        foreach ($subCategoryCounts as $label => $count) {
            $topSubCategories[] = [
                'label'   => $label,
                'count'   => $count,
                'percent' => $subCategoryTotal > 0
                    ? round(($count / $subCategoryTotal) * 100, 1)
                    : 0,
            ];
        }

        /*
         * -----------------------------------------------------
         * TOP NEWS SOURCES — breakdown by outlet (from_name)
         * -----------------------------------------------------
         *
         * Scoped to this writer's own articles, same as the
         * rest of the dashboard. The articles table only stores
         * the program_name (e.g. "24 Oras"), not the outlet it
         * airs/publishes under, so this joins against
         * tblprogram to resolve each article's program back to
         * its from_name (e.g. "GMA Network") and tallies by
         * that. Ranked by article count, highest first, capped
         * to the top 10 so the leaderboard stays readable.
         */
        $programRows = $base(
            $this->articleModel,
            $userId
        )
            ->select('tblprogram.from_name as source_name, COUNT(*) as cnt')
            ->join('tblprogram', 'tblprogram.program_name = program', 'inner')
            ->where('program IS NOT NULL')
            ->where('program !=', '')
            ->where('tblprogram.from_name IS NOT NULL')
            ->where('tblprogram.from_name !=', '')
            ->groupBy('tblprogram.from_name')
            ->orderBy('cnt', 'DESC')
            ->limit(10)
            ->findAll();

        $topSources = array_map(static function ($row) {
            return [
                'label' => $row['source_name'],
                'count' => (int) $row['cnt'],
            ];
        }, $programRows);

        /*
         * -----------------------------------------------------
         * OVERALL SLANT — breakdown by sentiment
         * (Positive / Negative / Neutral)
         * -----------------------------------------------------
         *
         * Scoped to this writer's own articles. The articles
         * table stores the raw slant symbol from
         * tblslant.slant_name ("+", "-", "0"), not a readable
         * word, so each row is mapped to a display label
         * ("Positive"/"Negative"/"Neutral") via
         * mapSlantSymbol() before being tallied.
         */
        $slantRows = $base(
            $this->articleModel,
            $userId
        )
            ->select('slant, COUNT(*) as cnt')
            ->where('slant IS NOT NULL')
            ->where('slant !=', '')
            ->groupBy('slant')
            ->findAll();

        $slantTotal = array_sum(array_column($slantRows, 'cnt'));
        $slantTally = [];

        foreach ($slantRows as $row) {
            $label = $this->mapSlantSymbol($row['slant'])['label'];
            $slantTally[$label] = ($slantTally[$label] ?? 0) + (int) $row['cnt'];
        }

        arsort($slantTally);

        $overallSlant = [];

        foreach ($slantTally as $label => $count) {
            $overallSlant[] = [
                'label'   => $label,
                'count'   => $count,
                'percent' => $slantTotal > 0
                    ? round(($count / $slantTotal) * 100, 1)
                    : 0,
            ];
        }

        /*
         * -----------------------------------------------------
         * TOP NEWS SOURCES BY SENTIMENT
         * (Program, split by slant)
         * -----------------------------------------------------
         *
         * Scoped to this writer's own articles. Same source
         * resolution as "Top News Sources" above (program ->
         * tblprogram.from_name), grouped together with the
         * article's raw slant symbol, which is then mapped via
         * mapSlantSymbol() into the positive/neutral/negative
         * buckets. Ranked by total article count, highest
         * first, capped to the top 10.
         */
        $sourceSlantRows = $base(
            $this->articleModel,
            $userId
        )
            ->select('tblprogram.from_name as source_name, slant, COUNT(*) as cnt')
            ->join('tblprogram', 'tblprogram.program_name = program', 'inner')
            ->where('program IS NOT NULL')
            ->where('program !=', '')
            ->where('tblprogram.from_name IS NOT NULL')
            ->where('tblprogram.from_name !=', '')
            ->where('slant IS NOT NULL')
            ->where('slant !=', '')
            ->groupBy(['tblprogram.from_name', 'slant'])
            ->findAll();

        $sourceSlantMap = [];

        foreach ($sourceSlantRows as $row) {
            $name = $row['source_name'];

            if (! isset($sourceSlantMap[$name])) {
                $sourceSlantMap[$name] = [
                    'positive' => 0,
                    'neutral'  => 0,
                    'negative' => 0,
                    'total'    => 0,
                ];
            }

            $slantKey = $this->mapSlantSymbol($row['slant'])['key'];
            $cnt      = (int) $row['cnt'];

            if (array_key_exists($slantKey, $sourceSlantMap[$name])) {
                $sourceSlantMap[$name][$slantKey] += $cnt;
            }

            $sourceSlantMap[$name]['total'] += $cnt;
        }

        uasort($sourceSlantMap, static fn ($a, $b) => $b['total'] <=> $a['total']);

        $topSourcesBySlant = [];

        foreach (array_slice($sourceSlantMap, 0, 10, true) as $label => $counts) {
            $topSourcesBySlant[] = array_merge(['label' => $label], $counts);
        }

        /*
         * -----------------------------------------------------
         * NEWS BY STATION
         * -----------------------------------------------------
         *
         * Scoped to this writer's own articles. Resolves each
         * article's station back to tblstation.station_name and
         * tallies by that. Ranked by article count, highest
         * first.
         */
        $stationRows = $base(
            $this->articleModel,
            $userId
        )
            ->select('tblstation.station_name as source_name, COUNT(*) as cnt')
            ->join('tblstation', 'tblstation.station_name = station', 'inner')
            ->where('station IS NOT NULL')
            ->where('station !=', '')
            ->where('tblstation.station_name IS NOT NULL')
            ->where('tblstation.station_name !=', '')
            ->groupBy('tblstation.station_name')
            ->orderBy('cnt', 'DESC')
            ->findAll();

        $newsByStation = array_map(static function ($row) {
            return [
                'label' => $row['source_name'],
                'count' => (int) $row['cnt'],
            ];
        }, $stationRows);

        /*
         * -----------------------------------------------------
         * Response
         * -----------------------------------------------------
         */
        return $this->response->setJSON([
            'status' => true,

            'user' => [
                'first_name' => session('first_name'),
                'last_name'  => session('last_name'),
            ],

            'statistics' => [
                'today'     => $todayCount,
                'yesterday' => $yesterdayCount,
                'total2025' => $total2025,
                'total2026' => $total2026,
            ],

            'activities' => $activities,

            'articles' => $recentArticles,

            'week' => [
                'labels' => $weekLabels,
                'values' => $weekValues,
                'label'  => 'Stats This Week',
            ],

            'sparkline' => $sparkline,

            'sparkline2025' => $sparkline2025,

            'sparkline2026' => $sparkline2026,

            'topNews' => [
                'categories'    => $topCategories,
                'subcategories' => $topSubCategories,
            ],

            'topSources'        => $topSources,
            'overallSlant'      => $overallSlant,
            'topSourcesBySlant' => $topSourcesBySlant,
            'newsByStation'     => $newsByStation,
        ]);
    }

    /**
     * =========================================================
     * DECODE SUB-CATEGORY VALUES
     * =========================================================
     *
     * The sub_category column is meant to hold a JSON-encoded
     * array (an article can belong to more than one), but in
     * practice it can show up in a few different shapes:
     *
     *   1. Proper JSON array:      ["A","B","C"]
     *   2. JSON array missing its  "A","B","C"
     *      outer brackets:
     *   3. A single plain value:   A
     *
     * Case 2 is NOT valid JSON on its own — json_decode() just
     * fails and returns null for it — so it's parsed as CSV
     * instead, which correctly keeps commas *inside* a quoted
     * value together (e.g. "Academic Freedom, Policies" stays
     * one value rather than splitting into two).
     */
    private function decodeSubCategoryValues(?string $raw): array
    {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return [];
        }

        // Case 1: proper JSON array.
        $decoded = json_decode($raw, true);

        // Case 2: JSON array with the outer [ ] stripped off —
        // re-wrap and try again.
        if (! is_array($decoded)) {
            $decoded = json_decode('[' . $raw . ']', true);
        }

        // Case 2 fallback: not valid JSON even after re-wrapping
        // (e.g. unescaped characters) — parse as a quoted CSV
        // row instead, which handles the same "A","B, C","D"
        // shape without choking on internal commas.
        if (! is_array($decoded)) {
            $csv = str_getcsv($raw);

            if (is_array($csv) && count($csv) > 1) {
                $decoded = $csv;
            }
        }

        // Case 3: give up trying to split it — treat the whole
        // string as a single value.
        if (! is_array($decoded)) {
            $decoded = [$raw];
        }

        return array_values(array_filter(array_map(
            static fn ($item) => trim((string) $item, " \t\n\r\0\x0B\""),
            $decoded
        ), static fn ($item) => $item !== ''));
    }

    /**
     * =========================================================
     * MAP SLANT SYMBOL
     * =========================================================
     *
     * tblslant stores each sentiment as a short symbol rather
     * than a readable word:
     *
     *   slant_id 1 -> "+"  (Positive)
     *   slant_id 2 -> "-"  (Negative)
     *   slant_id 3 -> "0"  (Neutral)
     *
     * tblarticle.slant stores that same raw symbol. This maps
     * a raw symbol to a display label (for the UI) and a
     * lowercase bucket key (for tallying), falling back to
     * "Neutral" for any unrecognized value so nothing silently
     * disappears from the totals.
     */
    private function mapSlantSymbol(?string $raw): array
    {
        $raw = trim((string) $raw);

        return match ($raw) {
            '+' => ['key' => 'positive', 'label' => 'Positive'],
            '-' => ['key' => 'negative', 'label' => 'Negative'],
            '0' => ['key' => 'neutral',  'label' => 'Neutral'],
            default => ['key' => 'neutral', 'label' => 'Neutral'],
        };
    }

    /**
     * =========================================================
     * WRITER ARTICLE LIST
     * =========================================================
     *
     * Only articles belonging to the currently logged-in
     * writer are returned.
     */
    public function data()
    {
        $userId = (int) session('user_id');

        $search = trim(
            (string) $this->request->getGet('search')
        );

        $page = max(
            1,
            (int) ($this->request->getGet('page') ?: 1)
        );

        $pageSize = 10;

        $query = $this->articleModel
            ->forWriter($userId);

        if ($search !== '') {
            $query
                ->groupStart()
                ->like('id', $search)
                ->orLike('summary', $search)
                ->orLike('news_date', $search)
                ->groupEnd();
        }

        $total = $query->countAllResults(false);

        $offset = ($page - 1) * $pageSize;

        $rows = $query->findAll(
            $pageSize,
            $offset
        );

        return $this->response->setJSON([
            'status'  => true,
            'data'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'perPage' => $pageSize,
        ]);
    }

    /**
     * =========================================================
     * DELETE ARTICLE
     * =========================================================
     *
     * A WRITER may only delete their own article while it is
     * still a DRAFT. Once submitted into the editorial pipeline,
     * it is no longer theirs to remove — only an EDITOR/ADMIN can
     * archive it via ArticleViewController::archive().
     */
    public function delete(string $id)
    {
        /*
         * Permission matrix is the first security boundary.
         */
        if (! Permissions::can('article', 'delete')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' => 'You do not have permission to delete articles.',
                ]);
        }

        $article = $this->articleModel->find($id);

        /*
         * Writer can only delete their own article.
         */
        if (
            ! $article
            || (int) $article['created_by'] !== (int) session('user_id')
        ) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Article not found.',
                ]);
        }

        /*
         * Only DRAFT articles are deletable by the writer. Once
         * submitted (or later archived), the article is immutable
         * from this endpoint.
         */
        if (
            strtolower((string) $article['status']) !== 'draft'
        ) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Submitted articles can no longer be deleted.',
                ]);
        }

        if (! $this->articleModel->delete($id)) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Unable to delete article.',
                ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' => 'Article deleted successfully.',
        ]);
    }
}