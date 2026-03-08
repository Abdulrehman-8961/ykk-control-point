<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportSampleTests implements ToCollection, WithStartRow
{
    private int $userId;

    private int $imported = 0;

    // Each row: ['line'=>int,'testname'=>string,'wo'=>string,'reason'=>string]
    private array $failed = [];

    public function __construct(int $userId)
    {
        $this->userId = $userId;
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getFailedRows(): array
    {
        return $this->failed;
    }

    public function startRow(): int
    {
        return 2; // Skip header row
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $row) {

            $lineNo = $this->startRow() + $i;

            if (!isset($row[0]) || trim((string)$row[0]) === '' || trim((string)$row[0]) === 'Test Name') {
                continue;
            }

            $testName = trim((string)$row[0]);
            $testDateRaw = trim((string)$row[1]);
            $woNumber = trim((string)$row[2]);
            $assetNo = trim((string)$row[3]);
            $bosubi = trim((string)$row[4]);
            $productionDateRaw = trim((string)$row[5]);
            $lot = trim((string)$row[6]);

            // Parse dates safely
            $testDate = $this->parseExcelOrStringDate($testDateRaw);
            $productionDate = $this->parseExcelOrStringDate($productionDateRaw);

            // Samples start from col 7
            $samples = array_slice($row->toArray(), 7);
            $samples = array_values(array_filter($samples, fn($s) => $s !== null && $s !== ''));

            $totalSamples = count($samples);
            if ($totalSamples === 0) {
                continue;
            }

            // (1) Validate TestName exists (definition)
            $testDefinition = DB::table('test_definitions')
                ->where('test_name', $testName)
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->first();

            if (!$testDefinition) {
                $this->fail($lineNo, $testName, $woNumber, 'testname');
                continue;
            }

            // (2) Validate WO# exists
            $workorder = DB::table('workorders')
                ->where('workorder_no', $woNumber)
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->first();

            if (!$workorder) {
                $this->fail($lineNo, $testName, $woNumber, 'wo#');
                continue;
            }

            // WO# -> itemcode / color / length / description
            $itemcode = DB::table('itemcodes')
                ->where('id', $workorder->itemcode_id)
                ->orWhere('item_code', $workorder->itemcode_id) // fallback if wo stores code string
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->first();


                // (3) Validate thresholds exist for TESTNAME + ITEMCAT
                $thresholdHeader = DB::table('test_thresholds')
                ->where('test_name_id', $testDefinition->id)
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->first();

            $item_code_ids = DB::table('itemcodes')
    ->where('item_category', $itemcode->item_category)
    ->pluck('id')
    ->toArray();
// dd($item_code_ids, $itemcode->item_category);
$thresholds = null;

if ($thresholdHeader && !empty($item_code_ids)) {
    $thresholds = DB::table('test_threshold_item_categories')
        ->where('test_threshold_id', $thresholdHeader->id)
        ->whereIn('item_category_id', $item_code_ids)
        ->where('is_deleted', 0)
        ->first();
}
                // dd($thresholdHeader, $itemcode, $thresholds);
            if (!$thresholdHeader || !$thresholds) {
                $this->fail($lineNo, $testName, $woNumber, 'threshold');
                continue;
            }

            // Always use YFS values (per requirement)
            $standardValue    = $thresholds->YFS;
            $safetyThreshold  = $thresholds->safety_threshold;
            $absorptionValue  = $thresholds->absorption;

            if (!empty($absorptionValue)) {
                // keep your existing behavior
                $safetyThreshold = 'MAX ABSORB %';
                $standardValue   = $absorptionValue;
            }

            // Asset (keep your existing requirement)
            $asset = DB::table('assets')
                ->where('asset_no', $assetNo)
                ->where('status', 1)
                ->where('is_deleted', 0)
                ->first();



            if (!$asset) {
                // If you want to STRICTLY only use 3 reason codes, change this to 'threshold' or 'wo#'
                $this->fail($lineNo, $testName, $woNumber, 'asset');
                continue;
            }

            // Calc min/max/avg
            $min = min($samples);
            $max = max($samples);
            $avg = array_sum($samples) / $totalSamples;
            $avg_minus = round($avg - $min, 2);
            $avg_plus  = round($max - $avg, 2);

            // STDVA (same logic you had)
            $stdva_value = null;
            if ($testDefinition->test_type === 'Perf-Str' && $totalSamples > 1) {
                $sumSqDiff = array_sum(array_map(fn($s) => pow($s - $avg, 2), $samples));
                $stdDev = sqrt($sumSqDiff / ($totalSamples - 1));

                if ($testDefinition->criteria === 'Min') {
                    $stdva_value = round($stdDev);
                } else {
                    $stdva_value = round($stdDev, 2);
                }
            }

            // Evaluate each sample (same logic)
            $sampleResults = [];
            $sampleCounter = 1;

            foreach ($samples as $sample) {
                $sample_result = 'fail';

                if ($testDefinition->test_type === 'Dimension') {
                    $sample_result = ($sample >= $thresholds->min && $sample <= $thresholds->max) ? 'pass' : 'fail';
                } elseif ($testDefinition->test_type === 'Perf-Str') {
                    if ($testDefinition->criteria === 'Min') {
                        if ($sample < $standardValue) {
                            $sample_result = 'fail';
                        } elseif ($sample <= ($standardValue + $safetyThreshold)) {
                            $sample_result = 'warning';
                        } else {
                            $sample_result = 'pass';
                        }
                    } elseif ($testDefinition->criteria === 'Max') {
                        $sample_result = ($sample <= $standardValue) ? 'pass' : 'fail';
                    }
                } elseif ($testDefinition->test_type === 'Perf-Weight') {
                    $sample_result = ($sample <= $absorptionValue) ? 'pass' : 'fail';
                }

                $sampleResults[] = [
                    'sample_number' => $sampleCounter,
                    'sample_value'  => $sample,
                    'sample_result' => $sample_result,
                ];

                $sampleCounter++;
            }

            $results = ['min' => null, 'avg' => null, 'max' => null];
            $results['min'] = $sampleResults[array_search($min, $samples)]['sample_result'] ?? 'fail';
            $results['max'] = $sampleResults[array_search($max, $samples)]['sample_result'] ?? 'fail';
            $results['avg'] = 'pass'; // fallback

            // Insert in a transaction
            DB::transaction(function () use (
                $testDefinition, $workorder, $itemcode, $asset,
                $bosubi, $lot, $productionDate, $testDate, $totalSamples,
                $min, $max, $avg, $avg_minus, $avg_plus, $stdva_value,
                $results, $standardValue, $safetyThreshold, $absorptionValue,
                $sampleResults
            ) {
                $sampleTestId = DB::table('sample_tests')->insertGetId([
                    'test_name_id'     => $testDefinition->id,
                    'workorder_id'     => $workorder->id,
                    'item_category'    => $itemcode->item_category ?? '',
                    'itemcode'         => $workorder->itemcode_id,
                    'itemcode_desc'    => $itemcode->description ?? '',
                    'color'            => $workorder->itemcode_color_id,
                    'length'           => $workorder->length,
                    'asset_id'         => $asset->id,
                    'bosubi'           => $bosubi,
                    'lot'              => $lot,
                    'production_date'  => $productionDate,
                    'sample_date'      => $testDate,
                    'sample_number'    => $totalSamples,

                    'min'              => $min,
                    'min_result'       => $results['min'],
                    'avg'              => round($avg, 2),
                    'avg_result'       => $results['avg'],
                    'max'              => $max,
                    'max_result'       => $results['max'],

                    'avg_minus'        => $avg_minus,
                    'avg_plus'         => $avg_plus,
                    'stdva_value'      => $stdva_value,

                    'standard_value'   => $standardValue, // YFS
                    'safety_threshold' => $safetyThreshold,
                    'test_standard'    => 'YFS',          // force for clarity
                    'absorption_value' => $absorptionValue,

                    'status'           => 1,
                    'created_by'       => $this->userId,
                    'updated_by'       => $this->userId,
                    'created_at'       => now(),
                    'updated_at'       => now(),
                ]);

                DB::table('sample_tests_audit_trail')->insert([
                    'user_id'        => $this->userId,
                    'description'    => 'QC Test imported successfully',
                    'sample_test_id' => $sampleTestId,
                    'created_at'     => now(),
                ]);

                foreach ($sampleResults as $sr) {
                    DB::table('sample_tests_samples')->insert([
                        'sample_test_id' => $sampleTestId,
                        'sample_number'  => $sr['sample_number'],
                        'sample_value'   => $sr['sample_value'],
                        'sample_result'  => $sr['sample_result'],
                        'created_at'     => now(),
                        'updated_at'     => now(),
                        'updated_by'     => $this->userId,
                    ]);
                }
            });

            $this->imported++;
        }
    }

    private function fail(int $line, string $testName, string $wo, string $reason): void
    {
        $this->failed[] = [
            'line' => $line,
            'testname' => $testName,
            'wo' => $wo,
            'reason' => $reason,
        ];
    }

    private function parseExcelOrStringDate(?string $raw): ?string
    {
        $raw = trim((string)$raw);
        if ($raw === '') return null;

        try {
            if (is_numeric($raw)) {
                return Date::excelToDateTimeObject($raw)->format('Y-m-d');
            }
            return date('Y-m-d', strtotime($raw));
        } catch (\Throwable $e) {
            return null;
        }
    }
}
