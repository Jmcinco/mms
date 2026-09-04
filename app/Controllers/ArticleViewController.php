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
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class ArticleViewController extends BaseController
{
    protected ArticleModel $articleModel;
    protected UserModel $userModel;
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
        $this->articleModel     = new ArticleModel();
        $this->userModel        = new UserModel();
        $this->categoryModel    = new CategoryModel();
        $this->subCategoryModel = new SubCategoryModel();
        $this->departmentModel  = new DepartmentModel();
        $this->slantModel       = new SlantModel();
        $this->typeModel        = new TypeModel();
        $this->mediumModel      = new MediumModel();
        $this->stationModel     = new StationModel();
        $this->programModel     = new ProgramModel();
        $this->reporterModel    = new ReporterModel();
    }

    public function view(string $id)
    {
        if (! Permissions::can('article', 'view')) {
            return redirect()
                ->to(site_url('/'))
                ->with('error', 'Unauthorized access.');
        }

        $article = $this->articleModel->find($id);

        if (! $article) {
            throw PageNotFoundException::forPageNotFound('Article not found.');
        }

        $role       = strtoupper(trim((string) session('role')));
        $isWriter   = $role === 'WRITER';
        $isArchived = strtolower(trim((string) ($article['status'] ?? ''))) === 'archived';
        $userId     = (int) session('user_id');

        $canEditPermission = ! $isWriter
            && Permissions::can('article', 'edit')
            && ! $isArchived;

        $lockState = $this->articleModel->lockState($article, $userId);

        if (! $isWriter && $canEditPermission && ! $lockState['is_locked']) {
            if ($this->articleModel->tryAcquireLock($id, $userId)) {
                $article = $this->articleModel->find($id);
                $lockState = $this->articleModel->lockState($article, $userId);

                $this->articleModel->update($id, [
                    'editing_start' => (string) time(),
                ]);
            } else {
                $lockState['is_locked'] = true;
            }
        }

        $lockedByName = null;

        if ($lockState['is_locked'] && $lockState['locked_by']) {
            $lockedUser = $this->userModel->find($lockState['locked_by']);

            $lockedByName = $lockedUser
                ? (trim(
                    ($lockedUser['first_name'] ?? '')
                    . ' '
                    . ($lockedUser['last_name'] ?? '')
                ) ?: 'another editor')
                : 'another editor';
        }

        $canEdit = $canEditPermission && ! $lockState['is_locked'];

        return view('ViewArticle', [
            'user'         => $this->currentUser(),
            'article'      => $article,
            'canArchive'   => ! $isWriter
                && Permissions::can('article', 'archive')
                && ! $lockState['is_locked'],
            'canEdit'      => $canEdit,
            'isLocked'     => $lockState['is_locked'],
            'lockedByName' => $lockedByName,
            'readOnly'     => $isWriter,

            'categories' => $this->categoryModel
                ->orderBy('category_name', 'ASC')
                ->findAll(),

            'subCategories' => $this->subCategoryModel
                ->orderBy('sub_category', 'ASC')
                ->findAll(),

            'departments' => $this->departmentModel
                ->orderBy('department_name', 'ASC')
                ->findAll(),

            'slants' => $this->slantModel
                ->orderBy('slant_name', 'ASC')
                ->findAll(),

            'types' => $this->typeModel
                ->orderBy('type_name', 'ASC')
                ->findAll(),

            'mediums' => $this->mediumModel
                ->orderBy('medium_name', 'ASC')
                ->findAll(),

            'stations' => $this->stationModel
                ->orderBy('station_name', 'ASC')
                ->findAll(),

            'programs' => $this->programModel
                ->orderBy('program_name', 'ASC')
                ->findAll(),

            'reporters' => $this->reporterModel
                ->orderBy('reporter_name', 'ASC')
                ->findAll(),
        ]);
    }

    public function heartbeat(string $id)
    {
        $userId = (int) session('user_id');
        $ok = $this->articleModel->refreshLock($id, $userId);

        return $this->response->setJSON([
            'status'    => $ok,
            'csrfToken' => csrf_hash(),
        ]);
    }

    public function releaseLockAction(string $id)
    {
        $userId = (int) session('user_id');

        $this->articleModel->releaseLock($id, $userId);

        return $this->response->setJSON(['status' => true]);
    }

    public function update(string $id)
    {
        $role = strtoupper(trim((string) session('role')));

        if ($role === 'WRITER' || ! Permissions::can('article', 'edit')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'    => false,
                    'message'   => 'You do not have permission to edit this article.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $article = $this->articleModel->find($id);

        if (! $article) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'    => false,
                    'message'   => 'Article not found.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        if (strtolower(trim((string) ($article['status'] ?? ''))) === 'archived') {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'    => false,
                    'message'   => 'Archived articles can no longer be edited.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $userId = (int) session('user_id');

        if (! $this->articleModel->tryAcquireLock($id, $userId)) {
            $lockState = $this->articleModel->lockState($article, $userId);

            $lockedUser = $lockState['locked_by']
                ? $this->userModel->find($lockState['locked_by'])
                : null;

            $name = $lockedUser
                ? (trim(
                    ($lockedUser['first_name'] ?? '')
                    . ' '
                    . ($lockedUser['last_name'] ?? '')
                ) ?: 'another editor')
                : 'another editor';

            return $this->response
                ->setStatusCode(423)
                ->setJSON([
                    'status'    => false,
                    'message'   => "This article is currently being edited by {$name}.",
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $content = trim((string) $this->request->getPost('content'));

        if ($content === '' || $content === '<p><br></p>') {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Content cannot be empty.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $plainText = trim(strip_tags($content));
        $summary = $this->articleModel->buildSummary($plainText);

        $category = trim((string) $this->request->getPost('category'));
        $remarks  = trim((string) $this->request->getPost('remarks'));
        $slant    = trim((string) $this->request->getPost('slant'));
        $type     = trim((string) $this->request->getPost('type'));
        $medium   = trim((string) $this->request->getPost('medium'));
        $station  = trim((string) $this->request->getPost('station'));
        $program  = trim((string) $this->request->getPost('program'));
        $alert    = trim((string) $this->request->getPost('alert'));

        if ($alert === '') {
            $alert = 'No';
        }

        $subCategory = $this->normalizeMultiplePostValue('subCategory');
        $govOffices  = $this->normalizeMultiplePostValue('govOffices');
        $reporter    = $this->normalizeMultiplePostValue('reporter');

        $data = [
            'content'      => $content,
            'summary'      => $summary,
            'category'     => $category,
            'sub_category' => $subCategory,
            'gov_offices'  => $govOffices,
            'remarks'      => $remarks,
            'slant'        => $slant,
            'type'         => $type,
            'medium'       => $medium,
            'station'      => $station,
            'program'      => $program,
            'reporter'     => $reporter,
            'alert'        => $alert,
        ];

        if (! $this->articleModel->update($id, $data)) {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Validation failed.',
                'errors'    => $this->articleModel->errors(),
                'csrfToken' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => true,
            'id'        => $id,
            'message'   => 'Article updated successfully!',
            'csrfToken' => csrf_hash(),
            'data'      => [
                'category'     => $category,
                'sub_category' => $subCategory,
                'gov_offices'  => $govOffices,
                'reporter'     => $reporter,
            ],
        ]);
    }

    public function archive(string $id)
    {
        $role = strtoupper(trim((string) session('role')));

        if ($role === 'WRITER' || ! Permissions::can('article', 'archive')) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'    => false,
                    'message'   => 'You do not have permission to archive articles.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $article = $this->articleModel->find($id);

        if (! $article) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'    => false,
                    'message'   => 'Article not found.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        if (strtolower(trim((string) ($article['status'] ?? ''))) === 'archived') {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Article is already archived.',
                'csrfToken' => csrf_hash(),
            ]);
        }

        $userId = (int) session('user_id');
        $lockState = $this->articleModel->lockState($article, $userId);

        if ($lockState['is_locked']) {
            return $this->response
                ->setStatusCode(423)
                ->setJSON([
                    'status'    => false,
                    'message'   => 'This article is currently being edited by another editor.',
                    'csrfToken' => csrf_hash(),
                ]);
        }

        $now = (string) time();

        if (! $this->articleModel->update($id, [
            'status'        => 'archived',
            'archived_at'   => $now,
            'archived_by'   => $userId,
            'editing_end'   => $now,
            'locked_by'     => null,
            'locked_at'     => null,
        ])) {
            return $this->response->setJSON([
                'status'    => false,
                'message'   => 'Unable to archive article.',
                'errors'    => $this->articleModel->errors(),
                'csrfToken' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'status'    => true,
            'message'   => 'Article successfully archived!',
            'csrfToken' => csrf_hash(),
        ]);
    }

    protected function normalizeMultiplePostValue(string $field): string
    {
        $value = $this->request->getPost($field);

        if ($value === null) {
            return '';
        }

        if (is_array($value)) {
            $values = [];

            foreach ($value as $item) {
                $item = trim((string) $item);

                if ($item !== '') {
                    $values[] = $item;
                }
            }
        } else {
            $value = trim((string) $value);

            if ($value === '') {
                return '';
            }

            $values = explode(',', $value);
        }

        $clean = [];

        foreach ($values as $item) {
            $item = trim((string) $item);

            if ($item === '') {
                continue;
            }

            if (! in_array($item, $clean, true)) {
                $clean[] = $item;
            }
        }

        return implode(',', $clean);
    }
}