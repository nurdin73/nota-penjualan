
<table style="width: 300px">
    <thead>
        <tr>
            <th>PT. TELE RING DISTRINDO</th>
        </tr>
        <tr>
            <th>Jl. Wahid Hasyim Pertokoan Sindanglaut</th>
        </tr>
        <tr>
            <th>Blok A4 Dusun 01 RT005 RW001</th>
        </tr>
        <tr>
            <th>Cipeujeuh Wetan Lemah Abang</th>
        </tr>
        <tr>
            <th>Kab Cirebon Jawa Barat</th>
        </tr>
        <tr>
            <th>NPWP 74.454.732.4-426.000</th>
        </tr>
    </thead>
    <tr>
        <th></th>
    </tr>
    <tbody>
        <tr>
            <th colspan="2">NOTA PENJUALAN</th>
            <th>{{ $nota->no_nota }}</th>
        </tr>
        <tr>
            <th colspan="2">Member ID</th>
            <th>{{ $nota->member_id }}</th>
        </tr>
        <tr>
            <th></th>
        </tr>
        <tr>
            <th colspan="2">NAMA BARANG</th>
            <th align="right">QYT</th>
            <th align="right">NILAI</th>
        </tr>
        @foreach ($nota->items as $item)
        <tr>
            <th colspan="2">{{ $item->nama_barang }}</th>
            <th>{{ $item->qyt }}</th>
            <th>{{ $item->nilai }}</th>
        </tr>
        @endforeach
    </tbody>
    <tr>
        <th></th>
    </tr>
    <tfoot>
        <tr>
            <th></th>
            <th>TOTAL</th>
            <th>{{ $nota->total }}</th>
        </tr>
    </tfoot>
</table>