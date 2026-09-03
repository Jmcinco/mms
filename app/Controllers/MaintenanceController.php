<?php

namespace App\Controllers;

use App\Libraries\Permissions;
use CodeIgniter\Model;

class MaintenanceController extends BaseController
{

    private const BULK_MAX_FILE_SIZE = 5 * 1024 * 1024;
    private const BULK_MAX_ROWS      = 5000;

    public function index(string $tab = 'slants')
    {
        if (! isset($this->maintenanceModules[$tab])) {
            $tab = 'slants';
        }

        $module = $this->maintenanceModules[$tab];
        $perms  = Permissions::maintenance($tab);

        if (! $perms['view']) {
            return redirect()
                ->to('/')
                ->with(
                    'error',
                    'You do not have permission to view this module.'
                );
        }

        return view('Maintenance', [
            'user'    => $this->currentUser(),
            'tabs'    => $this->maintenanceTabList(),
            'tab'     => $tab,
            'tabMeta' => $module,
            'fields'  => $this->moduleFields($module),
            'perms'   => $perms,
        ]);
    }

    public function data(string $tab)
    {
        $module = $this->moduleOrFail($tab);

        if ($module === null) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON([
                    'status'  => false,
                    'message' => 'Unknown maintenance module.',
                ]);
        }

        $perms = Permissions::maintenance($tab);

        if (! $perms['view']) {
            return $this->response
                ->setStatusCode(403)
                ->setJSON([
                    'status'  => false,
                    'message' =>
                        'You do not have permission to view this module.',
                ]);
        }

        $fields = $this->moduleFields($module);

        $model = new $module['model']();

        $rows = $model
            ->orderBy($module['primaryKey'], 'ASC')
            ->findAll();

        $items = [];

        foreach ($rows as $row) {
            $item = [
                'id' => $row[$module['primaryKey']],
            ];

            foreach ($fields as $field) {
                $item[$field['key']] =
                    $row[$field['key']] ?? '';
            }

            $items[] = $item;
        }

        return $this->response->setJSON([
            'status' => true,
            'label'  => $module['label'],
            'fields' => $fields,
            'data'   => $items,
            'perms'  => $perms,
        ]);
    }

    public function save(string $tab)
    {
        $module = $this->moduleOrFail($tab);

        if ($module === null) {
            return $this->jsonError(
                'Unknown maintenance module.',
                404
            );
        }

        $perms = Permissions::maintenance($tab);

        $id = (int) $this->request->getPost('id');

        if ($id === 0 && ! $perms['create']) {
            return $this->jsonError(
                'You do not have permission to add '
                . strtolower($module['label'])
                . ' items.',
                403
            );
        }

        if ($id > 0 && ! $perms['edit']) {
            return $this->jsonError(
                'You do not have permission to edit '
                . strtolower($module['label'])
                . ' items.',
                403
            );
        }

        $fields = $this->moduleFields($module);

        $data = [];

        foreach ($fields as $field) {
            $value = trim(
                (string) $this->request->getPost(
                    $field['key']
                )
            );

            if ($value === '') {
                return $this->jsonError(
                    $field['label'] . ' is required.',
                    422
                );
            }

            $data[$field['key']] = $value;
        }

        $model = new $module['model']();

        if ($id > 0) {
            $existing = $model->find($id);

            if (! $existing) {
                return $this->jsonError(
                    $module['label'] . ' not found.',
                    404
                );
            }
        }

        $builder = $model->builder();

        foreach ($fields as $field) {
            $builder->where(
                'LOWER(' . $field['key'] . ')',
                strtolower($data[$field['key']])
            );
        }

        if ($id > 0) {
            $builder->where(
                $module['primaryKey'] . ' !=',
                $id
            );
        }

        $duplicate = $builder
            ->get()
            ->getRowArray();

        if ($duplicate) {
            return $this->jsonError(
                $module['label'] . ' already exists.',
                409
            );
        }

        if ($id > 0) {
            if (! $model->update($id, $data)) {
                return $this->response
                    ->setStatusCode(422)
                    ->setJSON([
                        'status'    => false,
                        'message'   => 'Validation failed.',
                        'errors'    => $model->errors(),
                        'csrfToken' => csrf_hash(),
                    ]);
            }

            return $this->response->setJSON(
                array_merge(
                    [
                        'status'  => true,
                        'message' =>
                            $module['label']
                            . ' updated successfully!',
                        'id'        => $id,
                        'csrfToken' => csrf_hash(),
                    ],
                    $data
                )
            );
        }

        if (! $model->insert($data)) {
            return $this->response
                ->setStatusCode(422)
                ->setJSON([
                    'status'    => false,
                    'message'   => 'Validation failed.',
                    'errors'    => $model->errors(),
                    'csrfToken' => csrf_hash(),
                ]);
        }

        return $this->response->setJSON(
            array_merge(
                [
                    'status'  => true,
                    'message' =>
                        $module['label']
                        . ' added successfully!',
                    'id'        => $model->getInsertID(),
                    'csrfToken' => csrf_hash(),
                ],
                $data
            )
        );
    }

    public function bulkUpload(string $tab)
    {
        $module = $this->moduleOrFail($tab);

        if ($module === null) {
            return $this->jsonError(
                'Unknown maintenance module.',
                404
            );
        }

        $perms = Permissions::maintenance($tab);

        if (! $perms['create']) {
            return $this->jsonError(
                'You do not have permission to bulk upload '
                . strtolower($module['label'])
                . ' items.',
                403
            );
        }

        $file = $this->request->getFile('csv_file');

        if ($file === null || ! $file->isValid()) {
            return $this->jsonError(
                'Please select a valid CSV file to upload.',
                422
            );
        }

        if ($file->hasMoved()) {
            return $this->jsonError(
                'This file has already been processed. Please choose it again.',
                422
            );
        }

        $extension = strtolower((string) $file->getClientExtension());

        if ($extension !== 'csv') {
            return $this->jsonError(
                'Only CSV files are supported.',
                422
            );
        }

        if ($file->getSize() > self::BULK_MAX_FILE_SIZE) {
            return $this->jsonError(
                'File is too large. Maximum size is '
                . (self::BULK_MAX_FILE_SIZE / (1024 * 1024))
                . 'MB.',
                422
            );
        }

        $handle = fopen($file->getTempName(), 'r');

        if ($handle === false) {
            return $this->jsonError(
                'Unable to read the uploaded file.',
                422
            );
        }

        $header = fgetcsv($handle);

        if ($header === false || count(array_filter($header, static fn ($h) => trim((string) $h) !== '')) === 0) {
            fclose($handle);

            return $this->jsonError(
                'The CSV file appears to be empty.',
                422
            );
        }

        $fields      = $this->moduleFields($module);
        $fieldKeys   = array_column($fields, 'key');
        $fieldLabels = array_map('strtolower', array_column($fields, 'label'));

        $columnMap = [];

        foreach ($header as $index => $col) {
            $col = strtolower(trim((string) $col));

            if ($col === '') {
                continue;
            }

            $matchIndex = array_search($col, $fieldKeys, true);

            if ($matchIndex === false) {
                $matchIndex = array_search($col, $fieldLabels, true);
            }

            if ($matchIndex !== false) {
                $columnMap[$fields[$matchIndex]['key']] = $index;
            }
        }

        $missingColumns = array_diff($fieldKeys, array_keys($columnMap));

        if (! empty($missingColumns)) {
            fclose($handle);

            $missingLabels = array_map(
                static fn ($key) => $fields[array_search($key, $fieldKeys, true)]['label'],
                $missingColumns
            );

            return $this->jsonError(
                'The CSV is missing required column(s): '
                . implode(', ', $missingLabels)
                . '. Download the template for the exact format.',
                422
            );
        }

        $model = new $module['model']();

        $db = $model->db;
        $db->transStart();

        $rowNumber = 1;
        $processed = 0;
        $inserted  = 0;
        $skipped   = 0;
        $errors    = [];

        while (($row = fgetcsv($handle)) !== false) {
            $rowNumber++;

            if (count(array_filter($row, static fn ($v) => trim((string) $v) !== '')) === 0) {
                continue;
            }

            $processed++;

            if ($processed > self::BULK_MAX_ROWS) {
                $errors[] = 'Stopped after ' . self::BULK_MAX_ROWS
                    . ' rows — please split large files into smaller batches.';
                break;
            }

            $data    = [];
            $missing = [];

            foreach ($fields as $field) {
                $colIndex = $columnMap[$field['key']];
                $value    = trim((string) ($row[$colIndex] ?? ''));

                if ($value === '') {
                    $missing[] = $field['label'];
                }

                $data[$field['key']] = $value;
            }

            if (! empty($missing)) {
                $errors[] = "Row {$rowNumber}: missing " . implode(', ', $missing) . '.';
                $skipped++;
                continue;
            }

            $builder = $model->builder();

            foreach ($fields as $field) {
                $builder->where(
                    'LOWER(' . $field['key'] . ')',
                    strtolower($data[$field['key']])
                );
            }

            if ($builder->get()->getRowArray()) {
                $errors[] = "Row {$rowNumber}: already exists, skipped.";
                $skipped++;
                continue;
            }

            if (! $model->insert($data)) {
                $modelErrors = $model->errors();

                $errors[] = "Row {$rowNumber}: "
                    . ($modelErrors ? implode(' ', $modelErrors) : 'validation failed.');

                $skipped++;
                continue;
            }

            $inserted++;
        }

        fclose($handle);

        $db->transComplete();

        if (! $db->transStatus()) {
            return $this->jsonError(
                'A database error occurred while importing. No rows were saved.',
                500
            );
        }

        $errorPreview = array_slice($errors, 0, 50);

        if (count($errors) > count($errorPreview)) {
            $errorPreview[] = '...and ' . (count($errors) - count($errorPreview)) . ' more.';
        }

        return $this->response->setJSON([
            'status'    => true,
            'message'   => "{$inserted} " . strtolower($module['label'])
                . ' item(s) added, ' . $skipped . ' skipped.',
            'inserted'  => $inserted,
            'skipped'   => $skipped,
            'errors'    => $errorPreview,
            'csrfToken' => csrf_hash(),
        ]);
    }

    public function bulkTemplate(string $tab)
    {
        $module = $this->moduleOrFail($tab);

        if ($module === null) {
            return $this->jsonError(
                'Unknown maintenance module.',
                404
            );
        }

        $perms = Permissions::maintenance($tab);

        if (! $perms['create']) {
            return $this->jsonError(
                'You do not have permission to bulk upload '
                . strtolower($module['label'])
                . ' items.',
                403
            );
        }

        $fields = $this->moduleFields($module);

        $handle = fopen('php://temp', 'w+');
        fputcsv($handle, array_column($fields, 'key'));

        fputcsv($handle, array_map(
            static fn ($field) => 'Sample ' . $field['label'],
            $fields
        ));
        rewind($handle);

        $csv = stream_get_contents($handle);
        fclose($handle);

        $filename = strtolower($tab) . '_bulk_upload_template.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }

    public function delete(string $tab, int $id)
    {
        $module = $this->moduleOrFail($tab);

        if ($module === null) {
            return $this->jsonError(
                'Unknown maintenance module.',
                404
            );
        }

        if (! Permissions::canMaintenance(
            $tab,
            'delete'
        )) {
            return $this->jsonError(
                'You do not have permission to delete '
                . strtolower($module['label'])
                . ' items.',
                403
            );
        }

        $model = new $module['model']();

        if (! $model->find($id)) {
            return $this->jsonError(
                $module['label'] . ' not found.',
                404
            );
        }

        if (! $model->delete($id)) {
            return $this->jsonError(
                'Unable to delete '
                . strtolower($module['label'])
                . '.',
                500
            );
        }

        return $this->response->setJSON([
            'status'  => true,
            'message' =>
                $module['label']
                . ' deleted successfully.',
            'csrfToken' => csrf_hash(),
        ]);
    }

    private function moduleOrFail(string $tab): ?array
    {
        $tab = strtolower(trim($tab));

        return $this->maintenanceModules[$tab] ?? null;
    }

    private function jsonError(
        string $message,
        int $statusCode
    ) {
        return $this->response
            ->setStatusCode($statusCode)
            ->setJSON([
                'status'    => false,
                'message'   => $message,
                'csrfToken' => csrf_hash(),
            ]);
    }
}
