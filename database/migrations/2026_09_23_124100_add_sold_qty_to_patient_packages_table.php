<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddSoldQtyToPatientPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->integer('PTPK_SOLD_QNTY')->default(0)->after('PTPK_QNTY');
        });

        DB::transaction(function () {
            $matchedIds = $this->backfillSoldQtyFromLogs();

            $remaining = DB::table('patient_packages');
            if (count($matchedIds) > 0) {
                $remaining->whereNotIn('id', $matchedIds);
            }
            $remaining->update(['PTPK_SOLD_QNTY' => DB::raw('PTPK_QNTY')]);
        });
    }

    /**
     * Pair each "Adding Package" log with the package row created in that sale.
     * PKLG_AMNT is the quantity sold. PTPK_QNTY is only the remaining balance.
     *
     * @return array<int, int>
     */
    private function backfillSoldQtyFromLogs()
    {
        $labels = $this->pricelistLabels();
        $packagesByPatient = DB::table('patient_packages')->orderBy('id')->get()->groupBy('PTPK_PTNT_ID');
        $used = [];
        $matchedIds = [];

        $logs = DB::table('packages_logs')
            ->where('PKLG_TTLE', 'Adding Package')
            ->orderBy('id')
            ->get();

        foreach ($logs as $log) {
            $parsed = $this->parseSaleComment($log->PKLG_CMNT);
            $candidates = $packagesByPatient->get($log->PKLG_PTNT_ID, collect())
                ->reject(function ($package) use ($used) {
                    return isset($used[$package->id]);
                });

            if ($parsed !== null) {
                $candidates = $candidates->filter(function ($package) use ($parsed, $labels) {
                    $label = $labels[$package->PTPK_PLIT_ID] ?? null;
                    return $label === $parsed['name']
                        && abs(((float) $package->PTPK_PRCE) - $parsed['price']) < 0.01;
                });
            } else {
                $candidates = $candidates->count() === 1 ? $candidates : collect();
            }

            $match = $this->pickPackage($candidates, $log->created_at);
            if ($match === null) {
                continue;
            }

            $used[$match->id] = true;
            $matchedIds[] = $match->id;
            DB::table('patient_packages')->where('id', $match->id)->update([
                'PTPK_SOLD_QNTY' => (int) $log->PKLG_AMNT,
            ]);
        }

        return $matchedIds;
    }

    private function pricelistLabels()
    {
        $labels = [];
        $items = DB::table('pricelist_items')
            ->leftJoin('devices', 'pricelist_items.PLIT_DVIC_ID', '=', 'devices.id')
            ->leftJoin('areas', 'pricelist_items.PLIT_AREA_ID', '=', 'areas.id')
            ->select('pricelist_items.id', 'devices.DVIC_NAME', 'pricelist_items.PLIT_TYPE', 'areas.AREA_NAME')
            ->get();

        foreach ($items as $item) {
            $name = ($item->DVIC_NAME ?? 'Package') . ' ' . $item->PLIT_TYPE;
            if ($item->AREA_NAME) {
                $name .= ' (' . $item->AREA_NAME . ')';
            }
            $labels[$item->id] = $name;
        }

        return $labels;
    }

    private function parseSaleComment($comment)
    {
        if (!preg_match('/^Adding\s+[\d.]+\s+(.+)\s+for\s+([\d.]+)EGP$/', (string) $comment, $match)) {
            return null;
        }

        return [
            'name' => $match[1],
            'price' => (float) $match[2],
        ];
    }

    private function pickPackage($candidates, $logCreatedAt)
    {
        if ($candidates->isEmpty()) {
            return null;
        }

        $logTime = strtotime($logCreatedAt);
        $dated = $candidates->filter(function ($package) use ($logTime) {
            return $package->PTPK_DATE && abs(strtotime($package->PTPK_DATE) - $logTime) <= 5;
        });

        if ($dated->isNotEmpty()) {
            return $dated->sort(function ($a, $b) use ($logTime) {
                $delta = abs(strtotime($a->PTPK_DATE) - $logTime) <=> abs(strtotime($b->PTPK_DATE) - $logTime);
                return $delta !== 0 ? $delta : ($a->id <=> $b->id);
            })->first();
        }

        $undated = $candidates->filter(function ($package) {
            return !$package->PTPK_DATE;
        });

        return ($undated->isNotEmpty() ? $undated : $candidates)->sortBy('id')->first();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('patient_packages', function (Blueprint $table) {
            $table->dropColumn('PTPK_SOLD_QNTY');
        });
    }
}
