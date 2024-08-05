<div class="withdraw-info-aside_wrap position-relative" id="asideBar{{$request->id}}">
    <div class="withdraw-info-aside_backdrop withdraw-info-aside_close"></div>
    <div class="withdraw-info-aside">
        <div class="pt-3 pb-2 px-4">
            <button type="button" class="btn-close withdraw-info-aside_close" aria-label="Close"></button>
        </div>
{{--        <form action="{{ route('admin.driver.withdraw.action',$request->id ) }}" method="POST" class="d-flex flex-column flex-grow-1" id="singleAction{{$request->id}}">--}}
{{--            @csrf--}}
            <div class="p-4 pt-3 flex-grow-1 position-relative mh-100dvh" data-trigger="scrollbar">
                <h3 class="mb-3 text-center">{{ translate("Driver Withdraw Information") }}</h3>
                <div class="d-flex align-items-center justify-content-center text-center gap-2 mb-2">
                    <div>{{ translate("Withdraw Amount") }} :</div>
                    <strong class="text-primary">{{getCurrencyFormat($request->amount)}}</strong>
                    <span class="badge badge-info">{{ ucfirst($request->status) }}</span>
                </div>

                <div class="d-flex align-items-center justify-content-center text-center gap-2 {{$request->status == APPROVED ? '': 'mb-4'}}">
                    <div>{{ translate("Request Time") }}</div>
                    :
                    <div>{{date('Y-m-d h-i A',strtotime($request->created_at))}}</div>
                </div>
                <div class="d-flex align-items-center justify-content-center text-center gap-2 mb-4 {{$request->status == APPROVED ? 'mb-4': 'd-none'}}">
                    <div>{{ translate("Approval Time") }}</div>
                    :
                    <div>{{date('Y-m-d h-i A',strtotime($request->updated_at))}}</div>
                </div>


                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="text-capitalize">
                            {{ translate("Payment Info") }}
                        </h6>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="w-120px">{{ translate("Payment Method") }}</div>
                            :
                            <div>{{ $request->method?->method_name ?? "Method not longer exist" }}</div>
                        </div>
                        @foreach($request->method_fields as $key => $value)
                            <div class="d-flex align-items-center gap-2">
                                <div class="w-120px">{{ucfirst(str_replace('_',' ',$key))}}</div>
                                :
                                <div>{{ $value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="text-capitalize">
                            {{ translate("Driver information") }}
                        </h6>
                    </div>
                    <div class="card-body d-flex flex-column gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="min-w120">{{translate("name")}}</div>
                            :
                            <div>{{ $request->user? $request?->user?->first_name.' '.$request?->user?->last_name : "Driver not found" }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="min-w120">{{ translate("email") }}</div>
                            :
                            <div>{{ $request->user? $request?->user?->email : "Driver not found" }}</div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <div class="min-w120">{{translate("phone")}}</div>
                            :
                            <div>{{ $request->user? $request?->user?->phone : "Driver not found" }}</div>
                        </div>
                    </div>
                </div>
                @if($request->driver_note)
                    <div class="mb-4">
                        <h6 class="mb-2">{{ translate("Driver Note") }}</h6>
                        <div class="bg-light p-3 rounded">
                            {{$request->driver_note}}
                        </div>
                    </div>
                @endif
                @if($request->approval_note)
                    <div class="mb-4">
                        <h6 class="mb-2">{{ translate("Approval Note") }}</h6>
                        <div class="bg-light p-3 rounded">
                            {{$request->approval_note}}
                        </div>
                    </div>
                @endif
                @if($request->denied_note)
                    <div class="mb-4">
                        <h6 class="mb-2">{{ translate("Denied Note") }}</h6>
                        <div class="bg-light p-3 rounded">
                            {{$request->denied_note}}
                        </div>
                    </div>
                @endif

                @if($request->status == PENDING)
                    <h3 class="mb-3 text-center status-deny{{$request->id}} d-none">{{ translate("Denied Note") }}</h3>
                    <div class="mb-4 status-deny{{$request->id}} d-none">
                        <textarea class="form-control" placeholder="Type a note about request denial" name="denied_note"
                                  rows="5" disabled></textarea>
                    </div>
                @endif
                @if($request->status == PENDING)
                    <h3 class="mb-3 text-center status-approve{{$request->id}} d-none">{{ translate("Approval Note") }}</h3>
                    <div class="mb-4 status-approve{{$request->id}} d-none">
                        <textarea class="form-control" placeholder="Type a note about request approval"
                                  name="approval_note"
                                  rows="5" disabled></textarea>
                    </div>
                @endif
                <div class="py-5"></div>
            </div>

            <div class="d-flex shadow-lg bg-white justify-content-center gap-3 position-sticky bottom-0 py-2 px-4 submit-button{{$request->id}}"
                 id="submitButton">
                <button type="button" class="btn btn--cancel withdraw-info-aside_close">{{translate("Back")}}</button>
                @if($request->status == PENDING)
                    <div class="status-approve{{ $request->id }} d-none">
                        <input type="hidden" class="status-approve{{ $request->id }} d-none" name="ids[]" value="{{$request->id}}" disabled>
                        <input type="hidden" class="status-approve{{ $request->id }} d-none" name="status" value="{{APPROVED}}" disabled>
                        <button type="submit" class="btn btn-primary h-45 status-approve{{ $request->id }} d-none" data-id="{{ $request->id }}">{{translate("Approve")}}</button>
                    </div>
                    <div class="status-deny{{ $request->id }} d-none">
                        <input type="hidden" class="status-deny{{ $request->id }} d-none" name="ids[]" value="{{$request->id}}" disabled>
                        <input type="hidden" class="status-deny{{ $request->id }} d-none" name="status" value="{{DENIED}}" disabled>
                        <button type="submit" class="btn btn-primary h-45 status-deny{{ $request->id }} d-none" data-id="{{ $request->id }}">{{ translate("Deny") }}</button>
                    </div>
                @endif
                @if($request->status == APPROVED)
                    <div class="status-settle{{ $request->id }} d-none">
                        <input type="hidden" class="status-settle{{ $request->id }} d-none" name="ids[]" value="{{$request->id}}" disabled>
                        <input type="hidden" class="status-settle{{ $request->id }} d-none" name="status" value="{{SETTLED}}" disabled>
                        <button type="submit" class="btn btn-primary h-45 status-settle{{ $request->id }} d-none" data-id="{{ $request->id }}">{{ translate("Settled") }}</button>
                    </div>
                @endif
                @if($request->status != PENDING)
                    <div class="status-reverse{{ $request->id }} d-none">
                        <input type="hidden" class="status-reverse{{ $request->id }} d-none" name="ids[]" value="{{$request->id}}" disabled>
                        <input type="hidden" class="status-reverse{{ $request->id }} d-none" name="status" value="reverse" disabled>
                        <button type="submit" class="btn btn-primary h-45 status-reverse{{ $request->id }} d-none" data-id="{{ $request->id }}">{{ translate("reverse") }}</button>
                    </div>
                @endif

            </div>
{{--        </form>--}}
    </div>
</div>
