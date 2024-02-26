<?php

namespace App\Http\Controllers;
use App\Models\Client;
use Illuminate\Http\Request;
use PDF;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ClientController extends Controller
{

    public function index(){
        return view('clients.index');
    }

    public function downloadPDF()
    {
        $clients = Client::all();
        $pdf = PDF::loadView('clients.pdf', compact('clients'));
        return $pdf->download('clients.pdf');
    }


    public function get_clients(){
        $clients =  Client::get();
        return response()->json(["success" => true, "clients" => $clients]);  
    }


    public function store(Request $request){
        $clt = $request->all();
        Client::create($request->all());
        return response()->json(["success" => true]);  
    }

    
    public function detail($id){
        $client = Client::findOrFail($id);
        return response()->json($client); 
    }


    public function update(Request $request)
    {
        try {
            $client = Client::findOrFail($request->id);
            $client->update($request->all());
            return response()->json(['success' => true, 'message' => 'Client updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to update client']);
        }
    }


    public function delete(Request $request)
    {
        try {
            $clientId = $request->id;
            $client = Client::findOrFail($clientId);
            $client->delete();
            return response()->json(['success' => true, 'message' => 'Client deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to delete client']);
        }
    }
}
