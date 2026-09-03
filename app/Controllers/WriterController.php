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

    public function index()
    {
        return view('UserDashboard', [
            'user' => $this->currentUser(),
        ]);
    }

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

        $base = static function (
            ArticleModel $model,
            int $userId
        ): ArticleModel {
            return (clone $model)
                ->where('created_by', $userId);
        };

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

        $recentArticles = $base(
            $this->articleModel,
            $userId
        )
            ->orderBy('created_at', 'DESC')
            ->findAll(5);

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

    private function decodeSubCategoryValues(?string $raw): array
    {
        $raw = trim((string) $raw);

        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);

        if (! is_array($decoded)) {
            $decoded = json_decode('[' . $raw . ']', true);
        }

        if (! is_array($decoded)) {
            $csv = str_getcsv($raw);

            if (is_array($csv) && count($csv) > 1) {
                $decoded = $csv;
            }
        }

        if (! is_array($decoded)) {
            $decoded = [$raw];
        }

        return array_values(array_filter(array_map(
            static fn ($item) => trim((string) $item, " \t\n\r\0\x0B\""),
            $decoded
        ), static fn ($item) => $item !== ''));
    }

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

    public function delete(string $id)
    {

        if (! Permissions::can('article', 'delete')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' => 'You do not have permission to delete articles.',
                ]);
        }

        $article = $this->articleModel->find($id);

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
