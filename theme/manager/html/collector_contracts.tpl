{$meta_title='Мои договоры' scope=parent}

{capture name='page_scripts'}
    <script src="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/jquery.magnific-popup.min.js"></script>
    <script src="theme/manager/assets/plugins/moment/moment.js"></script>
    <script src="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.js"></script>
    <!-- Date range Plugin JavaScript -->
    <script src="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.js"></script>
    <script src="theme/manager/assets/plugins/daterangepicker/daterangepicker.js"></script>
    <script>
        $(function () {
            $('.js-open-show').hide();
            $('#casual_sms').on('click', function (e) {
                e.preventDefault();

                $('.casual-sms-form').toggle('slow');
            });
            $(document).on('click', '.js-mango-call', function (e) {
                e.preventDefault();

                var _phone = $(this).data('phone');
                var _user = $(this).data('user');
                var _yuk = $(this).hasClass('js-yuk') ? 1 : 0;

                Swal.fire({
                    title: 'Выполнить звонок?',
                    text: "Вы хотите позвонить на номер: " + _phone,
                    type: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    cancelButtonText: 'Отменить',
                    confirmButtonText: 'Да, позвонить'
                }).then((result) => {
                    if (result.value) {

                        $.ajax({
                            url: '/ajax/communications.php',
                            data: {
                                action: 'check',
                                user_id: _user,
                            },
                            success: function (resp) {
                                if (resp == 1) {
                                    $.ajax({
                                        url: 'ajax/mango_call.php',
                                        data: {
                                            phone: _phone,
                                            yuk: _yuk
                                        },
                                        beforeSend: function () {

                                        },
                                        success: function (resp) {
                                            if (!!resp.error) {
                                                if (resp.error == 'empty_mango') {
                                                    Swal.fire(
                                                        'Ошибка!',
                                                        'Необходимо указать Ваш внутренний номер сотрудника Mango-office.',
                                                        'error'
                                                    )
                                                }

                                                if (resp.error == 'empty_mango') {
                                                    Swal.fire(
                                                        'Ошибка!',
                                                        'Не хватает прав на выполнение операции.',
                                                        'error'
                                                    )
                                                }
                                            } else if (resp.success) {
                                                Swal.fire(
                                                    '',
                                                    'Выполняется звонок.',
                                                    'success'
                                                )

                                                $.ajax({
                                                    url: 'ajax/communications.php',
                                                    data: {
                                                        action: 'add',
                                                        user_id: _user,
                                                        type: 'call',
                                                    }
                                                });
                                            } else {
                                                console.error(resp);
                                                Swal.fire(
                                                    'Ошибка!',
                                                    '',
                                                    'error'
                                                )
                                            }
                                        }
                                    })

                                } else {
                                    Swal.fire(
                                        'Ошибка!',
                                        'Исчерпан лимит коммуникаций.',
                                        'error'
                                    )

                                }
                            }
                        })


                    }
                })


            });
            $(document).on('click', '.js-open-contract', function (e) {
                e.preventDefault();
                var _id = $(this).data('id')
                if ($(this).hasClass('open')) {
                    $(this).removeClass('open');
                    $('.js-open-hide.js-dopinfo-' + _id).show();
                    $('.js-open-show.js-dopinfo-' + _id).hide();
                } else {
                    $(this).addClass('open');
                    $('.js-open-hide.js-dopinfo-' + _id).hide();
                    $('.js-open-show.js-dopinfo-' + _id).show();
                }
            })
            $(document).on('change', '.js-contact-status', function () {
                var contact_status = $(this).val();
                var contract_id = $(this).data('contract');
                var user_id = $(this).data('user');
                var $form = $(this).closest('form');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: {
                        action: 'contact_status',
                        user_id: user_id,
                        contact_status: contact_status
                    },
                    success: function (resp) {
                        if (contact_status == 1)
                            $('.js-contact-status-block.js-dopinfo-' + contract_id).html('<span class="label label-success">Контактная</span>')
                        else if (contact_status == 2)
                            $('.js-contact-status-block.js-dopinfo-' + contract_id).html('<span class="label label-danger">Не контактная</span>')
                        else if (contact_status == 0)
                            $('.js-contact-status-block.js-dopinfo-' + contract_id).html('<span class="label label-warning">Нет данных</span>')

                    }
                })
            })
            $(document).on('change', '.js-contactperson-status', function () {
                var contact_status = $(this).val();
                var contactperson_id = $(this).data('contactperson');
                var $form = $(this).closest('form');

                $.ajax({
                    url: $form.attr('action'),
                    type: 'POST',
                    data: {
                        action: 'contactperson_status',
                        contactperson_id: contactperson_id,
                        contact_status: contact_status
                    }
                })
            })
            $(document).on('change', '.js-collection-manager', function () {
                var manager_id = $(this).val();
                var contract_id = $(this).data('contract');

                var manager_name = $(this).find('option:selected').text();

                $.ajax({
                    type: 'POST',
                    data: {
                        action: 'collection_manager',
                        manager_id: manager_id,
                        contract_id: contract_id
                    },
                    success: function (resp) {
                        if (manager_id == 0)
                            $('.js-collection-manager-block.js-dopinfo-' + contract_id).html('');
                        else
                            $('.js-collection-manager-block.js-dopinfo-' + contract_id).html(manager_name);
                    }
                })
            })
            $(document).on('click', '.js-open-comment-form', function (e) {
                e.preventDefault();

                if ($(this).hasClass('js-contactperson')) {
                    var contactperson_id = $(this).data('contactperson');
                    $('#modal_add_comment [name=contactperson_id]').val(contactperson_id);
                    $('#modal_add_comment [name=action]').val('contactperson_comment');
                    $('#modal_add_comment [name=order_id]').val($(this).data('order'));
                } else {
                    var contactperson_id = $(this).data('contactperson');
                    $('#modal_add_comment [name=order_id]').val($(this).data('order'));
                    $('#modal_add_comment [name=action]').val('order_comment');
                }


                $('#modal_add_comment [name=text]').text('')
                $('#modal_add_comment').modal();
            });
            $(document).on('click', '.js-open-sms-modal', function (e) {
                e.preventDefault();

                var _user_id = $(this).data('user');
                var _order_id = $(this).data('order');
                var _yuk = $(this).hasClass('is-yuk') ? 1 : 0;

                $('#modal_send_sms [name=user_id]').val(_user_id);
                $('#modal_send_sms [name=order_id]').val(_order_id);
                $('#modal_send_sms [name=yuk]').val(_yuk);
                $('#modal_send_sms').modal();
            });
            $(document).on('submit', '.js-sms-form', function (e) {
                e.preventDefault();

                var $form = $(this);

                var _user_id = $form.find('[name=user_id]').val();

                if ($form.hasClass('loading'))
                    return false;

                $.ajax({
                    url: '/ajax/communications.php',
                    data: {
                        action: 'check',
                        user_id: _user_id,
                    },
                    success: function (resp) {
                        if (!!resp) {
                            $.ajax({
                                type: 'POST',
                                data: $form.serialize(),
                                beforeSend: function () {
                                    $form.addClass('loading')
                                },
                                success: function (resp) {
                                    $form.removeClass('loading');
                                    $('#modal_send_sms').modal('hide');

                                    if (!!resp.error) {
                                        Swal.fire({
                                            timer: 5000,
                                            title: 'Ошибка!',
                                            text: resp.error,
                                            type: 'error',
                                        });
                                    } else {
                                        Swal.fire({
                                            timer: 5000,
                                            title: '',
                                            text: 'Сообщение отправлено',
                                            type: 'success',
                                        });
                                    }
                                },
                            })

                        } else {
                            Swal.fire({
                                title: 'Ошибка!',
                                text: 'Исчерпан лимит коммуникаций',
                                type: 'error',
                            });

                        }
                    }
                })

            });
            $(document).on('change', '.js-workout-input', function () {
                var $this = $(this);
                var _contract = $this.val();
                var _workout = $this.is(':checked') ? 1 : 0;

                $.ajax({
                    type: 'POST',
                    data: {
                        action: 'workout',
                        contract_id: _contract,
                        workout: _workout
                    },
                    beforeSend: function () {
                        $('.jsgrid-load-shader').show();
                        $('.jsgrid-load-panel').show();
                    },
                    success: function (resp) {
                        if (_workout)
                            $this.closest('.js-contract-row').addClass('workout-row');
                        else
                            $this.closest('.js-contract-row').removeClass('workout-row');

                        $('.jsgrid-load-shader').hide();
                        $('.jsgrid-load-panel').hide();
                    }
                })

            });
            $(document).on('click', '.js-distribute-open', function (e) {
                e.preventDefault();

                $('.js-distribute-contract').remove();
                $('.js-contract-row').each(function () {
                    $('#form_distribute').append('<input type="hidden" name="contracts[]" class="js-distribute-contract" value="' + $(this).data('contract') + '" />');
                });

                $('.js-select-type').val('all');

                $('#modal_distribute').modal();
            });
            $(document).on('change', '.js-select-type', function () {
                var _current = $(this).val();
                if (_current == 'all') {
                    $('.js-distribute-contract').remove();
                    $('.js-contract-row').each(function () {
                        $('#form_distribute').append('<input type="hidden" name="contracts[]" class="js-distribute-contract" value="' + $(this).data('contract') + '" />');
                    });
                } else if (_current == 'checked') {
                    $('.js-distribute-contract').remove();
                    $('.js-contract-check').each(function () {
                        if ($(this).is(':checked')) {
                            $('#form_distribute').append('<input type="hidden" name="contracts[]" class="js-distribute-contract" value="' + $(this).val() + '" />');
                        }
                    })
                } else if (_current == 'optional') {
                    $('.js-distribute-contract').remove();
                }

            });
            $(document).on('submit', '#form_distribute', function (e) {
                e.preventDefault();

                var $form = $(this);

                if ($form.hasClass('loading'))
                    return false;

                console.log(location.hash)
                var _hash = location.hash.replace('#', '?');
                $.ajax({
                    url: '/my_contracts' + _hash,
                    data: $form.serialize(),
                    type: 'POST',
                    beforeSend: function () {
                        $form.addClass('loading');
                    },
                    success: function (resp) {
                        if (resp.success) {
                            $('#modal_distribute').modal('hide');

                            Swal.fire({
                                timer: 5000,
                                title: 'Договора распределены.',
                                type: 'success',
                            });
//                        location.reload();
                        } else {
                            Swal.fire({
                                text: resp.error,
                                type: 'error',
                            });

                        }
                        $form.removeClass('loading');
                    }
                })
            })
            $(document).on('change', '.js-select-type', function () {
                var _current = $(this).val();

                if (_current == 'optional') {
                    $('.js-input-quantity').fadeIn();
                } else {
                    $('.js-input-quantity').fadeOut();
                }
            })
        })
    </script>
{/capture}

{capture name='page_styles'}
    <link href="theme/{$settings->theme|escape}/assets/plugins/Magnific-Popup-master/dist/magnific-popup.css"
          rel="stylesheet"/>
    <link type="text/css" rel="stylesheet" href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid.min.css"/>
    <link type="text/css" rel="stylesheet"
          href="theme/{$settings->theme|escape}/assets/plugins/jsgrid/jsgrid-theme.min.css"/>
    <link href="theme/manager/assets/plugins/bootstrap-datepicker/bootstrap-datepicker.min.css" rel="stylesheet"
          type="text/css"/>
    <!-- Daterange picker plugins css -->
    <link href="theme/manager/assets/plugins/timepicker/bootstrap-timepicker.min.css" rel="stylesheet">
    <link href="theme/manager/assets/plugins/daterangepicker/daterangepicker.css" rel="stylesheet">
    <style>
        .jsgrid-table {
            margin-bottom: 0
        }

        .label {
            white-space: pre;
        }

        .workout-row > td {
            background: #f2f7f8 !important;
        }

        .workout-row a, .workout-row small, .workout-row span {
            color: #555 !important;
            font-weight: 300;
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
                <h3 class="text-themecolor mb-0 mt-0"><i class="mdi mdi-animation"></i> Мои договоры</h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Договоры</li>
                </ol>
            </div>
            <div class="col-md-6 col-4 ">
                <div class="row">
                    <div class="col-6 ">
                        {if in_array($manager->role, ['developer', 'admin', 'chief_collector', 'team_collector'])}
                            <button class="btn btn-primary js-distribute-open float-right" type="button"><i
                                        class="mdi mdi-account-convert"></i> Распределить
                            </button>
                        {/if}
                    </div>
                </div>
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
                        <div class="clearfix">
                            <h4 class="card-title  float-left">Список договоров </h4>
                            <div class="float-right js-filter-client">
                                {foreach $periods as $period}
                                    <a href="#" class="btn btn-xs btn-outline-success">{$period->name}</a>
                                {/foreach}
                            </div>
                        </div>
                        <div id="basicgrid" class="jsgrid" style="position: relative; width: 100%;">
                            <div class="jsgrid-grid-header jsgrid-header-scrollbar">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tr class="jsgrid-header-row">
                                        <th style="width:20px;" class="jsgrid-header-cell">#</th>
                                        <th style="width:80px"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'manager_id_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'manager_id_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            {if $sort == 'manager_id_asc'}<a
                                                href="{url page=null sort='manager_id_desc'}">Пользователь</a>
                                            {else}<a href="{url page=null sort='manager_id_asc'}">
                                                    Пользователь</a>{/if}
                                        </th>
                                        <th style="width: 60px;"
                                            class="jsgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'order_id_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'order_id_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            {if $sort == 'order_id_asc'}<a href="{url page=null sort='order_id_desc'}">
                                                    ID</a>
                                            {else}<a href="{url page=null sort='order_id_asc'}">ID</a>{/if}
                                        </th>
                                        <th style="width: 120px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'fio_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'fio_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'fio_asc'}<a href="{url page=null sort='fio_desc'}">ФИО</a>
                                            {else}<a href="{url page=null sort='fio_asc'}">ФИО</a>{/if}
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'body_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'body_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'body_asc'}<a href="{url page=null sort='body_desc'}">ОД,
                                                руб</a>
                                            {else}<a href="{url page=null sort='body_asc'}">ОД, руб</a>{/if}
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'percents_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'percents_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'percents_asc'}<a href="{url page=null sort='percents_desc'}">
                                                    %, руб</a>
                                            {else}<a href="{url page=null sort='percents_asc'}">%, руб</a>{/if}
                                        </th>
                                        <th style="width: 70px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'total_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'total_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'total_asc'}<a href="{url page=null sort='total_desc'}">Итог,
                                                руб</a>
                                            {else}<a href="{url page=null sort='total_asc'}">Итог, руб</a>{/if}
                                        </th>
                                        <th style="width: 80px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'phone_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'phone_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'phone_asc'}<a href="{url page=null sort='phone_desc'}">
                                                    Телефон</a>
                                            {else}<a href="{url page=null sort='phone_asc'}">Телефон</a>{/if}
                                        </th>
                                        <th style="width: 80px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'return_asc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'return_desc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            {if $sort == 'return_asc'}<a href="{url page=null sort='return_desc'}">
                                                    Просрочен</a>
                                            {else}<a href="{url page=null sort='return_asc'}">Просрочен</a>{/if}
                                        </th>
                                        <th style="width: 80px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'return_asc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'return_desc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                            {if $sort == 'return_asc'}<a href="{url page=null sort='return_desc'}">Дата
                                                платежа</a>
                                            {else}<a href="{url page=null sort='return_asc'}">Дата платежа</a>{/if}
                                        </th>
                                        <th style="width: 80px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'tag_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'tag_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            {if $sort == 'tag_asc'}<a href="{url page=null sort='tag_desc'}">Тег</a>
                                            {else}<a href="{url page=null sort='tag_asc'}">Тег</a>{/if}
                                        </th>
                                        <th style="width: 140px;"
                                            class="jsgrid-header-cell jsgrid-header-sortable {if $sort == 'birth_asc'}jsgrid-header-sort jsgrid-header-sort-asc{elseif $sort == 'birth_desc'}jsgrid-header-sort jsgrid-header-sort-desc{/if}">
                                            Комментарий
                                        </th>
                                    </tr>

                                    <tr class="jsgrid-filter-row" id="search_form">
                                        <td style="width: 20px;" class="jsgrid-cell">
                                            <div class="custom-checkbox custom-control">
                                                <input type="checkbox" class="custom-control-input" id="check_all"
                                                       value=""/>
                                                <label for="check_all" title="Отметить все"
                                                       class="custom-control-label"> </label>
                                            </div>
                                        </td>
                                        <td style="width: 80px;" class="jsgrid-cell">
                                            <select class="form-control" name="manager_id">
                                                <option value="0"></option>
                                                {foreach $collectors as $collector}
                                                        <option value="{$collector->id}">{$collector->name}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                        <td style="width: 60px;" class="jsgrid-cell jsgrid-align-right">
                                            <input type="hidden" name="sort" value="{$sort}"/>
                                            <input type="text" name="order_id" value="{$search['order_id']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 120px;" class="jsgrid-cell">
                                            <input type="text" name="fio" value="{$search['fio']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 70px;" class="jsgrid-cell"></td>
                                        <td style="width: 70px;" class="jsgrid-cell"></td>
                                        <td style="width: 70px;" class="jsgrid-cell"></td>
                                        <td style="width: 80px;" class="jsgrid-cell">
                                            <input type="text" name="phone" value="{$search['phone']}"
                                                   class="form-control input-sm">
                                        </td>
                                        <td style="width: 80px;" class="jsgrid-cell">
                                            <div class="row no-gutter">
                                                <div class="col-6 pr-0">
                                                    <input type="text" placeholder="c" name="delay_from"
                                                           value="{$search['delay_from']}"
                                                           class="form-control input-sm">
                                                </div>
                                                <div class="col-6 pl-0">
                                                    <input type="text" name="delay_to" placeholder="по"
                                                           value="{$search['delay_to']}" class="form-control input-sm">
                                                </div>
                                            </div>
                                        </td>
                                        <td style="width: 80px;" class="jsgrid-cell"></td>
                                        <td style="width: 80px;" class="jsgrid-cell">
                                            <select class="form-control" name="tag_id">
                                                <option value="0"></option>
                                                {foreach $collector_tags as $t}
                                                    <option value="{$t->id}">{$t->name|escape}</option>
                                                {/foreach}
                                            </select>
                                        </td>
                                        <td style="width: 140px;" class="jsgrid-cell">
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="jsgrid-grid-body">
                                <table class="jsgrid-table table table-striped table-hover">
                                    <tbody>
                                    <tr class="jsgrid-row js-contract-row"
                                        data-contract="{$contract->id}">
                                        <td style="width: 20px" class="jsgrid-cell text-center"></td>
                                        <td style="width: 80px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 60px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 120px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 70px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 70px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 70px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 80px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 80px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 80px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 80px;" class="jsgrid-cell text-center"></td>
                                        <td style="width: 140px;" class="jsgrid-cell text-center"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Column -->
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End PAge Content -->
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

<div id="modal_add_comment" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Добавить комментарий</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form_add_comment" action="">

                    <input type="hidden" name="order_id" value=""/>
                    <input type="hidden" name="user_id" value=""/>
                    <input type="hidden" name="contactperson_id" value=""/>
                    <input type="hidden" name="action" value=""/>

                    <div class="alert" style="display:none"></div>

                    <div class="form-group">
                        <label for="name" class="control-label">Комментарий:</label>
                        <textarea class="form-control" name="text"></textarea>
                    </div>
                    <div class="custom-control custom-checkbox mr-sm-2 mb-3">
                        <input type="checkbox" name="official" class="custom-control-input" id="official_check"
                               value="1">
                        <label class="custom-control-label" for="official_check">Официальный</label>
                    </div>
                    <div class="form-action">
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">Сохранить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal_send_sms" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Отправить смс-сообщение?</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">


                <div class="card">
                    <div class="card-body">

                        <div class="tab-content tabcontent-border p-3" id="myTabContent">
                            <div role="tabpanel" class="tab-pane fade active show" id="waiting_reason"
                                 aria-labelledby="home-tab">
                                <form class="js-sms-form" data-manager-id="{$manager->id}">
                                    <input type="hidden" name="manager_id" value="{$manager->id}"/>
                                    <input type="hidden" name="user_id" value="{$order->user_id}"/>
                                    <input type="hidden" name="order_id" value=""/>
                                    <input type="hidden" name="yuk" value=""/>
                                    <input type="hidden" name="action" value="send_sms"/>
                                    <div class="form-group">
                                        <label for="name" class="control-label">Выберите шаблон сообщения:</label>
                                        <select name="template_id" class="form-control">
                                            {foreach $sms_templates as $sms_template}
                                                {if in_array($manager->role, ['developer', 'admin'])}
                                                    <option value="{$sms_template->id}"
                                                            title="{$sms_template->template|escape}">
                                                        {$sms_template->name|escape} ({$sms_template->template})
                                                    </option>
                                                {else}
                                                    {if $sms_template->type == 'collection'}
                                                        <option value="{$sms_template->id}"
                                                                title="{$sms_template->template|escape}">
                                                            {$sms_template->name|escape} ({$sms_template->template})
                                                        </option>
                                                    {/if}
                                                {/if}
                                            {/foreach}
                                        </select>
                                    </div>
                                    <div class="form-action clearfix">
                                        <button type="button" class="btn btn-danger btn-lg float-left waves-effect"
                                                data-dismiss="modal">Отменить
                                        </button>
                                        <button type="submit"
                                                class="btn btn-success btn-lg float-right waves-effect waves-light">Да,
                                            отправить
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div style="margin-left: 90px;" class="form-group">
                    <button class="btn btn-info btn-lg waves-effect waves-light" id="casual_sms">Свободное сообщение
                    </button>
                </div>
                <form class="js-sms-form" name="manager_id" data-manager-id="{$manager->id}">
                    <input type="hidden" name="manager_id" value="{$manager->id}"/>
                    <input type="hidden" name="user_id" value="{$order->user_id}"/>
                    <input type="hidden" name="order_id" value="{$order->id}"/>
                    <input type="hidden" name="role" value="{$manager->role}"/>
                    <input type="hidden" name="action" value="send_sms"/>
                    <textarea name="text_sms" class="form-control casual-sms-form"
                              style="display: none; height: 250px;"></textarea>
                    <ul class="casual-sms-form" style="display: none; margin-top: 5px">
                        <li>$firstname = Имя</li>
                        <li>$fio = ФИО</li>
                        <li>$prolongation_sum = Сумма для пролонгации</li>
                        <li>$final_sum = Сумма для погашения займа</li>
                        <li>Примечание: "ООО МКК Финансовый Аспект https://ecozaym24.ru/lk/login" дописывается
                            автоматически в любом сообщении
                        </li>
                    </ul>
                    <button class="btn btn-success btn-lg waves-effect waves-light casual-sms-form" id="send_casual_sms"
                            style="display: none;">Отправить свободное сообщение
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="modal_distribute" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content">

            <div class="modal-header">
                <h4 class="modal-title">Распределить договора</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                <form method="POST" id="form_distribute" action="">

                    <input type="hidden" name="action" value="distribute"/>

                    <div class="alert" style="display:none"></div>

                    <div class="form-group">
                        <select class="form-control js-select-type" name="type">
                            <option value="all">Все видимые</option>
                            <option value="checked">Все отмеченные</option>
                            <option value="optional">Выбрать количество</option>
                        </select>
                        <div class="pt-2">
                            <input class="form-control js-input-quantity" name="quantity" value="" style="display:none"
                                   placeholder="Количество договоров для распределения"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="name" class="control-label"><strong>Менеджеры для распределения:</strong></label>
                        <ul class="list-unstyled" style="max-height:250px;overflow:hidden auto;">
                            {foreach $managers as $m}
                                {if $m->role == 'collector' && !$m->blocked}
                                    <li>
                                        <div class="">
                                            <input class="" name="managers[]" id="distribute_{$m->id}" value="{$m->id}"
                                                   type="checkbox"/>
                                            <label for="distribute_{$m->id}" class="">{$m->name|escape}</label>
                                        </div>
                                    </li>
                                {/if}
                            {/foreach}
                        </ul>
                    </div>
                    <div class="form-action">
                        <button type="button" class="btn btn-danger waves-effect" data-dismiss="modal">Отмена</button>
                        <button type="submit" class="btn btn-success waves-effect waves-light">Распределить</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>