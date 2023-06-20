{$meta_title='' scope=parent}

{capture name='page_scripts'}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script>
        $(function () {

            moment.locale('ru');

            $('.daterange').daterangepicker({
                locale: {
                    format: 'DD.MM.YYYY',
                    "customRangeLabel": "Произвольно",
                },
                default: '',
                ranges: {
                    'Cегодня': [moment(), moment()],
                    'Вчера': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                    'Последние 7 дней': [moment().subtract(6, 'days'), moment()],
                    'Последние 30 дней': [moment().subtract(29, 'days'), moment()],
                    'Текущая неделя': [moment().startOf('week'), moment()],
                    'Прошлая неделя': [moment().startOf('week').subtract(7, 'days'), moment().startOf('week').subtract(1, 'days')],
                    'Текущий месяц': [moment().startOf('month'), moment().endOf('month')],
                    'Прошлый месяц': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                    'Текущий год': [moment().startOf('year'), moment()]
                }
            });

        });

        $('.checkContract').click(function() {
            let input = $(this).find('input');
            if (input.prop('checked')) {
                input.prop('checked', false);
            } else {
                input.prop('checked', true)
            }
        });

        $('.searchContract').keyup(function() {
            let search = $(this).val();

            $('#contracts tbody tr').each(function(key, item) {
                let number = $(item).attr('data-number');
                if (number.indexOf(search) !== -1) {
                    $(item).css('display', 'table-row');
                } else {
                    $(item).css('display', 'none');
                }
            });
        });

        $('#download_xml').click(function () {
            let contractsIds = [];
            $('#contracts input.contract_id:checked').each(function(key, contract_input) {
                contractsIds.push(contract_input.value)
            });
            if (contractsIds.length) {
                $.ajax({
                    type: 'GET',
                    data: {
                        action: 'generate_fedresurs',
                        contracts: contractsIds,
                    },
                    dataType: 'json',
                    success: function(resp){
                        if (resp.status == 'ok') {
                            let link = document.createElement('a');
                            link.setAttribute('href','https://crm.barents-finans.ru/files/fedresurs.xml');
                            link.setAttribute('download','report.xml');
                            link.click();
                        } else {
                            Swal.fire({
                                title: 'Ошибка!',
                                text: 'Произошла серверная ошибка! Попробуй повторить операцию позже!',
                                type: 'error',
                            });
                        }
                    }
                })
            } else {
                Swal.fire({
                    title: 'Ошибка!',
                    text: 'Нет выбранных контрактов для формирования отчёта',
                    type: 'error',
                });
            }
        });
    </script>
{/capture}

{capture name='page_styles'}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
    <style>
        .table th td {
            text-align: center !important;
        }

        table {
            font-size: 11px !important;
        }

        label {
            font-size: 12px !important;
            margin-bottom: 0 !important;
        }

        .btn, button, select, input {
            font-size: 12px !important;
        }
        .hidden_row {
            display: none;
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
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-file-chart"></i>Формирование списка для Федресурса</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="tools">Инструменты</a></li>
                    <li class="breadcrumb-item active">Формирование списка для Федресурса</li>
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
                    <form id="data">
                        <input type="hidden" name="to-do" value="report">
                        <div class="card-body">
                            <h4 class="card-title">Список {if $date_from}{$date_from|date} - {$date_to|date}{/if}</h4>
                            <div class="row">
                                <div class="input-group" style="width: 20%">
                                    <div style="margin-left: 12px; width: 100%" id="calendar">
                                        <input type="text" name="daterange" style="text-align: center; width: 100%"
                                               class="form-control daterange"
                                               value="{if $from && $to}{$from}-{$to}{/if}">
                                    </div>
                                </div>
                                <div class="col-2 col-md-1">
                                    <button type="submit" class="btn btn-info">Применить</button>
                                </div>
                                <div class="col-1 col-md-2">
                                    <button style="margin-left: 20px;" id="download_xml" type="button" class="btn btn-success">Скачать отчёт</button>
                                </div>
                            </div>
                            <br/>
                    </form>
                    {if $from}
                        <table class="table table-hover" id="contracts" style="display: inline-block;vertical-align: top;max-width: 100%;
                            overflow-x: auto;white-space: nowrap;-webkit-overflow-scrolling: touch;">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Дата</th>
                                    <th>ФИО</th>
                                    <th>Договор</th>
                                    <th>Дата возврата</th>
                                    <th>Сумма</th>
                                    <th>Статус</th>
                                    <th>Дней займа</th>
                                    <th>Период просрочки</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>
                                        <input type="text" class="form-control searchContract" placeholder="Поиск по договору">
                                    </th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach $contracts as $contract}
                                    <tr id="{$contract->id}" data-number="{$contract->number}" style="cursor: pointer" class="checkContract">
                                        <td>
                                            <input class="contract_id" type="checkbox" checked value="{$contract->id}">
                                        </td>
                                        <td>
                                            {$contract->user->lastname}
                                            {$contract->user->firstname}
                                            {$contract->user->patronymic}
                                        </td>
                                        <td>{$contract->date|date}</td>
                                        <td>
                                            <a target="_blank" href="order/{$contract->order_id}">{$contract->number}</a>
                                        </td>
                                        <td>
                                            {$contract->return_date|date}
                                        </td>
                                        <td>{$contract->amount*1}</td>
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
                                            {$contract->period}
                                        </td>
                                        <td>
                                            {$contract->expired_days} дн.
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
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