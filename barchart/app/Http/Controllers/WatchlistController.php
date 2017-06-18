<?php
namespace App\Http\Controllers;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class WatchlistController extends Controller
{
    public function __construct()
    {

    }

    public function listAll()
    {
        $quotes = DB::select('SELECT * FROM quotes LIMIT 3');
        return response()->json(["quotes" => $quotes]);
    }

    public function addSymbol(Request $request)
    {
        $symbol = $request->input('symbol');

        if(!$symbol){
            return (new Response(json_encode(["error" => "Symbol can not be null"]), 500))->header('Content-Type', 'application/json');
        }

        try{
            $quote = DB::select('SELECT * FROM quotes WHERE symbol = ?', [$symbol]);
        }catch (\Exception $e){
            return (new Response(json_encode(["error" => $e->getMessage()]), 500))->header('Content-Type', 'application/json');
        }

        if($quote) {
            return (new Response(json_encode(["quote" => $quote]), 200))->header('Content-Type', 'application/json');
        }else{
            return (new Response(json_encode(["error" => "The given symbol does not exist"]), 404))->header('Content-Type', 'application/json');
        }
    }

    public function deleteSymbol($symbol)
    {
        try{
            $delete = DB::delete('DELETE FROM quotes WHERE symbol = ?', [$symbol]);
        }catch (\Exception $e){
            return (new Response(json_encode(["error" => $e->getMessage()]), 500))->header('Content-Type', 'application/json');
        }

        if($delete) {
            return (new Response(json_encode(["symbol" => "{$symbol} Deleted Successfully"]), 200))->header('Content-Type', 'application/json');
        }else{
            return (new Response("Not Found", 404))->header('Content-Type', 'application/text');
        }
    }
}