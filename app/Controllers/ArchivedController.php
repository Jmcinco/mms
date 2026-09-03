<?php

namespace App\Controllers;

use App\Libraries\Permissions;
use App\Models\ArticleModel;

class ArchivedController extends BaseController
{
    protected ArticleModel $articleModel;

    public function __construct()
    {
        $this->articleModel = new ArticleModel();
    }

    /** GET /archived — view-only for ADMIN, WRITER, and EDITOR alike */
    public function index()
    {
        if (! Permissions::can('archived', 'view')) {
            return redirect()->to('/')->with('error', 'Unauthorized access.');
        }

        return view('ArchivedDashboard', [
            'user' => $this->currentUser(),
            'role' => session('role'),
        ]);
    }

    /** GET /archived/data */
    public function data()
    {
        if (! Permissions::can('archived', 'view')) {
            return $this->response->setStatusCode(403)->setJSON([
                'status'  => false,
                'message' => 'Unauthorized access.',
            ]);
        }

        $role  = strtoupper((string) session('role'));
        // Writers only ever see their own archived work; Editor and Admin
        // see everything, matching your spec's "view-only, all articles"
        // for those two roles.
        $scope = $role === 'WRITER' ? session('user_id') : null;

        $query = $this->articleModel->archivedFor($scope);

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
        if (! empty($category))    $query->where('category', $category);
        if (! empty($subCategory)) $query->where('sub_category', $subCategory);
        if (! empty($department))  $query->where('gov_offices', $department);
        if (! empty($slant))       $query->where('slant', $slant);
        if (! empty($type))        $query->where('type', $type);
        if (! empty($medium))      $query->where('medium', $medium);
        if (! empty($station))     $query->where('station', $station);
        if (! empty($program))     $query->where('program', $program);
        if (! empty($reporter))    $query->where('reporter', $reporter);

        $rows = $query->findAll();

        return $this->response->setJSON(['data' => $rows]);
    }
}