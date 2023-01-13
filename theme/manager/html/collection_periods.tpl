{$meta_title = 'Периоды' scope=parent}

{capture name='page_scripts'}
    <script>
        $(function () {
            $('.addModal, .editModal').on('click', function () {
                $('#periodModal').modal();

                if ($(this).hasClass('addModal')) {
                    $('.modal-title').text('Добавить период');
                    $('#periodForm').find('input[class="btn btn-success float-right"]').removeClass('editPeriod');
                    $('#periodForm').find('input[class="btn btn-success float-right"]').addClass('addPeriod');
                    $('#periodForm').find('input[name="action"]').attr('value', 'addPeriod');
                } else {

                    let id = $(this).attr('data-id');

                    $.ajax({
                        method: 'POST',
                        dataType: 'JSON',
                        data: {
                            id: id,
                            action: 'getPeriod'
                        },
                        success: function (period) {
                            $('#periodForm').find('input[name="name"]').val(period['name']);
                            $('#periodForm').find('input[name="period_from"]').val(period['period_from']);
                            $('#periodForm').find('input[name="period_to"]').val(period['period_to']);
                        }
                    });

                    $('.modal-title').text('Редактировать период');
                    $('#periodForm').find('input[class="btn btn-success float-right"]').removeClass('addPeriod');
                    $('#periodForm').find('input[class="btn btn-success float-right"]').addClass('editPeriod');
                    $('#periodForm').find('input[name="action"]').attr('value', 'editPeriod');
                    $('#periodForm').find('input[name="id"]').attr('value', id);
                }
            });

            $(document).on('click', '.addPeriod, .editPeriod', function () {
                let form = $(this).closest('form').serialize();

                $.ajax({
                    method: 'POST',
                    data: form,
                    success: function () {
                        location.reload();
                    }
                });
            });

            $('.delete').on('click', function () {
                let id = $(this).attr('data-id');

                $.ajax({
                    method: 'POST',
                    data: {
                        action: 'deletePeriod',
                        id: id
                    },
                    success: function () {
                        location.reload();
                    }
                });
            });
        });
    </script>
{/capture}

{capture name='page_styles'}

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
                    Периоды коллекшн
                </h3>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Главная</a></li>
                    <li class="breadcrumb-item active">Периоды коллекшн</li>
                </ol>
            </div>
        </div>
        <!-- ============================================================== -->
        <!-- End Bread crumb and right sidebar toggle -->
        <!-- ============================================================== -->
        <!-- ============================================================== -->
        <!-- Start Page Content -->
        <!-- ============================================================== -->
        <!-- Row -->
        <form class="" method="POST">
            <div class="card">
                <div class="card-body">
                    <div class="big-table">
                        <table id="config-table"
                               class="table table-hover">
                            <thead style="background-color: #009efb">
                            <tr>
                                <th style="width: 20%"
                                    class="sgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'index_number_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'index_number_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                    Название
                                </th>
                                <th style="width: 30%"
                                    class="jsgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'index_number_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'index_number_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                    Период c
                                </th>
                                <th style="width: 30%"
                                    class="jsgrid-header-cell jsgrid-align-right jsgrid-header-sortable {if $sort == 'index_number_desc'}jsgrid-header-sort jsgrid-header-sort-desc{elseif $sort == 'index_number_asc'}jsgrid-header-sort jsgrid-header-sort-asc{/if}">
                                    Период по
                                </th>
                                <th style="width: 1%"></th>
                                <th style="width: 30%"></th>
                            </tr>
                            </thead>
                            <tbody>
                            {foreach $periods as $period}
                                <tr>
                                    <td>{$period->name}</td>
                                    <td>{$period->period_from}</td>
                                    <td>{$period->period_to}</td>
                                    <td>
                                        <div class="btn btn-outline-warning editModal" data-id="{$period->id}"><i
                                                    class=" fas fa-edit"></i></div>
                                    </td>
                                    <td>
                                        <div class="btn btn-outline-danger delete" data-id="{$period->id}"><i
                                                    class=" fas fa-trash"></i></div>
                                    </td>
                                </tr>
                            {/foreach}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <hr class="mb-3 mt-3"/>

            <div class="row">
                <div class="col-12 grid-stack-item" data-gs-x="0" data-gs-y="0" data-gs-width="12">
                    <div class="btn btn-outline-success addModal">Добавить</div>
                </div>
        </form>
        <!-- Row -->
        <!-- ============================================================== -->
        <!-- End PAge Content -->
        <!-- ============================================================== -->
    </div>
    <!-- ============================================================== -->
    <!-- End Container fluid  -->
    <!-- ============================================================== -->
    {include file='footer.tpl'}
    <!-- ============================================================== -->
</div>

<div id="periodModal" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog"
     aria-labelledby="mySmallModalLabel" aria-hidden="true" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
                <form id="periodForm">
                    <input type="hidden" name="action" value="">
                    <input type="hidden" name="id">
                    <div class="form-group" style="display:flex; flex-direction: column">
                        <div class="form-group">
                            <label>Название</label>
                            <input type="text" name="name"
                                   class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Период c</label>
                            <input type="text" name="period_from"
                                   class="form-control"/>
                        </div>
                        <div class="form-group">
                            <label>Период по</label>
                            <input type="text" name="period_to"
                                   class="form-control"/>
                        </div>
                        <div>
                            <input type="button" class="btn btn-danger cancel" data-dismiss="modal" value="Отмена">
                            <input type="button" class="btn btn-success float-right" value="Сохранить">
                        </div>
                </form>
            </div>
        </div>
    </div>
</div>
