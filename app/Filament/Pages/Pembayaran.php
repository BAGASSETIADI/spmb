<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
namespace App\Filament\Pages;
use Filament\Pages\Page;
//panggil Auth
use Illuminate\Support\Facades\Auth;
//panggil http client untuk akses route di sikeu
use Illuminate\Support\Facades\Http;
//panggil model yang dibutuhkan
use App\Models\Formulir;
use App\Models\Periode;


class Pembayaran extends Page
{
protected static ?string $navigationIcon = 'heroicon-o-document-text';
protected static string $view = 'filament.pages.pembayaran';
//tampilkan menu navigasi jika user memiliki role 'Pendaftar'
protected static function shouldRegisterNavigation(): bool {
return Auth::user()->hasRole('Pendaftar');
}
public function mount(): void {
//tolak akses halaman jika user tidak memiliki role 'Pendaftar'
abort_unless( Auth::user()->hasRole('Pendaftar'), 403 );
}
private $snap_token = "";
protected function getViewData(): array {
//periode aktif
$periode = Periode::where("aktif",1)->first();
//data formulir
$formulir = Formulir::where("id_periode", $periode->id)
->where("id_user", Auth::user()->id)
->first();
if ($formulir) {
$url_biaya = 'http://localhost:3000/biaya/spmb/'.$formulir->program_studi.'/'.$formulir->id_periode;
$response = Http::withHeaders([
'spmb_token' => '842P37u<Ghdbu3t3gTgreOi*736_hgdrTT4',
])->get($url_biaya);
if ($response->status()==200) {
$biaya = $response->json();
$url_billing = 'http://localhost:3000/billing/spmb/'.$formulir->no_daftar;

$response = Http::withHeaders([

'spmb_token' => '842P37u<Ghdbu3t3gTgreOi*736_hgdrTT4',
])->get($url_billing);
$billing = $response->json();
foreach ($billing['data'] as $item) {
$url_status = "http://localhost:3000/paymentgateway/status/".$item['id'];
$response = Http::withHeaders([
'spmb_token' => '842P37u<Ghdbu3t3gTgreOi*736_hgdrTT4',
])->get($url_status);
}
return [
"biaya"=>$biaya,
"billing"=>$billing,
"snap_token" => $this->snap_token

];

} else {
return [
"status"=>false,

"pesan"=>"Gagal terhubung dengan server sikeu"

];
}
} else {
return [
"status"=>false,
"pesan"=>"Silahkan mengisi formulir terlebih dahulu"
];
}
}
public function buatBilling($id_biaya,$nama_biaya,$jumlah): void {
//periode aktif
$periode = Periode::where("aktif",1)->first();
//data formulir
$formulir = Formulir::where("id_periode", $periode->id)
->where("id_user", Auth::user()->id)
->first();
$dataBilling = [
"id" => $formulir->no_daftar."-".date("YmdHis"),
"snap_token" => "",
"no_daftar" => $formulir->no_daftar,
"nama" => $formulir->nama,
"email" => Auth::user()->email,
"status_code" => 0,
"transaction_status" => "Belum dibayar",
"billing_detail" => [
[
"id_biaya" => $id_biaya,
"nama_biaya" => $nama_biaya,
"jumlah" => $jumlah

]
]
];
$url_billing = 'http://localhost:3000/billing/spmb';
$response = Http::withHeaders([
'spmb_token' => '842P37u<Ghdbu3t3gTgreOi*736_hgdrTT4',
])->post($url_billing, $dataBilling);
$this->render();
}
public function prosesBayar($id_billing): void {
$url_inquiry = "http://localhost:3000/paymentgateway/inquiry/".$id_billing;
$response = Http::withHeaders([
'spmb_token' => '842P37u<Ghdbu3t3gTgreOi*736_hgdrTT4',
])->get($url_inquiry);
$this->snap_token = $response->body();
$this->render();
}
} //Penutup class Pembayaran