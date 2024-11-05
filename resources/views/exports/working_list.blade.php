<table>
    <thead>
        <tr>
            <th>NO</th>
            <th>WORKING LIST</th>
            <th>PIC</th>
            <th>RELATED PIC</th>
            <th>DEADLINE</th>
            <th>STATUS</th>
            <th>STATUS COMMENT</th>
            <th>COMPLETE DATE</th>
            <th>SCORE</th>
            <th>COMMENT DEPHEAD</th>
            <th>UPDATE PIC</th>
            <th>CREATED AT</th>
            <th>CREATED BY</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($workingLists as $workingList)
            @php
                $totalRows = $workingList->commentDepheads->sum(function ($dephead) {
                    return max($dephead->updatePics->count(), 1);
                });
            @endphp

            <tr>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $loop->iteration }}</td>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $workingList->name }}</td>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $workingList->picUser->name }}</td>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">
                    @if ($workingList->relatedpic)
                        @foreach ($workingList->relatedPicNames as $relpic)
                            {{ $relpic }}
                            @if (!$loop->last)
                                <!-- Cek jika bukan item terakhir -->
                                <br>
                            @endif
                        @endforeach
                    @endif
                </td>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">
                    {{ \Carbon\Carbon::parse($workingList->deadline)->format('d M Y') }}
                    <br>
                    <i class="fas fa-clock"></i>
                    {{ \Carbon\Carbon::parse($workingList->deadline)->format('g:i A') }}
                </td>

                {{-- Tambahkan class untuk status --}}
                @if ($workingList->status == 'Done')
                    <td class="status-done" rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $workingList->status }}</td>
                @elseif($workingList->status == 'In Progress')
                    <td class="status-in-progress" rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $workingList->status }}</td>
                @else
                    <td class="status-pending" rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $workingList->status }}</td>
                @endif

                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $workingList->status_comment }}</td>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">
                    @if ($workingList->complete_date)
                        {{ \Carbon\Carbon::parse($workingList->complete_date)->format('d M Y') }}
                        <br>
                        {{ \Carbon\Carbon::parse($workingList->complete_date)->format('g:i A') }}
                    @else
                        -
                    @endif
                </td>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $workingList->score }}</td>

                @php
                    $firstCommentDephead = $workingList->commentDepheads->first();
                    $updatePics = $firstCommentDephead->updatePics;
                @endphp
                <td rowspan="{{ max($updatePics->count(), 1) }}" style="vertical-align: middle;">{{ $firstCommentDephead->comment }}</td>
                <td style="vertical-align: middle;">
                    @if ($updatePics->isNotEmpty())
                        {{ $updatePics->first()->update }}
                    @endif
                </td>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">
                    {{ \Carbon\Carbon::parse($workingList->created_at)->format('d M Y') }}
                    <br>
                    {{ \Carbon\Carbon::parse($workingList->created_at)->format('g:i A') }}
                </td>
                <td rowspan="{{ $totalRows }}" style="text-align: center; vertical-align: middle;">{{ $workingList->creator->name }}</td>
            </tr>

            @foreach ($updatePics->skip(1) as $updatePic)
                <tr>
                    <td style="vertical-align: middle;">{{ $updatePic->update }}</td>
                </tr>
            @endforeach

            @foreach ($workingList->commentDepheads->skip(1) as $commentDephead)
                @php
                    $updatePics = $commentDephead->updatePics;
                @endphp
                <tr>
                    <td rowspan="{{ max($updatePics->count(), 1) }}" style="vertical-align: middle;">{{ $commentDephead->comment }}</td>
                    <td style="vertical-align: middle;">
                        @if ($updatePics->isNotEmpty())
                            {{ $updatePics->first()->update }}
                        @endif
                    </td>
                </tr>

                @foreach ($updatePics->skip(1) as $updatePic)
                    <tr>
                        <td style="vertical-align: middle;">{{ $updatePic->update }}</td>
                    </tr>
                @endforeach
            @endforeach
        @endforeach
    </tbody>
</table>
