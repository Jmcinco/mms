<?php

namespace App\Controllers;

use App\Libraries\Permissions;
use App\Models\ArticleModel;
use App\Models\UserModel;

class EditorController extends BaseController
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
        $today = date('Y-m-d');

        $yesterday = date(
            'Y-m-d',
            strtotime('-1 day')
        );

        $todayCount = (clone $this->articleModel)
            ->where('DATE(created_at)', $today)
            ->countAllResults();

        $yesterdayCount = (clone $this->articleModel)
            ->where('DATE(created_at)', $yesterday)
            ->countAllResults();

        $total2025 = (clone $this->articleModel)
            ->where('YEAR(news_date)', 2025)
            ->countAllResults();

        $total2026 = (clone $this->articleModel)
            ->where('YEAR(news_date)', 2026)
            ->countAllResults();

        

        $users = $this->userModel
            ->orderBy('user_id', 'DESC')
            ->findAll(5);

        $activities = array_map(
            static function (array $user): array {
                $firstName = $user['first_name'] ?? '';
                $lastName  = $user['last_name'] ?? '';
                $role      = $user['role'] ?? '';

                return [
                    'initials' => strtoupper(
                        substr($firstName, 0, 1)
                        . substr($lastName, 0, 1)
                    ),

                    'name' => trim(
                        $firstName . ' ' . $lastName
                    ),

                    'role' => ucfirst(
                        strtolower($role)
                    ),
                ];
            },
            $users
        );

        

        $recentArticles = $this->articleModel
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

            $count = (clone $this->articleModel)
                ->where(
                    'DATE(created_at)',
                    $day
                )
                ->countAllResults();

            $weekValues[] = $count;
            $sparkline[]  = $count;
        }

        

        $sparkline2025 = [];
        $sparkline2026 = [];

        for ($month = 1; $month <= 12; $month++) {
            $sparkline2025[] =
                (clone $this->articleModel)
                    ->where(
                        'YEAR(news_date)',
                        2025
                    )
                    ->where(
                        'MONTH(news_date)',
                        $month
                    )
                    ->countAllResults();

            $sparkline2026[] =
                (clone $this->articleModel)
                    ->where(
                        'YEAR(news_date)',
                        2026
                    )
                    ->where(
                        'MONTH(news_date)',
                        $month
                    )
                    ->countAllResults();
        }

        

        $categoryRows = (clone $this->articleModel)
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

        $subCategoryRows = (clone $this->articleModel)
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

        

        $programRows = (clone $this->articleModel)
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

        

        $slantRows = (clone $this->articleModel)
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

        

        $sourceSlantRows = (clone $this->articleModel)
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

        

        $stationRows = (clone $this->articleModel)
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
                'first_name' =>
                    session('first_name'),

                'last_name' =>
                    session('last_name'),
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

            'sparkline'     => $sparkline,
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

    

    public function newsTypeData()
    {
        $scope     = $this->request->getGet('scope') ?: 'today';
        $monitorId = (int) $this->request->getGet('monitor_id');

        $query = (clone $this->articleModel)
            ->select('type, COUNT(*) as cnt')
            ->where('type IS NOT NULL')
            ->where('type !=', '');

        if ($scope === 'today') {
            $query->where('DATE(created_at)', date('Y-m-d'));
        }

        if ($monitorId > 0) {
            $query->where('created_by', $monitorId);
        }

        $rows = $query
            ->groupBy('type')
            ->orderBy('cnt', 'DESC')
            ->findAll();

        $total = array_sum(array_column($rows, 'cnt'));

        $newsType = array_map(static function ($row) use ($total) {
            return [
                'label'   => $row['type'],
                'count'   => (int) $row['cnt'],
                'percent' => $total > 0
                    ? round(($row['cnt'] / $total) * 100, 1)
                    : 0,
            ];
        }, $rows);

        return $this->response->setJSON([
            'status'   => true,
            'scope'    => $scope,
            'monitor'  => $monitorId ?: null,
            'newsType' => $newsType,
        ]);
    }

    public function monitorsList()
    {
        $rows = (clone $this->articleModel)
            ->select('tblusers.user_id, tblusers.first_name, tblusers.last_name')
            ->join('tblusers', 'tblusers.user_id = tblarticle.created_by', 'inner')
            ->whereIn('tblarticle.status', ['draft', 'submitted'])
            ->groupBy('tblusers.user_id')
            ->orderBy('tblusers.first_name', 'ASC')
            ->findAll();

        $monitors = array_map(static function ($row) {
            return [
                'id'   => (int) $row['user_id'],
                'name' => trim($row['first_name'] . ' ' . $row['last_name']),
            ];
        }, $rows);

        return $this->response->setJSON([
            'status'   => true,
            'monitors' => $monitors,
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
            (int) $this->request->getGet('page')
        );

        $pageSize = 10;

        $query = $this->articleModel
            ->forEditor();

        if ($search !== '') {
            $query
                ->groupStart()
                ->like('id', $search)
                ->orLike('summary', $search)
                ->orLike('news_date', $search)
                ->groupEnd();
        }

        $total = $query
            ->countAllResults(false);

        $rows = $query
            ->limit(
                $pageSize,
                ($page - 1) * $pageSize
            )
            ->findAll();

        

        
        $rows = $this->articleModel->attachLockInfo(
            $rows,
            $userId
        );

        return $this->response->setJSON([
            'data'  => $rows,
            'total' => $total,
            'page'  => $page,
        ]);
    }

    

    public function delete(string $id)
    {
        if (! Permissions::can(
            'article',
            'delete'
        )) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => false,
                    'message' =>
                        'You do not have permission to delete articles.',
                ]);
        }

        $article = $this->articleModel
            ->find($id);

        if (! $article) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Article not found.',
                ]);
        }

        if (! $this->articleModel->delete($id)) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status'  => false,
                    'message' =>
                        'Unable to delete article.',
                ]);
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' =>
                'Article deleted successfully.',
        ]);
    }
}