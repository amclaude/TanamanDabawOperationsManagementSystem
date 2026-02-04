<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Inventory;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\Quote;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $totalClients = Client::count();
        $clients = Client::all();
        $totalActiveProjects = Project::where('is_active', 1)->count();
        $totalEmployees = User::where('is_admin', false)
            ->orderBy('created_at', 'desc')
            ->count();

        $totalAcceptedQuotes = Quote::where('status', 'accepted')->count();
        $totalSentInvoices = Invoice::where('status', 'sent')->count();
        $totalInventoryItems = Inventory::count();
        return view('dashboard', compact(
            'totalClients',
            'totalActiveProjects',
            'totalEmployees',
            'clients',
            'totalInventoryItems',
            'totalAcceptedQuotes',
            'totalSentInvoices'
        ));
    }
}
