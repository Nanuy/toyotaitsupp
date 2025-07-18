<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReportDetail;
use App\Models\Item;

class ReportDetailController extends Controller
{
    public function edit($id)
    {
        $detail = ReportDetail::findOrFail($id);
        $items = Item::all();
        return view('report.detail-edit', compact('detail', 'items'));

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
