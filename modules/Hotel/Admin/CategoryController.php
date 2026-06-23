<?php
namespace Modules\Hotel\Admin;

use Illuminate\Http\Request;
use Modules\AdminController;
use Modules\Hotel\Hook;
use Modules\Hotel\Models\HotelCategory;
use Modules\Hotel\Models\HotelCategoryTranslation;

class CategoryController extends AdminController
{
    protected $hotelCategoryClass;
    public function __construct()
    {
        $this->setActiveMenu(route('hotel.admin.index'));
        $this->hotelCategoryClass = HotelCategory::class;
    }

    public function index(Request $request)
    {
        $this->checkPermission('hotel_manage_others');
        $listCategory = $this->hotelCategoryClass::query();
        if (!empty($search = $request->query('s'))) {
            $listCategory->where('name', 'LIKE', '%' . $search . '%');
        }
        $listCategory->orderBy('created_at', 'desc');
        $data = [
            'rows'        => $listCategory->get()->toTree(),
            'row'         => new $this->hotelCategoryClass(),
            'translation'    => new HotelCategoryTranslation(),
            'breadcrumbs' => [
                [
                    'name' => __('Hotel'),
                    'url'  => route('hotel.admin.index')
                ],
                [
                    'name'  => __('Category'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Hotel::admin.category.index', $data);
    }

    public function edit(Request $request, $id)
    {
        $this->checkPermission('hotel_manage_others');
        $row = $this->hotelCategoryClass::find($id);
        if (empty($row)) {
            return redirect(route('hotel.admin.category.index'));
        }
        $translation = $row->translate($request->query('lang',get_main_lang()));
        $data = [
            'translation'    => $translation,
            'enable_multi_lang'=>true,
            'row'         => $row,
            'parents'     => $this->hotelCategoryClass::get()->toTree(),
            'breadcrumbs' => [
                [
                    'name' => __('Hotel'),
                    'url'  => route('hotel.admin.index')
                ],
                [
                    'name'  => __('Category'),
                    'class' => 'active'
                ],
            ]
        ];
        return view('Hotel::admin.category.detail', $data);
    }

    public function store(Request $request , $id)
    {
        $this->checkPermission('hotel_manage_others');
        $this->validate($request, [
            'name' => 'required'
        ]);
        if($id>0){
            $row = $this->hotelCategoryClass::find($id);
            if (empty($row)) {
                return redirect(route('hotel.admin.category.index'));
            }
        }else{
            $row = new $this->hotelCategoryClass();
            $row->status = "publish";
        }

        $row->fill($request->input());
        $res = $row->saveOriginOrTranslation($request->input('lang'),true);

        if ($res) {
            do_action(Hook::AFTER_SAVING_CATEGORY,$row,$request);
            return back()->with('success',  __('Category saved') );
        }
    }

    public function bulkEdit(Request $request)
    {
        $this->checkPermission('hotel_manage_others');
        $ids = $request->input('ids');
        $action = $request->input('action');
        if (empty($ids) or !is_array($ids)) {
            return redirect()->back()->with('error', __('Select at least 1 item!'));
        }
        if (empty($action)) {
            return redirect()->back()->with('error', __('Select an Action!'));
        }
        if ($action == "delete") {
            foreach ($ids as $id) {
                $query = $this->hotelCategoryClass::where("id", $id)->first();
                if(!empty($query)){
                    //Sync child category
                    $list_childs = $this->hotelCategoryClass::where("parent_id", $id)->get();
                    if(!empty($list_childs)){
                        foreach ($list_childs as $child){
                            $child->parent_id = null;
                            $child->save();
                        }
                    }
                    //Del parent category
                    $query->delete();
                }
            }
        } else {
            foreach ($ids as $id) {
                $query = $this->hotelCategoryClass::where("id", $id);
                $query->update(['status' => $action]);
            }
        }
        return redirect()->back()->with('success', __('Updated success!'));
    }

    public function getForSelect2(Request $request)
    {
        $pre_selected = $request->query('pre_selected');
        $selected = $request->query('selected');

        if($pre_selected && $selected){
            $items = $this->hotelCategoryClass::find($selected);


            return [
                'results'=>$items
            ];
        }
        $q = $request->query('q');
        $query = $this->hotelCategoryClass::select('id', 'name as text')->where("status","publish");
        if ($q) {
            $query->where('name', 'like', '%' . $q . '%');
        }
        $res = $query->orderBy('id', 'desc')->limit(20)->get();
        return response()->json([
            'results' => $res
        ]);
    }
}
