<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>INVOICE</th>
            <th>SUPPLIER NAME</th>
            <th>PAYMENT AMOUNT</th>
            <th>PURCHASE DATE</th>
            <th>DUE DATE</th>            
            <th>STATUS</th>
            <th>PAID DATE</th>
            <th>ATTACHMENT</th>
            <th>DESCRIPTION</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($paymentSchedules as $item)
            <tr>
                <td style="text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                <td style="text-align: center; vertical-align: middle;">{{ $item->invoice_number }}</td>
                <td style="text-align: center; vertical-align: middle;">{{ $item->supplier_name }}</td>
                <td style="text-align: center; vertical-align: middle;">{{ $item->payment_amount }}</td>
                <td style="text-align: center; vertical-align: middle;">{{ \Carbon\Carbon::parse($item->purchase_date)->format('d M Y') }}
                    <br>
                    {{ \Carbon\Carbon::parse($item->purchase_date)->format('H:i') }}
                </td>
                <td style="text-align: center; vertical-align: middle;">{{ \Carbon\Carbon::parse($item->due_date)->format('d M Y') }}
                    <br>
                    <i class="fas fa-clock"></i>
                    {{ \Carbon\Carbon::parse($item->due_date)->format('H:i') }}
                </td>
                <td style="text-align: center; vertical-align: middle;">{{ $item->status }}</td>
                <td style="text-align: center; vertical-align: middle;">
                    @if ($item->paid_date)
                        {{ \Carbon\Carbon::parse($item->paid_date)->format('d M Y') }}
                        <br>
                        {{ \Carbon\Carbon::parse($item->paid_date)->format('H:i') }}
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    @if ($item->attachment)
                        <a href="{{ asset('storage/'. $item->attachment) }}" target="_blank">
                            <i class="fas fa-paperclip"></i> Attachment
                        </a>
                    @else
                        -
                    @endif
                </td>
                <td style="text-align: center; vertical-align: middle;">
                    @if ($item->description)
                        {{ $item->description }}
                    @else
                        -
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
