<?php

namespace App\Controllers;

use App\Libraries\Permissions;
use App\Models\ArticleModel;

class ArchivedController extends BaseController
{
    protected ArticleModel $articleModel;
    protected const UNSET_TS = 946656000;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    public function index()
    {
        if (! Permissions::can('archived', 'view')) {
            return redirect()->to('/')->with('error', 'Unauthorized access.');
        }

        return view('ArchivedDashboard', array_merge([
            'user' => $this->currentUser(),
            'role' => session('role'),
        ], $this->filterOptions()));
    }

    public function data()
    {
        if (! Permissions::can('archived', 'view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Unauthorized access.',
            ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'data'   => $this->filteredQuery()->findAll(),
        ]);
    }

    public function averages()
    {
        if (! Permissions::can('archived', 'view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Unauthorized access.',
            ]);
        }

        $rows = $this->filteredQuery()->findAll();

        return $this->response->setJSON([
            'status'     => true,
            'monitoring' => $this->buildMonthlyBreakdown(
                $rows,
                userIdField: 'created_by',
                firstNameField: 'writer_first_name',
                lastNameField: 'writer_last_name',
                startField: 'entry_start',
                endField: 'entry_end'
            ),
            'archiving'  => $this->buildMonthlyBreakdown(
                $rows,
                userIdField: 'archived_by',
                firstNameField: 'editor_first_name',
                lastNameField: 'editor_last_name',
                startField: 'editing_start',
                endField: 'editing_end',
                startFallbackField: 'entry_end'
            ),
        ]);
    }

    protected function filterOptions(): array
    {
        return [
            'categories'    => (new \App\Models\CategoryModel())->orderBy('category_name', 'ASC')->findAll(),
            'subCategories' => (new \App\Models\SubCategoryModel())->orderBy('sub_category', 'ASC')->findAll(),
            'departments'   => (new \App\Models\DepartmentModel())->orderBy('department_name', 'ASC')->findAll(),
            'slants'        => (new \App\Models\SlantModel())->orderBy('slant_name', 'ASC')->findAll(),
            'types'         => (new \App\Models\TypeModel())->orderBy('type_name', 'ASC')->findAll(),
            'mediums'       => (new \App\Models\MediumModel())->orderBy('medium_name', 'ASC')->findAll(),
            'stations'      => (new \App\Models\StationModel())->orderBy('station_name', 'ASC')->findAll(),
            'programs'      => (new \App\Models\ProgramModel())->orderBy('program_name', 'ASC')->findAll(),
            'reporters'     => (new \App\Models\ReporterModel())->orderBy('reporter_name', 'ASC')->findAll(),
        ];
    }

protected function filteredQuery()
{
    $role  = strtoupper((string) session('role'));
    $scope = $role === 'WRITER' ? session('user_id') : null;

    $query = $this->articleModel
        ->select('tblarticle.*, writer.first_name as writer_first_name, writer.last_name as writer_last_name, editor.first_name as editor_first_name, editor.last_name as editor_last_name')
        ->join('tblusers as writer', 'writer.user_id = tblarticle.created_by', 'left')
        ->join('tblusers as editor', 'editor.user_id = tblarticle.archived_by', 'left')
        ->where('tblarticle.status', 'archived')
        ->orderBy('tblarticle.archived_at', 'DESC');

    if ($scope !== null) {
        $query->where('tblarticle.created_by', $scope);
    }

    $search      = $this->request->getGet('search');
    $dateFrom    = $this->request->getGet('date_from');
    $dateTo      = $this->request->getGet('date_to');
    $category    = $this->request->getGet('category');
    $subCategory = $this->request->getGet('subCategory');
    $department  = $this->request->getGet('department');
    $slant       = $this->request->getGet('slant');
    $type        = $this->request->getGet('type');
    $medium      = $this->request->getGet('medium');
    $station     = $this->request->getGet('station');
    $program     = $this->request->getGet('program');
    $reporter    = $this->request->getGet('reporter');

    if (! empty($search)) {
        $query->groupStart()
              ->like('id', $search)
              ->orLike('summary', $search)
              ->groupEnd();
    }
    if (! empty($dateFrom) && ! empty($dateTo)) {
        $query->where('news_date >=', $dateFrom)->where('news_date <=', $dateTo);
    }

    // Single-value columns: exact match.
    if (! empty($category)) $query->where('category', $category);
    if (! empty($slant))    $query->where('slant', $slant);
    if (! empty($type))     $query->where('type', $type);
    if (! empty($medium))   $query->where('medium', $medium);
    if (! empty($station))  $query->where('station', $station);
    if (! empty($program))  $query->where('program', $program);

    // Multi-select-at-creation columns: exact element match, not substring,
    // so filtering "DOH" never falsely matches "DOH-ARTA" etc.
    $this->filterInCommaList($query, 'reporter', $reporter);
    $this->filterInCommaList($query, 'gov_offices', $department);

    // sub_category is JSON-encoded, so it needs JSON-aware matching
    // rather than FIND_IN_SET (which expects plain comma values).
    $this->filterInJsonList($query, 'sub_category', $subCategory);

    return $query;
}
    /**
     * Matches rows where a comma-separated column contains $value as an
     * exact element (not a substring) — e.g. reporter = "Atom Araullo,Sample Reporter 1"
     * matches filter "Atom Araullo" but not "Ana" or "Araullo".
     */
 protected function filterInCommaList($query, string $column, ?string $value): void
{
    if (empty($value)) {
        return;
    }

    $escaped = $this->articleModel->db->escape($value);
    $query->where("FIND_IN_SET({$escaped}, {$column}) >", 0);
}


    /**
     * Matches rows where a JSON-encoded array column (e.g. '["#Tag1","#Tag2"]')
     * contains $value as one of its elements. Covers both a properly
     * JSON-encoded array and a bare comma list saved without brackets,
     * since existing data may be in either shape.
     */
protected function filterInJsonList($query, string $column, ?string $value): void
{
    if (empty($value)) {
        return;
    }

    $quoted = str_replace(['\\', '"'], ['\\\\', '\\"'], $value);

    $query->groupStart()
          ->like($column, '"' . $quoted . '"')
          ->orLike($column, $value)
          ->groupEnd();
}

protected function buildMonthlyBreakdown(
    array $rows,
    string $userIdField,
    string $firstNameField,
    string $lastNameField,
    string $startField,
    string $endField,
    ?string $startFallbackField = null
): array {
    $buckets = [];

    foreach ($rows as $row) {
        $userId = (int) ($row[$userIdField] ?? 0);

        if ($userId <= 0) {
            continue;
        }

        /*
         * Group by the actual work month based on the start timestamp.
         * Falls back to news_date when necessary.
         */
        $month = substr((string) ($row['news_date'] ?? ''), 0, 7);

        $start = (int) ($row[$startField] ?? 0);

        /*
         * For Archiving:
         * If editing_start was not captured, use entry_end.
         */
        if ($start <= self::UNSET_TS && $startFallbackField !== null) {
            $start = (int) ($row[$startFallbackField] ?? 0);
        }

        $end = (int) ($row[$endField] ?? 0);

        /*
         * Ignore invalid timestamps.
         */
        if (
            $start <= self::UNSET_TS ||
            $end <= self::UNSET_TS ||
            $end <= $start
        ) {
            continue;
        }

        if (strlen($month) < 7) {
            continue;
        }

        $key = $month . '|' . $userId;

        $buckets[$key] ??= [
            'month'      => $month,
            'first_name' => $row[$firstNameField] ?? '',
            'last_name'  => $row[$lastNameField] ?? '',
            'starts'     => [],
            'ends'       => [],
            'durations'  => [],
        ];

        /*
         * START and END are clock times.
         */
        $buckets[$key]['starts'][] = $this->decimalHour($start);
        $buckets[$key]['ends'][]   = $this->decimalHour($end);

        /*
         * IMPORTANT:
         * Calculate the actual duration per record.
         */
        $buckets[$key]['durations'][] = $end - $start;
    }

    $byMonth = [];

    foreach ($buckets as $bucket) {
        $count = count($bucket['durations']);

        if ($count === 0) {
            continue;
        }

        /*
         * Average clock start/end.
         * Do not round until the final output.
         */
        $avgStart = array_sum($bucket['starts'])
            / count($bucket['starts']);

        $avgEnd = array_sum($bucket['ends'])
            / count($bucket['ends']);

        /*
         * CORRECT AVERAGE:
         * Average the actual durations in seconds.
         */
        $avgDurationSeconds = array_sum($bucket['durations'])
            / $count;

        /*
         * Convert seconds to decimal hours.
         *
         * Example:
         * 90 seconds = 0.025 hours
         */
        $avgDurationHours = $avgDurationSeconds / 3600;

        $byMonth[$bucket['month']][] = [
            'count'   => $count,
            'name'    => trim(
                ($bucket['first_name'] ?? '') . ' ' .
                ($bucket['last_name'] ?? '')
            ) ?: 'Unknown',

            'start'   => round($avgStart, 2),
            'end'     => round($avgEnd, 2),

            /*
             * Correct average duration.
             */
            'average' => round($avgDurationHours, 4),

            /*
             * Optional: useful if you want HH:MM:SS display.
             */
            'average_seconds' => (int) round($avgDurationSeconds),
        ];
    }

    ksort($byMonth);

    foreach ($byMonth as &$people) {
        usort(
            $people,
            static fn ($a, $b) => strcasecmp($a['name'], $b['name'])
        );
    }

    unset($people);

    return $byMonth;
}

protected function decimalHour(int $unixTs): float
{
    $timezone = new \DateTimeZone('Asia/Manila');

    $dateTime = (new \DateTimeImmutable('@' . $unixTs))
        ->setTimezone($timezone);

    return (int) $dateTime->format('G')
        + ((int) $dateTime->format('i') / 60)
        + ((int) $dateTime->format('s') / 3600);
}
}