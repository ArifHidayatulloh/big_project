<table>
    <thead>
        <tr>
            <th style="width: 300px; background-color: #00BCD4; color: white; text-align: center;" colspan="2">DESCRIPTION</th>
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
        @endphp

        @if ($descriptions->isEmpty())
            <tr>
                <td colspan="6" style="text-align: center; color: red; font-weight: bold;">
                    No descriptions available for this Cost Review.
                </td>
            </tr>
        @else
            @foreach ($descriptions as $description)
                @if ($currentCategory !== $description['category'])
                    @php $currentCategory = $description['category']; @endphp
                    <tr>
                        <td colspan="7" style="font-weight: bold; color: blue;">
                            {{ $currentCategory }}
                        </td>
                    </tr>
                @endif

                @if ($currentSubcategory !== $description['subcategory'])
                    @php $currentSubcategory = $description['subcategory']; @endphp
                    <tr>
                        <td colspan="7" style="font-style: italic; background-color: #f0f0f0; padding-left: 20px;">
                            {{ $currentSubcategory }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td style="width: 20px;"></td>
                    <td style="padding-left: 40px; width:300px;">{{ $description['description'] ?? 'N/A' }}</td>
                    <td style="text-align: right;">
                        {{ number_format($description['actual_spent'], 2, ',', '.') }}
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($description['planned_budget'], 2, ',', '.') }}
                    </td>
                    <td style="text-align: right;">
                        {{ number_format($description['variance'], 2, ',', '.') }}
                    </td>
                    <td style="text-align: center;">
                        {{ number_format($description['percentage'], 2) }}%
                    </td>
                    <td>{{ $description['remarks'] }}</td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
