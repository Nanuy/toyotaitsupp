
<?php

use App\Models\ReportDetail;
use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ReportDetailController extends Controller
{

    public function edit($id)
{
    $detail = ReportDetail::with(['item'])->findOrFail($id);
    $items = Item::all(); // untuk dropdown item
    return view('report_detail.edit', compact('detail', 'items'));
}


    public function update(Request $request, $id)
{
    $request->validate([
        'item_id' => 'required|exists:items,id',
        'tindakan' => 'required|string',
        'uraian_masalah' => 'required|string',
    ]);

    $detail = ReportDetail::findOrFail($id);
    $detail->item_id = $request->item_id;
    $detail->tindakan = $request->tindakan;
    $detail->uraian_masalah = $request->uraian_masalah;
    $detail->save();

    return redirect()->route('report.show', $detail->report_id)->with('success', 'Detail laporan berhasil diperbarui.');
}


}