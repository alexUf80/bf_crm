{$meta_title='Статистика выданных займов' scope=parent}

{capture name='page_scripts'}

    <script src="theme/manager/assets/plugins/moment/moment.js"></script>

    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script>
    $(function(){
        $('.daterange').daterangepicker({
            autoApply: true,
            locale: {
                format: 'DD.MM.YYYY'
            },
            default:''
        });

        var excel_url = "{url download='excel'}";
        $(document).on('change', '.nbki', function (e) {

            var oldUrl = excel_url;
            var newUrl = oldUrl;

            if ($('.nbki').is(':checked')){
                newUrl = oldUrl+"&nbki=1";
            }
            $('.download_excel').attr("href", newUrl);
        });
    })
    </script>
{/capture}

{capture name='page_styles'}

    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <!-- Daterange picker plugins css -->
    <link href="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">

    <style>
    .table td {
//        text-align:center!important;
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
                    <span>Статистика выданных займов</span>
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="statistics">Статистика</a></li>
                    <li class="breadcrumb-item active">Выданные займы</li>
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
                        <h4 class="card-title">Выданные займы за период {if $date_from}{$date_from|date} - {$date_to|date}{/if}</h4>
                        <form>
                            <div class="row">
                                <div class="col-6 col-md-4">
                                    <div class="input-group mb-3">
                                        <input type="text" name="daterange" class="form-control daterange" value="{if $from && $to}{$from}-{$to}{/if}">
                                        <div class="input-group-append">
                                            <span class="input-group-text">
                                                <span class="ti-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4">
                                    <button type="submit" class="btn btn-info">Сформировать</button>
                                </div>
                                {if $date_from || $date_to}
                                <div class="col-12 col-md-4 text-right">
                                    <div class="custom-checkbox" style="display: inline-block; padding-right: 15px;">
                                        <input type="checkbox" class="input-custom nbki" name="nbki" id="nbki" checked/>
                                        <label for="nbki">Включить данные НБКИ</label>
                                    </div>
                                    <a href="{url download='excel' nbki=1}" class="btn btn-success download_excel">
                                        <i class="fas fa-file-excel"></i> Скачать
                                    </a>
                                </div>
                                {/if}
                            </div>

                        </form>

                        {if $from}
                        <table class="table table-hover" style="display: inline-block;vertical-align: top;max-width: 100%;
                            overflow-x: auto;white-space: nowrap;-webkit-overflow-scrolling: touch;">
                            <tr>
                                <th>ID клиента</th>
                                <th>ID договора</th>
                                <th>Дата</th>
                                <th>Договор</th>
                                <th>Дата возврата</th>
                                <th>ФИО</th>
                                <th>Дата рождения</th>
                                <th>Телефон</th>
                                <th>Email</th>
                                <th>Сумма</th>
                                <th>Пролонгация</th>
                                <th>Сумма пролонгаций</th>
                                <th>Источник</th>
                                <th>Сумма оплачено</th>
                                <th>ПК/НК</th>
                                <th>Тип ПК</th>
                                <th>Менеджер</th>
                                <th>Статус</th>
                                <th>Дата факт возврата</th>
                                <th>ПДН</th>
                                <th>Дней займа</th>
                                <th>Промокод</th>
                                <th>МФО2НБКИ</th>
                                <th>МАНИМАЭН</th>
                                <th>Зона качества</th>
                            </tr>

                            {foreach $contracts as $contract}
                            <tr {($contract->utm_source == 'kpk' || $contract->utm_source == 'part1')? 'style="background: #f1f1f1"' : ''}>
                                <td>{$contract->user_id}</td>
                                <td>{$contract->order_id}</td>
                                <td>{$contract->date|date}</td>
                                <td>
                                    <a target="_blank" href="order/{$contract->order_id}">{$contract->number}</a>
                                </td>
                                <td>
                                    {$contract->return_date|date}
                                </td>
                                <td>
                                    <a href="client/{$contract->user_id}" target="_blank">
                                        {$contract->lastname|escape}
                                        {$contract->firstname|escape}
                                        {$contract->patronymic|escape}
                                        {$contract->birth|escape}
                                    </a>
                                </td>
                                <td>{$contract->birth}</td>
                                <td>{$contract->phone_mobile}</td>
                                <td><small>{$contract->email}</small></td>
                                <td>{$contract->amount*1}</td>
                                <td>{$contract->count_prolongation}</td>
                                <td>{$contract->prolongations_amount}</td>
                                <td>{$contract->utm_source}</td>
                                <td>{$contract->sumPayed|number_format:2:',':''}</td>
                                <td>
                                    {if $contract->client_status == 'pk'}ПК{/if}
                                    {if $contract->client_status == 'nk'}НК{/if}
                                    {if $contract->client_status == 'crm'}ПК CRM{/if}
                                    {if $contract->client_status == 'rep'}НК{/if}
                                </td>
                                <td>
                                    {$contract->type_pk}
                                </td>
                                <td>
                                    {$managers[$contract->manager_id]->name|escape}
                                </td>
                                <td>

                                    {if $contract->collection_status}
                                    {if $contract->sold}
                                        ЮК
                                    {else}
                                        МКК
                                    {/if}
                                        {$collection_statuses[$contract->collection_status]}
                                    {else}
                                        {$statuses[$contract->status]}
                                    {/if}
                                </td>
                                <td>
                                    {if !empty($contract->close_date)}{$contract->close_date|date}{else}-{/if}
                                </td>
                                <td>
                                    {$contract->pdn}
                                </td>
                                <td>
                                    {$contract->period}
                                </td>
                                <td>
                                    {$contract->promocode}
                                </td>
                                <td>
                                    {$contract->score_mf0_2_nbki}
                                </td>
                                <td>
                                    {$contract->maniman}
                                </td>
                                <td>
                                    {$contract->zone}
                                </td>
                            </tr>
                            {/foreach}

                        </table>
                        {else}
                            <div class="alert alert-info">
                                <h4>Укажите даты для формирования отчета</h4>
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