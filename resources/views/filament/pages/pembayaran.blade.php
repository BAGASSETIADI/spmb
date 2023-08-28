<x-filament::page>
    @if ($biaya['status'])
    {!! $biaya['snap_js'] !!}
    <script type="text/javascript">
    function bayarSnap(tokenya) {
        snap.pay(tokenya);
    }
    </script>
    <p>Silahkan lakukan pembayaran berikut ini:</p>
    Nama Biaya: {{ $biaya['data']['nama_biaya'] }} <br>
    Nominal: {{ $biaya['data']['jumlah'] }} <br>
    @if (count($billing['data'])==0)
    <button class="filament-button bg-primary-600 text-white py-1 px-4"
        wire:click="buatBilling( {{$biaya['data']['id']}}, '{{$biaya['data']['nama_biaya']}}', {{$biaya['data']['jumlah']}})">
        Buat E-Billing
    </button>
    @else
    <table width="400" class="table-auto">
        <tr>
            <th width="200">ID Billing</th>

            <th>Status</th>
            <th>Aksi</th>

        </tr>
        @foreach ($billing['data'] as $item)
        <tr>
            <td>{{ $item['id'] }}</td>

            <td>{{ $item['transaction_status'] }}</td>
            <td>

                @if ($item['status_code']=="0" || $item['status_code']=="1")
                @if ($snap_token)
                <div class="text-white py-1 px-4" style="background: #0055FF;">
                    {!! $snap_token !!}
                </div>

                @else
                <button class="filament-button bg-primary-600 text-white py-1 px-4"
                    wire:click="prosesBayar('{{$item['id']}}')">
                    Proses Bayar
                </button>
                @endif
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    @endif
    @else
    Maaf ada kesalahan: <b>{{$biaya['pesan']}}</b>
    @endif
</x-filament::page>