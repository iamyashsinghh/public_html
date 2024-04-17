
@extends('bdm.layouts.app')
@section('title', $page_heading)
@section('main')
<style>
    .overlay_container {
        position: relative;
    }

    .overlay_container .overlay_image  {
        opacity: 1;
        display: block;
        width: 100%;
        height: auto;
        transition: .5s ease;
        backface-visibility: hidden;
    }

   .overlay_container .overlay_body {
        transition: .5s ease;
        opacity: 0;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        -ms-transform: translate(-50%, -50%);
        text-align: center;
    }

    .overlay_container:hover .overlay_image {
        opacity: 0.3;
    }

    .overlay_container:hover .overlay_body {
        opacity: 1;
    }
</style>
@php
    
@endphp
<div class="content-wrapper pb-5">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">{{$page_heading}}</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card mb-5">
                        <div class="card-header text-light" style="background-color: var(--wb-renosand);">
                            <h3 class="card-title">{{$bdm_lead->business_name }} -- {{$bdm_lead->get_lead_cat->name}}</h3>
                        </div>
                        <div class="card-body">
                            <h3>Images</h3>
                            <form action="{{route("$view_used_for.aggrement_images.manage_process", $data->id)}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="user_id" value="{{isset($user_id) ? $user_id : 0}}"> {{-- this input elem is only used for vendor users--}}
                                <div class="form-group">
                                    <label for="">Multiple Selection</label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="customFile" name="gallery_images[]" multiple>
                                        <label class="custom-file-label" for="customFile">Choose file</label>
                                        <div>Selected Files: <i id="selected_file_name"></></div>
                                    </div>
                                </div>
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-sm text-light" style="background: var(--wb-dark-red);">Save</button>
                                    <a href="{{route('bdm.lead.view')}}/{{$bdm_lead->lead_id }}" type="submit" class="btn btn-sm btn-secondary text-light mx-3">Close</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div id="images_gallery" class="row">
                @if ($data->order_agreement_farm_image != null)
                    @foreach (explode(",", $data->order_agreement_farm_image) as $key => $item)
                    <div class="col-sm-3 py-2 text-center">
                        <div class="overlay_container">
                            <img data-name="{{$item}}" src="{{asset("storage/uploads/bdmAgreementImg/$item")}}" class="img-thumbnail sortable_content overlay_image" style="width: 300px; height: 200px;">
                            <div class="overlay_body">
                                <a data-id="{{$key}}" href="javascript:void(0);" class="text-danger" onclick="handle_image_delete(this, '{{$item}}')" style="font-size: 20px;"><i class="fa fa-trash"></i></a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script>
    const data_id = "{{$data->id}}";

    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.querySelector('.custom-file-input');
        const fileLabel = document.querySelector('.custom-file-label');
        const selectedFileName = document.getElementById('selected_file_name');

        if (fileInput) {
            fileInput.addEventListener('change', function() {
                let files = fileInput.files;
                let names = [];
                for (let i = 0; i < files.length; i++) {
                    names.push(files[i].name);
                    console.log(files[i].name);
                }
                if (fileLabel) {
                    fileLabel.textContent = names.join(', ');
                }
                if (selectedFileName) {
                    selectedFileName.textContent = names.join(', ');
                } 
            });
        }
    });


    function handle_image_delete(elem, image_name) {
    let userConfirmed = window.confirm("Are you sure you want to delete this image?");
    let url = `{{route('bdm.agreement_images.delete')}}/${data_id}`;
    if (userConfirmed) {
        fetch(url, { // Adjusted to use a template string with the correct path
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ image_name: image_name })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log(data);
            if (data.success) {
                const gallery_card = elem.parentElement.parentElement.parentElement;
                gallery_card.remove();
            }
            toastr[data.alert_type](data.message);
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    }
}



</script>

@endsection