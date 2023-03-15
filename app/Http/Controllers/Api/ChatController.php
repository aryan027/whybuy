<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\ChatInteractions;
use App\Models\ChatMessages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function initiateChat(Request $request) {
        $validator = Validator::make($request->all(), [
            'ad_id' => 'required|exists:advertisements,id'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(401, 'Input validation failed');
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
            'cid' =>'required'
        ]);
        if ($validator->fails()) {
            return $this->ErrorResponse(401, 'Input validation failed');
        }
        $cid= $request->cid;
        if (!$request['message'] && !$request->hasFile('media')) {
            return $this->ErrorResponse(401, 'Nothing to send');
        }
        $init = ChatInteractions::find($cid);
        if (!$init['status']) {
            return $this->ErrorResponse(404, 'Can not send chat! may be deleted');
        }
        $data = array(
            'content' => $request['message'] ?? null,
            'chat_id' => $cid
        );
        if ($init['owner_id'] !== auth()->id()) {
            $data['sent_by_owner'] = false;
        } else {
            $data['sent_by_owner'] = true;
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

    public function adChatList($aid) {
        $advertisement = Advertisement::find($aid);
        if (!$advertisement) {
            return $this->ErrorResponse(404, 'Advertisement not found');
        }
        $chats = ChatInteractions::with('ownerInfo', 'userInfo', 'chats')->where(['owner_id' => $advertisement['user_id'], 'advertisement_id' => $advertisement['id'], 'status' => true])->get();
        return $this->SuccessResponse(200, 'fetched successfully', $chats);
    }

    public function deleteChat($cid) {
        $chat = ChatInteractions::find($cid);
        if (!$chat || $chat['user_id'] !== auth()->id()) {
            return $this->ErrorResponse(404, 'Chat not found');
        }
        if ($chat->update(['status' => false])) {
            return $this->SuccessResponse(200, 'Chat deleted successfully');
        } else {
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    public function deleteAllChat(Request $request) {
        $chats = ChatInteractions::where(['user_id' => auth()->id()])->get();
        foreach ($chats as $chat) {
            $chat->update(['status' => false]);
        }
        return $this->SuccessResponse(200, 'All Chats deleted successfully');
    }

    public function listingOfUser() {
        $chats = ChatInteractions::with('ownerInfo', 'userInfo', 'chats')->where(['owner_id' => auth()->id(), 'status' => true])->get();
        return $this->SuccessResponse(200, 'Listing Fetched', $chats);
    }
}
