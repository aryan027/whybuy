<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\ChatInteractions;
use App\Models\ChatMessages;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function initiateChat(Request $request) {
        $validator = Validator::make($request->all(), [
            'ad_id' => 'required|exists:advertisements,id'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(200, 'Input validation failed');
        }
        $advertisement = Advertisement::find($request['ad_id']);
        $exist = ChatInteractions::where(['advertisement_id' => $advertisement['id'], 'user_id' => auth()->id(), 'status' => true])->first();
        if ($exist) {
            return $this->SuccessResponse(200, 'Chat Initiated', $exist);
        } else {
            $init = ChatInteractions::create([
                'advertisement_id' => $advertisement['id'],
                'owner_id' => $advertisement['user_id'],
                'user_id' => auth()->id()
            ]);
            return $this->SuccessResponse(200, 'Chat Initiated', $init);
        }
    }

    public function sendMessage(Request $request) {
        $validator = Validator::make($request->all(), [
            'message' => 'nullable|string',
            'media' => 'nullable|mimes:jpg,jpeg,png',
            'cid' => 'required'
        ]);
        $cid= $request['cid'];
        if ($validator->fails()) {
            return $this->ErrorResponse(200, 'Input validation failed');
        }
        if (!$request['message'] && !$request->hasFile('media')) {
            return $this->ErrorResponse(200, 'Nothing to send');
        }
        $init = ChatInteractions::find($cid);
        if (!$init['status']) {
            return $this->ErrorResponse(200, 'Can not send chat! may be deleted');
        }
        $data = array(
            'content' => $request['message'] ?? null,
            'chat_id' => $cid
        );
        $user= User::find($init['user_id']);
        if ($init['owner_id'] != auth()->id()) {
            $data['sent_by_owner'] = false;
//            $user= User::find($init['user_id']);
            $this->SendMobileNotification(null,$user,$request['message']);
        } else {
            $data['sent_by_owner'] = true;
            $owener= User::find($init['user_id']);
            $this->SendMobileNotification(null,$user,$request['message']);
        }
        if ($request->hasFile('media')) {
            $file = $request->file('media');
            $extension = $file->extension();
            $name = auth()->id().'-'.$cid.rand(1111, 9999).'-pic.'.$extension;
            $path = 'chat/media/'.$cid.'/';
            $place = $path.$name;
            $file->move(public_path($path), $name);
            $data['is_media'] = true;
            $data['media_url'] = $place;
        } else {
            $data['is_media'] = false;
            $data['media_url'] = null;
        }
        $message = ChatMessages::create($data);

        return $this->SuccessResponse(200, 'message sent successfully', $message);
    }

    public function adChatList() {
        $chats = ChatInteractions::with('ownerInfo', 'userInfo', 'chats')->where(['owner_id' => auth()->id(),'status' => true])->get();
        return $this->SuccessResponse(200, 'fetched successfully', $chats);
    }

    public function deleteChat($cid) {
        $chat = ChatInteractions::find($cid);
        if (!$chat || $chat['user_id'] !== auth()->id()) {
            return $this->ErrorResponse(200, 'Chat not found');
        }
        if ($chat->update(['status' => false])) {
            return $this->SuccessResponse(200, 'Chat deleted successfully');
        } else {
            return $this->ErrorResponse(200, 'Something Went Wrong');
        }
    }

    public function deleteAllChat(Request $request) {
        $chatIds = $request->input('chatIds');
        if (empty($userIds)) {
            return $this->ErrorResponse(200,'No Chat ids provide');
        }
        $myArray = explode(',', $chatIds);
        foreach ($myArray as $chat) {
          $c= ChatInteractions::where(['user_id' => auth()->id(),'id'=> $chat])->get();
            $c->update(['status' => false]);
        }
        return $this->SuccessResponse(200, 'All Chats deleted successfully');
    }

    public function listingOfUser() {
        $chats = ChatInteractions::with('ownerInfo', 'userInfo', 'chats')->where(['user_id' => auth()->id(), 'status' => true])->get();
        return $this->SuccessResponse(200, 'Listing Fetched', $chats);
    }
}
