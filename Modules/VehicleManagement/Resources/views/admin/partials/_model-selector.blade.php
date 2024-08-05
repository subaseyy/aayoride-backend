<label for="vehicleModel" class="mb-2">{{ translate('vehicle_model') }} <span class="text-danger">*</span></label>
<select class="js-select theme-input-style w-100" name="model_id" id="vehicleModel" required>
    <option value="" selected disabled>{{ translate('select_model') }}</option>
    @foreach($models as $model)
        <option value="{{$model->id}}" {{isset($model_id) && $model_id == $model->id ? 'selected' : ''}}>{{$model->name}}</option>
    @endforeach
</select>

<script>
    "use strict";
    $(document).ready(function () {
        $('.js-select').select2();
    });
</script>
