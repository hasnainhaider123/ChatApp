<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Validator;

class SearchController extends Controller
{
    public function saveSearch(Request $request){
        $validator = Validator::make($request->all(), [
            'search_title' => 'required',
        ],[
            'search_title.required' => 'Search Message Required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        else{
            $input = $request->all();
            $input['user_id'] = auth()->id();
            SearchHistory::create($input);
        }
        $response =[
            'status' =>true,
            'message' => "Search Message saved Successfully"
        ];
        return response()->json($response);
    }
    public function getHistory($days){
        $data = SearchHistory::where('user_id',auth()->id())->whereDate('created_at','>=', Carbon::now()->subDays($days))->WhereDate('created_at','<=', Carbon::now())->latest()->limit(5)->get();
        $response =[
            'data'=>$data,
            'status' =>true,
            'message' => "data get successfully"
        ];
        return response()->json($response);
    }
}
