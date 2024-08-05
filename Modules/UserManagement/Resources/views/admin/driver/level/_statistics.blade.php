<div class="auto-items gap-3 mt-2" style="--repeat: auto-fill; --minWidth: 14rem">
    @foreach($levels as $level)
        <div class="card">
            <div class="card-body">
                <div class="d-flex gap-2 justify-content-between">
                    <div class="">
                        <h5 class="text-primary line-limit mb-3">{{ $level->name }}</h5>
                        <div class="fs-12 fw-semibold text-muted">{{translate('Drivers')}}</div>
                        <h3 class="fs-27">{{ $level->users->count() ?? 0 }}</h3>
                    </div>

                    <img src="{{ onErrorImage(
                        $level?->image,
                        asset('storage/app/public/driver/level') . '/' . $level?->image,
                        asset('public/assets/admin-module/img/media/level5.png'),
                        'driver/level/',
                    ) }}" class="dark-support mb-3 custom-box-size" alt="" style="--size: 48px">
                </div>
            </div>
        </div>
    @endforeach
</div>
@if($levels->isEmpty())
    <div class="card">
        <div class="card-body py-5">
            <h4 class="text-muted text-center">{{ translate('No Data Available') }}</h4>
        </div>
    </div>
@endif
