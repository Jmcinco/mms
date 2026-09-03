<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

use App\Models\CategoryModel;
use App\Models\DepartmentModel;
use App\Models\MediumModel;
use App\Models\ProgramModel;
use App\Models\ReporterModel;
use App\Models\SlantModel;
use App\Models\StationModel;
use App\Models\SubCategoryModel;
use App\Models\TypeModel;

abstract class BaseController extends Controller
{
    protected array $maintenanceModules = [

        'slants' => [
            'model'      => SlantModel::class,
            'primaryKey' => 'slant_id',
            'nameField'  => 'slant_name',
            'label'      => 'Slant',
        ],

        'categories' => [
            'model'      => CategoryModel::class,
            'primaryKey' => 'cat_id',
            'nameField'  => 'category_name',
            'label'      => 'Category',
        ],

        'subcategories' => [
            'model'      => SubCategoryModel::class,
            'primaryKey' => 'sub_id',
            'nameField'  => 'sub_category',
            'label'      => 'Sub-Category',
        ],

        'departments' => [
            'model'      => DepartmentModel::class,
            'primaryKey' => 'department_id',
            'nameField'  => 'department_name',
            'label'      => 'Government Offices',
        ],

        'types' => [
            'model'      => TypeModel::class,
            'primaryKey' => 'type_id',
            'nameField'  => 'type_name',
            'label'      => 'Type',
        ],

        'mediums' => [
            'model'      => MediumModel::class,
            'primaryKey' => 'medium_id',
            'nameField'  => 'medium_name',
            'label'      => 'Medium',
        ],

        'programs' => [
            'model'      => ProgramModel::class,
            'primaryKey' => 'program_id',
            'fields'     => [
                [
                    'key'   => 'from_name',
                    'label' => 'From',
                ],
                [
                    'key'   => 'program_name',
                    'label' => 'Program',
                ],
            ],
            'label' => 'Program',
        ],

        'stations' => [
            'model'      => StationModel::class,
            'primaryKey' => 'station_id',
            'fields'     => [
                [
                    'key'   => 'station_from',
                    'label' => 'From',
                ],
                [
                    'key'   => 'station_name',
                    'label' => 'Station',
                ],
            ],
            'label' => 'Station',
        ],

        'reporters' => [
            'model'      => ReporterModel::class,
            'primaryKey' => 'reporter_id',
            'nameField'  => 'reporter_name',
            'label'      => 'Reporter',
        ],
    ];

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController(
            $request,
            $response,
            $logger
        );
    }

    protected function maintenanceTabList(): array
    {
        $list = [];

        foreach ($this->maintenanceModules as $key => $meta) {
            $list[$key] = $meta['label'];
        }

        return $list;
    }

    protected function moduleFields(array $module): array
    {
        if (
            isset($module['fields']) &&
            is_array($module['fields'])
        ) {
            return $module['fields'];
        }

        return [
            [
                'key'   => $module['nameField'],
                'label' => $module['label'] . ' Name',
            ],
        ];
    }

    protected function currentUser(): array
    {
        $firstName = trim(
            (string) session('first_name')
        );

        $lastName = trim(
            (string) session('last_name')
        );

        return [
            'firstName' => $firstName,
            'lastName'  => $lastName,

            'initials' => strtoupper(
                substr($firstName, 0, 1)
                . substr($lastName, 0, 1)
            ),
        ];
    }
}