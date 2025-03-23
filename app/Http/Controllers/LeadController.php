<?php

namespace App\Http\Controllers;

use App\Mail\LeadStatusUpdated;
use App\Models\Lead;
use App\Models\LeadHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LeadController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Inertia\Response
     */
    public function index(Request $request)
    {
        $leads = Lead::paginate(10);

         if ($request->wantsJson()) {
            return response()->json($leads);
        }

        return Inertia::render('Leads', [
            'leads' => $leads,
        ]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $lead = Lead::findOrFail($id);
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('leads', 'email')->ignore($lead->id)],
            'phone' => 'required|string|max:15',
            'status' => 'required|in:New,In Progress,Closed',
        ]);

        foreach ($validatedData as $key => $value) {
            if ($lead->$key != $value) {
                LeadHistory::create([
                    'lead_id' => $id,
                    'changed_field' => $key,
                    'old_value' => $lead->$key,
                    'new_value' => $value,
                ]);
            }
        }

        $lead->update($validatedData);
        Mail::to($lead->email)->send(new LeadStatusUpdated($lead));

        return Redirect::route('leads.index')->with('message', 'Lead updated successfully');
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $lead = Lead::find($id);
        if (!$lead) {
            return response()->json(['error' => 'Lead not found'], 404);
        }
        $lead->delete();

        return response()->json(['message' => 'Lead deleted successfully']);
    }

    /**
     * @return StreamedResponse
     */
    public function export()
    {
        $leads = Lead::select('name', 'email', 'phone', 'status', 'created_at')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'Name');
        $sheet->setCellValue('B1', 'Email');
        $sheet->setCellValue('C1', 'Phone');
        $sheet->setCellValue('D1', 'Status');
        $sheet->setCellValue('E1', 'Created At');

        $row = 2;
        foreach ($leads as $lead) {
            $sheet->setCellValue("A$row", $lead->name);
            $sheet->setCellValue("B$row", $lead->email);
            $sheet->setCellValue("C$row", $lead->phone);
            $sheet->setCellValue("D$row", $lead->status);
            $sheet->setCellValue("E$row", $lead->created_at);
            $row++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = 'leads.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function dashboard()
    {
        return response()->json([
            'total_leads' => Lead::count(),
            'new_leads' => Lead::where('status', 'New')->count(),
            'in_progress' => Lead::where('status', 'In Progress')->count(),
            'closed_leads' => Lead::where('status', 'Closed')->count(),
        ]);
    }
}
