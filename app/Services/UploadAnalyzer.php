<?php

namespace App\Services;

use App\Models\DataUpload;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UploadAnalyzer
{
    public function extractTable(DataUpload $upload, int $limit = 15): array
    {
        $path = Storage::disk('public')->path($upload->archivo);
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (!in_array($extension, ['xlsx', 'xls', 'csv'])) {
            return [
                'headers' => [],
                'rows' => [],
                'message' => 'Descarga el archivo para visualizarlo: ' . Storage::disk('public')->url($upload->archivo),
            ];
        }

        $rows = $this->readSpreadsheet($path, $extension);
        if (empty($rows)) {
            return [
                'headers' => [],
                'rows' => [],
                'message' => 'No se encontraron filas en el archivo.',
            ];
        }

        $headers = array_map(fn ($header) => strtoupper(trim((string) $header)), array_shift($rows));
        $headers = $this->normalizeHeaders($headers);

        $previewRows = [];
        foreach ($rows as $row) {
            $previewRows[] = array_slice($row, 0, count($headers));
            if (count($previewRows) >= $limit) {
                break;
            }
        }

        return [
            'headers' => $headers,
            'rows' => $previewRows,
            'message' => null,
        ];
    }

    public function structuredRows(DataUpload $upload): Collection
    {
        $table = $this->extractTable($upload, 1000);
        $headers = $table['headers'];
        $rows = collect($table['rows'])->map(function ($row) use ($headers) {
            $assoc = [];
            foreach ($headers as $index => $header) {
                $assoc[$header] = $row[$index] ?? null;
            }
            return $assoc;
        });

        return $rows;
    }

    public function notesMetrics(Collection $rows): array
    {
        $rows = $rows->map(function ($row) {
            $row['nota'] = isset($row['nota']) ? (float) str_replace(',', '.', $row['nota']) : null;
            return $row;
        })->filter(fn ($row) => $row['nota'] !== null);

        $avg = round($rows->avg('nota'), 2);
        $max = $rows->max('nota');
        $min = $rows->min('nota');
        $passed = $rows->where('nota', '>=', 11)->count();
        $count = $rows->count();

        return [
            'promedio' => $avg,
            'max' => $max,
            'min' => $min,
            'aprobados' => $passed,
            'total' => $count,
            'porcentaje_aprobados' => $count ? round(($passed / $count) * 100, 1) : 0,
        ];
    }

    public function enrollmentProjection(Collection $rows): array
    {
        $data = $rows->map(function ($row) {
            return [
                'anio' => isset($row['anio']) ? (int) $row['anio'] : null,
                'matriculados' => isset($row['matriculados']) ? (int) $row['matriculados'] : null,
            ];
        })->filter(fn ($row) => $row['anio'] && $row['matriculados'])
            ->sortBy('anio')
            ->values();

        if ($data->count() < 2) {
            return [
                'datos' => $data,
                'proyecciones' => [],
            ];
        }

        $x = $data->pluck('anio')->all();
        $y = $data->pluck('matriculados')->all();
        $slope = $this->linearRegressionSlope($x, $y);
        $intercept = $this->linearRegressionIntercept($x, $y, $slope);

        $nextYears = [];
        $lastYear = end($x);
        for ($i = 1; $i <= 2; $i++) {
            $year = $lastYear + $i;
            $nextYears[] = [
                'anio' => $year,
                'matriculados' => max(0, round($slope * $year + $intercept)),
            ];
        }

        return [
            'datos' => $data,
            'proyecciones' => $nextYears,
        ];
    }

    private function readSpreadsheet(string $path, string $extension): array
    {
        try {
            if ($extension === 'csv') {
                $reader = IOFactory::createReader('Csv');
                $reader->setInputEncoding('UTF-8');
            } else {
                $reader = IOFactory::createReaderForFile($path);
            }

            $spreadsheet = $reader->load($path);
            /** @var Worksheet $sheet */
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            return array_map(function ($row) {
                return array_map(fn ($cell) => is_string($cell) ? trim($cell) : $cell, $row);
            }, $rows);
        } catch (\Throwable $e) {
            Log::error('Error leyendo archivo para preview', [
                'path' => $path,
                'exception' => $e->getMessage(),
            ]);
            return [];
        }
    }

    private function normalizeHeaders(array $headers): array
    {
        return array_map(function ($header) {
            $header = strtolower($header);
            $header = str_replace([' ', '-', '.'], '_', $header);
            $header = preg_replace('/[^a-z0-9_]/', '', $header);
            return $header ?: 'columna';
        }, $headers);
    }

    private function linearRegressionSlope(array $x, array $y): float
    {
        $n = count($x);
        $sumX = array_sum($x);
        $sumY = array_sum($y);
        $sumXY = array_sum(array_map(fn ($xi, $yi) => $xi * $yi, $x, $y));
        $sumX2 = array_sum(array_map(fn ($xi) => $xi * $xi, $x));

        $numerator = ($n * $sumXY) - ($sumX * $sumY);
        $denominator = ($n * $sumX2) - ($sumX ** 2);

        return $denominator == 0 ? 0 : $numerator / $denominator;
    }

    private function linearRegressionIntercept(array $x, array $y, float $slope): float
    {
        $n = count($x);
        $sumY = array_sum($y);
        $sumX = array_sum($x);

        return ($sumY - $slope * $sumX) / $n;
    }
}


