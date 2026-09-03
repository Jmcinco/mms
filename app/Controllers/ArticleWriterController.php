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

    /**
     * =========================================================
     * NEWS ARTICLE LIST
     * =========================================================
     */
    public function index()
    {
        return view('NewsArticle', [
            'user' => $this->currentUser(),
            'role' => strtoupper((string) session('role')),
        ]);
    }

    /**
     * =========================================================
     * ARTICLE LIST DATA
     * =========================================================
     */
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
                    ->where('user_id', $userId)
                    ->whereIn('status', [
                        'DRAFT',
                        'SUBMITTED',
                        'EDITING',
                        'COMPLETED',
                    ])
                    ->orderBy('created_at', 'DESC');
                break;

            case 'EDITOR':
                $builder = $this->articleModel
                    ->whereIn('status', [
                        'SUBMITTED',
                        'EDITING',
                        'COMPLETED',
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

    /**
     * =========================================================
     * CREATE ARTICLE
     *
     * NEW
     *   ↓
     * Save Entry Start to Session
     *   ↓
     * Open Article Form
     * =========================================================
     */
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

        /*
         * Create entry start only once.
         */
        if (! session()->has('article_entry_start')) {
            session()->set(
                'article_entry_start',
                date('Y-m-d H:i:s')
            );
        }

        $entryStartedAt = session(
            'article_entry_start'
        );

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

    /**
     * =========================================================
     * SAVE ARTICLE
     * =========================================================
     */
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

        $entryStart = trim(
            (string) $this->request->getPost('entry_start')
        );

        /*
         * Defensive fallback.
         * If entry_start is not sent, use current datetime.
         */
        if ($entryStart === '') {
            $entryStart = date('Y-m-d H:i:s');
        }

        $articleId = trim(
            (string) $this->request->getPost('id')
        );

        $subCategories =
            $this->request->getPost('subCategory');

        $governmentOffices =
            $this->request->getPost('govOffices');

        $reporters =
            $this->request->getPost('reporter');

        if (!is_array($subCategories)) {
            $subCategories = [];
        }

        if (!is_array($governmentOffices)) {
            $governmentOffices = [];
        }

        if (!is_array($reporters)) {
            $reporters = [];
        }

        $plainText = trim(
            strip_tags($content)
        );

        $data = [
            'news_date' => date('Y-m-d'),

            /*
             * ENTRY START
             */
            'entry_start' => $entryStart,

            /*
             * ENTRY END
             * Article submission time.
             */
            'entry_end' => date('Y-m-d H:i:s'),

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

            'status' => 'submitted',
        ];

        /*
         * UPDATE EXISTING ARTICLE
         */
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

            /*
             * Preserve the original entry_start.
             */
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

        /*
         * CREATE NEW ARTICLE
         */
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

    /**
     * =========================================================
     * DELETE ARTICLE
     * WRITER CAN ONLY DELETE DRAFT
     * =========================================================
     */
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
                $article['user_id'] ?? 0
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

    /**
     * =========================================================
     * CLEAN MULTIPLE VALUES
     * =========================================================
     */
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