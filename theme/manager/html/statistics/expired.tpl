{$meta_title='Просроченные займы' scope=parent}

{capture name='page_scripts'}
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script>
        $(function () {
            $('.daterange').daterangepicker({
                autoApply: true,
                locale: {
                    format: 'DD.MM.YYYY'
                },
                default: ''
            });
        })
    </script>
{/capture}

{capture name='page_styles'}
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Daterange picker plugins css -->
    <link href="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
    <style>
        .table th td {
            text-align: center !important;
        }
    </style>
{/capture}

<div class="page-wrapper">
    <!-- ============================================================== -->
    <!-- Container fluid  -->
    <!-- ============================================================== -->
    <div class="container-fluid">
        <!-- ============================================================== -->
        <!-- Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor mb-0 mt-0">
                    <i class="mdi mdi-file-chart"></i>
                    <span>Просроченные займы </span>
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="statistics">Статистика</a></li>
                    <li class="breadcrumb-item active">Просроченные займы</li>
                </ol>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <div class="row">
            <div class="col-12">
                <!-- Column -->
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title clearfix">
                            <div class="float-left">
                                Просроченные займы
                            </div>
                            <div class="float-right">
                                <a href="{url download='excel'}" class="btn btn-success ">
                                    <i class="fas fa-file-excel"></i> Скачать
                                </a>
                            </div>
                        </h4>


                        {if $contracts}
                            <table>
                                <thead>
                                <tr>
                                    <th>Номер заявки</th>
                                    <th>Договор / Дата выдачи</th>
                                    <th>ФИО</th>
                                    <th>Телефон</th>
                                    <th>Регистрация</th>
                                    <th>Проживание</th>
                                    <th>Займ</th>
                                    <th>Начислено</th>
                                    <th>Дата последнего платежа</th>
                                    <th>Сумма платежа</th>
                                    <th>Конт. лицо</th>
                                    <th>ИНН</th>
                                    <th>Работа</th>
                                </tr>

                                </thead>

                                <tbody id="table_content">
                                {foreach $contracts as $contract}
                                    <tr>
                                        <td>
                                            <a href="order/{$contract->order_id}">{$contract->order_id}</a>
                                        </td>
                                        <td>
                                            {if $contract->outer_id}
                                                <small class="text-primary">{$contract->outer_id}</small>
                                                <br/>
                                            {/if}
                                            <a href="order/{$contract->order_id}">{$contract->number|escape}</a>
                                            <br/>
                                            {$contract->create_date|date}
                                        </td>
                                        <td>
                                            <span class="label label-primary">{$contract->client_status}</span>
                                            <a href="client/{$contract->user_id}" target="_blank">
                                                <small style="text-transform:uppercase;font-weight:300;">{$contract->user->lastname|escape} {$contract->user->firstname|escape} {$contract->user->patronymic|escape}</small>
                                            </a>
                                            <br/>
                                            <small>{$contract->user->birth}</small>
                                            <span class="label label-primary">{$contract->user->age}</span>
                                        </td>
                                        <td>
                                            <strong>{$contract->user->phone_mobile}</strong>
                                            <br/>
                                            <small>{$contract->user->email}</small>
                                        </td>
                                        <td>
                                            <span class="label label-primary">{$contract->user->Regcode}</span>
                                            <small>

                                                {if $contract->user->Regindex}{$contract->user->Regindex|escape}, {/if}
                                                {$contract->user->Regregion} {$contract->user->Regregion_shorttype|escape}
                                                ,
                                                {if $contract->user->Regcity}{$contract->user->Regcity|escape} {$contract->user->Regcity_shorttype|escape},{/if}
                                                {if $contract->user->Regdistrict}{$contract->user->Regdistrict|escape} {$contract->user->Regdistrict_shorttype|escape},{/if}
                                                {if $contract->user->Reglocality}{$contract->user->Reglocality_shorttype|escape} {$contract->user->Reglocality|escape},{/if}
                                                {$contract->user->Regstreet} {$contract->user->Regstreet_shorttype|escape}
                                                ,
                                                д.{$contract->user->Reghousing|escape},
                                                {if $contract->user->Regbuilding}стр. {$contract->user->Regbuilding|escape},{/if}
                                                {if $contract->user->Regroom}кв.{$contract->user->Regroom|escape}{/if}
                                            </small>
                                        </td>
                                        <td>
                                            <small>
                                                {if $contract->user->Faktindex}{$contract->user->Faktindex|escape}, {/if}
                                                {$contract->user->Faktregion} {$contract->user->Faktregion_shorttype|escape}
                                                ,
                                                {if $contract->user->Faktcity}{$contract->user->Faktcity|escape} {$contract->user->Faktcity_shorttype|escape},{/if}
                                                {if $contract->user->Faktdistrict}{$contract->user->Faktdistrict|escape} {$contract->user->Faktdistrict_shorttype|escape},{/if}
                                                {if $contract->user->Faktlocality}{$contract->user->Faktlocality_shorttype|escape} {$contract->user->Faktlocality|escape},{/if}
                                                {if $contract->user->Faktstreet}{$contract->user->Faktstreet|escape} {$contract->user->Faktstreet_shorttype|escape},{/if}
                                                д.{$contract->user->Fakthousing|escape},
                                                {if $contract->user->Faktbuilding}стр. {$contract->user->Faktbuilding|escape},{/if}
                                                {if $contract->user->Faktroom}кв.{$contract->user->Faktroom|escape}{/if}
                                            </small>
                                        </td>
                                        <td>
                                            <small>Сумма:&nbsp;{$contract->amount*1}P</small>
                                            <br/>
                                            <small>Срок:&nbsp;{$contract->period|escape}
                                                &nbsp;{$contract->period|plural:'день':'дней':'дня'}</small>
                                            <br/>
                                            <small>Возврат:&nbsp;{$contract->return_date|date}</small>
                                        </td>
                                        <td>
                                            <small>ОД: {$contract->loan_body_summ*1}P</small>
                                            <br/>
                                            <small>Пр-ты: {$contract->loan_percents_summ*1}</small>
                                            <br/>
                                            <small>
                                                Всего: {$contract->loan_body_summ + $contract->loan_percents_summ}</small>

                                        </td>
                                        {if !empty($contract->last_operation)}
                                            <td>
                                                {$contract->last_operation->created|date}
                                            </td>
                                            <td>
                                                {$contract->last_operation->amount|number_format:'2':',':' '}
                                            </td>
                                        {else}
                                            <td colspan="2">
                                                Оплат не поступало
                                            </td>
                                        {/if}
                                        <td>
                                            {if $contract->user->contact_person_name}
                                                {$contract->user->contact_person_name|escape}
                                                {if $contract->user->contact_person_relation}({$contract->user->contact_person_relation|escape}){/if}
                                                {$contract->user->contact_person_phone|escape}
                                            {/if}
                                        </td>
                                        <td>
                                            {$contract->user->inn}
                                        </td>
                                        <td>
                                            {$contract->user->workplace}
                                            <br/>
                                            <strong>{$contract->user->workphone}</strong>
                                        </td>
                                    </tr>
                                {/foreach}
                                </tbody>

                            </table>
                            <p class="text-danger">
                                * данные по количеству продлений доступны с 01.01.2022
                            </p>
                        {else}
                            <div class="alert alert-info">
                                <h4>Нет данных для отображения</h4>
                            </div>
                        {/if}

                    </div>
                </div>
                <!-- Column -->
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
    <!-- ============================================================== -->
    <!-- footer -->
    <!-- ============================================================== -->
    {include file='footer.tpl'}
    <!-- ============================================================== -->
    <!-- End footer -->
    <!-- ============================================================== -->
</div>