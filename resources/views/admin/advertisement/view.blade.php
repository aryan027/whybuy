
@extends('admin.layout.master')
@section('title', 'View Advertisement')
@section('content')
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class = "col-md-6">
                        <h5 class="card-title">View Advertisement</h5>
                    </div>
                    <div class = "col-md-6 text-end">
                        <a href="{{route('advertisement.index')}}">
                            <button type="button" class="btn btn-secondary">Back</button>
                        </a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <h3>{{($advertisement->getUser->full_name) ? $advertisement->getUser->full_name : ''}}</h3>
                </div>
                <hr>
                <div class="row mt-2">
                    <div class="col-md-6">
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Category : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->getCategory)? $advertisement->getCategory->name : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Sub Category : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->getSubCategory) ? $advertisement->getSubCategory->name : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Title : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->title) ? $advertisement->title : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Brand : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->brand) ? $advertisement->brand : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Currency : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->currency) ? $advertisement->currency : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Item Condition : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->item_condition) ? $advertisement->item_condition : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Owner Type : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->owner_type) ? $advertisement->owner_type : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Address : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->address) ? $advertisement->address : ''}}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Deposit Amount : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->deposit_amount) ? $advertisement->deposit_amount : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Hourly Rent : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->hourly_rent) ? $advertisement->hourly_rent : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Daily Rent : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->daily_rent) ? $advertisement->daily_rent : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Weekly Rent : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->weekly_rent) ? $advertisement->weekly_rent : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Monthly Rent : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->monthly_rent) ? $advertisement->monthly_rent : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Yearly Rent : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->yearly_rent) ? $advertisement->yearly_rent : ''}}
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <b>Rent Base : </b>
                            </div>
                            <div class="col-sm-6">
                                {{!empty($advertisement->rent_base) ? $advertisement->rent_base : ''}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection


