<table>
    <thead>
        <tr>
            <th colspan="7"
                style="text-align: center; font-size: 18px; font-weight: bold; color: #4CAF50; padding: 10px 0;">
                <span
                    style="border: 2px solid #4CAF50; padding: 5px 20px; border-radius: 5px; background-color: #E8F5E9;">
                    Cost Review Report Consolidated
                </span>
            </th>
        </tr>
        <tr>
            <th colspan="7" style="text-align: center; font-size: 14px; color: #555; margin-top: 10px;">
                Months: {{ implode(', ', $months) }} | Year: {{ $year }}
            </th>
        </tr>
        <tr>
            <td colspan="7" style="height: 20px;"></td>
        </tr> <!-- Empty row for spacing -->
        <tr>
            <th style="width: 300px; background-color: #00BCD4; color: white; text-align: center;" colspan="2">
                DESCRIPTION</th>
            <th style="width: 120px; background-color: #00BCD4; color: white; text-align: center;">ACTUAL</th>
            <th style="width: 120px; background-color: #00BCD4; color: white; text-align: center;">PLAN</th>
            <th style="width: 120px; background-color: #00BCD4; color: white; text-align: center;">VAR</th>
            <th style="width: 80px; background-color: #00BCD4; color: white; text-align: center;">%</th>
            <th style="width: 200px; background-color: #00BCD4; color: white; text-align: center;">REMARK</th>
        </tr>
    </thead>
    <tbody style="font-size: 12px;">
        @php
            $currentCategory = null;
            $currentSubcategory = null;
            $hasData = false;
            $totalPlannedBudget = 0; // Inisialisasi variabel
            $totalActualSpent = 0; // Inisialisasi variabel
        @endphp

        @foreach ($descriptions as $description)
            @if ($description['has_monthly_budget'])
                @php $hasData = true; @endphp

                @if ($currentCategory !== $description['category'])
                    @php $currentCategory = $description['category']; @endphp
                    <tr>
                        <td colspan="7" style="font-weight: bold; color: blue;">
                            {{ $currentCategory }}
                        </td>
                    </tr>
                @endif

                @if ($currentSubcategory !== $description['subcategory'])
                    @php
                        $currentSubcategory = $description['subcategory'];
                        // Ambil semua description_group_id untuk subkategori ini
                        $descriptionGroups = \App\Models\BudgetDescriptionGrouping::where(
                            'sub_category_id',
                            $description['subcategory_id'],
                        )->pluck('id');

                        // Ambil semua description yang terkait dengan description_group_id
                        $desc = \App\Models\BudgetDescription::whereIn(
                            'description_grouping_id',
                            $descriptionGroups,
                        )->get();

                        // Menghitung total planned budget berdasarkan filter tahun dan bulan
                        $totalPlannSubcategory = $desc
                            ? $desc
                                ->flatMap(function ($item) use ($year, $months) {
                                    return $item->monthly_budget
                                        ->where('year', $year)
                                        ->whereIn('month', $months);
                                })
                                ->sum('planned_budget')
                            : 0;

                        // Menghitung total actual budget berdasarkan filter tahun dan bulan
                        $totalActSubcategory = $desc
                            ? $desc
                                ->flatMap(function ($item) use ($year, $months) {
                                    return $item->monthly_budget
                                        ->where('year', $year)
                                        ->whereIn('month', $months)
                                        ->flatMap(function ($monthlyBudget) {
                                            return $monthlyBudget->actual; // Mengakses relasi actual
                                        });
                                })
                                ->sum('actual_spent') // Sum nilai actual_spent dari actual
                            : 0;

                        $variance = $totalPlannSubcategory - $totalActSubcategory;
                        $percentage =
                            $totalPlannSubcategory > 0 ? ($totalActSubcategory / $totalPlannSubcategory) * 100 : 0;

                        // Tambahkan ke total keseluruhan
                        $totalPlannedBudget += $totalPlannSubcategory;
                        $totalActualSpent += $totalActSubcategory;
                        $totalVar = $totalPlannedBudget - $totalActualSpent;
                        $totalPercentage =
                            $totalPlannedBudget > 0 ? ($totalActualSpent / $totalPlannedBudget) * 100 : 0;
                    @endphp
                    <tr class="bg-light">
                        <td style="width: 20px;"></td>
                        <td style="font-style: italic; background-color: #f0f0f0; margin-left: 20px;">
                            {{ $currentSubcategory }}
                        </td>
                        <td style="text-align: right; background-color: #f0f0f0; ">
                            {{ number_format($totalActSubcategory, 2, ',', '.') }}</td>
                        <td style="text-align: right; background-color: #f0f0f0; ">
                            {{ number_format($totalPlannSubcategory, 2, ',', '.') }}</td>
                        <td style="text-align: right; background-color: #f0f0f0; ">
                            {{ number_format($variance, 2, ',', '.') }}</td>
                        <td style="text-align: center; background-color: #f0f0f0; ">
                            {{ number_format($percentage, 2) }}%</td>
                        <td style="background-color: #f0f0f0; "></td>
                    </tr>
                @endif

                <tr>
                    <td style="width: 40px;"></td>
                    <td style="padding-left: 40px; width:300px;">{{ $description['description_group'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">
                        {{ number_format($description['total_actual_spent'], 2, ',', '.') }}
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($description['total_planned_budget'], 2, ',', '.') }}
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($description['variance'], 2, ',', '.') }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($description['percentage'], 2) }}%
                    </td>
                    <td>{{ $description['remarks'] }}</td>
                </tr>
            @endif
        @endforeach

        @if (!$hasData)
            <tr>
                <td colspan="7" style="text-align: center; color: red; font-weight: bold;">
                    No data available for monthly budget.
                </td>
            </tr>
        @endif
    </tbody>
    <tfoot class="bg-info">
        <tr>
            <td style="text-align: left; background-color: #00BCD4; color: white; font-weight: bold;" colspan="2">
                Total</td>
            <td style="text-align: right; background-color: #00BCD4; color: white; font-weight: bold;">
                {{ number_format($totalActualSpent, 2, ',', '.') }}
            </td>
            <td style="text-align: right; background-color: #00BCD4; color: white; font-weight: bold;">
                {{ number_format($totalPlannedBudget, 2, ',', '.') }}
            </td>
            <td style="text-align: right; background-color: #00BCD4; color: white; font-weight: bold;">
                {{ number_format($totalVar, 2, ',', '.') }}</td>
            <td style="text-align: center; background-color: #00BCD4; color: white; font-weight: bold;">
                {{ number_format($totalPercentage, 2) }}%</td>
            <td style="text-align: center; background-color: #00BCD4; color: white; font-weight: bold;"></td>
        </tr>
    </tfoot>
</table>
