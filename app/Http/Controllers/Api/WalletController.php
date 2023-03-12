<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\TransactionHistory;
use App\Models\Wallet;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{

    protected  $wallet;

    public function __construct() {
        $this->wallet = Wallet::where('user_id',auth()->id())->first();
    }

    public function wallet_details() {
        try {
            if(empty($this->wallet)){
                return $this->ErrorResponse(400,"Wallet not found");
            }
            return $this->SuccessResponse(200, 'Wallet Fetched', $this->wallet);
        } catch (Exception $exception) {
            logger('error occurred in Wallet fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }
    public function wallet_transaction(Request $request){

        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all,[
                    'type'=>'required|in:0,1,2',
                    'amount'=>'required',
                    'remark'=>'nullable',
                    'payload'=>'nullable',
                    'ad_id'=>'nullable',
                    'txn_status'=>'nullable'
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $wallet = $this->wallet->where('user_id',auth()->id())->first();
                if(!$wallet){
                    return $this->ErrorResponse(400,'Wallet not found ..!');
                }
                $ad= $this->advertisement->find($request['ad_id']);
                if(empty($ad)){
                    return $this->ErrorResponse(400,'Ad not found');
                }
                $this->check_balance($ad['id']);
                if(!empty($ad['id'])){
                    $wallet->update(['balance'=>$wallet['balance']-$ad['deposit_amount'],'hold'=>$ad['deposit_amount']]);
                    $request['type']=0;
                    $request['user_id']= auth()->id();
                    $request['txn_id']= IdGenerator::generate(['table' => 'transaction_histories','field'=>'txn_id', 'length' => 16, 'prefix' => date('Y').'-'.auth()->id().'-']);
                    $trans= TransactionHistory::create($request->all());
                    if (!$trans) {
                        return $this->ErrorResponse(500, 'Something went wrong while transaction ..!');
                    }
                    return $this->SuccessResponse(200,'transaction successful ..!',$trans['txn_id']);
                }
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in Wallet fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    /***
     * @param $ad_id
     * @return bool|\Illuminate\Http\JsonResponse
     * checking rentability
     */
    public function check_balance($ad_id)
    {
        $ad= $this->advertisement->find($ad_id);
        $wallet= $this->wallet->where('user_id',auth()->id())->first();
        if($ad['deposit_amount'] == $wallet['balance']){
            return true;
        }
        return $this->ErrorResponse('400', 'You don\'t have sufficient balance ');
    }


    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|ResponseFactory|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     * @throws \Exception
     * Add balance to Wallet
     */
    public function add_balance(Request $request)
    {
        try {
            $user = auth()->user();
            if(!empty($user)){
                $validator= Validator::make($request->all,[
                    'type'=>'required|in:0,1,2',
                    'amount'=>'required',
                    'remark'=>'nullable',
                    'payload'=>'nullable',
                    'ad_id'=>'nullable',
                    'txn_status'=>'required|in:success,pending,failed',
                    'payment_method'=>'required|in:debit,credit,up'
                ]);
                if($validator->fails()){
                    return $this->ErrorResponse(400,$validator->errors()->first());
                }
                $wallet = $this->wallet;
                if(!$wallet){
                    return $this->ErrorResponse(400,'Wallet not found ..!');
                }
                    $request['type']=1;
                    $request['user_id']= auth()->id();
                    $request['txn_id']= IdGenerator::generate(['table' => 'transaction_histories','field'=>'txn_id', 'length' => 16, 'prefix' => date('Y').'-'.auth()->id().'-']);
                    $trans= TransactionHistory::create($request->all());
                    if (!$trans) {
                        return $this->ErrorResponse(500, 'Something went wrong while transaction ..!');
                    }
                    $wallet->update(['balance',$wallet['balance']+$request['amount']]);
                    return $this->SuccessResponse(200,'balance added successfully ..!',$trans);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in Wallet fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }

    }

    public function create_wallet(){

        try {
            $user = auth()->user();
            if(!empty($user)){
                if(Wallet::where('user_id',auth()->id())->exists()){
                    return $this->ErrorResponse(400,'Wallet already created ..!');
                }
                $wallet= Wallet::create([
                    'user_id'=>auth()->id(),
                    'balance'=>0,
                    'hold'=>0,
                ]);
                if($wallet){
                    return $this->SuccessResponse(200,'balance added successfully ..!',$wallet);
                }
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in Wallet fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }

    }

    public function product_wise_wallet_history(){
        dd('hell');
        $history = TransactionHistory::whereNotNull('ad_id')->where(['user_id' => auth()->id()])->get();
        return $this->SuccessResponse(200,'Product wise wallet history ..!',$history);
    }


}
