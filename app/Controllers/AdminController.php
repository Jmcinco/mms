<?php

namespace App\Controllers;

use App\Libraries\Permissions;
use App\Models\ArticleModel;
use App\Models\UserModel;

class AdminController extends BaseController
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
        $today     = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));

        $todayCount     = (clone $this->articleModel)->where('DATE(created_at)', $today)->countAllResults();
        $yesterdayCount = (clone $this->articleModel)->where('DATE(created_at)', $yesterday)->countAllResults();
        $total2025      = (clone $this->articleModel)->where('YEAR(news_date)', 2025)->countAllResults();
        $total2026      = (clone $this->articleModel)->where('YEAR(news_date)', 2026)->countAllResults();

        $activities = array_map(static function ($u) {
            return [
                'initials' => strtoupper(substr($u['first_name'], 0, 1) . substr($u['last_name'], 0, 1)),
                'name'     => $u['first_name'] . ' ' . $u['last_name'],
                'role'     => ucfirst(strtolower($u['role'])),
            ];
        }, $this->userModel->orderBy('user_id', 'DESC')->findAll(5));

        $recentArticles = $this->articleModel->orderBy('created_at', 'DESC')->findAll(5);

        $weekLabels = [];
        $weekValues = [];
        $sparkline  = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = date('Y-m-d', strtotime("-{$i} day"));
            $weekLabels[] = date('D', strtotime($day));
            $count = (clone $this->articleModel)->where('DATE(created_at)', $day)->countAllResults();
            $weekValues[] = $count;
            $sparkline[]  = $count;
        }

        $sparkline2025 = [];
        $sparkline2026 = [];
        for ($m = 1; $m <= 12; $m++) {
            $sparkline2025[] = (clone $this->articleModel)
                ->where('YEAR(news_date)', 2025)
                ->where('MONTH(news_date)', $m)
                ->countAllResults();

            $sparkline2026[] = (clone $this->articleModel)
                ->where('YEAR(news_date)', 2026)
                ->where('MONTH(news_date)', $m)
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
            'user'   => [
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
            'articles'   => $recentArticles,
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

    public function users()
    {
        return view('Users', [
            'user'  => $this->currentUser(),
            'tabs'  => $this->maintenanceTabList(),
            'perms' => Permissions::all('maintenance'),
        ]);
    }

    public function usersData()
    {
        $search = trim((string) $this->request->getGet('search'));

        $builder = $this->userModel->orderBy('user_id', 'ASC');

        if ($search !== '') {
            $builder->groupStart()
                    ->like('first_name', $search)
                    ->orLike('last_name', $search)
                    ->orLike('username', $search)
                    ->orLike('role', $search)
                    ->groupEnd();
        }

        $users = $builder->findAll();

        foreach ($users as &$user) {
            unset($user['password']);
        }
        unset($user);

        return $this->response->setJSON([
            'data'  => $users,
            'perms' => Permissions::all('users'),
        ]);
    }

    public function usersSave()
    {
        $id   = (int) $this->request->getPost('id');
        $perm = Permissions::all('users');

        if ($id === 0 && ! $perm['create']) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'    => false,
                'message'   => 'You do not have permission to create users.',
                'csrfToken' => csrf_hash(),
            ]);
        }
        if ($id > 0 && ! $perm['edit']) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'    => false,
                'message'   => 'You do not have permission to edit users.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $username = trim((string) $this->request->getPost('username'));
        $password = trim((string) $this->request->getPost('password'));
        $role     = strtoupper(trim((string) $this->request->getPost('role')));

        if ($this->userModel->usernameExists($username, $id ?: null)) {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Username already exists.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $data = [
            'first_name' => trim((string) $this->request->getPost('firstName')),
            'last_name'  => trim((string) $this->request->getPost('lastName')),
            'username'   => $username,
            'role'       => $role,
        ];

        if ($id > 0) {
            if ($password !== '') {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }

            if (! $this->userModel->update($id, $data)) {
                return $this->response->setJSON([
                    'status'    => false,
                    'message'   => 'Validation failed.',
                    'errors'    => $this->userModel->errors(),
                    'csrfToken' => csrf_hash(),
                ]);
            }

            return $this->response->setJSON([
                'status'    => true,
                'message'   => 'User updated successfully!',
                'csrfToken' => csrf_hash(),
            ]);
        }

        if ($password === '') {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Password is required.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        $data['status']   = 'ACTIVE';

        if (! $this->userModel->insert($data)) {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Validation failed.',
                'errors'    => $this->userModel->errors(),
                'csrfToken' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'User created successfully!',
            'csrfToken' => csrf_hash(),
        ]);
    }

    public function usersToggleStatus(int $id)
    {
        if (! Permissions::can('users', 'edit')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'    => false,
                'message'   => 'You do not have permission to change user status.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        if ($id === (int) session('user_id')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'    => false,
                'message'   => 'You cannot deactivate your own account.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $user = $this->userModel->find($id);

        if (! $user) {
            return $this->response->setStatusCode(404)->setJSON([
                'status'    => false,
                'message'   => 'User not found.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $newStatus = strtoupper($user['status']) === 'ACTIVE' ? 'INACTIVE' : 'ACTIVE';

        if (! $this->userModel->update($id, ['status' => $newStatus])) {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Unable to update status.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => true,
            'newStatus' => $newStatus,
            'message'   => "User marked as {$newStatus}.",
            'csrfToken' => csrf_hash(),
        ]);
    }
}