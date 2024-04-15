@extends('admin.layouts.app')
@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading." | Non Venue CRM")
@section('main')
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$page_heading}}</h1>
                </div>
            </div>
            <div class="button-group my-4">
                <button class="btn text-light btn-sm buttons-print" onclick="handle_manage_category()" style="background-color: var(--wb-renosand)"><i class="fa fa-plus"></i> New</button>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="clientTable" class="table text-sm">
                    <thead>
                        <tr>
                            <th class="text-nowrap">ID</th>
                            <th class="text-nowrap">Category Name</th>
                            <th class="text-nowrap">Created At</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categories as $list)
                        <tr>
                            <td>{{$list->id}}</td>
                            <td class="text-bold">{{$list->name}}</td>
                            <td>{{date('d-M-Y', strtotime($list->created_at))}}</td>
                            <td class="d-flex justify-content-around">
                                <a href="javascript:void(0);" onclick="handle_manage_category({{$list->id}}, `{{$list->name}}`)" class="text-success" title="Edit"><i class="fa fa-edit"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
    <div class="modal fade" id="categoryManageModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="categoryManageModalTitle">Add Vendor Category</h3>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <form id="categoryManageForm" action="" method="post">
                    <div class="modal-body text-sm">
                        <div class="form-group">
                            @csrf
                            <label for="category_name_inp">Category Name</label>
                            <input type="text" class="form-control" id="category_name_inp" placeholder="Enter category name" name="category_name">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-sm text-light" style="background-color: var(--wb-dark-red);">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
    initialize_datatable();

    const modal = new bootstrap.Modal("#categoryManageModal");
    function handle_manage_category(category_id = 0, category_name = null){
        const submit_url = `{{route('admin.vendorCategory.manage.process')}}/${category_id}`;
        categoryManageForm.action = submit_url;
        category_name_inp.value = category_name;
        if(category_id > 0){
            categoryManageModalTitle.innerText = 'Edit Vendor Category';
        }else{
            categoryManageModalTitle.innerText = 'Add Vendor Category';
        }
        modal.show();
    }
</script>
@endsection