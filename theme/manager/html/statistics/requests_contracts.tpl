{$meta_title='Отказные заявки и заключенные договора' scope=parent}

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
    </script>
{/capture}

{capture name='page_styles'}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css"/>
{/capture}

<div class="page-wrapper">
    <div class="container-fluid">
        <div class="row page-titles">
            <div class="col-md-6 col-8 align-self-center">
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-file-chart"></i>Отчёт по отказным заявкам и заключенным договорам</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item"><a href="statistics">Статистика</a></li>
                    <li class="breadcrumb-item active">Отказные заявки и заключенные договора</li>
                </ol>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <form id="data">
                        <input type="hidden" name="to-do" value="report">
                        <div class="card-body">
                            <h4 class="card-title">Отказные заявки и заключенные договора {if $date_from}{$date_from|date} - {$date_to|date}{/if}</h4>
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
                                {if $from || $to}
                                    <div class="col-1 col-md-2">
                                        <a href="{url download='excel'}" class="btn btn-success">
                                            <i class="fas fa-file-excel"></i> Скачать
                                        </a>
                                    </div>
                                {/if}
                            </div>
                            <br/>
                    </form>
                    <div class="big-table" style="overflow: auto;position: relative;">
                        {if $orders}
                        <table class="table table-hover" id="basicgrid" style="display: inline-block;vertical-align: top;max-width: 100%;
                            overflow-x: auto;white-space: nowrap;-webkit-overflow-scrolling: touch;">
                            <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Договор</th>
                                    <th>ФИО</th>
                                    <th>Телефон</th>
                                    <th>Почта</th>
                                    <th>Сумма</th>
                                    <th>ПК/НК</th>
                                    <th>Менеджер</th>
                                    <th>Статус</th>
                                    <th>Причина отказа</th>
                                    <th>Промокод</th>
                                    <th>Дата возврата</th>
                                    <th>ПДН</th>
                                    <th>Дней займа</th>
                                    <th>Дата факт возврата</th>
                                    <th>Сумма выплачено</th>
                                    <th>Источник привлечения</th>
                                    <th>ID заявки</th>
                                    <th>ID клиента</th>
                                </tr>
                            </thead>
                            <tbody>
                            {foreach $orders as $order}
                                <tr>
                                    <td>{$order->date}</td>
                                    <td>{$order->contract->number}</td>
                                    <td>{$order->client->lastname} {$order->client->firstname} {$order->client->patronymic}</td>
                                    <td>{$order->client->phone_mobile}</td>
                                    <td>{$order->client->email}</td>
                                    <td>{$order->total_amt}</td>
                                    <td>
                                        {if $order->client_status}
                                            {if $order->client_status == 'pk'}
                                                ПК
                                            {elseif $order->client_status == 'crm'}
                                                ПК CRM
                                            {elseif $order->client_status == 'rep'}
                                                Повтор
                                            {elseif $order->client_status == 'nk'}
                                                Новая
                                            {/if}
                                        {/if}
                                    </td>
                                    <td>{$order->manager->name}</td>
                                    <td>{$order->status}</td>
                                    <td>{$order->reject_reason}</td>
                                    <td>{$order->promocode}</td>
                                    <td>{$order->contract->return_date}</td>
                                    <td>{$order->client->pdn}</td>
                                    <td>
                                        {if $order->period}
                                            {$order->period} {$order->period|plural:'день':'дней':'дня'}
                                        {/if}
                                    </td>
                                    <td>{$order->contract->close_date}</td>
                                    <td>{$order->payed_summ}</td>
                                    <td>
                                        {if !empty($order->utm_source)}
                                            {$order->utm_source}
                                        {else}
                                            Не оп
                                        {/if}
                                    </td>
                                    <td>{$order->order_id}</td>
                                    <td>{$order->user_id}</td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                    {if $total_pages_num>1}

                        {* Количество выводимых ссылок на страницы *}
                        {$visible_pages = 11}
                        {* По умолчанию начинаем вывод со страницы 1 *}
                        {$page_from = 1}

                        {* Если выбранная пользователем страница дальше середины "окна" - начинаем вывод уже не с первой *}
                        {if $current_page_num > floor($visible_pages/2)}
                            {$page_from = max(1, $current_page_num-floor($visible_pages/2)-1)}
                        {/if}

                        {* Если выбранная пользователем страница близка к концу навигации - начинаем с "конца-окно" *}
                        {if $current_page_num > $total_pages_num-ceil($visible_pages/2)}
                            {$page_from = max(1, $total_pages_num-$visible_pages-1)}
                        {/if}

                        {* До какой страницы выводить - выводим всё окно, но не более ощего количества страниц *}
                        {$page_to = min($page_from+$visible_pages, $total_pages_num-1)}
                        <div class="jsgrid-pager-container float-left" style="">
                            <div class="jsgrid-pager">
                                Страницы:

                                {if $current_page_num == 2}
                                    <span class="jsgrid-pager-nav-button "><a href="{url page=null}">Пред.</a></span>
                                {elseif $current_page_num > 2}
                                    <span class="jsgrid-pager-nav-button "><a href="{url page=$current_page_num-1}">Пред.</a></span>
                                {/if}

                                <span class="jsgrid-pager-page {if $current_page_num==1}jsgrid-pager-current-page{/if}">
                                        {if $current_page_num==1}1{else}<a href="{url page=null}">1</a>{/if}
                                    </span>
                                {section name=pages loop=$page_to start=$page_from}
                                    {* Номер текущей выводимой страницы *}
                                    {$p = $smarty.section.pages.index+1}
                                    {* Для крайних страниц "окна" выводим троеточие, если окно не возле границы навигации *}
                                    {if ($p == $page_from + 1 && $p != 2) || ($p == $page_to && $p != $total_pages_num-1)}
                                        <span class="jsgrid-pager-page {if $p==$current_page_num}jsgrid-pager-current-page{/if}">
                                            <a href="{url page=$p}">...</a>
                                        </span>
                                    {else}
                                        <span class="jsgrid-pager-page {if $p==$current_page_num}jsgrid-pager-current-page{/if}">
                                            {if $p==$current_page_num}{$p}{else}<a href="{url page=$p}">{$p}</a>{/if}
                                        </span>
                                    {/if}
                                {/section}
                                <span class="jsgrid-pager-page {if $current_page_num==$total_pages_num}jsgrid-pager-current-page{/if}">
                                        {if $current_page_num==$total_pages_num}{$total_pages_num}{else}<a
                                            href="{url page=$total_pages_num}">{$total_pages_num}</a>{/if}
                                    </span>

                                {if $current_page_num<$total_pages_num}
                                    <span class="jsgrid-pager-nav-button"><a
                                                href="{url page=$current_page_num+1}">След.</a></span>
                                {/if}

                                &nbsp;&nbsp; {$current_page_num} из {$total_pages_num}
                            </div>
                        </div>
                    {/if}
                    <div class="float-right pt-1">
                        <select onchange="if (this.value) window.location.href = this.value"
                                class="form-control form-control-sm page_count" name="page-count">
                            <option value="{url page_count=25}" {if $page_count==25}selected=""{/if}>Показывать 25
                            </option>
                            <option value="{url page_count=50}" {if $page_count==50}selected=""{/if}>Показывать 50
                            </option>
                            <option value="{url page_count=100}" {if $page_count==100}selected=""{/if}>Показывать 100
                            </option>
                        </select>
                    </div>
                    {else}
                    <div class="alert alert-info">
                        <h4>Укажите параметры для отчета</h4>
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