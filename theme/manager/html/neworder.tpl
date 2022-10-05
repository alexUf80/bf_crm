{$meta_title="Новая заявка" scope=parent}

{capture name='page_scripts'}
    
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    
    
    <script src="theme/{$settings->theme|escape}/assets/plugins/inputmask/dist/min/jquery.inputmask.bundle.min.js"></script>
    
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/apps/neworder.js?v=1.01"></script>
    
    <script type="text/javascript" src="theme/{$settings->theme|escape}/js/validation.js"></script>
    <script>
    ! function(window, document, $) {
        "use strict";
        $("input,select,textarea").not("[type=submit]").jqBootstrapValidation()
    }(window, document, jQuery);
    </script>
{/capture}

{capture name='page_styles'}
    
    
{/capture}

<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i> Новая заявка</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="orders/offline">Заявки оффлайн</a></li>
                    <li class="breadcrumb-item active">Новая заявка</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 align-self-center">
                
            </div>
        </div>
        
        <div class="row" id="order_wrapper">
            <div class="col-lg-12">
                <div class="card card-outline-info">
                    <div class="card-header">
                        <h4 class="mb-0 text-white float-left">Заявка {$order->order_id}</h4>
                        <small class="text-white float-right">{$offline_points[$manager->offline_point_id]->address}</small>
                    </div>
                    <div class="card-body">
                        <form method="POST" id="">
                            <div class="form-body">
                                
                                
                                
                                <div class="row">
                                
                                    {if $error}
                                    <div class="col-12">
                                        <div class="alert alert-danger">{$error}</div>
                                    </div>
                                    {/if}
                                    
                                    <div class="col-md-6">
                                        <div class="row edit-block ">
                                            <div class="col-md-12">
                                                <div class="form-group row {if in_array('empty_period', (array)$amount_error)}has-danger{/if}">
                                                    <label class="control-label col-md-4">Организация:</label>
                                                    <div class="col-md-8">
                                                        <select name="organization_id" class="form-control">
                                                            {foreach $organizations as $org}
                                                            <option value="{$org->id}" {if $org->id==$order->organization_id}selected{/if}>{$org->name|escape}</option>
                                                            {/foreach}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        
                                    </div>
                                    
                                    <div class="col-12">
                                        <hr class="mt-0 mb-3" />
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="row edit-block ">
                                            {if $amount_error}
                                            <div class="col-md-12">
                                                <ul class="alert alert-danger">
                                                    {if in_array('empty_amount', (array)$amount_error)}<li>Укажите сумму заявки!</li>{/if}
                                                    {if in_array('empty_period', (array)$amount_error)}<li>Укажите срок заявки!</li>{/if}
                                                </ul>
                                            </div>
                                            {/if}
                                            <div class="col-md-12">
                                                <div class="form-group row {if in_array('empty_amount', (array)$amount_error)}has-danger{/if}">
                                                    <label class="control-label col-md-4">Клиент:</label>
                                                    <div class="col-md-8">
                                                        <input type="hidden" name="user_id" value="{$order->user_id}" class="form-control js-user-id-input" />
                                                        <input type="text" value="" class="form-control js-user-input" placeholder="Создать нового клиента" />
                                                        {if in_array('empty_amount', (array)$amount_error)}<small class="form-control-feedback"></small>{/if}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group row {if in_array('empty_amount', (array)$amount_error)}has-danger{/if}">
                                                    <label class="control-label col-md-4">Сумма:</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="amount" value="{$amount}" class="form-control" placeholder="Сумма заявки" required data-validation-required-message="This field is required" />
                                                        {if in_array('empty_amount', (array)$amount_error)}<small class="form-control-feedback">Укажите сумму заявки!</small>{/if}
                                                    </div>
                                                </div>
                                            </div>                                            
                                            <div class="col-md-12">
                                                <div class="form-group row {if in_array('empty_period', (array)$amount_error)}has-danger{/if}">
                                                    <label class="control-label col-md-4">Срок:</label>
                                                    <div class="col-md-8">
                                                        <input type="text" name="period" value="{$period}" class="form-control" placeholder="Срок" required data-validation-required-message="This field is required" />
                                                        {if in_array('empty_period', (array)$amount_error)}<small class="form-control-feedback">Укажите срок заявки!</small>{/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="form-group row {if in_array('empty_percent', (array)$amount_error)}has-danger{/if}">
                                            <label class="control-label col-md-6">Процент:</label>
                                            <div class="col-md-6">
                                                <input type="text" name="percent" value="{$percent}" class="form-control js-percent-input" required />
                                                {if in_array('empty_percent', (array)$amount_error)}<small class="form-control-feedback"></small>{/if}
                                            </div>
                                        </div>
                                        <div class="form-group row {if in_array('empty_charge', (array)$amount_error)}has-danger{/if}">
                                            <label class="control-label col-md-6">Ответсвенность:</label>
                                            <div class="col-md-6">
                                                <input type="text" name="charge" value="{$charge}" class="form-control js-charge-input" required data-validation-required-message="This field is required" />
                                                {if in_array('empty_charge', (array)$amount_error)}<small class="form-control-feedback"></small>{/if}
                                            </div>
                                        </div>
                                        <div class="form-group row {if in_array('empty_peni', (array)$amount_error)}has-danger{/if}">
                                            <label class="control-label col-md-6">Пени:</label>
                                            <div class="col-md-6">
                                                <input type="text" name="peni" value="{$peni}" class="form-control js-peni-input" required data-validation-required-message="This field is required" />
                                                {if in_array('empty_peni', (array)$amount_error)}<small class="form-control-feedback"></small>{/if}
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        {*}
                                        <div class="row edit-block ">
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="service_reason" id="service_reason" value="1" {if $order->service_reason}checked="true"{/if} />
                                                        <label class="custom-control-label" for="service_reason">Причина отказа</label>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="custom-control custom-switch">
                                                        <input type="checkbox" class="custom-control-input" name="service_insurance" id="service_insurance" value="1" {if $order->service_insurance}checked="true"{/if} />
                                                        <label class="custom-control-label" for="service_insurance">Страхование</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        {*}
                                    </div>
                                    
                                    <div class="col-md-12">
                                        <hr class="mt-3 mb-3" />
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group row {if in_array('empty_phone', (array)$personal_error)}has-danger{/if}">
                                                    <div class="col-4">
                                                        <label class="control-label">Телефон</label>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" name="phone" value="{$order->phone}" class="form-control js-phone-input js-mask-input" data-mask="7(999) 999-9999" placeholder="7(900)000-00-00" required />
                                                    </div>
                                                    {if in_array('empty_phone', (array)$personal_error)}<small class="form-control-feedback">Укажите Телефон!</small>{/if}
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group row {if in_array('empty_email', (array)$personal_error)}has-danger{/if}">
                                                    <div class="col-4">
                                                        <label class="control-label">Email</label>
                                                    </div>
                                                    <div class="col-8">
                                                        <input type="text" name="email" value="{$order->email}" class="form-control js-email-input" placeholder="user@mail.ru" required />
                                                    </div>
                                                    {if in_array('empty_email', (array)$personal_error)}<small class="form-control-feedback">Укажите Email!</small>{/if}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-2">
                                        <div class="border">
                                            <h5 class="card-header"><span class="text-white">Персональная информация</span></h5>
                                    
                                            <div class="row edit-block m-0 mb-2 mt-2 ">
                                                {if $personal_error}
                                                <div class="col-md-12">
                                                    <ul class="alert alert-danger">
                                                        {if in_array('empty_lastname', (array)$personal_error)}<li>Укажите Фамилию!</li>{/if}
                                                        {if in_array('empty_firstname', (array)$personal_error)}<li>Укажите Имя!</li>{/if}
                                                        {if in_array('empty_patronymic', (array)$personal_error)}<li>Укажите Отчество!</li>{/if}
                                                        {if in_array('empty_gender', (array)$personal_error)}<li>Укажите Пол!</li>{/if}
                                                        {if in_array('empty_birth', (array)$personal_error)}<li>Укажите Дату рождения!</li>{/if}
                                                        {if in_array('empty_birth_place', (array)$personal_error)}<li>Укажите Место рождения!</li>{/if}
                                                    </ul>
                                                </div>
                                                {/if}
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_lastname', (array)$personal_error)}has-danger{/if}">
                                                        <label class="control-label">Фамилия</label>
                                                        <input type="text" name="lastname" value="{$order->lastname}" class="form-control js-lastname-input" placeholder="Фамилия" required="true" />
                                                        {if in_array('empty_lastname', (array)$personal_error)}<small class="form-control-feedback">Укажите Фамилию!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_gender', (array)$personal_error)}has-danger{/if}">
                                                        <label class="control-label">Пол</label>
                                                        <select class="form-control custom-select js-gender-input" name="gender">
                                                            <option value="male" {if $order->gender == 'male'}selected="true"{/if}>Мужской</option>
                                                            <option value="female" {if $order->gender == 'female'}selected="true"{/if}>Женский</option>
                                                        </select>
                                                        {if in_array('empty_gender', (array)$personal_error)}<small class="form-control-feedback">Укажите Пол!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_firstname', (array)$personal_error)}has-danger{/if}">
                                                        <label class="control-label">Имя</label>
                                                        <input type="text" name="firstname" value="{$order->firstname}" class="form-control js-firstname-input" placeholder="Имя" required="true" />
                                                        {if in_array('empty_firstname', (array)$personal_error)}<small class="form-control-feedback">Укажите Имя!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_birth', (array)$personal_error)}has-danger{/if}">
                                                        <label class="control-label">Дата рождения</label>
                                                        <input type="text" class="form-control js-birth-input js-mask-input" name="birth" value="{$order->birth}" data-mask="99.99.9999" required="true" />
                                                        {if in_array('empty_birth', (array)$personal_error)}<small class="form-control-feedback">Укажите Дату рождения!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_patronymic', (array)$personal_error)}has-danger{/if}">
                                                        <label class="control-label">Отчество</label>
                                                        <input type="text" name="patronymic" value="{$order->patronymic}" class="form-control js-patronymic-input" placeholder="Отчество" required="true" />
                                                        {if in_array('empty_patronymic', (array)$personal_error)}<small class="form-control-feedback">Укажите Отчество!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_birth_place', (array)$personal_error)}has-danger{/if}">
                                                        <label class="control-label">Место рождения</label>
                                                        <input type="text" class="form-control js-birth-place-input" name="birth_place" value="{$order->birth_place}" placeholder="" required="true" />
                                                        {if in_array('empty_birth_place', (array)$personal_error)}<small class="form-control-feedback">Укажите Место рождения!</small>{/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-6 mb-2">
                                        <div class="border">
                                            <h5 class="card-header"><span class="text-white">Паспортные данные</span></h5>
                                    
                                            <div class="row edit-block m-0 mb-2 mt-2 ">
                                                {if $passport_error}
                                                <div class="col-md-12">
                                                    <ul class="alert alert-danger">
                                                        {if in_array('empty_passport_serial', (array)$passport_error)}<li>Укажите серию и номер паспорта!</li>{/if}
                                                        {if in_array('empty_passport_date', (array)$passport_error)}<li>Укажите дату выдачи паспорта!</li>{/if}
                                                        {if in_array('empty_subdivision_code', (array)$passport_error)}<li>Укажите код подразделения выдавшего паспорт!</li>{/if}
                                                        {if in_array('empty_passport_issued', (array)$passport_error)}<li>Укажите кем выдан паспорт!</li>{/if}
                                                    </ul>
                                                </div>
                                                {/if}
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_passport_serial', (array)$passport_error)}has-danger{/if}">
                                                        <label class="control-label">Серия и номер паспорта</label>
                                                        <input type="text" class="form-control js-passport-serial-input js-mask-input" name="passport_serial" value="{$order->passport_serial}" data-mask="9999-999999" required="true" />
                                                        {if in_array('empty_passport_serial', (array)$passport_error)}<small class="form-control-feedback">Укажите серию и номер паспорта!</small>{/if}
                                                    </div>
                                                    <div class="form-group {if in_array('empty_passport_date', (array)$passport_error)}has-danger{/if}">
                                                        <label class="control-label">Дата выдачи</label>
                                                        <input type="text" class="form-control js-passport-date-input js-mask-input" name="passport_date" value="{$order->passport_date}" data-mask="99.99.9999" required="true" />
                                                        {if in_array('empty_passport_date', (array)$passport_error)}<small class="form-control-feedback">Укажите дату выдачи паспорта!</small>{/if}
                                                    </div>
                                                    <div class="form-group {if in_array('empty_subdivision_code', (array)$passport_error)}has-danger{/if}">
                                                        <label class="control-label">Код подразделения</label>
                                                        <input type="text" class="form-control js-subdivision-code-input js-mask-input" name="subdivision_code" value="{$order->subdivision_code}" data-mask="999-999" required="true" />
                                                        {if in_array('empty_subdivision_code', (array)$passport_error)}<small class="form-control-feedback">Укажите код подразделения выдавшего паспорт!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_passport_issued', (array)$passport_error)}has-danger{/if}">
                                                        <label class="control-label">Кем выдан</label>
                                                        <textarea class="form-control js-passport-issued-input" required="">{$order->passport_issued}</textarea>
                                                        {if in_array('empty_passport_issued', (array)$passport_error)}<small class="form-control-feedback">Укажите кем выдан паспорт!</small>{/if}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

            
                                    <div class="col-md-6 mb-2">
                                        <div class="border">
                                            <h5 class="card-header"><span class="text-white">Адрес прописки</span></h5>
                                            <div class="row m-0 mb-2 mt-2 js-dadata-address ">

                                                {if $addresses_error}
                                                <div class="col-md-12">
                                                    <ul class="alert alert-danger">
                                                        {if in_array('empty_regregion', (array)$addresses_error)}<li>Укажите область!</li>{/if}
                                                        {if in_array('empty_regcity', (array)$addresses_error)}<li>Укажите город!</li>{/if}
                                                        {if in_array('empty_regstreet', (array)$addresses_error)}<li>Укажите улицу!</li>{/if}
                                                        {if in_array('empty_reghousing', (array)$addresses_error)}<li>Укажите дом!</li>{/if}
                                                        {if in_array('empty_faktregion', (array)$addresses_error)}<li>Укажите область!</li>{/if}
                                                        {if in_array('empty_faktcity', (array)$addresses_error)}<li>Укажите город!</li>{/if}
                                                        {if in_array('empty_faktstreet', (array)$addresses_error)}<li>Укажите улицу!</li>{/if}
                                                        {if in_array('empty_fakthousing', (array)$addresses_error)}<li>Укажите дом!</li>{/if}
                                                    </ul>
                                                </div>
                                                {/if}
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 {if in_array('empty_regregion', (array)$addresses_error)}has-danger{/if}">
                                                        <label class="control-label">Область</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-region js-regregion-input" name="Regregion" value="{$order->Regregion}" placeholder="" required="true" />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-region-type js-regregion-type-input" name="Regregion_shorttype" value="{$order->Regregion_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                        {if in_array('empty_regregion', (array)$addresses_error)}<small class="form-control-feedback">Укажите область!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 {if in_array('empty_regcity', (array)$addresses_error)}has-danger{/if}">
                                                        <label class="control-label">Город</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-city js-regcity-input" name="Regcity" value="{$order->Regcity}" placeholder="" />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-city-type js-regcity-type-input" name="Regcity_shorttype" value="{$order->Regcity_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                        {if in_array('empty_regcity', (array)$addresses_error)}<small class="form-control-feedback">Укажите город!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 ">
                                                        <label class="control-label">Район</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-district js-regdistrict-input" name="Regdistrict" value="{$order->Regdistrict}" placeholder=""/>
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-district-type js-regdistrict-type-input" name="Regdistrict_shorttype" value="{$order->Regdistrict_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 ">
                                                        <label class="control-label">Нас. пункт</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-locality js-reglocality-input" name="Reglocality" value="{$order->Reglocality}" placeholder="" />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-locality-type js-reglocality-type-input" name="Reglocality_shorttype" value="{$order->Reglocality_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 {if in_array('empty_regstreet', (array)$addresses_error)}has-danger{/if}">
                                                        <label class="control-label">Улица</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-street js-regstreet-input" name="Regstreet" value="{$order->Regstreet}" placeholder="" />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-street-type js-regstreet-type-input" name="Regstreet_shorttype" value="{$order->Regstreet_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                        {if in_array('empty_regstreet', (array)$addresses_error)}<small class="form-control-feedback">Укажите улицу!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="control-label">Индекс</label>
                                                        <input type="text" class="form-control js-dadata-index js-regindex-input" name="Regindex" value="{$order->Regindex}" placeholder="" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group {if in_array('empty_reghousing', (array)$addresses_error)}has-danger{/if}">
                                                        <label class="control-label">Дом</label>
                                                        <input type="text" class="form-control js-dadata-house js-reghousing-input" name="Reghousing" value="{$order->Reghousing}" placeholder="" />
                                                        {if in_array('empty_reghousing', (array)$addresses_error)}<small class="form-control-feedback">Укажите дом!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Строение</label>
                                                        <input type="text" class="form-control js-dadata-building js-regbuilding-input" name="Regbuilding" value="{$order->Regbuilding}" placeholder="" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Квартира</label>
                                                        <input type="text" class="form-control js-dadata-room js-regroom-input" name="Regroom" value="{$order->Regroom}" placeholder="" />
                                                    </div>
                                                </div>
                                                    
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-2">
                                        <div class="border">
                                            <h5 class="card-header"><span class="text-white">Адрес проживания</span></h5>
                                            <div class="row m-0 mb-2 mt-2 js-dadata-address">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 {if in_array('empty_faktregion', (array)$addresses_error)}has-danger{/if}">
                                                        <label class="control-label">Область</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-region js-faktregion-input" name="Faktregion" value="{$order->Faktregion}" placeholder="" required="true" />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-region-type js-faktregion-type-input" name="Faktregion_shorttype" value="{$order->Faktregion_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                        {if in_array('empty_faktregion', (array)$addresses_error)}<small class="form-control-feedback">Укажите область!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 {if in_array('empty_faktcity', (array)$addresses_error)}has-danger{/if}">
                                                        <label class="control-label">Город</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-city js-faktcity-input" name="Faktcity" value="{$order->Faktcity}" placeholder=""  />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-city-type js-faktcity-type-input" name="Faktcity_shorttype" value="{$order->Faktcity_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                        {if in_array('empty_faktcity', (array)$addresses_error)}<small class="form-control-feedback">Укажите город!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 ">
                                                        <label class="control-label">Район</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-district js-faktdistrict-input" name="Faktdistrict" value="{$order->Faktdistrict}" placeholder="" />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-district-type js-faktdistrict-type-input" name="Faktdistrict_shorttype" value="{$order->Faktdistrict_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 ">
                                                        <label class="control-label">Нас. пункт</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-locality js-faktlocality-input" name="Faktlocality" value="{$order->Faktlocality}" placeholder="" />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-locality-type js-faktlocality-type-input" name="Faktlocality_shorttype" value="{$order->Faktlocality_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-1 {if in_array('empty_faktstreet', (array)$addresses_error)}has-danger{/if}">
                                                        <label class="control-label">Улица</label>
                                                        <div class="row">
                                                            <div class="col-9">
                                                                <input type="text" class="form-control js-dadata-street js-faktstreet-input" name="Faktstreet" value="{$order->Faktstreet}" placeholder=""  />
                                                            </div>
                                                            <div class="col-3">
                                                                <input type="text" class="form-control js-dadata-street-type js-faktstreet-typr-input" name="Faktstreet_shorttype" value="{$order->Faktstreet_shorttype}" placeholder="" />
                                                            </div>
                                                        </div>
                                                        {if in_array('empty_faktstreet', (array)$addresses_error)}<small class="form-control-feedback">Укажите улицу!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-group">
                                                        <label class="control-label">Индекс</label>
                                                        <input type="text" class="form-control js-dadata-index js-faktindex-input" name="Faktindex" value="{$order->Faktindex}" placeholder="" />
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group {if in_array('empty_fakthousing', (array)$addresses_error)}has-danger{/if}">
                                                        <label class="control-label">Дом</label>
                                                        <input type="text" class="form-control js-dadata-house js-fakthousing-input" name="Fakthousing" value="{$order->Fakthousing}" placeholder="" />
                                                        {if in_array('empty_fakthousing', (array)$addresses_error)}<small class="form-control-feedback">Укажите дом!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Строение</label>
                                                        <input type="text" class="form-control js-dadata-building js-faktbuilding-input" name="Faktbuilding" value="{$order->Faktbuilding}" placeholder="" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="control-label">Квартира</label>
                                                        <input type="text" class="form-control js-dadata-room js-faktroom-input" name="Faktroom" value="{$order->Faktroom}" placeholder=""  />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-2">
                                        <div class="border">
                                            <h5 class="card-header"><span class="text-white">Данные о работе</span></h5>
                                            <div class="row m-0 pt-2 edit-block js-dadata-address ">
                                                {if $work_error}
                                                <div class="col-md-12">
                                                    <ul class="alert alert-danger">
                                                    
                                                        {if in_array('empty_workplace', (array)$work_error)}<li>Укажите название организации!</li>{/if}
                                                        {if in_array('empty_profession', (array)$work_error)}<li>Укажите должность!</li>{/if}
                                                        {if in_array('empty_workphone', (array)$work_error)}<li>Укажите рабочий телефон!</li>{/if}
                                                        {if in_array('empty_income', (array)$work_error)}<li>Укажите доход!</li>{/if}
                                                        {if in_array('empty_expenses', (array)$work_error)}<li>Укажите расход!</li>{/if}
                                                        {if in_array('empty_chief_name', (array)$work_error)}<li>Укажите ФИО начальника!</li>{/if}
                                                        {if in_array('empty_chief_position', (array)$work_error)}<li>Укажите Должность начальника!</li>{/if}
                                                        {if in_array('empty_chief_phone', (array)$work_error)}<li>Укажите Телефон начальника!</li>{/if}
                                                        
                                                    </ul>
                                                </div>
                                                {/if}
                                                <div class="col-md-6">
                                                    <div class="form-group mb-0 {if in_array('empty_workplace', (array)$work_error)}has-danger{/if}">
                                                        <label class="control-label">Название организации</label>
                                                        <input type="text" class="form-control js-workplace-input" name="workplace" value="{$order->workplace|escape}" placeholder="" required="true" />
                                                        {if in_array('empty_workplace', (array)$work_error)}<small class="form-control-feedback">Укажите название организации!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-0 {if in_array('empty_profession', (array)$work_error)}has-danger{/if}">
                                                        <label class="control-label">Должность</label>
                                                        <input type="text" class="form-control js-profession-input" name="profession" value="{$order->profession|escape}" placeholder="" required="true" />
                                                        {if in_array('empty_profession', (array)$work_error)}<small class="form-control-feedback">Укажите должность!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-0">
                                                        <label class="control-label">Адрес</label>
                                                        <input type="text" class="form-control js-workaddress-input" name="workaddress" value="{$order->workaddress|escape}" placeholder="" required="true" />
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0 {if in_array('empty_workphone', (array)$work_error)}has-danger{/if}">
                                                        <label class="control-label">Pабочий телефон</label>
                                                        <input type="text" class="form-control js-workphone-input js-mask-input" name="workphone" value="{$order->workphone|escape}" data-mask="7(999)999-99-99" required="true" />
                                                        {if in_array('empty_workphone', (array)$work_error)}<small class="form-control-feedback">Укажите рабочий телефон!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0 {if in_array('empty_income', (array)$work_error)}has-danger{/if}">
                                                        <label class="control-label">Доход</label>
                                                        <input type="text" class="form-control js-income-input" name="income" value="{$order->income|escape}" placeholder="" required="true" />
                                                        {if in_array('empty_income', (array)$work_error)}<small class="form-control-feedback">Укажите доход!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0 {if in_array('empty_expenses', (array)$work_error)}has-danger{/if}">
                                                        <label class="control-label">Расход</label>
                                                        <input type="text" class="form-control js-expenses-input" name="expenses" value="{$order->expenses|escape}" placeholder="" required="true" />
                                                        {if in_array('empty_expenses', (array)$work_error)}<small class="form-control-feedback">Укажите расход!</small>{/if}
                                                    </div>
                                                </div>

                                                <div class="col-md-4">
                                                    <div class="form-group mb-0 {if in_array('empty_chief_name', (array)$work_error)}has-danger{/if}">
                                                        <label class="control-label">ФИО начальника</label>
                                                        <input type="text" class="form-control js-chief-name-input" name="chief_name" value="{$order->chief_name|escape}" placeholder="" required="true" />
                                                        {if in_array('empty_chief_name', (array)$work_error)}<small class="form-control-feedback">Укажите ФИО начальника!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0 {if in_array('empty_chief_position', (array)$work_error)}has-danger{/if}">
                                                        <label class="control-label">Должность начальника</label>
                                                        <input type="text" class="form-control js-chief-position-input" name="chief_position" value="{$order->chief_position|escape}" placeholder="" required="true" />
                                                        {if in_array('empty_chief_position', (array)$work_error)}<small class="form-control-feedback">Укажите Должность начальника!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-group mb-0 {if in_array('empty_chief_phone', (array)$work_error)}has-danger{/if}">
                                                        <label class="control-label">Телефон начальника</label>
                                                        <input type="text" class="form-control js-chief-phone-input js-mask-input" name="chief_phone" value="{$order->chief_phone|escape}" data-mask="7(999)999-99-99" />
                                                        {if in_array('empty_chief_phone', (array)$work_error)}<small class="form-control-feedback">Укажите Телефон начальника!</small>{/if}
                                                    </div>
                                                </div>
                                                                                                        
                                                <div class="col-md-12 mb-2">
                                                    <div class="form-group mb-0">
                                                        <label class="control-label">Комментарий к работе</label>
                                                        <input type="text" class="form-control js-workcomment-input" name="workcomment" value="{$order->workcomment|escape}" placeholder="" />
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6 mb-2">
                                        <div class="border">
                                            <h5 class="card-header"><span class="text-white">Контактные лица</span></h5>
                                        
                                        
                                            <div class="row m-0 pt-2 pb-2 edit-block">
                                                {if $contacts_error}
                                                <div class="col-md-12">
                                                    <ul class="alert alert-danger">
                                                        {if in_array('empty_contact_person_name', (array)$contacts_error)}<li>Укажите ФИО контакного лица!</li>{/if}
                                                        {if in_array('empty_contact_person_phone', (array)$contacts_error)}<li>Укажите тел. контакного лица!</li>{/if}
                                                        {if in_array('empty_contact_person2_name', (array)$contacts_error)}<li>Укажите ФИО контакного лица 2!</li>{/if}
                                                        {if in_array('empty_contact_person2_phone', (array)$contacts_error)}<li>Укажите тел. контакного лица 2!</li>{/if}
                                                    </ul>
                                                </div>
                                                {/if}
                                                <div class="col-md-12">
                                                    <div class="form-group mb-0 {if in_array('empty_contact_person_name', (array)$contacts_error)}has-danger{/if}">
                                                        <label class="control-label">ФИО контакного лица</label>
                                                        <input type="text" class="form-control js-contactperson-name-input" name="contact_person_name" value="{$order->contact_person_name}" placeholder="" required="true" />
                                                        {if in_array('empty_contact_person_name', (array)$contacts_error)}<small class="form-control-feedback">Укажите ФИО контакного лица!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_contact_person_relation', (array)$contacts_error)}has-danger{/if}">
                                                        <label class="control-label">Кем приходится</label>
                                                        <select class="form-control custom-select js-contactperson-relation-input" name="contact_person_relation">
                                                            <option value="" {if $order->contact_person_relation == ''}selected=""{/if}>Выберите значение</option>
                                                            <option value="мать/отец" {if $order->contact_person_relation == 'мать/отец'}selected=""{/if}>мать/отец</option>
                                                            <option value="муж/жена" {if $order->contact_person_relation == 'муж/жена'}selected=""{/if}>муж/жена</option>
                                                            <option value="сын/дочь" {if $order->contact_person_relation == 'сын/дочь'}selected=""{/if}>сын/дочь</option>
                                                            <option value="коллега" {if $order->contact_person_relation == 'коллега'}selected=""{/if}>коллега</option>
                                                            <option value="друг/сосед" {if $order->contact_person_relation == 'друг/сосед'}selected=""{/if}>друг/сосед</option>
                                                            <option value="иной родственник" {if $order->contact_person_relation == 'иной родственник'}selected=""{/if}>иной родственник</option>
                                                        </select>
                                                        {if in_array('empty_contact_person_relation', (array)$contacts_error)}<small class="form-control-feedback">Укажите кем приходится контакное лицо!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_contact_person_phone', (array)$contacts_error)}has-danger{/if}">
                                                        <label class="control-label">Тел. контакного лица</label>
                                                        <input type="text" class="form-control js-contactperson-phone-input js-mask-input" name="contact_person_phone" value="{$order->contact_person_phone}" data-mask="7(999)999-99-99" required="true" />
                                                        {if in_array('empty_contact_person_phone', (array)$contacts_error)}<small class="form-control-feedback">Укажите тел. контакного лица!</small>{/if}
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12">
                                                <hr class="mb-3 mt-2" />
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="form-group mb-0 {if in_array('empty_contact_person2_name', (array)$contacts_error)}has-danger{/if}">
                                                        <label class="control-label">ФИО контакного лица 2</label>
                                                        <input type="text" class="form-control js-contactperson2-name-input" name="contact_person2_name" value="{$order->contact_person2_name}" placeholder="" required="true" />
                                                        {if in_array('empty_contact_person2_name', (array)$contacts_error)}<small class="form-control-feedback">Укажите ФИО контакного лица!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_contact_person_relation', (array)$contacts_error)}has-danger{/if}">
                                                        <label class="control-label">Кем приходится</label>
                                                        <select class="form-control custom-select js-contactperson2-relation-input" name="contact_person2_relation">
                                                            <option value="" {if $order->contact_person2_relation == ''}selected=""{/if}>Выберите значение</option>
                                                            <option value="мать/отец" {if $order->contact_person2_relation == 'мать/отец'}selected=""{/if}>мать/отец</option>
                                                            <option value="муж/жена" {if $order->contact_person2_relation == 'муж/жена'}selected=""{/if}>муж/жена</option>
                                                            <option value="сын/дочь" {if $order->contact_person2_relation == 'сын/дочь'}selected=""{/if}>сын/дочь</option>
                                                            <option value="коллега" {if $order->contact_person2_relation == 'коллега'}selected=""{/if}>коллега</option>
                                                            <option value="друг/сосед" {if $order->contact_person2_relation == 'друг/сосед'}selected=""{/if}>друг/сосед</option>
                                                            <option value="иной родственник" {if $order->contact_person2_relation == 'иной родственник'}selected=""{/if}>иной родственник</option>
                                                        </select>
                                                        {if in_array('empty_contact_person_relation', (array)$contacts_error)}<small class="form-control-feedback">Укажите кем приходится контакное лицо!</small>{/if}
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group {if in_array('empty_contact_person2_phone', (array)$contacts_error)}has-danger{/if}">
                                                        <label class="control-label">Тел. контакного лица 2</label>
                                                        <input type="text" class="form-control js-contactperson2-phone-input js-mask-input" name="contact_person2_phone" value="{$order->contact_person2_phone}" data-mask="7(999)999-99-99" />
                                                        {if in_array('empty_contact_person2_phone', (array)$contacts_error)}<small class="form-control-feedback">Укажите тел. контакного лица!</small>{/if}
                                                    </div>
                                                </div>                                                        
                                            </div>
                                        </div>
                                    </div>
                                    
                                    
                                    
                                    
                                
                                <div class="col-md-12">
                                    <div class="form-actions text-right">
                                        <button type="submit" class="btn btn-success "> 
                                            <span class="btn-label btn-label-lg"><i class="fa fa-check"></i> </span>
                                            <span>Сохранить</span>
                                        </button>
                                    </div>
                                </div>
                                
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
    
    {include file='footer.tpl'}
    
</div>