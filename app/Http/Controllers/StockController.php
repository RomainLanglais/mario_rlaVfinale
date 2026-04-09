<?php

namespace App\Http\Controllers;

use App\Services\ToadStockService;
use Illuminate\Http\Request;

class StockController extends Controller
{
    private ToadStockService $stockService;

    public function __construct(ToadStockService $stockService)
    {
        $this->middleware('auth');
        $this->stockService = $stockService;
    }

    public function index(Request $request)
    {
        $search = $request->input('search');
        $page   = max(0, (int) $request->input('page', 0));

        $result = $this->stockService->getStocks($page, $search);

        // Support réponse paginée Spring Boot { content, totalPages, totalElements, number }
        // ou tableau simple
        $stocks      = [];
        $totalPages  = 1;
        $totalItems  = 0;
        $currentPage = $page;

        if (is_array($result)) {
            if (isset($result['content'])) {
                $stocks      = $result['content'];
                $totalPages  = $result['totalPages'] ?? 1;
                $totalItems  = $result['totalElements'] ?? count($stocks);
                $currentPage = $result['number'] ?? $page;
            } else {
                $stocks     = $result;
                $totalItems = count($stocks);
            }
        }

        return view('stocks.index', compact('stocks', 'totalPages', 'totalItems', 'currentPage', 'search'));
    }

    public function show(Request $request, int $filmId)
    {
        $stockData   = $this->stockService->getFilmStock($filmId);
        if (!$stockData) abort(404);
        $activeStore = $request->input('store', $stockData['stores'][0]['storeId'] ?? null);
        return view('stocks.show', compact('stockData', 'activeStore'));
    }

    public function addDvd(Request $request, int $filmId)
    {
        $storeId = (int) $request->input('store_id');
        $this->stockService->addDvd($filmId, $storeId);
        return redirect()->route('stocks.show', ['filmId' => $filmId, 'store' => $storeId])
            ->with('status', 'DVD ajouté avec succès.');
    }

    public function removeDvd(Request $request, int $filmId, int $inventoryId)
    {
        $storeId = $request->input('store');
        $result  = $this->stockService->removeDvd($inventoryId);
        $params  = ['filmId' => $filmId];
        if ($storeId) $params['store'] = $storeId;
        if ($result === true) {
            return redirect()->route('stocks.show', $params)->with('status', 'DVD supprimé avec succès.');
        }
        return redirect()->route('stocks.show', $params)->with('error', $result);
    }
}
