<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    public function index()
    {
		$data = Category::get();
        $categories = Category::whereNull('subcategory_id')->get();
		return view('index')->with('categories', $categories)->with('data', $data);
    }

	public function store(Request $request)
	{
		$data = Category::select('id')->whereColumn([['category_title', '=', DB::raw('"'.$request->category_title.'"')]])->get();
		if($data->count() == 0)
		{
            $request->validate([
                'category_title' => 'required|string',
                'category_title' => 'nullable',
            ]);

            Category::create($request->all()); //insert data into category_subcategory table

			//updated Category Select Box
            $Category_list = '';
            $data = Category::whereNull('subcategory_id')->get();
            $Category_list = '<option value="">Choose Category</option>';
            foreach ($data as $key) {
                $Category_list .= '<option value="' . $key->id . '">' . $key->category_title . '</option>';
            }

            //updated table rows
            $rows = '';
            $data = Category::get();
            foreach ($data as $key) {
                if($key->subcategory_id == Null){ $key->subcategory_id = '-'; }
                else{
                    $key->subcategory_id =DB::table('category_subcategory as cat1')
                                        ->join('category_subcategory as cat2', 'cat1.id', '=', 'cat2.subcategory_id')
                                        ->select('cat1.category_title as category_title')
                                        ->where('cat2.subcategory_id',$key->subcategory_id)->first()->category_title;
                }
                $rows .= '<tr>';
                $rows .= '<td>' . $key->category_title . '</td>';
                $rows .= '<td>' . $key->id . '</td>';
                $rows .= '</tr>';
            }

            return $Category_list.'|'.$rows; // return updated Category Select Box
		}
		else
		return "exists";
	}

    public function create_subcategory(Request $request)
	{
        $category_title = Category::select('category_title')->whereColumn([['id', '=', DB::raw('"'.$request->id.'"')]])->first()->category_title;
        $request->merge(['category_title' => $category_title]);
        $data = Category::select('id','category_title')->whereColumn([['subcategory_id', '=', DB::raw('"'.$request->id.'"')]])->get();
        $str = '';
        if($data->count() == 0)
		{

            $validator = Validator::make($request->all(), [
				'category_title' => 'required|string',
                'subcategory_id' => 'nullable',
			]);

            for($i=1;$i<3;$i++){
                Category::create(array_merge($validator->validated(),
				    ['category_title' => $request->category_title.'-'.$i],
                    ['subcategory_id' => $request->id],
                ));
            }

			//updated Category Select Box
            $Category_list = '';
            $data = Category::where('subcategory_id',$request->id)->get();
            $Category_list = '<div class="col-md-12 py-1">
                    <select class="form-select" id="category'.$data[0]->id.'" name="category'.$data[0]->id.'" required>';
            $Category_list .= '<option value="">Choose Category</option>';
            foreach ($data as $key) {
                $Category_list .= '<option value="' . $key->id . '">' . $key->category_title . '</option>';
            }
            $Category_list .= '</select></div>';

            //updated table rows
            $rows = '';
            $data = Category::get();
            foreach ($data as $key) {
                if($key->subcategory_id == Null){ $key->subcategory_id = '-'; }
                else{
                    $key->subcategory_id =DB::table('category_subcategory as cat1')
                                        ->join('category_subcategory as cat2', 'cat1.id', '=', 'cat2.subcategory_id')
                                        ->select('cat1.category_title as category_title')
                                        ->where('cat2.subcategory_id',$key->subcategory_id)->first()->category_title;
                }
                $rows .= '<tr>';
                $rows .= '<td>' . $key->category_title . '</td>';
                $rows .= '<td>' . $key->subcategory_id . '</td>';
                $rows .= '</tr>';
            }
            $str = 'created@'.$Category_list.'|'.$rows;
		}
		else{
            $str = '<div class="col-md-12 py-1">
                    <select class="form-select" id="category'.$data[0]->id.'" name="category'.$data[0]->id.'" required>';
            $str .= '<option value="">Choose Category</option>';
            foreach( $data as $key ){
                $str .= '<option value="'.$key->id.'">'.$key->category_title.'</td>';
            }
            $str .= '</select></div>';
            $str = 'exists@'.$str;
        }
		return $str;
	}
}
