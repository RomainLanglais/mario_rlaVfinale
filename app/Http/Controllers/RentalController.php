<?php

namespace App\Http\Controllers;

use App\Services\ToadRentalService;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    private ToadRentalService $rentalService;

    public function __construct(ToadRentalService $rentalService)
    {
        $this->middleware('auth');
        $this->rentalService = $rentalService;
    }

    /**
     * Affiche la page des locations avec la liste des customers
     */
    public function index()
    {
        $customers = $this->rentalService->getAllCustomers();

        return view('rentals.index', [
            'customers' => $customers ?? []
        ]);
    }

    /**
     * Retourne les locations d'un customer en JSON (pour l'appel AJAX)
     */
    public function getByCustomer($customerId)
    {
        $rentals = $this->rentalService->getRentalsByCustomerId((int) $customerId);

        return response()->json($rentals ?? []);
    }
}
