<div class="col-12">
    <div class="">
        <ul class="nav d-inline-flex nav--tabs p-1 rounded bg-white">
            <li class="nav-item">
                <a href="{{ route('admin.vehicle.attribute-setup.brand.index') }}" class="{{ Request::is('admin/vehicle/attribute-setup/brand*')?'nav-link active':'nav-link' }} text-capitalize">{{ translate('vehicle_brand') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.vehicle.attribute-setup.model.index') }}" class="{{ Request::is('admin/vehicle/attribute-setup/model*')?'nav-link active':'nav-link' }} text-capitalize">{{ translate('vehicle_model') }}</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.vehicle.attribute-setup.category.index') }}" class="{{ Request::is('admin/vehicle/attribute-setup/category*')?'nav-link active':'nav-link' }} text-capitalize">{{ translate('vehicle_category') }}</a>
            </li>
        </ul>
    </div>
</div>
