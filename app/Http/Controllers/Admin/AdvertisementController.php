<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            if($request->ajax()){
                $advertisement = $this->advertisement->orderBy('id','DESC')->get();
                $data['status'] = 1;
                $data['advertisementData'] = View('admin.advertisement.data',compact('advertisement'))->render();
                return $data;
            }
            return view('admin.advertisement.index');
        }catch(Exception $e) {
            abort(500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $id = decrypt($id);
            $advertisement = $this->advertisement->where('id',$id)->first();
            return view('admin.advertisement.view',compact('advertisement'));
        }catch(Exception $e) {
            abort(500);
        }

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Advertisement Approve
     */
    public function advertisementApprove(Request $request, $id)
    {
        try {
            if($request->ajax()){
                $id = decrypt($id);
                $advertisement = $this->advertisement->where('id',$id)->first();
                $data['status'] = 0;
                if(!empty($advertisement)){
                    if($advertisement->approved == 0){
                        $type = 'approved_advertisement';
                        $message = 'Approved your '.$advertisement->title.' Advertisement';
                        $status = 2; //Approved
                        $senderId = \Auth::user()->id;
                        $receiverId = $advertisement->user_id;
                        $this->storeAdvertisementNotification($senderId,$receiverId,$status ,$advertisement->id,$type,$message);
                    }
                    $advertisement->approved = ($advertisement->approved == 0) ? 1 : 0;
                    if($request->reason != ''){
                        $advertisement->unapproved_reason = $request->reason;
                    }
                    $advertisement->save();
                    $data['status'] = 1;
                }
                return $data;
            }
        }catch(Exception $e) {
            abort(500);
        }
    }
    
}
