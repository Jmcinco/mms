<?php

namespace App\Controllers;

use App\Libraries\Permissions;
use App\Models\ArticleModel;
use App\Models\CategoryModel;
use App\Models\SubCategoryModel;
use App\Models\DepartmentModel;
use App\Models\SlantModel;
use App\Models\TypeModel;
use App\Models\MediumModel;
use App\Models\StationModel;
use App\Models\ProgramModel;
use App\Models\ReporterModel;

class ArticleWriterController extends BaseController
{
    protected ArticleModel $articleModel;
    protected CategoryModel $categoryModel;
    protected SubCategoryModel $subCategoryModel;
    protected DepartmentModel $departmentModel;
    protected SlantModel $slantModel;
    protected TypeModel $typeModel;
    protected MediumModel $mediumModel;
    protected StationModel $stationModel;
    protected ProgramModel $programModel;
    protected ReporterModel $reporterModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
        $this->categoryModel = new CategoryModel();
        $this->subCategoryModel = new SubCategoryModel();
        $this->departmentModel = new DepartmentModel();
        $this->slantModel = new SlantModel();
        $this->typeModel = new TypeModel();
        $this->mediumModel = new MediumModel();
        $this->stationModel = new StationModel();
        $this->programModel = new ProgramModel();
        $this->reporterModel = new ReporterModel();
    }

    public function index()
    {
        return view('NewsArticle', [
            'user' => $this->currentUser(),
            'role' => strtoupper((string) session('role')),
        ]);
    }

    public function data()
    {
        $role = strtoupper(trim((string) session('role')));
        $userId = (int) session('user_id');

        if ($userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON([
                    'status' => false,
                    'message' => 'Unauthorized.',
                ]);
        }

        $search = trim(
            (string) $this->request->getGet('search')
        );

        $page = max(
            1,
            (int) ($this->request->getGet('page') ?: 1)
        );

        $perPage = max(
            1,
            min(
                100,
                (int) ($this->request->getGet('perPage') ?: 10)
            )
        );

        switch ($role) {
            case 'WRITER':
                $builder = $this->articleModel
                    ->where('created_by', $userId)
                    ->whereIn('status', [
                        'draft',
                        'submitted',
                        'editing',
                        'completed',
                        'archived',
                    ])
                    ->orderBy('created_at', 'DESC');
                break;

            case 'EDITOR':
                $builder = $this->articleModel
                    ->whereIn('status', [
                        'draft',
                        'submitted',
                        'editing',
                        'completed',
                    ])
                    ->orderBy('created_at', 'DESC');
                break;

            case 'ADMIN':
                $builder = $this->articleModel
                    ->orderBy('created_at', 'DESC');
                break;

            default:
                return $this->response
                    ->setStatusCode(403)
                    ->setJSON([
                        'status' => false,
                        'message' => 'Unauthorized access.',
                    ]);
        }

        if ($search !== '') {
            $builder
                ->groupStart()
                ->like('id', $search)
                ->orLike('content', $search)
                ->orLike('summary', $search)
                ->orLike('news_date', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);

        $offset = ($page - 1) * $perPage;

        $articles = $builder->findAll(
            $perPage,
            $offset
        );

        $articles = $this->articleModel->attachLockInfo(
            $articles,
            $userId
        );

        return $this->response->setJSON([
            'status' => true,
            'data' => $articles,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }

    public function create()
    {
        $role = strtoupper(
            trim((string) session('role'))
        );

        if ($role !== 'WRITER') {
            return redirect()
                ->to('/')
                ->with(
                    'error',
                    'Unauthorized access.'
                );
        }

        session()->set(
            'article_entry_start',
            (string) time()
        );

        $entryStartedAt = (int) session(
            'article_entry_start'
        ) * 1000;

        return view('CreateArticle', [
            'article' => null,

            'categories' => $this->categoryModel
                ->findAll(),

            'subCategories' => $this->subCategoryModel
                ->findAll(),

            'departments' => $this->departmentModel
                ->findAll(),

            'slants' => $this->slantModel
                ->findAll(),

            'types' => $this->typeModel
                ->findAll(),

            'mediums' => $this->mediumModel
                ->findAll(),

            'stations' => $this->stationModel
                ->findAll(),

            'programs' => $this->programModel
                ->findAll(),

            'reporters' => $this->reporterModel
                ->findAll(),

            'entryStartedAt' => $entryStartedAt,
        ]);
    }

public function save()
{
    try {
        $content = trim(
            (string) $this->request->getPost('content')
        );

        if (
            $content === ''
            || $content === '<p><br></p>'
        ) {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Content cannot be empty.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $category = trim(
            (string) $this->request->getPost('category')
        );

        if ($category === '') {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select a Category.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $subCategories =
            $this->request->getPost('subCategory');

        if (!is_array($subCategories) || empty($subCategories)) {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select at least one Sub-Category.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $governmentOffices =
            $this->request->getPost('govOffices');

        if (!is_array($governmentOffices) || empty($governmentOffices)) {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select at least one Government Office.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $slant = trim(
            (string) $this->request->getPost('slant')
        );

        if ($slant === '') {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select a Slant.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $type = trim(
            (string) $this->request->getPost('type')
        );

        if ($type === '') {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select a Type.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $medium = trim(
            (string) $this->request->getPost('medium')
        );

        if ($medium === '') {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select a Medium.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $station = trim(
            (string) $this->request->getPost('station')
        );

        if ($station === '') {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select a Station.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $program = trim(
            (string) $this->request->getPost('program')
        );

        if ($program === '') {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select a Program.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $reporters =
            $this->request->getPost('reporter');

        if (!is_array($reporters) || empty($reporters)) {
            return $this->response->setStatusCode(422)
                ->setJSON([
                    'status' => false,
                    'message' => 'Please select at least one Anchor/Reporter.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        if (!is_array($subCategories)) {
            $subCategories = [];
        }

        if (!is_array($governmentOffices)) {
            $governmentOffices = [];
        }

        if (!is_array($reporters)) {
            $reporters = [];
        }

        $entryStart = trim(
            (string) $this->request->getPost('entry_start')
        );

        if ($entryStart === '' || ! is_numeric($entryStart)) {
            $entryStart = (string) time();
        } else {
            $entryStart = (string) (int) $entryStart;
        }

        $articleId = trim(
            (string) $this->request->getPost('id')
        );

        $plainText = trim(
            strip_tags($content)
        );

        $data = [
            'news_date' => date('Y-m-d'),

            'entry_start' => $entryStart,

            'entry_end' => (string) time(),

            'content' => $content,

            'summary' =>
                $this->articleModel->buildSummary(
                    $plainText
                ),

            'category' =>
                $this->request->getPost('category')
                ?: null,

            'sub_category' =>
                !empty($subCategories)
                    ? json_encode(
                        array_values($subCategories)
                    )
                    : null,

            'gov_offices' =>
                !empty($governmentOffices)
                    ? json_encode(
                        array_values($governmentOffices)
                    )
                    : null,

            'remarks' =>
                $this->request->getPost('remarks')
                ?: null,

            'slant' =>
                $this->request->getPost('slant')
                ?: null,

            'type' =>
                $this->request->getPost('type')
                ?: null,

            'medium' =>
                $this->request->getPost('medium')
                ?: null,

            'station' =>
                $this->request->getPost('station')
                ?: null,

            'program' =>
                $this->request->getPost('program')
                ?: null,

            'reporter' =>
                !empty($reporters)
                    ? json_encode(
                        array_values($reporters)
                    )
                    : null,

            'alert' =>
                $this->request->getPost('alert') === 'Yes'
                    ? 'Yes'
                    : 'No',

            'status' => 'draft',
        ];

        if ($articleId !== '') {

            $existing =
                $this->articleModel->find($articleId);

            if (!$existing) {
                return $this->response
                    ->setStatusCode(404)
                    ->setJSON([
                        'status' => false,
                        'message' => 'Article not found.',
                        'csrfToken' => csrf_hash(),
                    ]);
            }

            $data['entry_start'] =
                $existing['entry_start']
                ?: $entryStart;

            $this->articleModel->update(
                $articleId,
                $data
            );

            return $this->response->setJSON([
                'status' => true,
                'message' => 'Article updated successfully!',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $data['id'] =
            $this->articleModel->generateId();

        $data['created_by'] =
            (int) session('user_id');

        $this->articleModel->insert($data);

        return $this->response->setJSON([
            'status' => true,
            'message' => 'Article submitted successfully!',
            'csrfToken' => csrf_hash(),
        ]);

    } catch (\Throwable $e) {

        log_message(
            'error',
            'Article save error: {message}',
            [
                'message' => $e->getMessage(),
            ]
        );

        return $this->response
            ->setStatusCode(500)
            ->setJSON([
                'status' => false,
                'message' =>
                    'Unable to save article. '
                    . 'Please try again.',
                'csrfToken' => csrf_hash(),
            ]);
    }
}
    public function delete(string $id)
    {
        if (
            ! Permissions::can(
                'article',
                'delete'
            )
        ) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => false,
                    'message' =>
                        'You do not have permission to delete articles.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $article = $this->articleModel
            ->find($id);

        if (
            ! $article
            || (int) (
                $article['created_by'] ?? 0
            ) !== (int) session('user_id')
        ) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => false,
                    'message' =>
                        'You do not have permission to delete this article.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        if (
            strtoupper(
                (string) (
                    $article['status'] ?? ''
                )
            ) !== 'DRAFT'
        ) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status' => false,
                    'message' =>
                        'Only draft articles can be deleted.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        if (
            ! $this->articleModel
                ->delete($id)
        ) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'status' => false,
                    'message' =>
                        'Unable to delete article.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        return $this->response->setJSON([
            'status' => true,
            'message' =>
                'Draft deleted successfully.',
            'csrfToken' => csrf_hash(),
        ]);
    }

    private function cleanMultipleValues(
        mixed $values
    ): array {
        if (
            $values === null
            || $values === ''
        ) {
            return [];
        }

        if (! is_array($values)) {
            $values = [$values];
        }

        $values = array_map(
            static fn ($value): string =>
                trim((string) $value),
            $values
        );

        $values = array_filter(
            $values,
            static fn (string $value): bool =>
                $value !== ''
        );

        return array_values(
            array_unique($values)
        );
    }
}