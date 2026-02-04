<?php

namespace App\Http\Controllers;

use App\Mail\InvoiceSent;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InvoiceController extends Controller
{
    public function index()
    {
        // Get invoices with relations for the table
        $invoices = Invoice::with(['client', 'project'])->latest()->get();

        // Get active projects for the "Create Invoice" dropdown
        $projects = Project::where('is_active', true)->get();

        return view('invoice', compact('invoices', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id'   => 'required|exists:projects,id',
            'client_id'    => 'required|exists:clients,id',
            'issue_date'   => 'required|date',
            'due_date'     => 'required|date|after_or_equal:issue_date',
            'items'        => 'required|array|min:1', // Must have at least 1 item
            'items.*.desc' => 'required|string',
            'items.*.qty'  => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::create([
            'project_id'   => $validated['project_id'],
            'client_id'    => $validated['client_id'],
            'issue_date'   => $validated['issue_date'],
            'due_date'     => $validated['due_date'],
            'total_amount' => 0,
            'status'       => 'draft'
        ]);

        $grandTotal = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['qty'] * $item['price'];
            $grandTotal += $lineTotal;

            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['desc'],
                'quantity'    => $item['qty'],
                'price'       => $item['price'],
                'total'       => $lineTotal
            ]);
        }

        $invoice->update(['total_amount' => $grandTotal]);

        return response()->json(['message' => 'Invoice created successfully']);
    }

    public function update(Request $request, $id)
    {
        $invoice = Invoice::findOrFail($id);

        // 1. Validation (Same as store)
        $validated = $request->validate([
            'project_id'    => 'required|exists:projects,id',
            'client_id'     => 'required|exists:clients,id',
            'issue_date'    => 'required|date',
            'due_date'      => 'required|date|after_or_equal:issue_date',
            'items'         => 'required|array|min:1',
            'items.*.desc'  => 'required|string',
            'items.*.qty'   => 'required|numeric|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);
        // Update Invoice Parent Details
        $invoice->update([
            'project_id' => $validated['project_id'],
            'client_id'  => $validated['client_id'],
            'issue_date' => $validated['issue_date'],
            'due_date'   => $validated['due_date'],
        ]);

        $invoice->items()->delete();


        $grandTotal = 0;

        foreach ($validated['items'] as $item) {
            $lineTotal = $item['qty'] * $item['price'];
            $grandTotal += $lineTotal;

            InvoiceItem::create([
                'invoice_id'  => $invoice->id,
                'description' => $item['desc'],
                'quantity'    => $item['qty'],
                'price'       => $item['price'],
                'total'       => $lineTotal
            ]);
        }

        $invoice->update(['total_amount' => $grandTotal]);


        return response()->json(['message' => 'Invoice updated successfully']);
    }
    public function destroy($id)
    {
        try {
            $invoice = Invoice::findOrFail($id);


            $invoice->items()->delete();


            $invoice->delete();

            return response()->json(['message' => 'Invoice deleted successfully']);
        } catch (\Exception $e) {
            Log::error("Delete Error: " . $e->getMessage());
            return response()->json(['message' => 'Failed to delete invoice.'], 500);
        }
    }

    public function sendEmail($id)
    {
        $invoice = Invoice::with(['client', 'project'])->findOrFail($id);

        if (!$invoice->client->email) {
            return response()->json(['message' => 'Client has no email address linked.'], 400);
        }

        try {
            Mail::to($invoice->client->email)->send(new InvoiceSent($invoice));

            if ($invoice->status === 'draft') {
                $invoice->update(['status' => 'sent']);
            }

            return response()->json(['message' => 'Invoice sent successfully via Brevo!']);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error("Email Error: " . $e->getMessage());
            return response()->json(['message' => 'Failed to send email. Check logs.'], 500);
        }
    }

    public function markAsPaid($id)
    {
        // Find the invoice or fail with 404
        $invoice = Invoice::findOrFail($id);

        // Update the status
        $invoice->update(['status' => 'paid']);

        // Return success to the frontend
        return response()->json(['message' => 'Invoice marked as paid']);
    }
}
