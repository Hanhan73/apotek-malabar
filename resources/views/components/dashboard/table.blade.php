@props(['title', 'headers', 'rows', 'columns' => [], 'status' => null, 'statusColor' => 'info', 'custom' => false, 'link' => '#'])

<div class="card shadow-sm h-100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="mb-0">{{ $title }}</h6>
        <a href="{{ $link }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead class="table-light">
                    <tr>
                        @foreach($headers as $header)
                        <th>{{ $header }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($rows as $row)
                        <tr>
                            @if($custom)
                                <td>{{ $row->nomor_nota }}</td>
                                <td>{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                <td>Rp {{ number_format($row->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge bg-{{ $row->status_pembayaran === 'sudah_dibayar' ? 'success' : 'warning' }}">
                                        {{ $row->status_pembayaran === 'sudah_dibayar' ? 'Lunas' : 'Belum Lunas' }}
                                    </span>
                                </td>
                            @else
                                @foreach($columns as $col)
                                    <td>{{ $row->$col }}</td>
                                @endforeach
                                <td><span class="badge bg-{{ $statusColor }}">{{ $status }}</span></td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($headers) }}" class="text-center text-muted">Tidak ada data</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
