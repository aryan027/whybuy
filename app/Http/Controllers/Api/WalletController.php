<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\RentItem;
use App\Models\TransactionHistory;
use App\Models\Wallet;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class WalletController extends Controller
{

    protected $wallet;

    public function __construct()
    {
        $this->wallet = Wallet::where('user_id', auth()->id())->first();
    }

    public function wallet_details()
    {
        try {
            $wallet = Wallet::where('user_id', auth()->id())->first();
            return $this->SuccessResponse(200, 'Wallet Fetched', $wallet);
        } catch (Exception $exception) {
            logger('error occurred in Wallet fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

    public function wallet_transaction(Request $request)
    {

        try {
            $user = auth()->user();
            if (!empty($user)) {
                $validator = Validator::make($request->all(), [
                    'type' => 'required|in:0,1,2',
                    'amount' => 'required',
                    'remark' => 'nullable',
                    'payload' => 'nullable',
                    'rent_id' => 'required',
                    'txn_status' => 'nullable'
                ]);
                if ($validator->fails()) {
                    return $this->ErrorResponse(400, $validator->errors()->first());
                }

                if (!Wallet::where('user_id', auth()->id())->exists()) {
                    return $this->ErrorResponse(400, 'Wallet not found ..!');
                }
                $wallet = Wallet::where('user_id', auth()->id())->first();
                $rent = RentItem::find($request['rent_id']);
                if (empty($rent)) {
                    return $this->ErrorResponse(400, 'Rental product not found');
                }
                $ad = Advertisement::find($rent['ads_id']);
                if (empty($ad)) {
                    return $this->ErrorResponse(400, 'Ad not found');
                }
                $this->check_balance($ad, $wallet);
                if (!empty($ad['id'])) {
                    $wallet->update(['balance' => $wallet['balance'] - $rent['block'], 'hold' => $rent['block']]);
                    $request['type'] = 0;
                    $request['user_id'] = auth()->id();
                    $request['rent_id'] = $rent['ads_id'];
                    $request['txn_id'] = IdGenerator::generate(['table' => 'transaction_histories', 'field' => 'txn_id', 'length' => 16, 'prefix' => date('Y') . '-' . auth()->id() . '-']);
                    $trans = TransactionHistory::create($request->all());
                    if (!$trans) {
                        return $this->ErrorResponse(500, 'Something went wrong while transaction ..!');
                    }
                    return $this->SuccessResponse(200, 'transaction successful ..!', $trans['txn_id']);
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
    public function check_balance($ad, $wallet)
    {
        if ($ad['deposit_amount'] == $wallet['balance']) {
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
            if (!empty($user)) {
                $validator = Validator::make($request->all(), [
                    'amount' => 'required',
                    'remark' => 'nullable',
                    'payload' => 'nullable',
                    'ad_id' => 'nullable',
                    'txn_status' => 'required|in:success,pending,failed',
                    'payment_method' => 'required|in:debit,credit,up'
                ]);
                if ($validator->fails()) {
                    return $this->ErrorResponse(400, $validator->errors()->first());
                }
                $wallet = Wallet::where('user_id', auth()->id())->first();
                if (!$wallet) {
                    return $this->ErrorResponse(400, 'Wallet not found ..!');
                }
                $request['type'] = 1;
                $request['user_id'] = auth()->id();
                $request['txn_id'] = IdGenerator::generate(['table' => 'transaction_histories', 'field' => 'txn_id', 'length' => 16, 'prefix' => date('Y') . '-' . auth()->id() . '-']);
                $trans = TransactionHistory::create($request->all());
                if (!$trans) {
                    return $this->ErrorResponse(500, 'Something went wrong while transaction ..!');
                }
                $wallet->update(['balance' => $wallet['balance'] + $request['amount']]);
                return $this->SuccessResponse(200, 'balance added successfully ..!', $trans);
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in Wallet fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }

    }

    public function create_wallet()
    {

        try {
            $user = auth()->user();
            if (!empty($user)) {
                if (Wallet::where('user_id', auth()->id())->exists()) {
                    return $this->ErrorResponse(400, 'Wallet already created ..!');
                }
                $wallet = Wallet::create([
                    'user_id' => auth()->id(),
                    'balance' => 0,
                    'hold' => 0,
                    'status' => 1
                ]);
                if ($wallet) {
                    return $this->SuccessResponse(200, 'balance added successfully ..!', $wallet);
                }
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in Wallet fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }

    }

    public function product_wise_wallet_history()
    {
        $history = TransactionHistory::whereNotNull('ad_id')->where(['user_id' => auth()->id()])->get();
        return $this->SuccessResponse(200, 'Product wise wallet history ..!', $history);
    }

    public function add_money_history()
    {
        $history = TransactionHistory::whereNull('ad_id')->where(['user_id' => auth()->id()])->get()->map(function ($list) {
            unset($list['remark']);
            unset($list['payload']);
            unset($list['ad_id']);
            return $list;
        });
        return $this->SuccessResponse(200, 'money added history fetch..!', $history);
    }

    public function release_product_value(Request $request)
    {
        try {
            $user = auth()->user();
            if (!empty($user)) {
                $validator = Validator::make($request->all(), [
                    'rant_id' => 'required',
                ]);
                if ($validator->fails()) {
                    return $this->ErrorResponse(400, $validator->errors()->first());
                }

                $ad = Advertisement::find($request['ad_id']);
                if (empty($ad)) {
                    return $this->ErrorResponse(200, 'Ad not found');
                }

                $rent = RentItem::where(['id' => $request['rent_id'], 'status' => 'pending'])->first();
                if (empty($rent)) {
                    return $this->ErrorResponse(200, 'Rental product not found');
                }
                if (Wallet::where('user_id', auth()->id())->exists()) {
                    return $this->ErrorResponse(200, 'Wallet already created ..!');
                }
                $wallet = Wallet::where('user_id', auth()->id())->first();
                $update = $rent->update([
                    'status' => 'cancel',
                ]);
                if ($update) {
                    $trans = TransactionHistory::create([
                        'type' => 1,
                        'amount' => $rent['block'],
                        'user_id' => auth()->id(),
                        'remark' => 'released blocked amount',
                        'payload' => 'nullable',
                        'ad_id' => $rent['ads_id'],
                        'txn_status' => 'success'
                    ]);
                    if ($trans) {
                        $wallet->update(['balance' => $wallet['balance'] + $rent['block'], 'hold' => $wallet['hold'] - $rent['block']]);
                    }
                }
            }
            return $this->ErrorResponse(500, 'Something Went Wrong');
        } catch (Exception $exception) {
            logger('error occurred in releasing amount  fetching process');
            logger(json_encode($exception));
            return $this->ErrorResponse(500, 'Something Went Wrong');
        }
    }

}
